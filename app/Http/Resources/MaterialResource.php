<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MaterialResource extends JsonResource
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
            'code' => $this->code,
            'name' => $this->name,
            'category' => new CategoryResource($this->whenLoaded('category')),
            'presentation' => new PresentationResource($this->whenLoaded('presentation')),
            'series' => $this->series,
            'model' => $this->model,
            'brand' => new BrandResource($this->whenLoaded('brand')),
            'min' => $this->min,
            'type' => $this->type,
            'image' => $this->image,
            'status' => $this->status,
        ];
    }
}
