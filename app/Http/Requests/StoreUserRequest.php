<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreUserRequest extends FormRequest
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
            'dni' => 'required|string|unique:users|max:255',
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:255',
            'position' => 'required|string|max:255',
            'status' => 'required|boolean',
        ];
    }


    /**
     * Mensajes de error personalizados para las validaciones.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'dni.required' => 'El DNI es requerido.',
            'dni.unique' => 'El DNI ya se encuentra registrado.',
            'email.unique' => 'El email ya se encuentra registrado.',
            'password.min' => 'El password debe tener 6 caracteres o más.',
        ];
    }
}
