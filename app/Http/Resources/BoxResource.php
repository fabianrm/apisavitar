<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BoxResource extends JsonResource
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
            'name' => $this->name,
            'city_id' => $this->city_id,
            'address' => $this->address,
            'reference' => $this->reference,
            'latitude'=> $this->latitude,
            'longitude'=> $this->longitude,
            'totalPorts' => $this->total_ports,
            'availablePorts' => $this->available_ports,
            'status' => $this->status,
        ];
    }
}
