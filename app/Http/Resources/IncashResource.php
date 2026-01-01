<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IncashResource extends JsonResource
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
            'account_number' => $this->account_number,
            'amount' => $this->amount,
            'type' => $this->type,
            'status' => $this->status,
            'cashier' => new UserResource($this->whenLoaded('cashier')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
