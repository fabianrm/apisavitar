<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ServiceCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);

        return [
            'services' => $this->collection->map(function ($service) {
                return [
                    'id' => $service->id,
                    'router_ip' => $service->routers->ip,
                    'customer_name' => $service->customers->name,
                    'plan_name' => $service->plans->name,
                    'box_name' => $service->boxes->name,
                    'port_number' => $service->port_number,
                    'registration_date' => $service->registration_date,
                    'billing_date' => $service->billing_date,
                    'recurrent' => $service->recurrent,
                    'due_date' => $service->due_date,
                    'address_instalation' => $service->address_instalation,
                    'city' => $service->city,
                    'latitude' => $service->latitude,
                    'longitude' => $service->longitude,
                    'is_active' => $service->is_active,
                    'status' => $service->status,
                ];
            }),
        ];

    }
}
