<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
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
            'user' => new UserResource($this->whenLoaded('user')),
            'name' => $this->name,
            'address' => $this->address,
            'phone' => $this->phone,
            'position' => $this->position,
            'department' => $this->department,
            'status' => $this->status,
        ];
    }
}
