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
            'mac' => $this->mac,
            'serie' => $this->serie,
            'model' => $this->model,
            'brandId' => $this->brand_id,
            'brand' => $this->brand->name,
            'purchaseDate' => $this->purchase_date,
            'contractCode' => $this->service ? $this->service->service_code : null,
            'status' => $this->status,
        ];
        // return parent::toArray($request);
    }
}
