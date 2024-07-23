<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EnterpriseResource extends JsonResource
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
            'ruc' => $this->ruc,
            'name' => $this->name,
            'cityId' => $this->city_id,
            'city' => $this->cities->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'coordinates' => [$this->cities->latitude, $this->cities->longitude],
            'updatedAt' => $this->updated_at,
        ];
    }
}
