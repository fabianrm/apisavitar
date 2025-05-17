<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePromotionRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'price' => 'required|numeric|between:0,99999999.99',
            'duration_months' => 'required|numeric|min:1|max:12',
            'status' => 'required|boolean',
        ];
    }

    //Mensajes de validación
    public function messages(): array
    {
        return [
            'name.required' => 'El nombre es obligatorio.',
            'name.string' => 'El nombre debe ser una cadena de texto.',
            'name.max' => 'El nombre no debe exceder los 255 caracteres.',

            'start_date.required' => 'La fecha de inicio es obligatoria.',
            'start_date.date' => 'La fecha de inicio debe ser una fecha válida.',

            'end_date.required' => 'La fecha de fin es obligatoria.',
            'end_date.date' => 'La fecha de fin debe ser una fecha válida.',
            'end_date.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la fecha de inicio.',

            'price.required' => 'El precio es obligatorio.',
            'price.numeric' => 'El precio debe ser un valor numérico.',
            'price.between' => 'El precio debe estar entre 1 y 99,999,999.99.',

            'duration_months.required' => 'La duración en meses es obligatoria.',
            'duration_months.numeric' => 'La duración debe ser un valor numérico.',
            'duration_months.min' => 'La duración mínima es de 1 meses.',
            'duration_months.max' => 'La duración máxima es de 12 meses.',

            'status.required' => 'El estado es obligatorio.',
            'status.boolean' => 'El estado debe ser verdadero o falso.',
        ];
    }
}
