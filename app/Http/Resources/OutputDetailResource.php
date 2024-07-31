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
            'exit' => new OutputResource($this->whenLoaded('output')),
            'entry_detail' => new EntryDetailResource($this->whenLoaded('entry_detail')),
            'quantity' => $this->quantity,
            'subtotal' => $this->subtotal,
            'status' => $this->status,
        ];

    }
}
