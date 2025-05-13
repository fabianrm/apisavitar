<?php

namespace App\Exports;

use App\Models\Invoice;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;


class InvoicesExport implements FromQuery, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct(array $filters)
    {
        $this->filters = $filters;
    }

    public function query()
    {
        $query = Invoice::query();

        //Join with the Service and Customer tables to allow filtering by customer name
        $query->join('services', 'invoices.service_id', '=', 'services.id')
            ->join('customers', 'services.customer_id', '=', 'customers.id')
            ->select('invoices.*');

        // Filtrar por estado si se proporciona
        if (isset($this->filters['status'])) {
            $query->where('invoices.status', $this->filters['status']);
        }

        // Filtrar por rango de fechas si se proporciona
        if (isset($this->filters['start_date']) && isset($this->filters['end_date'])) {
            $query->whereBetween('invoices.start_date', [$this->filters['start_date'], $this->filters['end_date']]);
        }

        // Filtrar por nombre del cliente si se proporciona
        if (isset($this->filters['customer_name'])) {
            $query->whereHas('service.customers', function ($q) {
                $q->where('name', 'like', '%' . $this->filters['customer_name'] . '%');
            });
        }

        //Filtrar por ciudad si se proporciona
        if (isset($this->filters['city_id'])) {
            $query->where('services.city_id', $this->filters['city_id']);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            'ID',
            'Cod.Contrato.',
            'Nombre de Cliente',
            'Dirección',
            'Plan',
            'Precio',
            'Descuento',
            'Total',
            'Inicio Fact.',
            'Fin Fact',
            'Vencimiento',
            'Fecha Pago',
            'Recibo Nro.',
            'Tipo Pago.',
            'Nota.',
            'Status',
        ];
    }

    public function map($invoice): array
    {
        return [
            $invoice->id,
            $invoice->service->service_code,
            $invoice->service->customers->name,
            $invoice->service->customers->address,
            $invoice->service->plans->name,
            $invoice->price,
            $invoice->discount,
            $invoice->amount,
            $invoice->start_date,
            $invoice->end_date,
            $invoice->due_date,
            $invoice->paid_dated,
            $invoice->receipt,
            $invoice->tipo_pago,
            $invoice->note,
            $invoice->status,
        ];
    }
}
