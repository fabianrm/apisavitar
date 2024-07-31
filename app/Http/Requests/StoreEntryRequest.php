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
            'series' => 'required|string|max:255',
            'correlative' => 'required|string|max:255',
            'provider_id' => 'required|exists:providers,id',
            'document_id' => 'required|exists:documents,id',
            'entry_type_id' => 'required|exists:entry_types,id',
            'status' => 'required|boolean',
        ];
    }
}
