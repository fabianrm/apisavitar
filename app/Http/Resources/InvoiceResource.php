<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'serviceId' => $this->service_id,
            'amount' => $this->amount,
            'igv' => $this->igv,
            'discount' => $this->discount,
            'letterAmount' => $this->letter_amount,
            'dueDate' => $this->due_date,
            'startDate' => $this->start_date,
            'endDate' => $this->end_date,
            'paidDated' => $this->paid_dated,
            'status' => $this->status
        ];
        // return parent::toArray($request);
    }
}
