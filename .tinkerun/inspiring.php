<?php

use App\Models\Invoice;
use App\Models\Suspension;
use Carbon\Carbon;

// $invoiceStart = Carbon::parse('2025-05-10');
// $invoiceEnd = Carbon::parse('2025-06-09');
// $serviceId = 184;

// $exists = Invoice::where('service_id', $serviceId)
//     ->whereDate('start_date', $invoiceStart->toDateString())
//     ->whereDate('end_date', $invoiceEnd->toDateString())
//     ->exists();


// $serviceId = 184;
// $startDate = '2025-04-10';
// $endDate = '2025-05-09';

// $exists = Invoice::where('service_id', $serviceId)
//     ->whereDate('start_date', $startDate)
//     ->whereDate('end_date', $endDate)
//     ->exists();

// dump('¿Existe factura?', $exists ? 'Sí' : 'No');

// $exists;


$suspension = Suspension::with(['service'])
    ->where('service_id', '474')
    ->where('status', true)
    ->get();

$suspension;
