<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEntryDetailRequest extends FormRequest
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
            'entry_id' => 'sometimes|required|exists:entries,id',
            'date' => 'sometimes|required|date',
            'material_id' => 'sometimes|required|exists:materials,id',
            'quantity' => 'sometimes|required|integer',
            'current_stock' => 'sometimes|required|integer',
            'price' => 'sometimes|required|numeric',
            'subtotal' => 'sometimes|required|numeric',
            'warehouse_id' => 'sometimes|required|exists:warehouses,id',
            'location' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|boolean',
        ];
    }
}
