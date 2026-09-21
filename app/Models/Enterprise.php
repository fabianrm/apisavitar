<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Enterprise extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'ruc',
        'city_id',
        'address',
        'phone',
        'logo',
        'status',
        'telegram_bot_token',
        'telegram_chat_id',
        'whatsapp_reminders_enabled',
        'wa_instance',
        'wa_api_key',
        'wa_reminder_days_before',
        'wa_payment_info',
        'wa_message_template_due',
        'wa_message_template_overdue',
    ];

    protected $casts = [
        'whatsapp_reminders_enabled' => 'boolean',
    ];

    const DEFAULT_WA_TEMPLATE_DUE = "Hola {cliente} 👋\n\nTe recordamos que tu servicio de internet con *{empresa}* vence el {vencimiento} por un monto de S/ {monto}.\n\n{pago}\n\n¡Gracias por tu preferencia!";

    const DEFAULT_WA_TEMPLATE_OVERDUE = "Hola {cliente} 👋\n\nTu pago con *{empresa}* venció el {vencimiento} y aún no lo hemos detectado. Por favor regulariza antes del {corte} para evitar la suspensión del servicio.\n\n{pago}\n\n¡Gracias por tu preferencia!";

    public function hasTelegramConfigured(): bool
    {
        return ! empty($this->telegram_bot_token) && ! empty($this->telegram_chat_id);
    }

    public function hasWhatsappReminderConfigured(): bool
    {
        return ! empty($this->wa_instance) && ! empty($this->wa_api_key);
    }

    /**
     * Reemplaza placeholders {clave} en la plantilla de recordatorio con los
     * valores reales de la factura -- así el texto que ve el cliente vive en
     * la BD (editable desde Configuración) y no en el workflow de n8n.
     */
    public function renderReminderMessage(string $template, array $placeholders): string
    {
        $message = $template;

        foreach ($placeholders as $key => $value) {
            $message = str_replace('{' . $key . '}', (string) $value, $message);
        }

        return trim($message);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class);
    }

    public function cities()
    {
        return $this->belongsTo(City::class, "city_id");
    }

    //Una tienda puede tener muchas promociones
    public function promotions()
    {
        return $this->hasMany(Promotion::class);
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user')->withPivot('role_id');
    }
}
