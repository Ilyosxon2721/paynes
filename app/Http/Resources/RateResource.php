<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RateResource extends JsonResource
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
            'buy_rate' => $this->buy_rate,
            'sell_rate' => $this->sell_rate,
            'date' => $this->date,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
        ];
    }
}
