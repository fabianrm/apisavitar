<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOutputDetailRequest extends FormRequest
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
            'output_id' => 'required|exists:exits,id',
            'material_id' => 'required|exists:materials,id',
            'quantity' => 'required|integer',
            'subtotal' => 'required|numeric',
            'status' => 'required|boolean',
        ];
    }
}
