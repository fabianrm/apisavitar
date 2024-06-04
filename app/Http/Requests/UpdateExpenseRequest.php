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
        if ($method === "POST") {
            return [
                'description' => ['required'],
                'amount' => ['required'],
                'date' => ['required'],
                'reason' => ['required'],
                'voutcher' => [''],
                'note' => [''],
                'userId' => ['required'],
            ];
        } else {
            return [
                'description' => ['sometimes', 'required'],
                'amount' => ['sometimes', 'required'],
                'date' => ['sometimes', 'required'],
                'reason' => ['sometimes', 'required'],
                'voutcher' => ['sometimes', 'required'],
                'note' => ['sometimes', 'required'],
                'userId' => ['sometimes', 'required'],
            ];
        }
    }

    protected function prepareForValidation(): void
    {
        if ($this->userId) {
            $this->merge([
                'user_id' => $this->userId,
            ]);
        }
    }
}
