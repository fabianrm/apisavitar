<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EntryResource extends JsonResource
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
            'date' => $this->date,
            'series' => $this->series,
            'correlative' => $this->correlative,
            'supplier' => new SupplierResource($this->whenLoaded('supplier')),
            'document' => new DocumentResource($this->whenLoaded('document')),
            'document_number' => $this->document_number,
            'entry_type' => new EntryTypeResource($this->whenLoaded('entryType')),
            'total' => $this->total,
            'status' => $this->status,
        ];
    }
}
