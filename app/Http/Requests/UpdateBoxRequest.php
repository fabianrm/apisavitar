<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBoxRequest extends FormRequest
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
        $method = $this->method();
        if ($method === "POST") {
            return [
                'city' => ['required'],
                'address' => ['required'],
                'reference' => ['required'],
                'totalPorts' => ['required'],
                'availablePorts' => ['required'],
                'status' => ['required'],
            ];
        } else {
            return [
                'city' => ['sometimes', 'required'],
                'address' => ['sometimes', 'required'],
                'reference' => ['sometimes', 'required'],
                'totalPorts' => ['sometimes', 'required'],
                'availablePorts' => ['sometimes', 'required'],
                'status' => ['sometimes', 'required'],
            ];
        }

    }
}
