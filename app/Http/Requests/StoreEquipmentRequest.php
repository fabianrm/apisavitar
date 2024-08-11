<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEquipmentRequest extends FormRequest
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
            'type' => ['required'],
            'mac' => ['required'],
            'serie' => ['required'],
            'model' => ['required'],
            'brandId' => ['required'],
            'purchaseDate' => ['required'],
            'status' => ['required']
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'brand_id' => $this->brandId,
            'purchase_date' => $this->purchaseDate
        ]);

    }
}
