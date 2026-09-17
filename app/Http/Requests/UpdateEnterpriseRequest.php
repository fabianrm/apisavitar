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
    }
}
