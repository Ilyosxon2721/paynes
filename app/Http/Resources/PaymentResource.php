<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PaymentResource extends JsonResource
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
            'list_number' => $this->list_number,
            'random_number' => $this->random_number,
            'date' => $this->date,
            'time' => $this->time,
            'payment_type' => new PaymentTypeResource($this->whenLoaded('paymentType')),
            'payer_name' => $this->payer_name,
            'purpose' => $this->purpose,
            'amount' => $this->amount,
            'commission' => $this->commission,
            'total' => $this->total,
            'payment_method' => $this->payment_method,
            'currency' => $this->currency,
            'status' => $this->status,
            'cashier' => new UserResource($this->whenLoaded('cashier')),
            'formatted_total' => $this->formatNumber($this->total),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Format number with spaces
     *
     * @param float|null $number
     * @return string
     */
    private function formatNumber($number): string
    {
        if ($number === null) {
            return '0';
        }

        return number_format($number, 2, '.', ' ');
    }
}
