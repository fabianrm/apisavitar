<?php

namespace App\Http\Controllers;

use App\Models\Router;
use App\Services\WireguardProvisioningService;
use Illuminate\Support\Facades\Log;

class RouterVpnController extends Controller
{
    public function __construct(private WireguardProvisioningService $wireguard) {}

    /**
     * Genera el keypair, registra el peer en el servidor y devuelve
     * el script RouterOS para pegar una sola vez en el Mikrotik.
     */
    public function provision(Router $router)
    {
        try {
            $result = $this->wireguard->provision($router);

            return response()->json([
                'success' => true,
                'ip' => $result['ip'],
                'script' => $result['script'],
            ]);
        } catch (\Throwable $e) {
            Log::error("Error provisionando VPN para router {$router->id}: ".$e->getMessage());

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    /**
     * Reconstruye el script RouterOS de un router ya provisionado, sin generar
     * un peer nuevo (para reinstalar/reconfigurar el equipo físico).
     */
    public function script(Router $router)
    {
        try {
            return response()->json([
                'success' => true,
                'ip' => $router->ip,
                'script' => $this->wireguard->renderScript($router),
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 404);
        }
    }
}
