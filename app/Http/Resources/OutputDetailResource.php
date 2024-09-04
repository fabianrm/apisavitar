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
            'entry_detail_id' => $this->entry_detail_id,
            'entry_detail' => new EntryDetailResource($this->whenLoaded('entryDetail')),
            'material' => new MaterialResource($this->whenLoaded('entryDetail.material')),
            'quantity' => $this->quantity,
            'subtotal' => (float) $this->subtotal,
        ];

    }
}
