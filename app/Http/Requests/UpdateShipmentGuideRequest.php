<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShipmentGuideRequest extends FormRequest
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
            'number' => 'sometimes|required|string|max:255',
            'issue_date' => 'sometimes|required|date',
            'transfer_date' => 'sometimes|required|date',
            'origin_address' => 'sometimes|required|string|max:255',
            'destination_address' => 'sometimes|required|string|max:255',
            'driver_name' => 'sometimes|required|string|max:255',
            'vehicle_plate' => 'sometimes|required|string|max:255',
            'warehouse_id' => 'sometimes|required|exists:warehouses,id',
            'sender_name' => 'sometimes|required|string|max:255',
            'receiver_name' => 'sometimes|required|string|max:255',
            'comment' => 'nullable|string|max:255',
            'status' => 'sometimes|required|boolean',
        ];
    }
}
