<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class KardexResource extends JsonResource
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
            'date' => $this->date,
            'has' => $this->has,
            'operation' => $this->operation,
            'quantity' => $this->quantity,
            'stock' => $this->stock,
            'comment' => $this->comment,
            'status' => $this->status,
        ];
    }
}
