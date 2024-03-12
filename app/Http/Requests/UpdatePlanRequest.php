<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePlanRequest extends FormRequest
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
        } else {
            return [
                'name' => ['sometimes', 'required'],
                'download' => ['sometimes', 'required'],
                'upload' => ['sometimes', 'required'],
                'price' => ['sometimes', 'required'],
                'guaranteedSpeed' => ['sometimes'],
                'priority' => ['sometimes'],
                'burstLimit' => ['sometimes'],
                'burstThreshold' => ['sometimes'],
                'burstTime' => ['sometimes'],
                'status' => ['sometimes'],
            ];
        }

    }
}
