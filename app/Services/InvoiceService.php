<?php

namespace App\Services;

use App\Models\Service;
use App\Models\Invoice;
use App\Models\Plan;
use App\Models\Suspension;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class InvoiceService
{
    // Esta funcion genera la factura de mes siguiente, sin fin
    public function generateMonthlyInvoices()
    {
        $contracts = Service::where('status', 'activo')->get();

        foreach ($contracts as $service) {

            Log::info("Processing service_id: {$service->id}");
            $lastInvoice = $service->invoices()->orderBy('end_date', 'desc')->first();
            Log::info("Last invoice: " . ($lastInvoice ? $lastInvoice->id : 'none'));

            if ($lastInvoice) {
                $startDate = Carbon::parse($lastInvoice->end_date)->addDay();
            } else {
                $startDate = Carbon::parse($service->installation_date);
            }

            // Ajustar la fecha de fin al mismo día del próximo mes
            $endDate = $startDate->copy()->addMonth()->subDay();

            // Create the new invoice
            Invoice::create([
                'service_id' => $service->id,
                'amount' => $service->plans->price,
                'igv' => 0.00,
                'discount' => 0.00,
                'letter_amount' => "",
                'due_date' => $endDate->copy()->addDays(5), //due_date 5 days after end date for example
                'start_date' => $startDate,
                'end_date' => $endDate,
                'receipt' => "",
                'note' => "",
                'status' => 'pending',
            ]);
            Log::info("Created invoice for service_id: {$service->id} from {$startDate} to {$endDate}");
        }
    }

    //Generar facturas del mes actual sin repetir
    public function generateCurrentMonthInvoices2()
    {

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;
        $totalInvoices = 0;

        $services = Service::where('status', 'activo')->get();

        foreach ($services as $service) {
            Log::info("Processing service_id: {$service->id}");

            $lastInvoice = $service->invoices()->orderBy('end_date', 'desc')->first();
            Log::info("Last invoice: " . ($lastInvoice ? $lastInvoice->id : 'none'));

            if ($lastInvoice) {
                $startDate = Carbon::parse($lastInvoice->end_date)->addDay();
            } else {
                $startDate = Carbon::parse($service->installation_date);
                Log::info("Start date: {$startDate}");
            }

            // Ajustar la fecha de fin al mismo día del próximo mes
            $endDate = $startDate->copy()->addMonth()->subDay();
            Log::info("End date: {$endDate}");
            // Verificar si la fecha de fin pertenece al mes y año actuales
            if ($startDate->month != $currentMonth || $startDate->year != $currentYear) {
                Log::info("Skipping creación de factura para service_id: {$service->id}, no pertenece al mes en curso o ya existe.");
                continue;
            }

            Log::info("Start date: {$startDate}, End date: {$endDate}");

            // Crear la nueva factura solo si pertenece al mes en curso
            Invoice::create([
                'service_id' => $service->id,
                'price' => $service->plans->price,
                'igv' => 0.00,
                'discount' => 0.00,
                'amount' => 0.00,
                'letter_amount' => null,
                'due_date' => $endDate->copy()->addDays(5), //due_date 5 days after end date for example
                'start_date' => $startDate,
                'end_date' => $endDate,
                'paid_dated' => null,
                'receipt' => null,
                'note' => null,
                'status' => 'pendiente',
            ]);

            $totalInvoices++;

            Log::info("Factura creada para service_id: {$service->id} from {$startDate} to {$endDate}");
        }
        return $totalInvoices;
    }


    //Implementado en el sistema
    public function generateCurrentMonthInvoices()
    {
        $currentDate = Carbon::now();
        //$startOfLoop = $currentDate->copy()->subMonths(1)->startOfMonth(); // Factura desde el mes anterior si aplica
        $startOfLoop = $currentDate->copy()->startOfMonth();
        $endOfLoop = $currentDate->copy()->endOfMonth(); // Hasta fin de este mes
        //$endOfLoop =  $currentDate->copy()->addMonth();
        $totalInvoices = 0;

        Log::info("#### Generando facturas desde {$startOfLoop->toDateString()} a {$endOfLoop->toDateString()}");

        $services = Service::where('status', 'activo')->get();

        foreach ($services as $service) {
            $lastInvoice = $service->invoices()->orderBy('end_date', 'desc')->first();
            $startDate = $lastInvoice
                ? Carbon::parse($lastInvoice->end_date)->addDay()
                : Carbon::parse($service->installation_date);

            // Verificar si ya se facturó la instalación
            $installationInvoiceExists = Invoice::where('service_id', $service->id)
                ->where('start_date', $service->installation_date)
                ->where('end_date', $service->installation_date)
                ->exists();

            // Obtener el último número de recibo
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

            // Bucle mensual con corte personalizado
            while ($startDate->lessThanOrEqualTo($endOfLoop)) {
                $endDate = $startDate->copy()->addMonth()->subDay();

                Log::info("## Vamos a generar facturas desde {$startDate->toDateString()} a {$endDate->toDateString()} para el servicio {$service->id}");

                // Verificar si ya existe la factura para ese rango exacto
                $existingInvoice = Invoice::withoutStoreScope()
                    ->where('service_id', $service->id)
                    ->where('enterprise_id', $service->enterprise_id)
                    ->whereDate('start_date', $startDate)
                    ->whereDate('end_date', $endDate)
                    ->exists();

                Log::info('¿Factura ya existe?: ' . ($existingInvoice ? 'Sí' : 'No'));

                if (!$existingInvoice) {
                    Log::info("Generando factura para service_id: {$service->id} del {$startDate->toDateString()} al {$endDate->toDateString()}");

                    // Buscar suspensiones en el rango
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
                    $fullPrice = $service->plans->price;
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
                        'note' => null,
                        'status' => 'pendiente',
                    ]);
                    $totalInvoices++;
                    Log::info("Factura creada (service_id: {$service->id}) - Días activos: {$activeDays}, Precio: {$proratedPrice}");
                } else {
                    Log::info("Factura ya existe para service_id: {$service->id} del {$startDate->toDateString()} al {$endDate->toDateString()}, se omite.");
                }

                // Avanzar al próximo mes
                $startDate = $startDate->copy()->addMonth();
            }
        }

        $this->updateOverdueInvoices();
        return $totalInvoices;
    }



    //Generar facturas por id de servicio
    public function generateInvoicesForService($serviceId)
    {
        $currentDate = Carbon::now();
        $startOfMonth = $currentDate->copy()->startOfMonth();
        $endOfMonth = $currentDate->copy()->endOfMonth();
        $totalInvoices = 0;

        Log::info("#### Generando facturas para el servicio {$serviceId} desde {$startOfMonth->toDateString()} a {$endOfMonth->toDateString()}");

        // Buscar el servicio por ID
        $service = Service::where('status', 'activo')->find($serviceId);

        if (!$service) {
            Log::error("Servicio con ID {$serviceId} no encontrado o no está activo.");
            return $totalInvoices;
        }

        $lastInvoice = $service->invoices()->orderBy(
            'end_date',
            'desc'
        )->first();
        $startDate = $lastInvoice ? Carbon::parse($lastInvoice->end_date)->addDay() : Carbon::parse($service->installation_date);

        // Asegurarse de que el startDate esté dentro del rango de tres meses
        if ($startDate->lessThan($startOfMonth)) {
            $startDate = $startOfMonth;
        }

        // Validar si ya se generó la factura de instalación
        $installationInvoiceExists = Invoice::where('service_id', $service->id)
            ->where('start_date', $service->installation_date)
            ->where('end_date', $service->installation_date)
            ->exists();

        // Obtener el último número de recibo
        $lastReceipt = Invoice::whereNotNull('receipt')->orderBy('receipt', 'desc')->first();
        $nextReceiptNumber = $lastReceipt ? intval(substr($lastReceipt->receipt, -6)) + 1 : 1;

        if ($service->installation_payment && !$installationInvoiceExists) {

            // Formatear y asignar el recibo
            $formattedReceipt = '003-N°. ' . sprintf('%06d', $nextReceiptNumber++);

            Invoice::create([
                'service_id' => $service->id,
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

        while ($startDate->lessThanOrEqualTo($endOfMonth)) {
            $endDate = $startDate->copy()->addMonth()->subDay();

            // Verificar si la factura ya existe para el rango de fechas
            $existingInvoice = Invoice::where('service_id', $service->id)
                ->where('start_date', $startDate)
                ->where('end_date', $endDate)
                ->first();

            if (!$existingInvoice) {
                Log::info("Creando factura para service_id: {$service->id} desde {$startDate->toDateString()} hasta {$endDate->toDateString()}");

                $dueDate = $service->prepayment
                    ? $startDate->copy()->addDays(5)
                    : $endDate->copy()->addDays(5);

                // Formatear y asignar el recibo
                $formattedReceipt = '003-N°. ' . sprintf('%06d', $nextReceiptNumber++);

                Invoice::create([
                    'service_id' => $service->id,
                    'price' => $service->plans->price,
                    'igv' => 0.00,
                    'discount' => 0.00,
                    'amount' => 0.00,
                    'letter_amount' => null,
                    'due_date' => $dueDate,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'paid_dated' => null,
                    'receipt' => $formattedReceipt,
                    'note' => null,
                    'status' => 'pendiente',
                ]);
                $totalInvoices++;
                Log::info("Factura creada para service_id: {$service->id} desde {$startDate} hasta {$endDate}");
            }

            // Asegurar que el bucle avance al próximo mes
            $startDate = $startDate->copy()->addMonth();
        }

        $this->updateOverdueInvoices();
        return $totalInvoices;
    }


    private function updateOverdueInvoices()
    {
        $currentDate = Carbon::now();
        $overdueInvoices = Invoice::where('status', 'pendiente')
            ->where('due_date', '<', $currentDate)
            ->get();

        foreach ($overdueInvoices as $invoice) {
            Log::info("Updating invoice_id: {$invoice->id} to status 'vencida'");
            $invoice->update(['status' => 'vencida']);
        }
    }
}
