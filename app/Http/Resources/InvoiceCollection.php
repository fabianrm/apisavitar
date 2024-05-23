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

        return [
            'invoices' => $this->collection->map(function ($invoice) {
                return [
                    'invoiceId' => $invoice->id,
                    'contractId' => $invoice->service->service_code,
                    'amount' => $invoice->amount,
                    'dueDate' => $invoice->due_date,
                    'status' => $invoice->status,
                    'discount' => $invoice->discount,
                    'startDate' => $invoice->start_date,
                    'endDate' => $invoice->end_date,
                    'customerName' => $invoice->service->customers->name,
                    'planName' => $invoice->service->plans->name,

                ];

            })
        ];
    }
}
