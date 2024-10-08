<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBoxRequest extends FormRequest
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
            'name' => ['required'],
            'city_id' => ['required'],
            'address' => ['required'],
            'reference' => [''],
            'latitude'=> [''],
            'longitude'=> [''],
            'totalPorts' => ['required'],
            'availablePorts' => [''],
            'note' => [''],
            'status' => ['required'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'total_ports' => $this->totalPorts,
            'available_ports' => $this->availablePorts,
        ]);
    }
}
