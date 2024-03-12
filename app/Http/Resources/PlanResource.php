<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PlanResource extends JsonResource
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
            'name'=> $this->name,
            'download'=> $this->download,
            'upload'=> $this->upload,
            'price'=> $this->price,
            'guaranteed_speed'=> $this->guaranteed_speed,
            'priority'=> $this->priority,
            'burst_limit'=> $this->burst_limit,
            'burst_threshold'=> $this->burst_threshold,
            'burst_time'=> $this->burst_time,
            'service' => ServiceResource::collection($this->whenLoaded('services')),
        ];
    }
}
