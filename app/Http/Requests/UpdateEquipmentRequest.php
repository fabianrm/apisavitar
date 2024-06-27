<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEquipmentRequest extends FormRequest
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
                'type' => ['required'],
                'serie' => ['required'],
                'model' => ['required'],
                'brandId' => ['required'],
                'purchaseDate' => ['required'],
                'status' => ['required']
            ];
        } else {
            return [
                'type' => ['sometimes', 'required'],
                'serie' => ['sometimes', 'required'],
                'model' => ['sometimes', 'required'],
                'brandId' => ['sometimes', 'required'],
                'purchaseDate' => ['sometimes', 'required'],
                'status' => ['sometimes', 'required']
            ];
        }
    }

    protected function prepareForValidation(): void
    {
        if ($this->purchaseDate || $this->brandId) {
            $this->merge([
                'purchase_date' => $this->purchaseDate,
                'brand_id' => $this->brandId
            ]);
        }
    }
}
