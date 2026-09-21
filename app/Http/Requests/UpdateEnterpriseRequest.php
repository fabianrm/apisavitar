<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEnterpriseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $method = $this->method();

        if ($method === "PUT") {
            return [
                'name' => ['required'],
                'cityId' => ['required'],
                'address' => ['required'],
                'phone' => [''],
                'logo' => [''],
                'telegramBotToken' => [''],
                'telegramChatId' => [''],
                'whatsappRemindersEnabled' => [''],
                'waInstance' => [''],
                'waApiKey' => [''],
                'waReminderDaysBefore' => [''],
                'waPaymentInfo' => [''],
                'waMessageTemplateDue' => [''],
                'waMessageTemplateOverdue' => [''],
            ];
        } else {
            return [
                'name' => ['sometimes'],
                'cityId' => ['sometimes'],
                'address' => ['sometimes'],
                'phone' => ['sometimes'],
                'logo' => ['sometimes'],
                'telegramBotToken' => ['sometimes'],
                'telegramChatId' => ['sometimes'],
                'whatsappRemindersEnabled' => ['sometimes'],
                'waInstance' => ['sometimes'],
                'waApiKey' => ['sometimes'],
                'waReminderDaysBefore' => ['sometimes', 'integer', 'min:1', 'max:30'],
                'waPaymentInfo' => ['sometimes'],
                'waMessageTemplateDue' => ['sometimes'],
                'waMessageTemplateOverdue' => ['sometimes'],
            ];
        }
    }

    protected function prepareForValidation(): void
    {

        if ($this->cityId ) {
            $this->merge([
                'city_id' => $this->cityId,
            ]);
        }

        if ($this->has('telegramBotToken')) {
            $this->merge([
                'telegram_bot_token' => $this->telegramBotToken,
            ]);
        }

        if ($this->has('telegramChatId')) {
            $this->merge([
                'telegram_chat_id' => $this->telegramChatId,
            ]);
        }

        if ($this->has('whatsappRemindersEnabled')) {
            $this->merge([
                'whatsapp_reminders_enabled' => $this->boolean('whatsappRemindersEnabled'),
            ]);
        }

        if ($this->has('waInstance')) {
            $this->merge(['wa_instance' => $this->waInstance]);
        }

        if ($this->has('waApiKey')) {
            $this->merge(['wa_api_key' => $this->waApiKey]);
        }

        if ($this->has('waReminderDaysBefore')) {
            $this->merge(['wa_reminder_days_before' => $this->waReminderDaysBefore]);
        }

        if ($this->has('waPaymentInfo')) {
            $this->merge(['wa_payment_info' => $this->waPaymentInfo]);
        }

        if ($this->has('waMessageTemplateDue')) {
            $this->merge(['wa_message_template_due' => $this->waMessageTemplateDue]);
        }

        if ($this->has('waMessageTemplateOverdue')) {
            $this->merge(['wa_message_template_overdue' => $this->waMessageTemplateOverdue]);
        }
    }
}
