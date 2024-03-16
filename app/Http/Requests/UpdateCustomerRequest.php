<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCustomerRequest extends FormRequest
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
        if ($method === 'PUT') {
            return [
                'type' => ['required', Rule::in(['natural', 'juridica'])],
                'documentNumber' => ['required'],
                'name' => ['required'],
                'address' => ['required'],
                'phoneNumber' => ['required'],
                'email' => ['required', 'email'],
                'status' => ['required'],
            ];
        } else {
            return [
                'type' => ['sometimes', 'required', Rule::in(['natural', 'juridica'])],
                'documentNumber' => ['sometimes', 'required'],
                'name' => ['sometimes', 'required'],
                'address' => ['sometimes', 'required'],
                'phoneNumber' => ['sometimes', 'required'],
                'email' => ['sometimes', 'required', 'email'],
                'status' => ['sometimes', 'required'],
            ];

        }
    }
    protected function prepareForValidation(): void
    {
        if ($this->documentNumber || $this->phoneNumber) {
            $this->merge([
                'document_number' => $this->documentNumber,
                'phone_number' => $this->phoneNumber
            ]);
        }
    }
}
