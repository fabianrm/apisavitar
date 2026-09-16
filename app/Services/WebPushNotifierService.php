<?php

namespace App\Services;

use App\Models\PushSubscription;
use App\Models\Service;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\WebPush;

class WebPushNotifierService
{
    public function sendTicketAssigned(Ticket $ticket, User $technician): void
    {
        $this->send(
            PushSubscription::where('user_id', $technician->id)->get(),
            '🎫 Ticket asignado',
            "#{$ticket->code}: {$ticket->subject}",
            '/support/ticket'
        );
    }

    public function sendServiceSuspended(Service $service): void
    {
        $this->send(
            $this->technicianSubscriptions(),
            '🔴 Servicio suspendido',
            $this->serviceBody($service),
            '/dashboard/customer/customers'
        );
    }

    public function sendServiceTerminated(Service $service): void
    {
        $this->send(
            $this->technicianSubscriptions(),
            '⛔ Servicio cortado',
            $this->serviceBody($service),
            '/dashboard/customer/customers'
        );
    }

    private function serviceBody(Service $service): string
    {
        $service->loadMissing(['customers', 'routers']);

        $customerName = $service->customers->name ?? 'Cliente desconocido';
        $vlan = $service->routers->vlan ?? 'N/D';

        return "{$customerName} — VLAN: {$vlan}";
    }

    private function technicianSubscriptions()
    {
        $technicianIds = User::whereHas('roles', fn ($q) => $q->where('name', 'Técnico'))->pluck('id');

        return PushSubscription::whereIn('user_id', $technicianIds)->get();
    }

    private function send($subscriptions, string $title, string $body, string $url): void
    {
        $publicKey = config('services.vapid.public_key');
        $privateKey = config('services.vapid.private_key');

        if (! $publicKey || ! $privateKey) {
            Log::warning('VAPID no configurado, se omite notificación push.');
            return;
        }

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
                'title' => $title,
                'body' => $body,
                'icon' => 'pwa-icons/icon-192x192.png',
                'vibrate' => [200, 100, 200],
                'data' => [
                    'onActionClick' => [
                        'default' => ['operation' => 'openWindow', 'url' => $url],
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
