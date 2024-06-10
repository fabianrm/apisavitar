<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateExpenseRequest extends FormRequest
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

        $rules = [
            'description' => ['required', 'string'],
            'amount' => ['required', 'numeric'],
            'date' => ['required', 'date'],
            'reason_id' => ['required', 'integer'],
            'voutcher' => ['nullable', 'string'],
            'note' => ['nullable', 'string'],
            'date_paid' => ['nullable', 'date'],
            'status' => ['nullable', 'boolean'],
        ];

        if ($method === 'PATCH') {
            foreach ($rules as $key => $rule) {
                $rules[$key] = array_merge(['sometimes'], $rule);
            }
        }

        return $rules;
    }

    protected function prepareForValidation(): void
    {
        $data = [];
        if ($this->has('reasonId')) {
            $data['reason_id'] = $this->input('reasonId');
        }
        if ($this->has('datePaid')) {
            $data['date_paid'] = $this->input('datePaid');
        }

        if (!empty($data)) {
            $this->merge($data);
        }

    }
}
