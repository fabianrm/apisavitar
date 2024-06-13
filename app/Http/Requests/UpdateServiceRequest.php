<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateServiceRequest extends FormRequest
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
                'service_code',
                'customerId' => ['required'],
                'planId' => ['required'],
                'routerId' => ['required'],
                'boxId' => ['required'],
                'portNumber' => ['required'],
                'equipmentId' => ['required'],
                'cityId' => ['required'],
                'addressInstallation' => ['required'],
                'reference',
                'registrationDate' => ['required'],
                'installationDate' => ['required'],
                'latitude',
                'longitude',
                'billingDate' => [''],
                'dueDate' => [''],
                'status' => ['required'],
                'endDate' => ['required']
            ];
        } else {
            return [
                'service_code' => ['sometimes', 'required'],
                'customerId' => ['sometimes','required'],
                'planId' => ['sometimes','required'],
                'routerId' => ['sometimes','required'],
                'boxId' => ['sometimes','required'],
                'portNumber' => ['sometimes','required'],
                'equipmentId' => ['sometimes','required'],
                'cityId' => ['sometimes','required'],
                'addressInstallation' => ['sometimes','required'],
                'reference' => ['sometimes', 'required'],
                'registrationDate' => ['sometimes','required'],
                'installationDate' => ['sometimes','required'],
                'latitude' => ['sometimes', 'required'],
                'longitude' => ['sometimes', 'required'],
                'billingDate' => ['sometimes','required'],
                'dueDate' => ['sometimes','required'],
                'status' => ['sometimes','required'],
                'endDate' => ['sometimes','required'],
            ];
        }
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'service_code' => $this->serviceCode,
            'customer_id' => $this->customerId,
            'plan_id' => $this->planId,
            'router_id' => $this->routerId,
            'box_id' => $this->boxId,
            'port_number' => $this->portNumber,
            'equipment_id' => $this->equipmentId,
            'city_id' => $this->cityId,
            'address_installation' => $this->addressInstallation,
            'registration_date' => $this->registrationDate,
            'installation_date' => $this->installationDate,
            'billing_date' => $this->billingDate,
            'end_date' => $this->endDate,
        ]);
    }
}
