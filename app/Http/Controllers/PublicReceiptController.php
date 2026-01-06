<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Http\Resources\PaymentResource;
use Illuminate\Http\JsonResponse;

class PublicReceiptController extends Controller
{
    /**
     * Display public receipt by share token
     *
     * @param string $token
     * @return JsonResponse|PaymentResource
     */
    public function show(string $token): JsonResponse|PaymentResource
    {
        $payment = Payment::with(['paymentType', 'cashier', 'agent'])
            ->where('share_token', $token)
            ->firstOrFail();

        return new PaymentResource($payment);
    }
}
