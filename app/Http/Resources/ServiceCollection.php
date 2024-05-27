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
            'data' => $this->collection
        ];

        // return [
        //     'services' => $this->collection->map(function ($service) {
        //         return [
        //             'id' => $service->id,
        //             'serviceCode' => $service->service_code,
        //             'customerName' => $service->customers->name,
        //             'planName' => $service->plans->name,
        //             'routerIp' => $service->routers->ip,
        //             'boxName' => $service->boxes->name,
        //             'portNumber' => $service->port_number,
        //             'equipmentSerie' => $service->equipments->serie,
        //             'city' => $service->cities->name,
        //             'addressInstallation' => $service->address_installation,
        //             'reference' => $service->reference,
        //             'registrationDate' => $service->registration_date,
        //             'installationDate' => $service->installation_date,
        //             'latitude' => $service->latitude,
        //             'longitude' => $service->longitude,
        //             'billingDate' => $service->billing_date,
        //             'dueDate' => $service->due_date,
        //             'status' => $service->status,
        //             'endDate' => $service->end_date,
        //         ];
        //     }),
        // ];

    }
}
