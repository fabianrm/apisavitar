<?php

namespace App\Services;

use App\Models\Enterprise;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use RuntimeException;

class WhatsappReminderService
{
    /**
     * Crea una instancia nueva en Evolution API para una empresa que todavía
     * no tiene número propio, y la deja guardada lista para usar -- sin que
     * el admin tenga que copiar/pegar nada del panel de Evolution API.
     *
     * Requiere el token GLOBAL del servidor (distinto del api_key por
     * instancia); ese token nunca se guarda por empresa ni se expone al front.
     *
     * @return array{instance: string, qrcode: ?string}
     */
    public function createInstance(Enterprise $enterprise): array
    {
        $baseUrl = $this->baseUrl();
        $globalToken = config('services.evolution.global_token');

        if (! $globalToken) {
            throw new RuntimeException('EVOLUTION_API_GLOBAL_TOKEN no está configurado.');
        }

        // Sufijo con el id para garantizar unicidad en el servidor Evolution
        // API completo (no solo dentro de Savitar), sin depender de que el
        // nombre de la empresa por sí solo no choque con otra instancia.
        $instanceName = Str::slug($enterprise->name) . '-wa-' . $enterprise->id;

        $response = Http::timeout(15)
            ->withHeaders(['apikey' => $globalToken])
            ->post("{$baseUrl}/instance/create", [
                'instanceName' => $instanceName,
                'integration' => 'WHATSAPP-BAILEYS',
                'qrcode' => true,
            ]);

        if (! $response->successful()) {
            Log::error('Fallo al crear instancia de Evolution API: ' . $response->body());

            throw new RuntimeException('No se pudo crear la instancia en Evolution API.');
        }

        $data = $response->json();

        $apiKey = data_get($data, 'hash.apikey') ?? data_get($data, 'hash');
        $qrcode = data_get($data, 'qrcode.base64') ?? data_get($data, 'qrcode');

        if (! $apiKey) {
            Log::error('Evolution API creó la instancia pero no devolvió api_key: ' . $response->body());

            throw new RuntimeException('La instancia se creó pero no se recibió el api_key.');
        }

        $enterprise->update([
            'wa_instance' => $instanceName,
            'wa_api_key' => $apiKey,
        ]);

        return [
            'instance' => $instanceName,
            'qrcode' => $this->normalizeQrcode($qrcode),
        ];
    }

    /**
     * Regenera el código QR de una instancia ya creada (sesión vencida, o el
     * admin quiere reconectar desde otro celular), sin perder la config.
     */
    public function regenerateQrCode(Enterprise $enterprise): ?string
    {
        if (! $enterprise->hasWhatsappReminderConfigured()) {
            throw new RuntimeException('Esta empresa todavía no tiene una instancia creada.');
        }

        $response = Http::timeout(15)
            ->withHeaders(['apikey' => $enterprise->wa_api_key])
            ->get($this->baseUrl() . "/instance/connect/{$enterprise->wa_instance}");

        if (! $response->successful()) {
            Log::error('Fallo al regenerar el QR de Evolution API: ' . $response->body());

            throw new RuntimeException('No se pudo generar un nuevo código QR.');
        }

        $data = $response->json();
        $qrcode = data_get($data, 'base64') ?? data_get($data, 'qrcode.base64') ?? data_get($data, 'qrcode');

        return $this->normalizeQrcode($qrcode);
    }

    /**
     * Estado actual de la conexión ('open' = conectado). El front hace
     * polling a esto mientras muestra el QR, para avisar en cuanto se
     * escanea sin que el admin tenga que refrescar la página.
     */
    public function connectionState(Enterprise $enterprise): string
    {
        if (! $enterprise->hasWhatsappReminderConfigured()) {
            return 'not_configured';
        }

        $response = Http::timeout(10)
            ->withHeaders(['apikey' => $enterprise->wa_api_key])
            ->get($this->baseUrl() . "/instance/connectionState/{$enterprise->wa_instance}");

        if (! $response->successful()) {
            return 'unknown';
        }

        return data_get($response->json(), 'instance.state') ?? data_get($response->json(), 'state') ?? 'unknown';
    }

    private function baseUrl(): string
    {
        $baseUrl = config('services.evolution.base_url');

        if (! $baseUrl) {
            throw new RuntimeException('EVOLUTION_API_BASE_URL no está configurado.');
        }

        return rtrim($baseUrl, '/');
    }

    private function normalizeQrcode(?string $qrcode): ?string
    {
        if (! $qrcode) {
            return null;
        }

        return str_starts_with($qrcode, 'data:image')
            ? $qrcode
            : "data:image/png;base64,{$qrcode}";
    }

    /**
     * Envía un mensaje de prueba directo a Evolution API para que el admin
     * verifique instancia/api_key desde la pantalla de Configuración, sin
     * depender del workflow de n8n.
     *
     * Asume el contrato estándar de Evolution API (POST /message/sendText/{instance}
     * con header apikey). Si tu instalación usa una ruta distinta, ajusta aquí.
     */
    public function sendTest(Enterprise $enterprise, string $phone): bool
    {
        if (! $enterprise->hasWhatsappReminderConfigured()) {
            return false;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders(['apikey' => $enterprise->wa_api_key])
                ->post($this->baseUrl() . "/message/sendText/{$enterprise->wa_instance}", [
                    'number' => $phone,
                    'text' => "✅ Conexión de prueba\nSi ves este mensaje, la configuración de WhatsApp de {$enterprise->name} está bien hecha.",
                ]);

            if (! $response->successful()) {
                Log::error('Fallo al enviar WhatsApp de prueba: ' . $response->body());

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::error('Excepción al enviar WhatsApp de prueba: ' . $e->getMessage());

            return false;
        }
    }
}
