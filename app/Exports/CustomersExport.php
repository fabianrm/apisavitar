<?php

namespace App\Exports;

use App\Models\Customer;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class CustomersExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        // Obtener todos los clientes con sus contratos y planes asociados
        return Customer::with([
            'services.plans',
            'services.invoices' => function ($query) {
                $query->whereNotNull('paid_dated')->orderBy('paid_dated', 'desc');
            }
        ])->get();
    }

    public function headings(): array
    {
        return [
            'NOMBRE',
            'DNI',
            'TELEFONO',
            'MONTO',
            'DIA DE PAGO',
            'ULTIMO PAGO',
            'ESTADO',
        ];
    }

    public function map($customer): array
    {
        $lastPaidInvoice = $customer->services->flatMap(function ($contract) {
            return $contract->invoices;
        })->whereNotNull('paid_dated')->sortByDesc('paid_dated')->first();


        $planPrice = $customer->services->first()->plans->price ?? 'N/A';
        $installationDate = $customer->services->first()->installation_date ?? null;
        $paymentDay = Carbon::parse($installationDate)->day ?? 'N/A';
        $lastInvoiceDate = $lastPaidInvoice ? $lastPaidInvoice->paid_dated : 'N/A';
        $serviceStatus = $customer->services->first()->status ?? 'N/A';

        return [
            $customer->name,
            $customer->document_number,
            $customer->phone_number,
            $planPrice,
            $paymentDay,
            $lastInvoiceDate,
            $serviceStatus,
        ];
    }
}
