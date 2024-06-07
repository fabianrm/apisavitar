<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreExpenseRequest extends FormRequest
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
            'expenseCode' => [''],
            'description' => ['required'],
            'amount' => ['required'],
            'date' => ['required'],
            'reasonId' => ['required'],
            'voutcher' => [''],
            'note' => [''],
            'datePaid' => [''],
        ];
    }
    protected function prepareForValidation(): void
    {
        $this->merge([
            'reason_id' => $this->reasonId,
            'date_paid' => $this->datePaid,
            'expense_code' => $this->expenseCode,
        ]);

    }
}
