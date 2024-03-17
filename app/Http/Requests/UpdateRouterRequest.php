<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateRouterRequest extends FormRequest
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
        if ($method === "POST") {
            return [
                'ip' => ['required'],
                'usuario' => ['required'],
                'password' => [''],
                'port' => [''],
                'apiConnection' => [''],
                'status' => ['required'],
            ];
        } else {
            return [
                'ip' => ['sometimes', 'required'],
                'usuario' => ['sometimes', 'required'],
                'password' => ['sometimes', ''],
                'port' => ['sometimes', ''],
                'apiConnection' => ['sometimes', ''],
                'status' => ['sometimes', 'required'],
            ];
        }
    }

    protected function prepareForValidation(): void
    {
        if ($this->apiConnection) {
            $this->merge([
                'api_connection' => $this->apiConnection
            ]);
        }
    }
}
