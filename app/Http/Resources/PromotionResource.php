<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PromotionResource extends JsonResource
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
            'enterprise' => $this->enterprise->name,
            'plan' => [
                'id' => $this->plan_id,
                'name' => $this->plan->name,
            ],
            'name' => $this->name,
            'description' => $this->description,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'price' => $this->price,
            'duration_months' => $this->duration_months,
            'status' => $this->status == 1 ? 'Activa' : 'Finalizada',
            'created_at' => $this->created_at,
            'created_by' => $this->creator->name,
        ];
    }
}
