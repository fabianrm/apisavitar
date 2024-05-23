<?php

namespace App\Services;

use App\Models\Service;
use App\Models\Invoice;
use Carbon\Carbon;

class InvoiceService
{
    // Esta funcion genera la factura de mes siguiente, sin fin
    public function generateMonthlyInvoices()
    {
        $contracts = Service::where('status', 'activo')->get();

        foreach ($contracts as $service) {

            \Log::info("Processing service_id: {$service->id}");
            $lastInvoice = $service->invoices()->orderBy('end_date', 'desc')->first();
            \Log::info("Last invoice: " . ($lastInvoice ? $lastInvoice->id : 'none'));

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
                'status' => 'pending',
            ]);
            \Log::info("Created invoice for service_id: {$service->id} from {$startDate} to {$endDate}");
        }
    }


    //Generar facturas del mes actual sin repetir
    public function generateCurrentMonthInvoices()
    {

        $currentMonth = Carbon::now()->month;
        $currentYear = Carbon::now()->year;

        $services = Service::where('status', 'activo')->get();

        foreach ($services as $service) {
            \Log::info("Processing service_id: {$service->id}");

            $lastInvoice = $service->invoices()->orderBy('end_date', 'desc')->first();
            \Log::info("Last invoice: " . ($lastInvoice ? $lastInvoice->id : 'none'));

            if ($lastInvoice) {
                $startDate = Carbon::parse($lastInvoice->end_date)->addDay();
            } else {
                $startDate = Carbon::parse($service->installation_date);
                \Log::info("Start date: {$startDate}");
            }

            // Ajustar la fecha de fin al mismo día del próximo mes
            $endDate = $startDate->copy()->addMonth()->subDay();
            \Log::info("End date: {$endDate}");
            // Verificar si la fecha de fin pertenece al mes y año actuales
            if ($startDate->month != $currentMonth || $startDate->year != $currentYear) {
                \Log::info("Skipping creación de factura para service_id: {$service->id}, no pertenece al mes en curso o ya existe.");
                continue;
            }

            \Log::info("Start date: {$startDate}, End date: {$endDate}");

            // Crear la nueva factura solo si pertenece al mes en curso
            Invoice::create([
                'service_id' => $service->id,
                'amount' => $service->plans->price,
                'igv' => 0.00,
                'discount' => 0.00,
                'letter_amount' => "",
                'due_date' => $endDate->copy()->addDays(5),//due_date 5 days after end date for example
                'start_date' => $startDate,
                'end_date' => $endDate,
                'status' => 'pendiente',
            ]);

            \Log::info("Factura creada para service_id: {$service->id} from {$startDate} to {$endDate}");
        }
    }


}