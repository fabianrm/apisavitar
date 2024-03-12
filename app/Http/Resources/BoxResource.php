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
            'city' => $this->city,
            'address' => $this->address,
            'reference' => $this->reference,
            'total_ports' => $this->total_ports,
            'available_ports' => $this->available_ports,
            'status' => $this->status,
        ];
    }
}
