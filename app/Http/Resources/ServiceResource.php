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
            'customerName' => $this->customers->name,
            'planName' => $this->plans->name,
            'routerIp' => $this->routers->ip,
            'boxName' => $this->boxes->name,
            'portNumber' => $this->port_number,
            'equipmentSerie' => $this->equipments->serie,
            'city' => $this->cities->name,
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
