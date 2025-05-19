<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePromotionRequest extends FormRequest
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
            'plan_id' => 'sometimes|exists:plans,id',
            'name' => 'sometimes|string|max:255',
            'description' => 'sometimes|string|max:255',
            'start_date' => 'sometimes|date',
            'end_date' => 'sometimes|date|after_or_equal:start_date',
            'price' => 'required|numeric|between:0,99999999.99',
            'duration_months' => 'sometimes|numeric|min:0|max:12',
            'status' => 'sometimes|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'plan_id.exists' => 'El Plan seleccionado no existe.',

            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no debe exceder los 255 caracteres.',

            'start_date.date' => 'La fecha de inicio debe ser una fecha válida.',

            'end_date.date' => 'La fecha de fin debe ser una fecha válida.',
            'end_date.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',

            'price.required' => 'El precio es obligatorio.',
            'price.numeric' => 'El precio debe ser un valor numérico.',
            'price.between' => 'El precio debe estar entre 1 y 99,999,999.99.',

            'duration_months.numeric' => 'La duración debe ser un valor numérico.',
            'duration_months.min' => 'La duración mínima es de 1 meses.',
            'duration_months.max' => 'La duración máxima es de 12 meses.',

            'status.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }
}
