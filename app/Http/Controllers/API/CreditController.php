<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCreditRequest;
use App\Http\Requests\UpdateCreditRequest;
use App\Http\Requests\RepaymentCreditRequest;
use App\Http\Resources\CreditResource;
use App\Http\Responses\ApiResponse;
use App\Models\Credit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CreditController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Credit::with(['cashier']);

            // Filter by status
            if ($request->has('status')) {
                if ($request->status === 'pending') {
                    $query->pending();
                } elseif ($request->status === 'confirmed') {
                    $query->confirmed();
                }
            }

            // Order by latest
            $query->orderBy('created_at', 'desc');

            $credits = $query->paginate(20);

            return response()->json([
                'success' => true,
                'data' => CreditResource::collection($credits),
                'meta' => [
                    'current_page' => $credits->currentPage(),
                    'last_page' => $credits->lastPage(),
                    'per_page' => $credits->perPage(),
                    'total' => $credits->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении списка кредитов: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCreditRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            // Create credit
            $credit = Credit::create([
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'recipient' => $request->recipient,
                'account_number' => null, // Will be generated after creation
                'branch' => $request->branch,
                'debit' => $request->debit,
                'credit' => $request->credit,
                'confirmed_by' => null,
                'status' => 'pending',
                'cashier_id' => auth()->id(),
            ]);

            // Generate account number using model method
            $accountNumber = $credit->generateAccountNumber();
            $credit->update(['account_number' => $accountNumber]);

            $credit->load(['cashier']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Кредит успешно создан',
                'data' => new CreditResource($credit),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании кредита: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $credit = Credit::with(['cashier'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new CreditResource($credit),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Кредит не найден: ' . $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCreditRequest $request, string $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $credit = Credit::findOrFail($id);

            // Prepare update data
            $updateData = $request->only([
                'recipient',
                'branch',
                'debit',
                'credit',
            ]);

            $credit->update($updateData);
            $credit->load(['cashier']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Кредит успешно обновлен',
                'data' => new CreditResource($credit),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при обновлении кредита: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $credit = Credit::findOrFail($id);
            $credit->delete();

            return response()->json([
                'success' => true,
                'message' => 'Кредит успешно удален',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении кредита: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Confirm the credit.
     */
    public function confirm(string $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $credit = Credit::findOrFail($id);

            if ($credit->status === 'confirmed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Кредит уже подтвержден',
                ], 400);
            }

            $credit->update([
                'status' => 'confirmed',
                'confirmed_by' => auth()->user()->name ?? auth()->id(),
            ]);

            $credit->load(['cashier']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Кредит успешно подтвержден',
                'data' => new CreditResource($credit),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при подтверждении кредита: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Repay the credit.
     */
    public function repay(RepaymentCreditRequest $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            $credit = Credit::findOrFail($request->credit_id);

            if ($credit->status !== 'confirmed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Можно погашать только подтвержденные кредиты',
                ], 400);
            }

            // Calculate new credit amount
            $newCreditAmount = $credit->credit - $request->amount;

            if ($newCreditAmount < 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Сумма погашения превышает остаток кредита',
                ], 400);
            }

            // Update credit amount
            $credit->update([
                'credit' => $newCreditAmount,
            ]);

            $credit->load(['cashier']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Кредит успешно погашен на сумму ' . $request->amount,
                'data' => new CreditResource($credit),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при погашении кредита: ' . $e->getMessage(),
            ], 500);
        }
    }
}
