<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class TicketRegisteredNotification extends Notification
{
    use Queueable;

    public $ticket;

    /**
     * Create a new notification instance.
     */
    public function __construct(\App\Models\Ticket $ticket)
    {
        $this->ticket = $ticket;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nuevo ticket de soporte registrado: #'.$this->ticket->id)
            ->line('Se ha registrado un nuevo ticket de soporte con el asunto: '.$this->ticket->subject)
            ->action('Ver Ticket', url('/tickets/'.$this->ticket->id))
            ->line('Gracias por usar nuestra aplicación.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'code' => $this->ticket->code,
            'subject' => $this->ticket->subject,
            'description' => $this->ticket->description,
            'status' => $this->ticket->status,
            'customer_id' => $this->ticket->customer_id ?? null,
            'message' => 'Se ha registrado un nuevo ticket de soporte: '.$this->ticket->code,
        ];
    }
}
