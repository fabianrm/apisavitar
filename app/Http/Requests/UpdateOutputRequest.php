<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOutputRequest extends FormRequest
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
            'number' => 'sometimes|required|string|max:255',
            'date' => 'sometimes|required|date',
            'destination_id' => 'sometimes|required|exists:destinations,id',
            'employee_id' => 'sometimes|required|exists:employees,id',
            'total' => 'sometimes|required|numeric',
            'comment' => 'nullable|string|max:255',
            'status' => 'sometimes|required|boolean',
        ];
    }
}
