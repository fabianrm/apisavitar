<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSuspensionRequest extends FormRequest
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
            'enterprise_id' => 'required|exists:enterprises,id',
            'service_id' => 'required|exists:services,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'reason' => 'required|string',
            'observation' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'enterprise_id.required' => 'La empresa es obligatorio.',
            'enterprise_id.exists' => 'La empresa seleccionada no existe.',
            'service_id.required' => 'El servicio es obligatorio.',
            'service_id.exists' => 'El servicio seleccionado no existe.',
            'start_date.required' => 'La fecha de incio es obligatoria.',
            'end_date.required' => 'La fecha de fin es obligatoria.',
            'end_date.after' => 'La fecha de fin debe ser mayor a la de inicio.',

        ];
    }
}
