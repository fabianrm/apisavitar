<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreEntryRequest extends FormRequest
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
            'date' => 'required|date',
            'document_number' => 'required|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
            'document_id' => 'required|exists:documents,id',
            'entry_type_id' => 'required|exists:entry_types,id',
        ];
    }
}
