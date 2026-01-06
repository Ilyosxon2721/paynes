<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExchangeRequest;
use App\Http\Resources\ExchangeResource;
use App\Http\Responses\ApiResponse;
use App\Models\Exchange;
use App\Models\Rate;
use App\Models\CashierShift;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ExchangeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = Exchange::with(['cashier']);

            // Filter by type
            if ($request->has('type')) {
                if ($request->type === 'buy') {
                    $query->buy();
                } elseif ($request->type === 'sell') {
                    $query->sell();
                }
            }

            // Filter by date
            if ($request->has('date')) {
                $query->byDate($request->date);
            }

            // Order by latest
            $query->orderBy('created_at', 'desc');

            $exchanges = $query->paginate(20);

            return response()->json([
                'success' => true,
                'data' => ExchangeResource::collection($exchanges),
                'meta' => [
                    'current_page' => $exchanges->currentPage(),
                    'last_page' => $exchanges->lastPage(),
                    'per_page' => $exchanges->perPage(),
                    'total' => $exchanges->total(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении списка обменов: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreExchangeRequest $request): JsonResponse
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
                    'message' => 'У вас нет открытой смены. Откройте смену перед созданием обмена.',
                ], 400);
            }

            // Get latest rate
            $latestRate = Rate::getLatest();

            if (!$latestRate) {
                return ApiResponse::error(
                    'Курс не найден. Пожалуйста, добавьте курс перед созданием обмена.',
                    400
                );
            }

            // Determine which rate to use based on type
            $rate = $request->type === 'buy' ? $latestRate->buy_rate : $latestRate->sell_rate;

            // Calculate UZS amount
            $uzsAmount = $request->usd_amount * $rate;

            // Create exchange
            $exchange = Exchange::create([
                'date' => now()->toDateString(),
                'time' => now()->toTimeString(),
                'usd_amount' => $request->usd_amount,
                'uzs_amount' => $uzsAmount,
                'type' => $request->type,
                'rate' => $rate,
                'cashier_id' => auth()->id(),
                'cashier_shift_id' => $currentShift->id,
            ]);

            $exchange->load(['cashier', 'cashierShift']);

            DB::commit();

            // Log exchange creation
            Log::channel('exchanges')->info('Exchange created', [
                'exchange_id' => $exchange->id,
                'type' => $exchange->type,
                'usd_amount' => $exchange->usd_amount,
                'uzs_amount' => $exchange->uzs_amount,
                'cashier_id' => $exchange->cashier_id,
                'cashier_shift_id' => $exchange->cashier_shift_id,
            ]);

            return ApiResponse::success(
                new ExchangeResource($exchange),
                'Обмен успешно создан',
                201
            );
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при создании обмена: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $exchange = Exchange::with(['cashier'])->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new ExchangeResource($exchange),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Обмен не найден: ' . $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $exchange = Exchange::findOrFail($id);
            $exchange->delete();

            return response()->json([
                'success' => true,
                'message' => 'Обмен успешно удален',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при удалении обмена: ' . $e->getMessage(),
            ], 500);
        }
    }
}
