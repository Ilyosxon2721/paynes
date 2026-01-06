<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreIncashRequest;
use App\Http\Resources\IncashResource;
use App\Http\Responses\ApiResponse;
use App\Models\Incash;
use App\Models\IncashStatusHistory;
use App\Models\CashierShift;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class IncashController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Incash::with(['cashier']);

            // Filter by account_number
            if ($request->has('account_number')) {
                $query->byAccountNumber($request->account_number);
            }

            // Filter by date
            if ($request->has('date')) {
                $query->whereDate('date', $request->date);
            }

            // Order by latest
            $query->orderBy('created_at', 'desc');

            $incashes = $query->paginate(20);

            return response()->json([
                'success' => true,
                'data' => IncashResource::collection($incashes),
                'meta' => [
                    'current_page' => $incashes->currentPage(),
                    'last_page' => $incashes->lastPage(),
                    'per_page' => $incashes->perPage(),
                    'total' => $incashes->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении списка инкассаций: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreIncashRequest $request): JsonResponse
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
                    'message' => 'У вас нет открытой смены. Откройте смену перед созданием инкассации.',
                ], 400);
            }

            // Create incash
            $incash = Incash::create([
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'account_number' => $request->account_number,
                'amount' => $request->amount,
                'type' => $request->type,
                'cashier_id' => auth()->id(),
                'cashier_shift_id' => $currentShift->id,
                'status' => 'pending',
            ]);

            // Записываем историю создания
            IncashStatusHistory::create([
                'incash_id' => $incash->id,
                'old_status' => null,
                'new_status' => 'pending',
                'changed_by' => auth()->id(),
                'comment' => $request->type === 'income' ? 'Создан приход' : 'Создан расход'
            ]);

            $incash->load(['cashier', 'cashierShift']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Инкассация успешно создана',
                'data' => new IncashResource($incash),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании инкассации: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $incash = Incash::with(['cashier'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new IncashResource($incash),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Инкассация не найдена: ' . $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Confirm the specified incash.
     */
    public function confirm(string $id): JsonResponse
    {
        try {
            $incash = Incash::findOrFail($id);

            if ($incash->status !== 'pending') {
                return response()->json([
                    'success' => false,
                    'message' => 'Инкассация уже обработана',
                ], 400);
            }

            $oldStatus = $incash->status;
            $incash->update(['status' => 'confirmed']);

            // Записываем историю изменения статуса
            IncashStatusHistory::create([
                'incash_id' => $incash->id,
                'old_status' => $oldStatus,
                'new_status' => 'confirmed',
                'changed_by' => auth()->id(),
                'comment' => 'Инкассация подтверждена администратором'
            ]);

            $incash->load(['cashier']);

            return response()->json([
                'success' => true,
                'message' => 'Инкассация успешно подтверждена',
                'data' => new IncashResource($incash),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при подтверждении инкассации: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $incash = Incash::findOrFail($id);
            $incash->delete();

            return response()->json([
                'success' => true,
                'message' => 'Инкассация успешно удалена',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении инкассации: ' . $e->getMessage(),
            ], 500);
        }
    }
}
