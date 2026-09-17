<?php

namespace App\Services;

use App\Models\Enterprise;
use App\Models\Router;
use App\Models\Ticket;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotifierService
{
    public function sendRouterConnectivity(Router $router, bool $isUp): void
    {
        $router->loadMissing('enterprise');

        if (! $router->enterprise) {
            return;
        }

        $lines = $isUp
            ? ["🟢 <b>Mikrotik reconectado</b>"]
            : ["🔴 <b>Mikrotik sin conexión</b>"];

        $lines[] = "IP: {$router->ip}";
        if ($router->enterprise) {
            $lines[] = "Empresa: {$router->enterprise->name}";
        }
        $lines[] = $isUp
            ? 'Se restableció la comunicación.'
            : 'No responde desde hace varios minutos.';

        $this->send($router->enterprise, implode("\n", $lines));
    }

    public function sendTicketRegistered(Ticket $ticket): void
    {
        $ticket->loadMissing(['customer', 'categoryTicket', 'enterprise']);

        if (! $ticket->enterprise) {
            return;
        }

        $priorityIcon = match ($ticket->priority) {
            'alta' => '🚨',
            'normal' => '🟡',
            'baja' => '🟢',
            default => 'ℹ️',
        };

        $lines = [
            "🎫 <b>Nuevo ticket registrado</b>",
            "Código: {$ticket->code}",
            "Asunto: {$ticket->subject}",
            "Prioridad: {$priorityIcon} ".ucfirst($ticket->priority),
        ];

        if ($ticket->categoryTicket) {
            $lines[] = "Categoría: {$ticket->categoryTicket->name}";
        }

        if ($ticket->customer) {
            $lines[] = "Cliente: {$ticket->customer->name}";
        }

        if ($ticket->description) {
            $lines[] = "Descripción: {$ticket->description}";
        }

        $this->send($ticket->enterprise, implode("\n", $lines));
    }

    public function sendDailyServiceCutsSummary(Enterprise $enterprise, Collection $suspended, Collection $terminated): void
    {
        $fecha = now()->format('d/m/Y');
        $lines = ["📋 <b>Resumen de cortes/suspensiones — {$fecha}</b>"];

        if ($suspended->isEmpty() && $terminated->isEmpty()) {
            $lines[] = 'Sin cortes ni suspensiones hoy ✅';
            $this->send($enterprise, implode("\n", $lines));

            return;
        }

        if ($suspended->isNotEmpty()) {
            $lines[] = '';
            $lines[] = "🟡 <b>Suspendidos ({$suspended->count()})</b>";
            foreach ($suspended as $service) {
                $lines[] = '• '.$this->serviceLine($service);
            }
        }

        if ($terminated->isNotEmpty()) {
            $lines[] = '';
            $lines[] = "🔴 <b>Cortados ({$terminated->count()})</b>";
            foreach ($terminated as $service) {
                $lines[] = '• '.$this->serviceLine($service);
            }
        }

        $this->send($enterprise, implode("\n", $lines));
    }

    private function serviceLine($service): string
    {
        $customerName = $service->customers->name ?? 'Cliente desconocido';
        $vlan = $service->routers->vlan ?? 'N/D';

        return "{$customerName} — VLAN {$vlan}";
    }

    private function send(Enterprise $enterprise, string $message): void
    {
        $token = $enterprise->telegram_bot_token;
        $chatId = $enterprise->telegram_chat_id;

        if (! $token || ! $chatId) {
            Log::warning("Telegram no configurado para la empresa {$enterprise->id} ({$enterprise->name}), se omite notificación.");

            return;
        }

        try {
            $response = Http::timeout(5)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            if (! $response->successful()) {
                Log::error('Fallo al enviar notificación a Telegram: '.$response->body());
            }
        } catch (\Throwable $e) {
            Log::error('Excepción al enviar notificación a Telegram: '.$e->getMessage());
        }
    }
}
