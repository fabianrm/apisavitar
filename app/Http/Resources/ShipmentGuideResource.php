<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShipmentGuideResource extends JsonResource
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
            'issue_date' => $this->issue_date,
            'transfer_date' => $this->transfer_date,
            'origin_address' => $this->origin_address,
            'destination_address' => $this->destination_address,
            'driver_name' => $this->driver_name,
            'vehicle_plate' => $this->vehicle_plate,
            'warehouse' => new WarehouseResource($this->whenLoaded('warehouse')),
            'sender_name' => $this->sender_name,
            'receiver_name' => $this->receiver_name,
            'comment' => $this->comment,
            'status' => $this->status,
        ];
    }
}
