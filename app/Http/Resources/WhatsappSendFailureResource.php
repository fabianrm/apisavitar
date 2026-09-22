<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WhatsappSendFailureResource extends JsonResource
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
            'invoiceId' => $this->invoice_id,
            'customerName' => $this->customer_name,
            'phone' => $this->phone,
            'type' => $this->type,
            'errorMessage' => $this->error_message,
            'status' => $this->status,
            'resolvedAt' => $this->resolved_at,
            'resolvedByName' => $this->resolvedBy?->name,
            'createdAt' => $this->created_at,
        ];
    }
}
