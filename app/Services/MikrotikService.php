<?php

namespace App\Services;

use RouterOS\Client;
use RouterOS\Query;
use Illuminate\Support\Facades\Log;
use App\Models\Connection;

class MikrotikService
{
    protected $client;
    protected $connection;

    public function __construct(array $connectionConfig)
    {
        if ($connectionConfig) {
            $this->initializeConnection($connectionConfig);
        }
    }

    /**
     * Inicializa la conexión con parámetros dinámicos
     */
    public function initializeConnection(array $config): void
    {
        try {
            $this->connection = $config;

            $this->client = new Client([
                'host'    => $config['host'],
                'user'    => $config['user'],
                'pass'    => $config['pass'],
                //'port'    => $config['port'] ?? 8728,
                //'timeout' => $config['timeout'] ?? 10,
                //'ssl'     => $config['ssl'] ?? false,
                //'legacy'  => $config['legacy'] ?? false,
            ]);
        } catch (\Exception $e) {
            Log::error('Error al conectar con MikroTik: ' . $e->getMessage());
            throw new \RuntimeException('Error de conexión con MikroTik: ' . $e->getMessage());
        }
    }

    /**
     * Crea un usuario PPPoE en el MikroTik
     */
    public function crearUsuarioPPP(array $userData): array
    {
        $this->validateConnection();

        try {
            return $this->ejecutarComando('/ppp/secret/add', [
                'name'     => $userData['username'],
                'password' => $userData['password'],
                'service'  => 'pppoe',
                'profile'  => $userData['profile'],
                'comment'  => $userData['comment'] ?? 'Cliente ID: ' . $userData['customer_id']
            ]);
        } catch (\Exception $e) {
            Log::error('Error creando usuario PPPoE: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Desactiva un usuario PPPoE en el MikroTik
     */
    public function desactivarUsuario(string $username): array
    {
        $this->validateConnection();

        try {
            return $this->ejecutarComando('/ppp/secret/set', [
                '.id' => $username,
                'disabled' => 'yes'
            ]);
        } catch (\Exception $e) {
            Log::error('Error desactivando usuario PPPoE: ' . $e->getMessage());
            throw $e;
        }
    }


    /**
     * Desactiva un usuario PPPoE en el MikroTik
     */
    public function activarUsuario(string $username): array
    {
        $this->validateConnection();

        try {
            return $this->ejecutarComando('/ppp/secret/set', [
                '.id' => $username,
                'disabled' => 'no'
            ]);
        } catch (\Exception $e) {
            Log::error('Error activando usuario PPPoE: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Desactiva un usuario PPPoE en el MikroTik
     */
    public function cambiarPlan(string $username, string $profile): array
    {
        $this->validateConnection();

        try {
            return $this->ejecutarComando('/ppp/secret/set', [
                '.id' => $username,
                'profile' => $profile
            ]);
        } catch (\Exception $e) {
            Log::error('Error activando usuario PPPoE: ' . $e->getMessage());
            throw $e;
        }
    }


    protected function validateConnection(): void
    {
        if (!$this->client) {
            throw new \RuntimeException('Conexión a MikroTik no inicializada');
        }
    }

    public function verificarConexion(): bool
    {
        try {
            // Intenta ejecutar un comando simple
            $resp = $this->ejecutarComando('/system/identity/print');
            Log::info($resp);

            return true;
        } catch (\Exception $e) {
            Log::error('Error verificando conexión MikroTik: ' . $e->getMessage());
            return false;
        }
    }

    protected function ejecutarComando(string $path, array $params = []): array
    {
        $this->validateConnection();

        try {
            $query = new Query($path);
            foreach ($params as $name => $value) {
                $query->equal($name, $value);
            }
            return $this->client->query($query)->read();
        } catch (\Exception $e) {
            Log::error("Error ejecutando comando $path: " . $e->getMessage());
            throw $e;
        }
    }
}
