<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateKardexRequest extends FormRequest
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
            'entry_detail_id' => 'sometimes|required|exists:entry_details,id',
            'date' => 'sometimes|required|date',
            'operation' => 'sometimes|required|in:entry,exit',
            'quantity' => 'sometimes|required|integer',
            'price' => 'sometimes|required|numeric',
            'total' => 'sometimes|required|numeric',
            'comment' => 'nullable|string|max:255',
            'status' => 'sometimes|required|boolean',
        ];
    }
}
