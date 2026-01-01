<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExchangeRequest;
use App\Http\Resources\ExchangeResource;
use App\Models\Exchange;
use App\Models\Rate;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

            // Get latest rate
            $latestRate = Rate::getLatest();

            if (!$latestRate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Курс не найден. Пожалуйста, добавьте курс перед созданием обмена.',
                ], 400);
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
            ]);

            $exchange->load(['cashier']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Обмен успешно создан',
                'data' => new ExchangeResource($exchange),
            ], 201);
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
