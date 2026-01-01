<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExchangeResource extends JsonResource
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
            'usd_amount' => $this->usd_amount,
            'uzs_amount' => $this->uzs_amount,
            'type' => $this->type,
            'rate' => $this->rate,
            'cashier' => new UserResource($this->whenLoaded('cashier')),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
