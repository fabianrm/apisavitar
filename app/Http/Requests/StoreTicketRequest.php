<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTicketRequest extends FormRequest
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
            'category_ticket_id' => ['required'],
            'subject' => ['required'],
            'description' => ['required'],
            'customer_id' => ['required'],
            'technician_id' => [''],
            'admin_id' =>   ['required'],
            'assigned_at' => [''],
            'resolved_at' => [''],
            'closed_at' => [''],
            'status' => ['required'],
         
        ];
    }
}
