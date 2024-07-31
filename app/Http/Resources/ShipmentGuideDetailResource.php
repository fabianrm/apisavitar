<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentGuideDetailResource extends JsonResource
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
            'transfer_guide' => new ShipmentGuideResource($this->whenLoaded('shipmentGuide')),
            'output' => new OutputResource($this->whenLoaded('output')),
            'status' => $this->status,
        ];
    }
}
