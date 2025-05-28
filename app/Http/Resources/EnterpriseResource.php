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
            'city' => [
                'id' => $this->city_id,
                'name' => $this->cities->name,
                'coordinates' => [$this->cities->latitude, $this->cities->longitude],
            ],
            'address' => $this->address,
            'phone' => $this->phone,
            'logo' => asset('storage/' . $this->logo),
            'status' => $this->status,
            'createdAt' => $this->created_at,
            'updatedAt' => $this->updated_at,
        ];
    }
}
