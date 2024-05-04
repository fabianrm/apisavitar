<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            'customer_id' => $this->customer_id,
            'router_id' => $this->router_id,
            'plan_id' => $this->plan_id,
            'box_id' => $this->box_id,
            'port_number' => $this->port_number,
            'registration_date' => $this->registration_date,
            'billing_date' => $this->billing_date,
            'recurrent' => $this->recurrent,
            'due_date' => $this->due_date,
            'address_instalation' => $this->address_instalation,
            'city' => $this->city,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'is_active' => $this->is_active,
            'status' => $this->status
        ];

        // return parent::toArray($request);
    }
}
