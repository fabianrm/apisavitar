<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMaterialRequest extends FormRequest
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
            'code' => 'sometimes|required|string|max:255',
            'name' => 'sometimes|required|string|max:255',
            'category_id' => 'sometimes|required|exists:categories,id',
            'presentation_id' => 'sometimes|required|exists:presentations,id',
            'serial' => 'sometimes|nullable|string|max:255',
            'model' => 'sometimes|nullable|string|max:255',
            'brand_id' => 'sometimes|required|exists:brands,id',
            'min' => 'sometimes|required|integer',
            'type' => 'sometimes|required|string|in:M,H',
            'image' => 'sometimes|nullable|string|max:255',
            'status' => 'sometimes|required|boolean',
        ];
    }
}
