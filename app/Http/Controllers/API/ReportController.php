<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Exchange;
use App\Models\Credit;
use App\Models\Incash;
use App\Models\CashierShift;
use App\Models\User;
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
            $user = auth()->user();
            $isAdmin = $user && ($user->hasRole('admin') || $user->position === 'admin');
            $cashierId = $isAdmin ? $request->input('cashier_id') : $user->id;
            $branch = $isAdmin ? $request->input('branch') : null;

            // Validate dates
            if (!$startDate || !$endDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'Необходимо указать начальную и конечную дату',
                ], 400);
            }

            $applyScope = function ($query) use ($cashierId, $branch, $isAdmin) {
                if (!$isAdmin || $cashierId) {
                    $query->where('cashier_id', $cashierId);
                }

                if ($branch) {
                    $query->whereHas('cashier', function ($branchQuery) use ($branch) {
                        $branchQuery->where('branch', $branch);
                    });
                }
            };

            $branches = [];
            if ($isAdmin) {
                $branches = User::where('position', 'cashier')
                    ->whereNotNull('branch')
                    ->where('branch', '!=', '')
                    ->orderBy('branch')
                    ->distinct()
                    ->pluck('branch')
                    ->values();
            }

            // Get payments data
            $paymentsQuery = Payment::whereBetween('date', [$startDate, $endDate]);
            $applyScope($paymentsQuery);

            $paymentsCount = (clone $paymentsQuery)->count();
            $paymentsTotal = (clone $paymentsQuery)->sum('total');

            // Get exchanges data
            $exchangesQuery = Exchange::whereBetween('date', [$startDate, $endDate]);
            $applyScope($exchangesQuery);

            $exchangesCount = (clone $exchangesQuery)->count();
            $exchangesTotal = (clone $exchangesQuery)->sum('uzs_amount');

            // Get credits data
            $creditsQuery = Credit::whereBetween('date', [$startDate, $endDate]);
            $applyScope($creditsQuery);

            $creditsCount = (clone $creditsQuery)->count();
            $creditsTotal = (clone $creditsQuery)
                ->selectRaw('SUM(CASE WHEN debit > 0 THEN debit ELSE credit END) as total')
                ->value('total');

            // Get incashes data
            $incashesQuery = Incash::whereBetween('date', [$startDate, $endDate]);
            $applyScope($incashesQuery);

            $incashesCount = (clone $incashesQuery)->count();
            $incashesIncome = (clone $incashesQuery)->where('type', 'income')->sum('amount');
            $incashesExpense = (clone $incashesQuery)->where('type', 'expense')->sum('amount');
            $incashesTotal = $incashesIncome - $incashesExpense;

            $shiftQuery = CashierShift::with(['payments.methodDetails', 'exchanges', 'credits', 'creditRepayments', 'incashes'])
                ->where('status', 'open');

            if (!$isAdmin || $cashierId) {
                $shiftQuery->where('cashier_id', $cashierId);
            }

            if ($branch) {
                $shiftQuery->whereHas('cashier', function ($branchQuery) use ($branch) {
                    $branchQuery->where('branch', $branch);
                });
            }

            $balances = [
                'cash_uzs' => 0,
                'cashless_uzs' => 0,
                'card_uzs' => 0,
                'p2p_uzs' => 0,
                'cash_usd' => 0,
            ];

            foreach ($shiftQuery->get() as $shift) {
                $shiftBalances = $shift->calculateClosingBalances();
                foreach ($balances as $key => $value) {
                    $balances[$key] += $shiftBalances[$key] ?? 0;
                }
            }

            $balances = CashierShift::normalizeBalances($balances);

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
                    'balances' => $balances,
                    'branches' => $branches,
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
            $payments = $shift->payments()
                ->whereIn('status', ['pending', 'confirmed', 'processed'])
                ->with(['paymentType', 'methodDetails'])
                ->get();
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
            $credits = $shift->credits()
                ->whereIn('status', ['pending', 'confirmed'])
                ->get();
            $totalIssued = $credits->sum(function ($credit) {
                return $credit->debit > 0 ? $credit->debit : $credit->credit;
            });
            $totalRepaid = $credits->sum('total_repaid');
            $outstanding = $credits->sum('remaining_balance');

            $creditsData = [
                'total_issued' => $totalIssued,
                'total_repaid' => $totalRepaid,
                'outstanding' => $outstanding,
                'count' => $credits->count(),
            ];

            // Инкассации
            $incashes = $shift->incashes()
                ->whereIn('status', ['pending', 'confirmed'])
                ->get();
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
                ? CashierShift::normalizeBalances([
                    'cash_uzs' => $shift->closing_cash_uzs,
                    'cashless_uzs' => $shift->closing_cashless_uzs,
                    'card_uzs' => $shift->closing_card_uzs,
                    'p2p_uzs' => $shift->closing_p2p_uzs,
                    'cash_usd' => $shift->closing_cash_usd,
                ])
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

