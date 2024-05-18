<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EquipmentResource extends JsonResource
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
            'type' => $this->type,
            'serie' => $this->serie,
            'model' => $this->model,
            'brand' => $this->brand,
            'purchase_date' => $this->purchase_date,
            'status' => $this->status
        ];


        // return parent::toArray($request);
    }
}
