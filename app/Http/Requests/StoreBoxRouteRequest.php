<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBoxRouteRequest extends FormRequest
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
            'start_box_id' => 'required|exists:boxes,id|different:end_box_id',
            'end_box_id' => 'required|exists:boxes,id|different:start_box_id',
            'color' => 'nullable|string|max:7',
            'points' => 'nullable|array',
            'distance' => 'nullable|numeric',
            'status' => 'nullable|string|in:active,inactive',
        ];
    }
}
