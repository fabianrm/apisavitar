<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class CustomerCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'data' => $this->collection,
            'total' => count($this->collection)
        ];

        // return $this->collection->map(function ($customer) {
        //     return [
        //         'id' => $customer->id,
        //         'name' => $customer->name,
        //         'email' => $customer->email,
        //         'total_contracts' => $customer->contracts_count,
        //     ];
        // });


        // return [
        //     'data' => $this->collection->map(function ($customer) {
        //         return [
        //             'id' => $customer->id,
        //             'type' => $customer->type,
        //             'customerCode' => $customer->client_code,
        //             'customerName' => $customer->name,
        //             'documentNumber' => $customer->document_number,
        //             'name' => $customer->name,
        //             'address' => $customer->address,
        //             'reference' => $customer->reference,
        //             'latitude' => $customer->latitude,
        //             'longitude' => $customer->longitude,
        //             'phoneNumber' => $customer->phone_number,
        //             'email' => $customer->email,
        //             'status' => $customer->status,
        //             'createdAt' => $customer->created_at,
        //             'updatedAt' => $customer->updated_at,
        //             'totalContracts' => $customer->services_count,
        //         ];
        //     }),
        // ];


        //return parent::toArray($request);
    }
}
