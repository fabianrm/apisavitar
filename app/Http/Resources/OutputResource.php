<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OutputResource extends JsonResource
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
            'number' => $this->number,
            'date' => $this->date,
            'destination' => new DestinationResource($this->whenLoaded('destination')),
            'employee' => new UserResource($this->whenLoaded('user')),
            'total' => $this->total,
            'comment' => $this->comment,
            'status' => $this->status,
            'output_details' => OutputDetailResource::collection($this->whenLoaded('outputDetails')),
        ];
    }
}
