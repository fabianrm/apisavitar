<?php

namespace App\Services;

use App\Models\Ticket;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotifierService
{
    public function sendTicketRegistered(Ticket $ticket): void
    {
        $token = config('services.telegram.bot_token');
        $chatId = config('services.telegram.ticket_chat_id');

        if (! $token || ! $chatId) {
            Log::warning('Telegram no configurado (TELEGRAM_BOT_TOKEN / TELEGRAM_TICKET_CHAT_ID), se omite notificación de ticket.');

            return;
        }

        $ticket->loadMissing(['customer', 'categoryTicket']);

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

        $message = implode("\n", $lines);

        try {
            $response = Http::timeout(5)->post("https://api.telegram.org/bot{$token}/sendMessage", [
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'HTML',
            ]);

            if (! $response->successful()) {
                Log::error('Fallo al enviar notificación de ticket a Telegram: '.$response->body());
            }
        } catch (\Throwable $e) {
            Log::error('Excepción al enviar notificación de ticket a Telegram: '.$e->getMessage());
        }
    }
}
