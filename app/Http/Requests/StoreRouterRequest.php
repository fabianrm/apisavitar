<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreRouterRequest extends FormRequest
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
        return [
            'ip' => ['required'],
            'usuario' => ['required'],
            'password' => [''],
            'port' => [''],
            'apiConnection' => [''],
            'status' => ['required'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'api_connection' => $this->apiConnection,
        ]);
    }
}
