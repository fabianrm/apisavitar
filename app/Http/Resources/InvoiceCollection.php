<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class InvoiceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        //return parent::toArray($request);
        //Forma corta(debe usarse)
        return [
            'data' => $this->collection
        ];

        // return [
        //     'invoices' => $this->collection->map(function ($invoice) {
        //         return [
        //             'invoiceId' => $invoice->id,
        //             'contractId' => $invoice->service->service_code,
        //             'customerName' => $invoice->service->customers->name,
        //             'planName' => $invoice->service->plans->name,
        //             'price' => $invoice->price,
        //             'discount' => $invoice->discount,
        //             'amount' => $invoice->amount,
        //             'startDate' => $invoice->start_date,
        //             'endDate' => $invoice->end_date,
        //             'dueDate' => $invoice->due_date,
        //             'paidDated' => $invoice->paid_dated,
        //             'receipt' => $invoice->receipt,
        //             'note' => $invoice->note,
        //             'status' => $invoice->status,

        //         ];

        //     })
        // ];
    }
}
