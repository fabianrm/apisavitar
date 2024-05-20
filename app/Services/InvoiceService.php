<?php

namespace App\Services;

use App\Models\Service;
use App\Models\Invoice;
use Carbon\Carbon;

class InvoiceService
{
    public function generateMonthlyInvoices()
    {

       // $contracts = Service::with(['invoices'])->get();

        $contracts = Service::where('status', 'activo')->get();

        // return response()->json([
        //     'old_contract' => $contracts,
        // ], 201);

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
}