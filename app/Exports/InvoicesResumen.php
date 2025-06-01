<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;


class InvoicesResumen implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Invoice::query();

        // Filtrar por rango de fechas si se proporciona
        if (isset($this->filters['start_date']) && isset($this->filters['end_date'])) {
            $query->whereBetween('invoices.paid_dated', [$this->filters['start_date'], $this->filters['end_date']]);
        }

        //Ordenar
        $query->orderBy('invoices.paid_dated', 'desc');

        return $query;
    }

    public function headings(): array
    {
        return [
            'Cod.Contrato.',
            'Fecha Pago',
            'Periodo',
            'Nombre de Cliente',
            'Plan',
            'Precio',
            'Descuento',
            'Total',
        ];
    }

    public function map($invoice): array
    {
        $periodo = Carbon::parse($invoice->start_date);
        return [
            $invoice->service->service_code,
            $invoice->paid_dated,
            strtoupper($periodo->translatedFormat('F')),
            $invoice->service->customers->name,
            $invoice->service->plans->name,
            $invoice->price,
            $invoice->discount,
            $invoice->amount,

        ];
    }
}
