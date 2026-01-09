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
            'amount' => $this->debit > 0 ? $this->debit : $this->credit,
            'debit' => $this->debit,
            'credit' => $this->credit,
            'remaining_balance' => $this->remaining_balance,
            'total_repaid' => $this->total_repaid,
            'confirmed_by' => $this->confirmed_by,
            'status' => $this->status,
            'cashier' => new UserResource($this->whenLoaded('cashier')),
            'cashier_shift' => $this->whenLoaded('cashierShift', function () {
                return [
                    'id' => $this->cashierShift->id,
                    'shift_date' => $this->cashierShift->shift_date,
                    'status' => $this->cashierShift->status,
                ];
            }),
            'repayments' => $this->whenLoaded('repayments', function () {
                return $this->repayments->map(function ($repayment) {
                    return [
                        'id' => $repayment->id,
                        'amount' => $repayment->amount,
                        'repayment_date' => $repayment->repayment_date,
                        'repayment_time' => $repayment->repayment_time,
                        'notes' => $repayment->notes,
                        'created_at' => $repayment->created_at?->format('Y-m-d H:i:s'),
                    ];
                });
            }),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
