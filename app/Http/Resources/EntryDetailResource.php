<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EntryDetailResource extends JsonResource
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
            //'entry' => new EntryResource($this->whenLoaded('entry')),
            'date' => $this->date,
            'material' => new MaterialResource($this->whenLoaded('material')),
            'quantity' => $this->quantity,
            'price' => $this->price,
            'subtotal' => $this->subtotal,
            'warehouse' => new WarehouseResource($this->whenLoaded('warehouse')),
            'location' => $this->location,
            'status' => $this->status,
        ];
    }
}
