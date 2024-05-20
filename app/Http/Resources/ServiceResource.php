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
            'id' => $this->id,
            'serviceCode' => $this->service_code,
            'customerId' => $this->customer_id,
            'planId' => $this->plan_id,
            'routerId' => $this->router_id,
            'boxId' => $this->box_id,
            'portNumber' => $this->port_number,
            'equipmentId' => $this->equipment_id,
            'cityId' => $this->city_id,
            'addressInstallation' => $this->address_installation,
            'reference' => $this->reference,
            'registrationDate' => $this->registration_date,
            'installationDate' => $this->installation_date,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'billingDate' => $this->billing_date,
            'dueDate' => $this->due_date,
            'status' => $this->status,
            'endDate' => $this->end_date,
            
        ];

        // return parent::toArray($request);
    }
}
