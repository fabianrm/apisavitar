<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
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
            'description' => $this->description,
            'amount' => $this->amount,
            'date' => $this->date,
            'reasonId' => $this->reason_id,
            'reason' => $this->reasons->name,
            'type' => $this->reasons->type,
            'voutcher' => $this->voutcher,
            'note' => $this->note,
            'datePaid' => $this->date_paid,
            'status' => $this->status,
            'userId' => $this->user_id,
            'updatedAt' => $this->updated_at,
            'createdBy' => $this->createdBy->name,
            'updatedBy' => $this->updatedBY->name

        ];

        //return parent::toArray($request);
    }
}
