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
            'enterpriseId' => [''],
            'customerId' => ['required'],
            'planId' => ['required'],
            'routerId' => ['required'],
            'boxId' => ['required'],
            'portNumber' => ['required'],
            'equipmentId' => ['required'],
            'cityId' => ['required'],
            'addressInstallation' => ['required'],
            'reference' => [''],
            'registrationDate' => ['required'],
            'installationDate' => ['required'],
            'latitude' => [''],
            'longitude' => [''],
            'billingDate' => [''],
            'dueDate' => [''],
            'endDate' => [''],
            'userPppoe' => [''],
            'passPppoe' => [''],
            'iptv' => [''],
            'userIptv' => [''],
            'passIptv' => [''],
            'promotionId' => [''],
            'observation' => [''],
            'installationPayment' => [''],
            'installationAmount' => [''],
            'prepayment' => [''],
            'status' => ['required']
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'service_code' => $this->serviceCode,
            'enterprise_id' => $this->enterpriseId,
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
            'due_date' => $this->dueDate,
            'end_date' => $this->endDate,
            'user_pppoe' => $this->userPppoe,
            'pass_pppoe' => $this->passPppoe,
            'user_iptv' => $this->userIptv,
            'pass_iptv' => $this->passIptv,
            'promotion_id' => $this->promotionId,
            'installation_payment' => $this->installationPayment,
            'installation_amount' => $this->installationAmount,

        ]);
    }
}
