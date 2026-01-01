<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CreditResource extends JsonResource
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
            'date' => $this->date,
            'time' => $this->time,
            'recipient' => $this->recipient,
            'account_number' => $this->account_number,
            'branch' => $this->branch,
            'debit' => $this->debit,
            'credit' => $this->credit,
            'confirmed_by' => $this->confirmed_by,
            'status' => $this->status,
            'cashier' => new UserResource($this->whenLoaded('cashier')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
