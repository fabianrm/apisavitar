<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class ServiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'serviceCode' => $this->service_code,
            'customerName' => $this->customers->name,
            'planName' => $this->plans->name,
            'routerId' => $this->router_id,
            'routerIp' => $this->routers->ip,
            'vlan' => $this->routers->vlan,
            'boxName' => $this->boxes ? $this->boxes->name : '-',
            'portNumber' => $this->port_number,
            'equipmentId' => $this->equipment_id,
            'equipmentSerie' => $this->equipments?->serie,
            'equipmentMac' => $this->equipments?->mac,
            'cityId' => $this->city_id,
            'city' => $this->cities->name,
            'addressInstallation' => $this->address_installation,
            'reference' => $this->reference,
            'registrationDate' => $this->registration_date,
            'installationDate' => $this->installation_date,
            'installationPayment' => $this->installation_payment,
            'installationAmount' => $this->installation_amount,
            'prepayment' => $this->prepayment,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'coordinates' => [$this->latitude, $this->longitude],
            'billingDate' => $this->billing_date,
            'dueDate' => $this->due_date,
            'endDate' => $this->end_date,
            'userPppoe' => $this->user_pppoe,
            'passPppoe' => $this->pass_pppoe,
            'iptv' => $this->iptv,
            'userIptv' => $this->user_iptv,
            'passIptv' => $this->pass_iptv,
            'observation' => $this->observation,
            'status' => $this->status,

        ];

        // return parent::toArray($request);
    }
}
