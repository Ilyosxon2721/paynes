<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentTypeRequest;
use App\Http\Requests\UpdatePaymentTypeRequest;
use App\Http\Resources\PaymentTypeResource;
use App\Models\PaymentType;
use Illuminate\Http\JsonResponse;

class PaymentTypeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $paymentTypes = PaymentType::orderBy('name', 'asc')->get();

            return response()->json([
                'success' => true,
                'data' => PaymentTypeResource::collection($paymentTypes),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении списка типов платежей: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePaymentTypeRequest $request): JsonResponse
    {
        try {
            $paymentType = PaymentType::create($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Тип платежа успешно создан',
                'data' => new PaymentTypeResource($paymentType),
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании типа платежа: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $paymentType = PaymentType::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new PaymentTypeResource($paymentType),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Тип платежа не найден: ' . $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePaymentTypeRequest $request, string $id): JsonResponse
    {
        try {
            $paymentType = PaymentType::findOrFail($id);
            $paymentType->update($request->validated());

            return response()->json([
                'success' => true,
                'message' => 'Тип платежа успешно обновлен',
                'data' => new PaymentTypeResource($paymentType),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении типа платежа: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $paymentType = PaymentType::findOrFail($id);

            // Check if payment type has associated payments
            if ($paymentType->payments()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Невозможно удалить тип платежа, так как у него есть связанные платежи',
                ], 400);
            }

            $paymentType->delete();

            return response()->json([
                'success' => true,
                'message' => 'Тип платежа успешно удален',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении типа платежа: ' . $e->getMessage(),
            ], 500);
        }
    }
}
