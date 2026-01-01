<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentTypeResource extends JsonResource
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
            'name' => $this->name,
            'organization' => $this->organization,
            'bank' => $this->bank,
            'account_number' => $this->account_number,
            'mfo' => $this->mfo,
            'inn' => $this->inn,
            'commission_percentage' => $this->commission_percentage,
            'commission_fixed' => $this->commission_fixed,
            'type' => $this->type,
        ];
    }
}
