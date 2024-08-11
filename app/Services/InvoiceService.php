<?php

namespace App\Services;

use App\Models\Service;
use App\Models\Invoice;
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
                'due_date' => $endDate->copy()->addDays(5),//due_date 5 days after end date for example
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
                'due_date' => $endDate->copy()->addDays(5),//due_date 5 days after end date for example
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

    //Generar facturas desde hace 3 meses
    // public function generateCurrentMonthInvoices()
    // {
    //     $currentDate = Carbon::now();
    //     $threeMonthsAgo = $currentDate->copy()->subMonths(3)->startOfMonth();
    //     $endOfCurrentMonth = $currentDate->copy()->addMonth();
    //     $totalInvoices = 0;

    //     Log::info("####Generando facturas desde {$threeMonthsAgo->toDateString()} a {$endOfCurrentMonth->toDateString()}");

    //     $services = Service::where('status', 'activo')->get();

    //     foreach ($services as $service) {
    //         $lastInvoice = $service->invoices()->orderBy('end_date', 'desc')->first();
    //         Log::info("lastInvoice=> {$lastInvoice}" );
    //         $startDate = $lastInvoice ? Carbon::parse($lastInvoice->end_date)->addDay() : Carbon::parse($service->installation_date);

    //         Log::info("startdateI=> {$startDate}");

    //         // Asegurarse de que el startDate esté dentro del rango de tres meses
    //         if ($startDate->lessThan($threeMonthsAgo)) {
    //             $startDate = $threeMonthsAgo;
    //         }

    //         while ($startDate->lessThanOrEqualTo($endOfCurrentMonth)) {
    //             $endDate = $startDate->copy()->addMonth()->subDay();

    //             // Verificar si la factura ya existe para el rango de fechas
    //             $existingInvoice = Invoice::where('service_id', $service->id)
    //                 ->where('start_date', $startDate)
    //                 ->where('end_date', $endDate)
    //                 ->first();

    //             if ($existingInvoice) {
    //                 Log::info("Factura existe para el service_id: {$service->id} desde {$startDate->toDateString()} to {$endDate->toDateString()}");
    //             } else {
    //                 // Crear la factura solo si la fecha de inicio está dentro del rango de los últimos tres meses hasta el mes actual
    //                 if ($startDate->greaterThanOrEqualTo($threeMonthsAgo) && $endDate->lessThanOrEqualTo($endOfCurrentMonth)) {
    //                     Log::info("Creando factura para service_id: {$service->id} desde {$startDate->toDateString()} hasta {$endDate->toDateString()}");

    //                     Invoice::create([
    //                         'service_id' => $service->id,
    //                         'price' => $service->plans->price,
    //                         'igv' => 0.00,
    //                         'discount' => 0.00,
    //                         'amount' => 0.00,
    //                         'letter_amount' => null,
    //                         'due_date' => $endDate->copy()->addDays(5),//due_date 5 days after end date for example
    //                         'start_date' => $startDate,
    //                         'end_date' => $endDate,
    //                         'paid_dated' => null,
    //                         'receipt' => null,
    //                         'note' => null,
    //                         'status' => 'pendiente',
    //                     ]);
    //                     $totalInvoices++;

    //                     Log::info("Factura creada para service_id: {$service->id} desde {$startDate} hasta {$endDate}");
    //                 }

    //             }

    //             // Asegurar que el bucle avance al próximo mes
    //             $startDate = $startDate->copy()->addMonth();
    //         }
    //     }
    //     $this->updateOverdueInvoices();
    //     return $totalInvoices;
    // }


    public function generateCurrentMonthInvoices()
    {
        $currentDate = Carbon::now();
        $threeMonthsAgo = $currentDate->copy()->subMonths(3)->startOfMonth();
        $endOfCurrentMonth = $currentDate->copy()->addMonth();
        $totalInvoices = 0;

        Log::info("#### Generando facturas desde {$threeMonthsAgo->toDateString()} a {$endOfCurrentMonth->toDateString()}");

        $services = Service::where('status', 'activo')->get();

        foreach ($services as $service) {
            $lastInvoice = $service->invoices()->orderBy('end_date', 'desc')->first();
            $startDate = $lastInvoice ? Carbon::parse($lastInvoice->end_date)->addDay() : Carbon::parse($service->installation_date);

            // Asegurarse de que el startDate esté dentro del rango de tres meses
            if ($startDate->lessThan($threeMonthsAgo)) {
                $startDate = $threeMonthsAgo;
            }

            // Validar si ya se generó la factura de instalación
            $installationInvoiceExists = Invoice::where('service_id', $service->id)
            ->where('start_date', $service->installation_date)
                ->where('end_date', $service->installation_date)
                ->exists();

            if ($service->installation_payment && !$installationInvoiceExists
            ) {
                Invoice::create([
                    'service_id' => $service->id,
                    'price' => $service->installation_amount,
                    'igv' => 0.00,
                    'discount' => 0.00,
                    'amount' => 0.00,
                    'letter_amount' => null,
                    'due_date' =>  Carbon::parse($service->installation_date)->copy()->addDays(5),
                    'start_date' => $service->installation_date,
                    'end_date' => $service->installation_date,
                    'paid_dated' => null,
                    'receipt' => null,
                    'note' => 'Factura por instalación',
                    'status' => 'pendiente',
                ]);
                $totalInvoices++;
                Log::info("Factura de instalación creada para service_id: {$service->id}");
            }

            while ($startDate->lessThanOrEqualTo($endOfCurrentMonth)) {
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
                        'receipt' => null,
                        'note' => null,
                        'status' => 'pendiente',
                    ]);
                    $totalInvoices++;
                    Log::info("Factura creada para service_id: {$service->id} desde {$startDate} hasta {$endDate}");
                }

                // Asegurar que el bucle avance al próximo mes
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
        $threeMonthsAgo = $currentDate->copy()->subMonths(3)->startOfMonth();
        $endOfCurrentMonth = $currentDate->copy()->addMonth();
        $totalInvoices = 0;

        Log::info("#### Generando facturas para el servicio {$serviceId} desde {$threeMonthsAgo->toDateString()} a {$endOfCurrentMonth->toDateString()}");

        // Buscar el servicio por ID
        $service = Service::where('status', 'activo')->find($serviceId);

        if (!$service) {
            Log::error("Servicio con ID {$serviceId} no encontrado o no está activo.");
            return $totalInvoices;
        }

        $lastInvoice = $service->invoices()->orderBy('end_date',
            'desc'
        )->first();
        $startDate = $lastInvoice ? Carbon::parse($lastInvoice->end_date)->addDay() : Carbon::parse($service->installation_date);

        // Asegurarse de que el startDate esté dentro del rango de tres meses
        if ($startDate->lessThan($threeMonthsAgo)) {
            $startDate = $threeMonthsAgo;
        }

        // Validar si ya se generó la factura de instalación
        $installationInvoiceExists = Invoice::where('service_id', $service->id)
        ->where('start_date', $service->installation_date)
        ->where('end_date', $service->installation_date)
        ->exists();

        if ($service->installation_payment && !$installationInvoiceExists) {
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
                'receipt' => null,
                'note' => 'Factura por instalación',
                'status' => 'pendiente',
            ]);
            $totalInvoices++;
            Log::info("Factura de instalación creada para service_id: {$service->id}");
        }

        while ($startDate->lessThanOrEqualTo($endOfCurrentMonth)) {
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
                    'receipt' => null,
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
