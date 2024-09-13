<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketHistoryResource extends JsonResource
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
            'ticket_id' => $this->ticket_id,
            'changed_by' => new UserResource($this->whenLoaded('user')),
            'comment' => $this->comment,
            'updated_at' => $this->updated_at,
            'status' => $this->status,
        ];
    }
}
