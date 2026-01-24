<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class BoxRouteResource extends JsonResource
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
            'start_box_id' => $this->start_box_id,
            'end_box_id' => $this->end_box_id,
            'color' => $this->color,
            'points' => $this->points,
            'distance' => $this->distance,
            'notes' => $this->notes,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'start_box' => new BoxResource($this->whenLoaded('startBox')),
            'end_box' => new BoxResource($this->whenLoaded('endBox')),
            'city_id' => $this->startBox?->city_id,
        ];
    }
}
