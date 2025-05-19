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
    public function generateCurrentMonthInvoices($id = null)
    {
        $serviceId = $id; // Puede ser null
        $currentDate = Carbon::now();
        $startOfLoop = $currentDate->copy()->startOfMonth();
        $endOfLoop = $currentDate->copy()->endOfMonth();
        $totalInvoices = 0;

        Log::info("#### Generando facturas desde {$startOfLoop->toDateString()} a {$endOfLoop->toDateString()} para {$serviceId}");

        try {
            DB::beginTransaction();

            // Obtener solo el servicio específico si se proporciona serviceId
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

                $installationInvoiceExists = Invoice::where('service_id', $service->id)
                    ->where('start_date', $service->installation_date)
                    ->where('end_date', $service->installation_date)
                    ->exists();

                $lastReceipt = Invoice::whereNotNull('receipt')->orderBy('receipt', 'desc')->first();
                $nextReceiptNumber = $lastReceipt ? intval(substr($lastReceipt->receipt, -6)) + 1 : 1;

                if ($service->installation_payment && !$installationInvoiceExists) {
                    $formattedReceipt = '003-N°. ' . sprintf('%06d', $nextReceiptNumber++);

                    Invoice::create([
                        'service_id' => $service->id,
                        'enterprise_id' => $service->enterprise_id,
                        'price' => $service->installation_amount,
                        'igv' => 0.00,
                        'discount' => 0.00,
                        'amount' => 0.00,
                        'letter_amount' => null,
                        'due_date' => Carbon::parse($service->installation_date)->copy()->addDays(5),
                        'start_date' => $service->installation_date,
                        'end_date' => $service->installation_date,
                        'paid_dated' => null,
                        'receipt' => $formattedReceipt,
                        'note' => 'Factura por instalación',
                        'status' => 'pendiente',
                    ]);
                    $totalInvoices++;
                    Log::info("Factura de instalación creada para service_id: {$service->id}");
                }

                while ($startDate->lessThanOrEqualTo($endOfLoop)) {
                    $endDate = $startDate->copy()->addMonth()->subDay();

                    Log::info("## Vamos a generar facturas desde {$startDate->toDateString()} a {$endDate->toDateString()} para el servicio {$service->id}");

                    // Verificar si ya existe la factura para ese rango exacto
                    $existingInvoice = Invoice::withoutStoreScope()
                        ->where('service_id', $service->id)
                        ->where('enterprise_id', $service->enterprise_id)
                        ->whereDate('start_date', $startDate)
                        ->whereDate('end_date', $endDate)
                        ->where('status', 'pendiente') // Solo si es pendiente
                        ->first();

                    Log::info('¿Factura ya existe?: ' . ($existingInvoice ? 'Sí' : 'No'));

                    // Buscar si hay una suspensión REACTIVADA (status = 0) que afecte el rango
                    $suspensionExists = Suspension::where('service_id', $service->id)
                        ->where('status', 0) // Solo reactivadas
                        ->where(function ($query) use ($startDate, $endDate) {
                            $query->whereBetween('start_date', [$startDate, $endDate])
                                ->orWhereBetween('end_date', [$startDate, $endDate])
                                ->orWhere(function ($q) use ($startDate, $endDate) {
                                    $q->where('start_date', '<=', $startDate)
                                        ->where('end_date', '>=', $endDate);
                                });
                        })
                        ->exists();

                    if ($existingInvoice && $suspensionExists) {
                        Log::info("Factura existente encontrada con suspensión para el rango {$startDate->toDateString()} - {$endDate->toDateString()}, eliminando para regenerar...");
                        $existingInvoice->delete();
                        $existingInvoice = null; // Marcamos que ya no existe
                    }

                    if (!$existingInvoice) {
                        Log::info("Generando factura para service_id: {$service->id} del {$startDate->toDateString()} al {$endDate->toDateString()}");

                        $suspensions = Suspension::where('service_id', $service->id)
                            ->where('status', true)
                            ->where(function ($query) use ($startDate, $endDate) {
                                $query->whereBetween('start_date', [$startDate, $endDate])
                                    ->orWhereBetween('end_date', [$startDate, $endDate])
                                    ->orWhere(function ($q) use ($startDate, $endDate) {
                                        $q->where('start_date', '<=', $startDate)
                                            ->where('end_date', '>=', $endDate);
                                    });
                            })->get();

                        $totalDays = $startDate->diffInDays($endDate) + 1;
                        $suspendedDays = 0;

                        foreach ($suspensions as $suspension) {
                            $suspendStart = Carbon::parse($suspension->start_date)->greaterThan($startDate)
                                ? Carbon::parse($suspension->start_date)
                                : $startDate;
                            $suspendEnd = Carbon::parse($suspension->end_date)->lessThan($endDate)
                                ? Carbon::parse($suspension->end_date)
                                : $endDate;

                            $suspendedDays += $suspendStart->diffInDays($suspendEnd) + 1;
                        }

                        $activeDays = max($totalDays - $suspendedDays, 0);

                        // Verificar si el servicio tiene una promoción activa
                        $note = null;
                        if ($service->promotion_id) {
                            $promotion = $service->promotion;
                            $endDatePromotion = Carbon::parse($service->installation_date)->addMonths($promotion->duration_months);

                            if (Carbon::now()->lte($endDatePromotion)) {
                                $fullPrice = $promotion->price;
                                $note = 'Promoción aplicada: ' . $promotion->name;
                            } else {
                                $fullPrice = $service->plans->price;
                            }
                        } else {
                            $fullPrice = $service->plans->price;
                        }

                        $proratedPrice = $totalDays > 0 ? round(($fullPrice * $activeDays) / $totalDays, 2) : 0;

                        $dueDate = $service->prepayment
                            ? $startDate->copy()->addDays(5)
                            : $endDate->copy()->addDays(5);

                        $formattedReceipt = '003-N°. ' . sprintf('%06d', $nextReceiptNumber++);

                        Invoice::create([
                            'service_id' => $service->id,
                            'enterprise_id' => $service->enterprise_id,
                            'price' => $proratedPrice,
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
                        $totalInvoices++;
                        Log::info("Factura creada (service_id: {$service->id}) - Días activos: {$activeDays}, Precio: {$proratedPrice}");
                    } else {
                        Log::info("Factura ya existe para service_id: {$service->id} del {$startDate->toDateString()} al {$endDate->toDateString()}, se omite.");
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
}
