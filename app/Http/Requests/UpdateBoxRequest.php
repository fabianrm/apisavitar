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
                'name' => ['required'],
                'city_id' => ['required'],
                'address' => ['required'],
                'reference' => [''],
                'latitude' => [''],
                'longitude' => [''],
                'totalPorts' => ['required'],
                'availablePorts' => [''],
                'status' => ['required'],
            ];
        } else {
            return [
                'name' => ['sometimes', 'required'],
                'city_id' => ['sometimes', 'required'],
                'address' => ['sometimes', 'required'],
                'reference' => ['sometimes', 'required'],
                'latitude' => ['sometimes', 'required'],
                'longitude' => ['sometimes', 'required'],
                'totalPorts' => ['sometimes', 'required'],
                'availablePorts' => ['sometimes', 'required'],
                'status' => ['sometimes', 'required'],
            ];
        }
    }

    protected function prepareForValidation(): void
    {
        if ($this->totalPorts || $this->availablePorts) {
            $this->merge([
                'total_ports' => $this->totalPorts,
                'available_ports' => $this->availablePorts
            ]);
        }
    }
}
