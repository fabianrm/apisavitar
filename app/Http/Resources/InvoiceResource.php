<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

Carbon::setLocale('es');

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $periodic = Carbon::parse($this->start_date)->translatedFormat('F');

        return [
            'invoiceId' => $this->id,
            'serviceId' => $this->service_id,
            'contractId' => $this->service->service_code,
            'customerName' => $this->service->customers->name,
            'address' => $this->service->customers->address,
            'planName' => $this->service->plans->name,
            'price' => $this->price,
            'igv' => $this->igv,
            'discount' => $this->discount,
            'amount' => $this->amount,
            'letterAmount' => $this->letter_amount,
            'startDate' => $this->start_date,
            'endDate' => $this->end_date,
            'dueDate' => $this->due_date,
            'paidDated' => $this->paid_dated,
            'receipt' => $this->receipt,
            'tipoPago' => $this->tipo_pago,
            'note' => $this->note,
            'periodic' => $periodic,
            'status' => $this->status
        ];
        // return parent::toArray($request);
    }
}
