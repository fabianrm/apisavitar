<?php

namespace App\Http\Controllers;

use App\Models\Router;
use App\Models\RouterMetric;
use App\Services\MikrotikService;

class RouterMetricController extends Controller
{
    /**
     * Chequeo en vivo: conecta al Mikrotik en este instante (no usa el cache
     * de mk:monitor-connectivity) y devuelve conectividad + recursos actuales.
     */
    public function live(Router $router)
    {
        try {
            $mk = MikrotikService::forRouter($router);
            $isUp = $mk->verificarConexion();
        } catch (\Throwable $e) {
            $isUp = false;
            $mk = null;
        }

        $resource = ($isUp && $mk) ? $mk->getResourceInfo() : null;

        return response()->json([
            'connectivity' => [
                'status' => $isUp ? 'online' : 'offline',
                'checked_at' => now()->toIso8601String(),
            ],
            'latest' => $resource ? [
                'cpu_load' => (int) ($resource['cpu-load'] ?? 0),
                'mem_used' => (int) ($resource['total-memory'] ?? 0) - (int) ($resource['free-memory'] ?? 0),
                'mem_total' => (int) ($resource['total-memory'] ?? 0),
                'disk_used' => (int) ($resource['total-hdd-space'] ?? 0) - (int) ($resource['free-hdd-space'] ?? 0),
                'disk_total' => (int) ($resource['total-hdd-space'] ?? 0),
                'uptime' => $resource['uptime'] ?? null,
            ] : null,
        ]);
    }

    public function index(Router $router)
    {
        $latest = RouterMetric::where('router_id', $router->id)
            ->latest('recorded_at')
            ->first();

        $history = RouterMetric::where('router_id', $router->id)
            ->where('recorded_at', '>=', now()->subDays(7))
            ->orderBy('recorded_at')
            ->get(['cpu_load', 'mem_used', 'mem_total', 'disk_used', 'disk_total', 'recorded_at']);

        return response()->json([
            'router' => [
                'id' => $router->id,
                'ip' => $router->ip,
                'wg_public_key' => $router->wg_public_key,
                'wg_provisioned_at' => $router->wg_provisioned_at,
            ],
            'connectivity' => $router->connectivityStatus(),
            'latest' => $latest,
            'history' => $history,
        ]);
    }
}
