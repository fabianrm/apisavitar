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
            "id" => $this->id,
            'type' => $this->type,
            'document_number' => $this->document_number,
            'name' => $this->name,
            'address' => $this->address,
            'reference' => $this->reference,
            'city' => $this->city,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'phone_number' => $this->phone_number,
            'email' => $this->email,
            'status' => $this->status,
            'service' => ServiceResource::collection($this->whenLoaded('services')),
        ];


        //return parent::toArray($request);
    }
}
