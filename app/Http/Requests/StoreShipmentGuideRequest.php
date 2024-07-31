<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreShipmentGuideRequest extends FormRequest
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
            'number' => 'required|string|max:255',
            'issue_date' => 'required|date',
            'transfer_date' => 'required|date',
            'origin_address' => 'required|string|max:255',
            'destination_address' => 'required|string|max:255',
            'driver_name' => 'required|string|max:255',
            'vehicle_plate' => 'required|string|max:255',
            'warehouse_id' => 'required|exists:warehouses,id',
            'sender_name' => 'required|string|max:255',
            'receiver_name' => 'required|string|max:255',
            'comment' => 'nullable|string|max:255',
            'status' => 'required|boolean',
        ];

    }
}
