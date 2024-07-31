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
            'entry_detail' => new EntryDetailResource($this->whenLoaded('entry_detail')),
            'date' => $this->date,
            'operation' => $this->operation,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'total' => $this->total,
            'comment' => $this->comment,
            'status' => $this->status,
        ];
    }
}
