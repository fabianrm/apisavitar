<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushNotifierService
{
    public function sendTicketAssigned(Ticket $ticket, User $technician): void
    {
        $publicKey = config('services.vapid.public_key');
        $privateKey = config('services.vapid.private_key');

        if (! $publicKey || ! $privateKey) {
            Log::warning('VAPID no configurado, se omite notificación push de ticket asignado.');
            return;
        }

        $subscriptions = PushSubscription::where('user_id', $technician->id)->get();
        if ($subscriptions->isEmpty()) {
            return;
        }

        $webPush = new WebPush([
            'VAPID' => [
                'subject' => config('services.vapid.subject'),
                'publicKey' => $publicKey,
                'privateKey' => $privateKey,
            ],
        ]);

        $payload = json_encode([
            'notification' => [
                'title' => '🎫 Ticket asignado',
                'body' => "#{$ticket->code}: {$ticket->subject}",
                'icon' => 'pwa-icons/icon-192x192.png',
                'vibrate' => [200, 100, 200],
                'data' => [
                    'onActionClick' => [
                        'default' => ['operation' => 'openWindow', 'url' => '/support/ticket'],
                    ],
                ],
            ],
        ]);

        foreach ($subscriptions as $subscription) {
            $webPush->queueNotification(
                Subscription::create([
                    'endpoint' => $subscription->endpoint,
                    'publicKey' => $subscription->public_key,
                    'authToken' => $subscription->auth_token,
                    'contentEncoding' => $subscription->content_encoding ?? 'aes128gcm',
                ]),
                $payload
            );
        }

        foreach ($webPush->flush() as $report) {
            if ($report->isSuccess()) {
                continue;
            }

            $statusCode = $report->getResponse()?->getStatusCode();
            if (in_array($statusCode, [404, 410], true)) {
                PushSubscription::where('endpoint', $report->getRequest()->getUri()->__toString())->delete();
            } else {
                Log::error('Fallo al enviar notificación push: '.$report->getReason());
            }
        }
    }
}
