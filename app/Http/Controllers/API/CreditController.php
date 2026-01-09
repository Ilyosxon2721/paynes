<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCreditRequest;
use App\Http\Requests\UpdateCreditRequest;
use App\Http\Requests\RepaymentCreditRequest;
use App\Http\Resources\CreditResource;
use App\Http\Responses\ApiResponse;
use App\Models\Credit;
use App\Models\CashierShift;
use App\Models\CreditRepayment;
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

            // Получаем текущую открытую смену кассира
            $currentShift = CashierShift::where('cashier_id', auth()->id())
                ->where('status', 'open')
                ->first();

            if (!$currentShift) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет открытой смены. Откройте смену перед созданием кредита.',
                ], 400);
            }

            $amount = $request->input('amount') ?? $request->debit ?? $request->credit ?? 0;

            // Create credit
            $credit = Credit::create([
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'recipient' => $request->recipient,
                'account_number' => null, // Will be generated after creation
                'branch' => $request->branch,
                'debit' => $amount,
                'credit' => 0,
                'confirmed_by' => null,
                'status' => 'pending',
                'cashier_id' => auth()->id(),
                'cashier_shift_id' => $currentShift->id,
                'remaining_balance' => 0, // Будет установлен при подтверждении
                'total_repaid' => 0,
            ]);

            // Generate account number using model method
            $accountNumber = $credit->generateAccountNumber();
            $credit->update(['account_number' => $accountNumber]);

            $credit->load(['cashier', 'cashierShift']);

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
            $credit = Credit::with(['cashier', 'cashierShift', 'repayments'])->findOrFail($id);

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
            ]);

            if ($request->has('amount')) {
                $updateData['debit'] = $request->amount;
                $updateData['credit'] = 0;
            }

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
            $user = auth()->user();
            if (!$user || (!$user->hasRole('admin') && $user->position !== 'admin')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Только администратор может подтверждать кредиты.',
                ], 403);
            }

            DB::beginTransaction();

            $credit = Credit::with('cashierShift')->findOrFail($id);

            if ($credit->status === 'confirmed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Кредит уже подтвержден',
                ], 400);
            }

            // Проверяем, что смена еще открыта
            if ($credit->cashierShift && $credit->cashierShift->status === 'closed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Смена уже закрыта. Невозможно подтвердить кредит.',
                ], 400);
            }

            // Проверяем наличие достаточных средств в кассе (для debit - выдача)
            $creditAmount = $credit->debit > 0 ? $credit->debit : $credit->credit;

            if ($creditAmount > 0 && $credit->cashierShift) {
                $currentBalances = $credit->cashierShift->calculateClosingBalances();

                if ($currentBalances['cash_uzs'] < $creditAmount) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Недостаточно наличных в кассе для выдачи кредита. Доступно: ' . number_format($currentBalances['cash_uzs'], 2) . ' UZS',
                    ], 400);
                }
            }

            // Подтверждаем кредит и устанавливаем остаток долга
            $credit->update([
                'status' => 'confirmed',
                'confirmed_by' => auth()->user()->name ?? auth()->id(),
                'remaining_balance' => $creditAmount,
            ]);

            $credit->load(['cashier', 'cashierShift']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Кредит успешно подтвержден. Сумма ' . number_format($creditAmount, 2) . ' UZS вычтена из баланса кассы.',
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
    public function repay(RepaymentCreditRequest $request, string $id): JsonResponse
    {
        try {
            DB::beginTransaction();

            $credit = Credit::with('cashierShift')->findOrFail($id);

            if ($credit->status !== 'confirmed') {
                return response()->json([
                    'success' => false,
                    'message' => 'Можно погашать только подтвержденные кредиты',
                ], 400);
            }

            // Проверяем остаток долга
            if ($request->amount > $credit->remaining_balance) {
                return response()->json([
                    'success' => false,
                    'message' => 'Сумма погашения (' . number_format($request->amount, 2) . ') превышает остаток долга (' . number_format($credit->remaining_balance, 2) . ')',
                ], 400);
            }

            // Получаем текущую открытую смену кассира
            $currentShift = CashierShift::where('cashier_id', auth()->id())
                ->where('status', 'open')
                ->first();

            if (!$currentShift) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас нет открытой смены. Откройте смену перед погашением кредита.',
                ], 400);
            }

            // Создаем запись о погашении
            $repayment = CreditRepayment::create([
                'credit_id' => $credit->id,
                'cashier_shift_id' => $currentShift->id,
                'amount' => $request->amount,
                'repayment_date' => now()->toDateString(),
                'repayment_time' => now()->toTimeString(),
                'notes' => $request->notes,
            ]);

            // Обновляем кредит
            $credit->update([
                'remaining_balance' => $credit->remaining_balance - $request->amount,
                'total_repaid' => $credit->total_repaid + $request->amount,
            ]);

            $credit->load(['cashier', 'cashierShift', 'repayments']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Кредит успешно погашен на сумму ' . number_format($request->amount, 2) . ' UZS. Остаток долга: ' . number_format($credit->remaining_balance, 2) . ' UZS',
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





