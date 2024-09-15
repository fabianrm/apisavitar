<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
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
            'subject' => $this->subject,
            'description' => $this->description,
            'project' => new DestinationResource($this->whenLoaded('project')),
            'category' => new CategoryTicketResource($this->whenLoaded('categoryTicket')),
            'customer' => new CustomerResource($this->whenLoaded('customer')),
            'technician' => new UserResource($this->whenLoaded('technician')),
            'admin' => new UserResource($this->whenLoaded('admin')),
            'priority' => $this->priority, 
            'expiration' => $this->expiration, 
            'assigned_at' => $this->assigned_at,
            'resolved_at' => $this->resolved_at,
            'closed_at' => $this->closed_at,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'history' => TicketHistoryResource::collection($this->whenLoaded('history')),
            'attachments' => TicketAttachmentResource::collection($this->whenLoaded('attachments')),
        ];
    }
}
