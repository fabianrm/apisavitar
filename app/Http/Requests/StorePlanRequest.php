<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePlanRequest extends FormRequest
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
            'download' => ['required'],
            'upload' => ['required'],
            'price' => ['required'],
            'guaranteedSpeed',
            'priority',
            'burstLimit',
            'burstThreshold',
            'burstTime',
            'status'
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'guaranteed_speed' => $this->guaranteedSpeed,
            'burst_limit' => $this->burstLimit,
            'burst_threshold' => $this->burstThreshold,
            'burst_time' => $this->burstTime,

        ]);
    }
}
