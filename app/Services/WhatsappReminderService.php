<?php

namespace App\Services;

use App\Models\Enterprise;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsappReminderService
{
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
        $baseUrl = config('services.evolution.base_url');

        if (! $baseUrl || ! $enterprise->hasWhatsappReminderConfigured()) {
            return false;
        }

        try {
            $response = Http::timeout(10)
                ->withHeaders(['apikey' => $enterprise->wa_api_key])
                ->post(rtrim($baseUrl, '/') . "/message/sendText/{$enterprise->wa_instance}", [
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
