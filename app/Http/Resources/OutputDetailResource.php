<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class OutputDetailResource extends JsonResource
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
            'material' => new MaterialResource($this->whenLoaded('material')),
            'quantity' => $this->quantity,
            'subtotal' => (float) $this->subtotal,
        ];

    }
}
