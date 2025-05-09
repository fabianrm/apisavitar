<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreCustomerRequest extends FormRequest
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
            'type' => ['required', Rule::in(['natural', 'juridica'])],
            'documentNumber' => ['required'],
            'name' => ['required'],
            'clientCode' => [''],
            'cityId' => ['required'],
            'address' => ['required'],
            'reference' => ['required'],
            'latitude' => ['required'],
            'longitude' => ['required'],
            'phoneNumber' => ['required'],
            'whatsapp' => ['required'],
            'email' => ['required', 'email'],
            'status' => ['required'],
        ];
    }
    protected function prepareForValidation(): void
    {
        $this->merge([
            'document_number' => $this->documentNumber,
            'client_code' => $this->clientCode,
            'city_id' => $this->cityId,
            'phone_number' => $this->phoneNumber
        ]);
    }
}
