<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOutputDetailRequest extends FormRequest
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
            'output_id' => 'sometimes|required|exists:exits,id',
            'entry_detail_id' => 'sometimes|required|exists:entry_details,id',
            'quantity' => 'sometimes|required|integer',
            'subtotal' => 'sometimes|required|numeric',
            'status' => 'sometimes|required|boolean',
        ];
    }
}
