<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreMaterialRequest extends FormRequest
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
            'code' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'presentation_id' => 'required|exists:presentations,id',
            'serial' => 'nullable|string|max:255',
            'model' => 'nullable|string|max:255',
            'brand_id' => 'required|exists:brands,id',
            'min' => 'required|integer',
            'type' => 'required|string|in:material,tool',
            'image' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ];
    }
}
