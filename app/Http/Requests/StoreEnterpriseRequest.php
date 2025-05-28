<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEnterpriseRequest extends FormRequest
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
            'name' => ['required'],
            'ruc' => [
                'required',
                'string',
                'regex:/^(10|20)\d{9}$/',
            ],
            'cityId' => ['required'],
            'address' => ['required'],
            'phone' => [''],
            'logo' => 'nullable|image|mimes:jpeg,jpg|max:2048',
            'status' => ['required'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'city_id' => $this->cityId,
        ]);
    }
}
