<?php

namespace App\Services;

use App\Models\Router;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Process;
use RuntimeException;

class WireguardProvisioningService
{
    /**
     * Encuentra la siguiente IP libre del rango de túnel (10.100.100.2 - .254),
     * mirando tanto los routers ya provisionados como el .1 reservado para el servidor.
     */
    public function nextAvailableIp(): string
    {
        $prefix = config('wireguard.subnet_prefix');

        $used = Router::withoutStoreScope()
            ->where('ip', 'like', $prefix.'%')
            ->pluck('ip')
            ->map(fn ($ip) => (int) substr($ip, strlen($prefix)))
            ->all();

        for ($host = 2; $host <= 254; $host++) {
            if (! in_array($host, $used, true)) {
                return $prefix.$host;
            }
        }

        throw new RuntimeException('No quedan IPs libres en el rango de túnel WireGuard.');
    }

    /**
     * Genera un keypair WireGuard para el router, registra el peer en el servidor,
     * y devuelve la IP asignada junto con el script RouterOS listo para pegar.
     */
    public function provision(Router $router): array
    {
        if ($router->wg_public_key) {
            throw new RuntimeException('Este router ya tiene una VPN configurada.');
        }

        $privateKey = $this->generatePrivateKey();
        $publicKey = $this->derivePublicKey($privateKey);
        $ip = $this->nextAvailableIp();

        $this->addPeerToServer($publicKey, $ip);

        $router->wg_public_key = $publicKey;
        $router->wg_private_key = $privateKey;
        $router->wg_provisioned_at = now();
        $router->ip = $ip;
        $router->save();

        Log::info("VPN WireGuard provisionada para router {$router->id}: ip={$ip}");

        return [
            'ip' => $ip,
            'script' => $this->buildScript($router, $privateKey, $ip),
        ];
    }

    /**
     * Reconstruye el script RouterOS a partir de los datos ya guardados,
     * para poder mostrarlo de nuevo sin re-provisionar (ej. reinstalar el equipo).
     */
    public function renderScript(Router $router): string
    {
        if (! $router->wg_public_key) {
            throw new RuntimeException('Este router todavía no tiene una VPN configurada.');
        }

        return $this->buildScript($router, $router->wg_private_key, $router->ip);
    }

    private function generatePrivateKey(): string
    {
        $result = Process::run([config('wireguard.wg_binary'), 'genkey']);

        if (! $result->successful()) {
            throw new RuntimeException('No se pudo generar la clave privada WireGuard: '.$result->errorOutput());
        }

        return trim($result->output());
    }

    private function derivePublicKey(string $privateKey): string
    {
        $result = Process::input($privateKey)->run([config('wireguard.wg_binary'), 'pubkey']);

        if (! $result->successful()) {
            throw new RuntimeException('No se pudo derivar la clave pública WireGuard: '.$result->errorOutput());
        }

        return trim($result->output());
    }

    private function addPeerToServer(string $publicKey, string $ip): void
    {
        $command = config('wireguard.add_peer_command');
        $cidr = $ip.'/32';

        $result = Process::run(['sudo', $command, $publicKey, $cidr]);

        if (! $result->successful()) {
            throw new RuntimeException('No se pudo registrar el peer en el servidor WireGuard: '.$result->errorOutput());
        }
    }

    private function buildScript(Router $router, string $privateKey, string $ip): string
    {
        $interfaceName = 'wg'.$router->id;
        $serverPublicKey = config('wireguard.server_public_key');
        $endpointHost = config('wireguard.endpoint_host');
        $endpointPort = config('wireguard.endpoint_port');
        $cidr = config('wireguard.subnet_cidr');

        return <<<SCRIPT
        /interface wireguard add name={$interfaceName} mtu=1420 private-key="{$privateKey}"
        /interface wireguard peers add interface={$interfaceName} public-key="{$serverPublicKey}" endpoint-address={$endpointHost} endpoint-port={$endpointPort} allowed-address=10.100.100.1/32 persistent-keepalive=25s
        /ip address add address={$ip}/{$cidr} interface={$interfaceName}
        SCRIPT;
    }
}
