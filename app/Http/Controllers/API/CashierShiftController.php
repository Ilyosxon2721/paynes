<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CashierShift;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CashierShiftController extends Controller
{
    /**
     * Получить список смен
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = CashierShift::with('cashier');

            // Фильтр по кассиру
            if ($request->has('cashier_id')) {
                $query->where('cashier_id', $request->cashier_id);
            }

            // Фильтр по дате
            if ($request->has('date')) {
                $query->whereDate('shift_date', $request->date);
            }

            // Фильтр по статусу
            if ($request->has('status')) {
                $query->where('status', $request->status);
            }

            // Для кассиров показываем только их смены
            if (auth()->user()->position === 'cashier') {
                $query->where('cashier_id', auth()->id());
            }

            $shifts = $query->orderBy('shift_date', 'desc')
                ->orderBy('opened_at', 'desc')
                ->paginate(20);

            return response()->json([
                'success' => true,
                'data' => $shifts,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении списка смен: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Получить текущую открытую смену кассира
     */
    public function current(): JsonResponse
    {
        try {
            $shift = CashierShift::where('cashier_id', auth()->id())
                ->where('status', 'open')
                ->with('cashier')
                ->first();

            if (!$shift) {
                return response()->json([
                    'success' => true,
                    'data' => null,
                    'message' => 'Нет открытой смены',
                ]);
            }

            return response()->json([
                'success' => true,
                'data' => $shift,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении текущей смены: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Открыть новую смену
     */
    public function open(Request $request): JsonResponse
    {
        try {
            // Проверяем, нет ли уже открытой смены
            $existingShift = CashierShift::where('cashier_id', auth()->id())
                ->where('status', 'open')
                ->first();

            if ($existingShift) {
                return response()->json([
                    'success' => false,
                    'message' => 'У вас уже есть открытая смена',
                ], 400);
            }

            DB::beginTransaction();

            // Получаем последнюю закрытую смену для начальных балансов
            $lastShift = CashierShift::where('cashier_id', auth()->id())
                ->where('status', 'closed')
                ->orderBy('shift_date', 'desc')
                ->orderBy('closed_at', 'desc')
                ->first();

            // Создаем новую смену
            $shift = CashierShift::create([
                'cashier_id' => auth()->id(),
                'shift_date' => now()->toDateString(),
                'opened_at' => now()->toTimeString(),
                'status' => 'open',
                'opening_cash_uzs' => $request->opening_cash_uzs ?? ($lastShift->closing_cash_uzs ?? 0),
                'opening_cashless_uzs' => $request->opening_cashless_uzs ?? ($lastShift->closing_cashless_uzs ?? 0),
                'opening_card_uzs' => $request->opening_card_uzs ?? ($lastShift->closing_card_uzs ?? 0),
                'opening_p2p_uzs' => $request->opening_p2p_uzs ?? ($lastShift->closing_p2p_uzs ?? 0),
                'opening_cash_usd' => $request->opening_cash_usd ?? ($lastShift->closing_cash_usd ?? 0),
                'opening_notes' => $request->opening_notes,
            ]);

            $shift->load('cashier');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Смена успешно открыта',
                'data' => $shift,
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при открытии смены: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Закрыть смену
     */
    public function close(string $id, Request $request): JsonResponse
    {
        try {
            $shift = CashierShift::findOrFail($id);

            // Проверяем, что это смена текущего кассира или пользователь - админ
            if ($shift->cashier_id !== auth()->id() && auth()->user()->position !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Вы не можете закрыть чужую смену',
                ], 403);
            }

            // Проверяем, что смена открыта
            if ($shift->status !== 'open') {
                return response()->json([
                    'success' => false,
                    'message' => 'Смена уже закрыта',
                ], 400);
            }

            // Проверяем возможность закрытия
            $canClose = $shift->canClose();
            if (!$canClose['can_close']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Невозможно закрыть смену',
                    'errors' => $canClose['errors'],
                ], 400);
            }

            DB::beginTransaction();

            // Рассчитываем итоговые балансы
            $closingBalances = $shift->calculateClosingBalances();

            // Обновляем смену
            $shift->update([
                'status' => 'closed',
                'closed_at' => now()->toTimeString(),
                'closing_cash_uzs' => $closingBalances['cash_uzs'],
                'closing_cashless_uzs' => $closingBalances['cashless_uzs'],
                'closing_card_uzs' => $closingBalances['card_uzs'],
                'closing_p2p_uzs' => $closingBalances['p2p_uzs'],
                'closing_cash_usd' => $closingBalances['cash_usd'],
                'closing_notes' => $request->closing_notes,
            ]);

            $shift->load('cashier');

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Смена успешно закрыта',
                'data' => $shift,
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Ошибка при закрытии смены: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Получить детали смены
     */
    public function show(string $id): JsonResponse
    {
        try {
            $shift = CashierShift::with(['cashier'])->findOrFail($id);

            // Проверяем доступ
            if ($shift->cashier_id !== auth()->id() && auth()->user()->position !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Доступ запрещен',
                ], 403);
            }

            return response()->json([
                'success' => true,
                'data' => $shift,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Смена не найдена: ' . $e->getMessage(),
            ], 404);
        }
    }

    /**
     * Получить отчет по смене
     */
    public function report(string $id): JsonResponse
    {
        try {
            $shift = CashierShift::with(['cashier', 'payments.paymentType', 'exchanges', 'credits', 'incashes'])
                ->findOrFail($id);

            // Проверяем доступ
            if ($shift->cashier_id !== auth()->id() && auth()->user()->position !== 'admin') {
                return response()->json([
                    'success' => false,
                    'message' => 'Доступ запрещен',
                ], 403);
            }

            // Получаем статистику
            $stats = $shift->getTotalOperations();

            // Получаем текущие балансы
            $balances = $shift->status === 'closed'
                ? [
                    'cash_uzs' => $shift->closing_cash_uzs,
                    'cashless_uzs' => $shift->closing_cashless_uzs,
                    'card_uzs' => $shift->closing_card_uzs,
                    'p2p_uzs' => $shift->closing_p2p_uzs,
                    'cash_usd' => $shift->closing_cash_usd,
                ]
                : $shift->calculateClosingBalances();

            return response()->json([
                'success' => true,
                'data' => [
                    'shift' => $shift,
                    'statistics' => $stats,
                    'balances' => $balances,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при получении отчета: ' . $e->getMessage(),
            ], 500);
        }
    }
}
