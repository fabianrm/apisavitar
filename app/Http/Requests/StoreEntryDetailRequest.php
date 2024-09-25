<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEntryDetailRequest extends FormRequest
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
            'entry_id' => 'required|exists:entries,id',
            'date' => 'required|date',
            'material_id' => 'required|exists:materials,id',
            'quantity' => 'required|integer',
            'price' => 'required|numeric',
            'subtotal' => 'required|numeric',
            'warehouse_id' => 'required|exists:warehouses,id',
            'location' => 'required|string|max:255',
            'status' => 'required|boolean',
        ];
    }
}
