<?php

namespace App\Listeners;

use App\Events\PaymentCreated;
use Illuminate\Support\Facades\Log;

class LogPaymentCreated
{
    /**
     * Handle the event.
     */
    public function handle(PaymentCreated $event): void
    {
        Log::channel('payments')->info('Payment created', [
            'payment_id' => $event->payment->id,
            'amount' => $event->payment->amount,
            'commission' => $event->payment->commission,
            'total' => $event->payment->total,
            'cashier_id' => $event->payment->cashier_id,
            'payment_type' => $event->payment->paymentType->name ?? null,
            'status' => $event->payment->status,
        ]);
    }
}
