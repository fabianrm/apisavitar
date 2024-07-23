<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
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
            'type' => $this->type,
            'customerCode' => $this->client_code,
            'documentNumber' => $this->document_number,
            'customerName' => $this->name,
            'city' => $this->cities->name,
            'cityId' => $this->city_id,
            'address' => $this->address,
            'reference' => $this->reference,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'coordinates' => [$this->latitude, $this->longitude],
            'phoneNumber' => $this->phone_number,
            'email' => $this->email,
            'status' => $this->status,
            'updated_at' => $this->updated_at,
            'totalContracts' => $this->services->count(),
            'service' => ServiceResource::collection($this->whenLoaded('services')),
        ];


        //return parent::toArray($request);
    }
}
