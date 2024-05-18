<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
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
            'serviceCode' => [''],
            'customerId'=> ['required'],
            'planId'=> ['required'],
            'routerId'=> ['required'],
            'boxId'=> ['required'],
            'portNumber'=> ['required'],
            'equipmentId'=> ['required'],
            'cityId'=> ['required'],
            'addressInstalation'=> ['required'],
            'reference' => [''],
            'registrationDate'=> ['required'],
            'instalationDate'=> ['required'],
            'latitude' => [''],
            'longitude' => [''],
            'billingDate'=> ['required'],
            'dueDate'=> ['required'],
            'status'=> ['required']
        ];
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
            'address_instalation' => $this->addressInstalation,
            'registration_date' => $this->registrationDate,
            'instalation_date' => $this->instalationDate,
            'billing_date' => $this->billingDate,
            'due_date' => $this->dueDate,
        ]);
    }

}
