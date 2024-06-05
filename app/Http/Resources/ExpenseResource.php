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
            'description'=>$this->description,
            'amount' => $this->amount,
            'date' => $this->date ,
            'reasonId' => $this->reason_id,
            'reason' => $this->reasons->name,
            'voutcher' => $this->voutcher,
            'note' => $this->note,
            'datePaid' => $this->date_paid,
            'userId' => $this-> user_id,
            'createdBy' => $this->created_by,
            'updatedBy' => $this->updated_by,
            'updatedAt' => $this->updated_at,

        ];

        //return parent::toArray($request);
    }
}
