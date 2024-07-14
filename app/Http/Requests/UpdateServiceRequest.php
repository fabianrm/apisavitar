<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Log;

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

        $rules = [
            'customer_id' => ['required'],
            'plan_id' => ['required'],
            'router_id' => ['required'],
            'box_id' => ['required'],
            'port_number' => ['required'],
            'equipment_id' => ['required'],
            'city_id' => ['required'],
            'address_installation' => ['required'],
            'reference' => ['required'],
            'registration_date' => ['required'],
            'installation_date' => ['required'],
            'latitude' => ['required'],
            'longitude' => ['required'],
            'billing_date' => [''],
            'due_date' => [''],
            'status' => ['required'],
            'end_date' => ['required']
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
        if ($this->has('customerId')) {
            $data['customer_id'] = $this->customerId;
        }
        if ($this->has('planId')) {
            $data['plan_id'] = $this->planId;
        }
        if ($this->has('routerId')) {
            $data['router_id'] = $this->routerId;
        }
        if ($this->has('boxId')) {
            $data['box_id'] = $this->boxId;
        }
        if ($this->has('portNumber')) {
            $data['port_number'] = $this->portNumber;
        }
        if ($this->has('equipmentId')) {
            $data['equipment_id'] = $this->equipmentId;
        }
        if ($this->has('cityId')) {
            $data['city_id'] = $this->cityId;
        }
        if ($this->has('addressInstallation')) {
            $data['address_installation'] = $this->addressInstallation;
        }
        if ($this->has('registrationDate')) {
            $data['registration_date'] = $this->registrationDate;
        }
        if ($this->has('installationDate')) {
            $data['installation_date'] = $this->installationDate;
        }
        if ($this->has('billingDate')) {
            $data['billing_date'] = $this->billingDate;
        }
        if ($this->has('dueDate')) {
            $data['due_date'] = $this->dueDate;
        }
        if ($this->has('status')) {
            $data['status'] = $this->status;
        }
        if ($this->has('endDate')) {
            $data['end_date'] = $this->endDate;
        }
        if ($this->has('userPppoe')) {
            $data['user_pppoe'] = $this->userPppoe;
        }
        if ($this->has('passPppoe')) {
            $data['pass_pppoe'] = $this->passPppoe;
        }
        if ($this->has('observation')) {
            $data['observation'] = $this->observation;
        }

        if (!empty($data)) {
            $this->merge($data);
        }
    }
}
