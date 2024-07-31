<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEntryRequest extends FormRequest
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
            'date' => 'sometimes|required|date',
            'series' => 'sometimes|required|string|max:255',
            'correlative' => 'sometimes|required|string|max:255',
            'provider_id' => 'sometimes|required|exists:providers,id',
            'document_id' => 'sometimes|required|exists:documents,id',
            'entry_type_id' => 'sometimes|required|exists:entry_types,id',
            'status' => 'sometimes|required|boolean',
        ];
    }
}
