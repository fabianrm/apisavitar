<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateInvoiceRequest extends FormRequest
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
        if ($method === "PUT") {
            return [
                'serviceId' => ['required'],
                'price' => ['required'],
                'igv' => [''],
                'discount' => [''],
                'amount' => ['required'],
                'letterAmount' => [''],
                'dueDate' => [''],
                'startDate' => [''],
                'endDate' => [''],
                'paidDated' => ['required'],
                'receipt' => [''],
                'tipo_pago' => [''],
                'note' => [''],
                'status' => ['required'],
            ];
        } else {
            return [
                'serviceId' => ['sometimes'],
                'price' => ['sometimes',],
                'igv' => ['sometimes', ''],
                'discount' => ['sometimes', ''],
                'amount' => ['sometimes', 'required'],
                'letterAmount' => ['sometimes', ''],
                'dueDate' => ['sometimes', ''],
                'startDate' => ['sometimes', ''],
                'endDate' => ['sometimes', ''],
                'paidDated' => ['sometimes', 'required'],
                'receipt' => ['sometimes', ''],
                'tipo_pago' => ['sometimes', ''],
                'note' => ['sometimes', ''],
                'status' => ['sometimes', 'required'],
            ];
        }
    }
}
