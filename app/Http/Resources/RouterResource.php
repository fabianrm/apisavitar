<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;

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
            'connectivity' => $this->connectivity(),
        ];
    }

    /**
     * Último estado conocido reportado por mk:monitor-connectivity (cada 5 min),
     * solo para routers con túnel (ver Router::isMonitored). No es un chequeo en vivo.
     */
    private function connectivity(): array
    {
        if (! $this->isMonitored()) {
            return ['status' => 'no_monitoreado', 'checked_at' => null];
        }

        $cached = Cache::get($this->connectivityCacheKey());

        if (! $cached) {
            return ['status' => 'desconocido', 'checked_at' => null];
        }

        return [
            'status' => $cached['up'] ? 'online' : 'offline',
            'checked_at' => $cached['checked_at'],
        ];
    }
}
