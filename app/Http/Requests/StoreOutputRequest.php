<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOutputRequest extends FormRequest
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
            'destination_id' => 'required|exists:destinations,id',
            'employee_id' => 'required|exists:employees,id',
            'total' => '',
            'comment' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ];
    }
}
