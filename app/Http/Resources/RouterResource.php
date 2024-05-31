<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RouterResource extends JsonResource
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
            'ip' => $this->ip,
            'vlan' => $this->vlan,
            'usuario' => $this->usuario,
            'password' => $this->password,
            'port' => $this->port,
            'api_connection' => $this->api_connection,
            'status' => $this->status,
        ];
    }
}
