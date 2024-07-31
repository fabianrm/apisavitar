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
            'entry_detail_id' => 'required|exists:entry_details,id',
            'quantity' => 'required|integer',
            'subtotal' => 'required|numeric',
            'status' => 'required|boolean',
        ];
    }
}
