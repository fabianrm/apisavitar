<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTicketRequest extends FormRequest
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
                'category_ticket_id' => ['required'],
                'subject' => ['required'],
                'description' => ['required'],
                'customer_id' => [''],
                'technician_id' => [''],
                'admin_id' => [''],
                'assigned_at' => ['required'],
                'resolved_at' => [''],
                'closed_at' => [''],
                'status' => ['required'],
            ];
        } else {
            return [
                'category_ticket_id' => ['sometimes','required'],
                'subject' => ['sometimes','required'],
                'description' => ['sometimes','required'],
                'customer_id' => ['sometimes',''],
                'technician_id' => ['sometimes',''],
                'admin_id' => ['sometimes',''],
                'assigned_at' => ['sometimes','required'],
                'resolved_at' => ['sometimes',''],
                'closed_at' => ['sometimes',''],
                'status' => ['sometimes','required'],
            ];
        }
    }
}
