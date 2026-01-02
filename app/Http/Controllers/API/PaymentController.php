<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Http\Resources\PaymentResource;
use App\Models\Payment;
use App\Models\PaymentType;
use App\Events\PaymentCreated;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Payment::with(['paymentType', 'cashier']);

            // Filter by status
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Filter by date
            if ($request->has('date')) {
                $query->byDate($request->date);
            }

            // Filter by cashier_id
            if ($request->has('cashier_id')) {
                $query->byCashier($request->cashier_id);
            }

            // Order by latest
            $query->orderBy('created_at', 'desc');

            $payments = $query->paginate(20);

            return response()->json([
                'success' => true,
                'data' => PaymentResource::collection($payments),
                'meta' => [
                    'current_page' => $payments->currentPage(),
                    'last_page' => $payments->lastPage(),
                    'per_page' => $payments->perPage(),
                    'total' => $payments->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении списка платежей: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Get payment type for commission calculation
            $paymentType = PaymentType::findOrFail($request->payment_type_id);

            // Calculate commission
            $commission = $paymentType->calculateCommission($request->amount);
            $total = $request->amount + $commission;

            // Generate random number (6 digits)
            $randomNumber = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            // Create payment
            $payment = Payment::create([
                'list_number' => $request->list_number,
                'random_number' => $randomNumber,
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'payment_type_id' => $request->payment_type_id,
                'payer_name' => $request->payer_name,
                'purpose' => $request->purpose,
                'amount' => $request->amount,
                'commission' => $commission,
                'total' => $total,
                'payment_method' => $request->payment_method,
                'currency' => $request->currency,
                'status' => 'pending',
                'cashier_id' => auth()->id(),
            ]);

            $payment->load(['paymentType', 'cashier']);

            DB::commit();

            // Fire PaymentCreated event
            event(new PaymentCreated($payment));

            return ApiResponse::success(
                new PaymentResource($payment),
                'Платеж успешно создан',
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании платежа: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $payment = Payment::with(['paymentType', 'cashier'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new PaymentResource($payment),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Платеж не найден: ' . $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentRequest $request, string $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $payment = Payment::findOrFail($id);

            // Prepare update data
            $updateData = $request->only([
                'list_number',
                'payer_name',
                'purpose',
                'payment_method',
                'currency',
            ]);

            // If amount or payment_type_id changed, recalculate commission
            if ($request->has('amount') || $request->has('payment_type_id')) {
                $paymentTypeId = $request->payment_type_id ?? $payment->payment_type_id;
                $amount = $request->amount ?? $payment->amount;

                $paymentType = PaymentType::findOrFail($paymentTypeId);
                $commission = $paymentType->calculateCommission($amount);
                $total = $amount + $commission;

                $updateData['amount'] = $amount;
                $updateData['commission'] = $commission;
                $updateData['total'] = $total;

                if ($request->has('payment_type_id')) {
                    $updateData['payment_type_id'] = $paymentTypeId;
                }
            }

            $payment->update($updateData);
            $payment->load(['paymentType', 'cashier']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Платеж успешно обновлен',
                'data' => new PaymentResource($payment),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении платежа: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $payment = Payment::findOrFail($id);
            $payment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Платеж успешно удален',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении платежа: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Confirm the payment.
     */
    public function confirm(string $id): JsonResponse
    {
        try {
            $payment = Payment::findOrFail($id);

            if ($payment->status === 'confirmed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Платеж уже подтвержден',
                ], 400);
            }

            $payment->update(['status' => 'confirmed']);
            $payment->load(['paymentType', 'cashier']);

            return response()->json([
                'success' => true,
                'message' => 'Платеж успешно подтвержден',
                'data' => new PaymentResource($payment),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при подтверждении платежа: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Duplicate the payment.
     */
    public function duplicate(string $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $originalPayment = Payment::findOrFail($id);

            // Generate new random number
            $randomNumber = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);

            // Create duplicate payment
            $duplicatePayment = Payment::create([
                'list_number' => $originalPayment->list_number,
                'random_number' => $randomNumber,
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'payment_type_id' => $originalPayment->payment_type_id,
                'payer_name' => $originalPayment->payer_name,
                'purpose' => $originalPayment->purpose,
                'amount' => $originalPayment->amount,
                'commission' => $originalPayment->commission,
                'total' => $originalPayment->total,
                'payment_method' => $originalPayment->payment_method,
                'currency' => $originalPayment->currency,
                'status' => 'pending',
                'cashier_id' => auth()->id(),
            ]);

            $duplicatePayment->load(['paymentType', 'cashier']);

            DB::commit();

            // Fire PaymentCreated event for duplicated payment
            event(new PaymentCreated($duplicatePayment));

            return ApiResponse::success(
                new PaymentResource($duplicatePayment),
                'Платеж успешно дублирован',
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при дублировании платежа: ' . $e->getMessage(),
            ], 500);
        }
    }
}
