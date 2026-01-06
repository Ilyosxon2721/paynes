<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Exchange;
use App\Models\Credit;
use App\Models\Incash;
use App\Models\CashierShift;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    /**
     * Get general report for cashier.
     */
    public function general(Request $request): JsonResponse
    {
        try {
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $cashierId = auth()->id();

            // Validate dates
            if (!$startDate || !$endDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Необходимо указать начальную и конечную дату',
                ], 400);
            }

            // Get payments data
            $paymentsQuery = Payment::where('cashier_id', $cashierId)
                ->whereBetween('date', [$startDate, $endDate]);

            $paymentsCount = $paymentsQuery->count();
            $paymentsTotal = $paymentsQuery->sum('total');

            // Get exchanges data
            $exchangesQuery = Exchange::where('cashier_id', $cashierId)
                ->whereBetween('date', [$startDate, $endDate]);

            $exchangesCount = $exchangesQuery->count();
            $exchangesTotal = $exchangesQuery->sum('uzs_amount');

            // Get credits data
            $creditsQuery = Credit::where('cashier_id', $cashierId)
                ->whereBetween('date', [$startDate, $endDate]);

            $creditsCount = $creditsQuery->count();
            $creditsTotal = $creditsQuery->sum('credit');

            // Get incashes data
            $incashesQuery = Incash::where('cashier_id', $cashierId)
                ->whereBetween('date', [$startDate, $endDate]);

            $incashesCount = $incashesQuery->count();
            $incashesTotal = $incashesQuery->sum('total');

            return response()->json([
                'success' => true,
                'data' => [
                    'payments' => [
                        'count' => $paymentsCount,
                        'total' => $paymentsTotal,
                    ],
                    'exchanges' => [
                        'count' => $exchangesCount,
                        'total' => $exchangesTotal,
                    ],
                    'credits' => [
                        'count' => $creditsCount,
                        'total' => $creditsTotal,
                    ],
                    'incashes' => [
                        'count' => $incashesCount,
                        'total' => $incashesTotal,
                    ],
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при формировании отчета: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get detailed dashboard data for cashier's current shift.
     */
    public function cashierDashboard(Request $request): JsonResponse
    {
        try {
            // Получаем текущую смену или смену по ID
            $shiftId = $request->input('shift_id');

            if ($shiftId) {
                $shift = CashierShift::findOrFail($shiftId);
            } else {
                $shift = CashierShift::where('cashier_id', auth()->id())
                    ->where('status', 'open')
                    ->first();

                if (!$shift) {
                    return response()->json([
                        'success' => false,
                        'message' => 'У вас нет открытой смены',
                    ], 404);
                }
            }

            // Загружаем все связанные данные
            $shift->load(['cashier', 'payments.paymentType', 'payments.methodDetails', 'exchanges', 'credits.repayments', 'incashes']);

            // Платежи: детальная аналитика
            $payments = $shift->payments()->confirmed()->with(['paymentType', 'methodDetails'])->get();
            $paymentsTotal = $payments->sum('total');
            $paymentsCount = $payments->count();

            // Платежи по методам оплаты
            $paymentsByMethod = [];
            foreach ($payments as $payment) {
                foreach ($payment->methodDetails as $detail) {
                    $method = $detail->method;
                    if (!isset($paymentsByMethod[$method])) {
                        $paymentsByMethod[$method] = ['count' => 0, 'amount' => 0];
                    }
                    $paymentsByMethod[$method]['count']++;
                    $paymentsByMethod[$method]['amount'] += floatval($detail->amount);
                }
            }

            // Платежи по типам
            $paymentsByType = $payments->groupBy('payment_type_id')->map(function ($group) {
                return [
                    'type_name' => $group->first()->paymentType->name,
                    'count' => $group->count(),
                    'amount' => $group->sum('total'),
                ];
            })->values();

            // Платежи по платежным системам (для карт)
            $paymentsBySystem = [];
            foreach ($payments as $payment) {
                foreach ($payment->methodDetails as $detail) {
                    if ($detail->method === 'card' && $detail->payment_system) {
                        $system = $detail->payment_system;
                        if (!isset($paymentsBySystem[$system])) {
                            $paymentsBySystem[$system] = ['count' => 0, 'amount' => 0];
                        }
                        $paymentsBySystem[$system]['count']++;
                        $paymentsBySystem[$system]['amount'] += floatval($detail->amount);
                    }
                }
            }

            // Обмены валют
            $exchanges = $shift->exchanges;
            $exchangesBuy = $exchanges->where('type', 'buy');
            $exchangesSell = $exchanges->where('type', 'sell');

            $exchangesData = [
                'total_uzs' => $exchanges->sum('uzs_amount'),
                'count' => $exchanges->count(),
                'buy' => [
                    'count' => $exchangesBuy->count(),
                    'usd_amount' => $exchangesBuy->sum('usd_amount'),
                    'uzs_amount' => $exchangesBuy->sum('uzs_amount'),
                    'avg_rate' => $exchangesBuy->count() > 0 ? $exchangesBuy->avg('rate') : 0,
                ],
                'sell' => [
                    'count' => $exchangesSell->count(),
                    'usd_amount' => $exchangesSell->sum('usd_amount'),
                    'uzs_amount' => $exchangesSell->sum('uzs_amount'),
                    'avg_rate' => $exchangesSell->count() > 0 ? $exchangesSell->avg('rate') : 0,
                ],
            ];

            // Кредиты
            $credits = $shift->credits()->confirmed()->get();
            $totalIssued = $credits->sum('debit');
            $totalRepaid = $credits->sum('total_repaid');
            $outstanding = $credits->sum('remaining_balance');

            $creditsData = [
                'total_issued' => $totalIssued,
                'total_repaid' => $totalRepaid,
                'outstanding' => $outstanding,
                'count' => $credits->count(),
            ];

            // Инкассации
            $incashes = $shift->incashes()->confirmed()->get();
            $incashesIncome = $incashes->where('type', 'income');
            $incashesExpense = $incashes->where('type', 'expense');

            $incashesData = [
                'income' => [
                    'count' => $incashesIncome->count(),
                    'amount' => $incashesIncome->sum('amount'),
                ],
                'expense' => [
                    'count' => $incashesExpense->count(),
                    'amount' => $incashesExpense->sum('amount'),
                ],
                'balance' => $incashesIncome->sum('amount') - $incashesExpense->sum('amount'),
            ];

            // Балансы
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
                    'shift' => [
                        'id' => $shift->id,
                        'shift_date' => $shift->shift_date,
                        'opened_at' => $shift->opened_at,
                        'closed_at' => $shift->closed_at,
                        'status' => $shift->status,
                        'cashier' => [
                            'id' => $shift->cashier->id,
                            'name' => $shift->cashier->name,
                        ],
                    ],
                    'payments' => [
                        'total' => $paymentsTotal,
                        'count' => $paymentsCount,
                        'by_method' => $paymentsByMethod,
                        'by_type' => $paymentsByType,
                        'by_payment_system' => $paymentsBySystem,
                    ],
                    'exchanges' => $exchangesData,
                    'credits' => $creditsData,
                    'incashes' => $incashesData,
                    'balances' => $balances,
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Ошибка при формировании дашборда: ' . $e->getMessage(),
            ], 500);
        }
    }
}
