<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreRateRequest;
use App\Http\Requests\UpdateRateRequest;
use App\Http\Resources\RateResource;
use App\Models\Rate;
use App\Services\CacheService;
use App\Http\Responses\ApiResponse;
use Illuminate\Http\JsonResponse;

class RateController extends Controller
{
    protected CacheService $cacheService;

    public function __construct(CacheService $cacheService)
    {
        $this->cacheService = $cacheService;
    }

    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        try {
            $rates = Rate::orderBy('date', 'desc')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'success' => true,
                'data' => RateResource::collection($rates),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении списка курсов: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get the latest rate with caching.
     */
    public function latest(): JsonResponse
    {
        try {
            $latestRate = $this->cacheService->getLatestRate();

            if (!$latestRate) {
                return ApiResponse::error('Курс не найден', 404);
            }

            return ApiResponse::success(new RateResource($latestRate));
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Ошибка при получении последнего курса: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreRateRequest $request): JsonResponse
    {
        try {
            $rate = Rate::create($request->validated());

            // Clear rates cache
            $this->cacheService->clearRates();

            return ApiResponse::success(
                new RateResource($rate),
                'Курс успешно создан',
                201
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Ошибка при создании курса: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id): JsonResponse
    {
        try {
            $rate = Rate::findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => new RateResource($rate),
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Курс не найден: ' . $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateRateRequest $request, string $id): JsonResponse
    {
        try {
            $rate = Rate::findOrFail($id);
            $rate->update($request->validated());

            // Clear rates cache
            $this->cacheService->clearRates();

            return ApiResponse::success(
                new RateResource($rate),
                'Курс успешно обновлен'
            );
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Ошибка при обновлении курса: ' . $e->getMessage(),
                500
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id): JsonResponse
    {
        try {
            $rate = Rate::findOrFail($id);

            // Check if this is the only rate
            if (Rate::count() === 1) {
                return ApiResponse::error('Невозможно удалить последний курс', 400);
            }

            $rate->delete();

            // Clear rates cache
            $this->cacheService->clearRates();

            return ApiResponse::success(null, 'Курс успешно удален');
        } catch (\Exception $e) {
            return ApiResponse::error(
                'Ошибка при удалении курса: ' . $e->getMessage(),
                500
            );
        }
    }
}
