<?php

namespace App\Services;

use App\Models\Service;
use App\Models\Invoice;
use App\Models\Promotion;
use App\Models\Suspension;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class InvoiceService
{
    //Implementado en el sistema
    public function generateCurrentMonthInvoices($id = null, $months = 0)
    {
        $serviceId = $id;
        $currentDate = Carbon::now();
        $startOfLoop = $currentDate->copy()->startOfMonth();
        $endOfLoop = $currentDate->copy()->addMonths($months)->endOfMonth();
        $totalInvoices = 0;

        Log::info("#### Generando facturas desde {$startOfLoop->toDateString()} a {$endOfLoop->toDateString()} para {$serviceId}");

        try {
            DB::beginTransaction();

            $servicesQuery = Service::where('status', 'activo');
            if ($serviceId) {
                $servicesQuery->where('id', $serviceId);
            }
            $services = $servicesQuery->get();

            foreach ($services as $service) {
                $lastInvoice = $service->invoices()->orderBy('end_date', 'desc')->first();
                $startDate = $lastInvoice
                    ? Carbon::parse($lastInvoice->end_date)->addDay()
                    : Carbon::parse($service->installation_date);

                $nextReceiptNumber = $this->getNextReceiptNumber();

                // Generar factura de instalación si corresponde
                if ($this->shouldGenerateInstallationInvoice($service)) {
                    $this->generateInstallationInvoice($service, $nextReceiptNumber);
                    $totalInvoices++;
                }

                while ($startDate->lessThanOrEqualTo($endOfLoop)) {
                    $endDate = $startDate->copy()->addMonth()->subDay();

                    Log::info("## Verificando período {$startDate->toDateString()} a {$endDate->toDateString()} para servicio {$service->id}");

                    // Verificar suspensiones activas
                    $activeSuspensions = Suspension::where('service_id', $service->id)
                        //->where('status', false) // Asumiendo que 0 (false) = Reactivada (para contabilizar)
                        ->whereNotNull('reactivation_date') // Mantenemos este filtro, pero te recomiendo verificar el dato
                        ->where(function ($query) use ($startDate, $endDate) {
                            // Lógica de Solapamiento Optimizada:
                            // La suspensión debe empezar antes o en la fecha de fin del período de facturación ($endDate)
                            // Y debe terminar después o en la fecha de inicio del período de facturación ($startDate)
                            $query->where('start_date', '<=', $endDate)
                                ->where('reactivation_date', '>=', $startDate);
                        })->get();

                    // Log de depuración (Mantenlo para confirmar si se encuentra el dato)
                    Log::info("Suspensiones encontradas para {$startDate->toDateString()} a {$endDate->toDateString()}: " . $activeSuspensions->toJson());


                    // Calcular días activos y suspendidos
                    $totalDays = $startDate->diffInDays($endDate) + 1;
                    $suspendedDays = $this->calculateSuspendedDays($activeSuspensions, $startDate, $endDate);
                    $activeDays = $totalDays - $suspendedDays;

                    // Si el período está completamente suspendido, saltamos
                    if ($activeDays <= 0) {
                        Log::info("Período completamente suspendido para service_id: {$service->id}");
                        $startDate = $startDate->copy()->addMonth();
                        continue;
                    }

                    // Verificar si ya existe una factura
                    $existingInvoice = $this->checkExistingInvoice($service, $startDate, $endDate);
                    if ($existingInvoice) {
                        Log::info("Factura ya existe para service_id: {$service->id}");
                        $startDate = $startDate->copy()->addMonth();
                        continue;
                    }

                    // Calcular precio según promoción o plan regular
                    $fullPrice = $this->calculatePrice($service, $currentDate);
                    $proratedPrice = $this->calculateProratedPrice($fullPrice, $activeDays, $totalDays);

                    // Generar factura si el precio es mayor a 0
                    if ($proratedPrice > 0) {
                        $nextReceiptNumber = $this->getNextReceiptNumber();
                        $this->createInvoice(
                            $service,
                            $startDate,
                            $endDate,
                            $proratedPrice,
                            $activeDays,
                            $nextReceiptNumber,
                            $activeSuspensions
                        );
                        $totalInvoices++;
                    }

                    $startDate = $startDate->copy()->addMonth();
                }
            }

            $this->updateOverdueInvoices();
            DB::commit();
            return $totalInvoices;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error($e->getMessage());
            return response()->json([
                'message' => 'Error al Generar las facturas.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function checkExistingInvoice($service, $startDate, $endDate)
    {
        return Invoice::where('service_id', $service->id)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($q) use ($startDate, $endDate) {
                        $q->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })->exists();
    }

    private function generateInvoiceNote($service, $activeDays, $suspensions)
    {
        $notes = [];

        // Agregar información de promoción si existe
        if ($service->promotion_id) {
            $notes[] = "Promoción: {$service->promotion->name}";
        }

        // Agregar información de prorrateo si hay suspensiones
        if ($suspensions->isNotEmpty()) {
            $notes[] = "Factura prorrateada - {$activeDays} días activos";

            // Agregar detalle de suspensiones
            foreach ($suspensions as $suspension) {
                $start = Carbon::parse($suspension->start_date)->format('d/m/Y');
                $end = Carbon::parse($suspension->reactivation_date)->format('d/m/Y');
                $notes[] = "Suspensión: {$start} - {$end}";
            }
        }

        return !empty($notes) ? implode(' | ', $notes) : null;
    }


    private function shouldGenerateInstallationInvoice($service)
    {
        // Verificar si ya existe una factura de instalación
        $hasInstallationInvoice = Invoice::where('service_id', $service->id)
            ->where('note', 'like', '%Cargo de instalación%')
            ->exists();

        // Generar factura de instalación solo si:
        // 1. No existe una factura previa de instalación
        // 2. El servicio tiene cargo de instalación (installation_fee > 0)
        return !$hasInstallationInvoice && $service->installation_fee > 0;
    }

    private function generateInstallationInvoice($service, $receiptNumber)
    {
        $installDate = Carbon::parse($service->installation_date);
        $dueDate = $installDate->copy()->addDays(5);

        $formattedReceipt = '003-N°. ' . sprintf('%06d', $receiptNumber);

        Invoice::create([
            'service_id' => $service->id,
            'enterprise_id' => $service->enterprise_id,
            'price' => $service->installation_fee,
            'igv' => 0.00,
            'discount' => 0.00,
            'amount' => 0.00,
            'letter_amount' => null,
            'due_date' => $dueDate,
            'start_date' => $installDate,
            'end_date' => $installDate,
            'paid_dated' => null,
            'receipt' => $formattedReceipt,
            'note' => 'Cargo de instalación',
            'status' => 'pendiente',
        ]);

        Log::info("Factura de instalación creada para service_id: {$service->id}");
    }


    private function calculateSuspendedDays($suspensions, $startDate, $endDate)
    {
        $suspendedDays = 0;
        foreach ($suspensions as $suspension) {
            $suspendStart = max($startDate, Carbon::parse($suspension->start_date));
            $suspendEnd = min($endDate, Carbon::parse($suspension->reactivation_date));
            $suspendedDays += $suspendStart->diffInDays($suspendEnd) + 1;
        }
        Log::info("Total de días suspendidos: {$suspendedDays}");
        return $suspendedDays;
    }

    private function calculatePrice($service, $currentDate)
    {
        if (!$service->promotion_id) {
            return $service->plans->price;
        }

        $promotion = $service->promotion;
        $endDatePromotion = Carbon::parse($service->installation_date)
            ->startOfMonth()
            ->addMonths($promotion->duration_months - 1)
            ->endOfMonth();

        return $currentDate->lte($endDatePromotion)
            ? $promotion->price
            : $service->plans->price;
    }

    private function calculateProratedPrice($fullPrice, $activeDays, $totalDays)
    {
        return $totalDays > 0 ? round(($fullPrice * $activeDays) / $totalDays, 2) : 0;
    }

    private function createInvoice($service, $startDate, $endDate, $price, $activeDays, $receiptNumber, $suspensions)
    {
        $dueDate = $service->prepayment
            ? $startDate->copy()->addDays(5)
            : $endDate->copy()->addDays(5);

        $note = $this->generateInvoiceNote($service, $activeDays, $suspensions);
        $formattedReceipt = '003-N°. ' . sprintf('%06d', $receiptNumber);

        Invoice::create([
            'service_id' => $service->id,
            'enterprise_id' => $service->enterprise_id,
            'price' => $price,
            'igv' => 0.00,
            'discount' => 0.00,
            'amount' => 0.00,
            'letter_amount' => null,
            'due_date' => $dueDate,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'paid_dated' => null,
            'receipt' => $formattedReceipt,
            'note' => $note,
            'status' => 'pendiente',
        ]);

        Log::info("Factura creada (service_id: {$service->id}) - Días activos: {$activeDays}, Precio: {$price}");
    }


    private function updateOverdueInvoices()
    {
        $currentDate = Carbon::now();
        $overdueInvoices = Invoice::where('status', 'pendiente')
            ->where('start_date', '<', $currentDate)
            ->get();

        foreach ($overdueInvoices as $invoice) {
            Log::info("Actualizando invoice_id: {$invoice->id} to status 'vencida'");
            $invoice->update(['status' => 'vencida']);
        }
    }

    // private function getNextReceiptNumber()
    // {
    //     $lastInvoice = Invoice::orderBy('id', 'desc')->first();

    //     if (!$lastInvoice) {
    //         return 1;
    //     }

    // Extraer el número del último recibo (formato: '003-N°. 000001')
    //     $lastNumber = (int) substr($lastInvoice->receipt, 7);
    //     return $lastNumber + 1;
    // }


    private function getNextReceiptNumber()
    {
        // 1. Buscar la factura que tenga el número de recibo MÁS ALTO
        // Usamos orderByRaw para convertir la parte numérica del string en un número
        // y ordenarlo descendientemente.
        // SUBSTRING(receipt, 9) asume 8 caracteres de prefijo ('003-N°. ')
        // y empieza en el 9no caracter (que es el número).
        $lastInvoice = Invoice::where('receipt', 'LIKE', '003-N°. %')
            ->orderByRaw('CAST(SUBSTRING(receipt, 9) AS UNSIGNED) DESC')
            ->first();

        if (!$lastInvoice) {
            // No se encontró ninguna factura con ese formato, empezar en 1
            return 1;
        }

        // 2. Extraer el número de forma segura (el prefijo '003-N°. ' tiene 8 chars)
        $lastNumber = (int) substr($lastInvoice->receipt, 8);

        // 3. Retornar el siguiente número
        return $lastNumber + 1;
    }
}
