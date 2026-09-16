<?php

namespace App\Http\Controllers;

use App\Models\Router;
use App\Services\MikrotikService;
use Illuminate\Http\Request;

class RouterTrafficController extends Controller
{
    public function interfaces(Router $router)
    {
        try {
            $mk = MikrotikService::forRouter($router);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'No se pudo conectar al router'], 503);
        }

        $interfaces = collect($mk->listInfrastructureInterfaces())->map(fn ($i) => [
            'name' => $i['name'] ?? null,
            'type' => $i['type'] ?? null,
            'running' => filter_var($i['running'] ?? false, FILTER_VALIDATE_BOOLEAN),
        ])->values();

        return response()->json(['data' => $interfaces]);
    }

    public function traffic(Request $request, Router $router)
    {
        $data = $request->validate([
            'interface' => 'required|string',
        ]);

        try {
            $mk = MikrotikService::forRouter($router);
        } catch (\Throwable $e) {
            return response()->json(['message' => 'No se pudo conectar al router'], 503);
        }

        $result = $mk->monitorTraffic($data['interface']);

        if (! $result) {
            return response()->json(['message' => 'No se pudo leer el tráfico de esa interfaz'], 503);
        }

        return response()->json([
            'interface' => $result['name'] ?? $data['interface'],
            'rx_bps' => (int) ($result['rx-bits-per-second'] ?? 0),
            'tx_bps' => (int) ($result['tx-bits-per-second'] ?? 0),
            'rx_pps' => (int) ($result['rx-packets-per-second'] ?? 0),
            'tx_pps' => (int) ($result['tx-packets-per-second'] ?? 0),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
