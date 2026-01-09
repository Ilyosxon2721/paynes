<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\PaymentTypeController;
use App\Http\Controllers\API\ExchangeController;
use App\Http\Controllers\API\CreditController;
use App\Http\Controllers\API\IncashController;
use App\Http\Controllers\API\RateController;
use App\Http\Controllers\API\ReportController;
use App\Http\Controllers\API\CashierShiftController;
use App\Http\Controllers\API\TicketController;
use App\Http\Controllers\PublicReceiptController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Health Check (Public)
Route::get('/health', function () {
    $dbStatus = 'disconnected';
    try {
        DB::connection()->getPdo();
        $dbStatus = 'connected';
    } catch (\Exception $e) {
        // Database is disconnected
    }

    $cacheStatus = 'disconnected';
    try {
        Cache::put('health_check', true, 1);
        $cacheStatus = Cache::get('health_check') ? 'connected' : 'disconnected';
    } catch (\Exception $e) {
        // Cache is disconnected
    }

    return response()->json([
        'status' => 'ok',
        'timestamp' => now()->toIso8601String(),
        'services' => [
            'database' => $dbStatus,
            'cache' => $cacheStatus,
        ],
        'version' => config('app.version', '2.0.0'),
    ]);
})->name('health');

// Public routes
Route::post('/login', [AuthController::class, 'login'])->name('login');
Route::post('/register', [AuthController::class, 'register'])->name('register');

// Public receipt access (no authentication required)
Route::get('/receipts/{token}', [PublicReceiptController::class, 'show'])
    ->middleware('throttle:20,1')
    ->name('receipts.public');

// Protected routes with rate limiting
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/user', [AuthController::class, 'user'])->name('user');

    // Payments
    Route::apiResource('payments', PaymentController::class);
    Route::post('/payments/{id}/confirm', [PaymentController::class, 'confirm'])->name('payments.confirm');
    Route::post('/payments/{id}/duplicate', [PaymentController::class, 'duplicate'])->name('payments.duplicate');

    // Payment Types
    Route::apiResource('payment-types', PaymentTypeController::class);

    // Exchanges
    Route::apiResource('exchanges', ExchangeController::class)->except(['update']);

    // Credits
    Route::apiResource('credits', CreditController::class);
    Route::post('/credits/{id}/confirm', [CreditController::class, 'confirm'])->name('credits.confirm');
    Route::post('/credits/{id}/repay', [CreditController::class, 'repay'])->name('credits.repay');

    // Incashes
    Route::apiResource('incashes', IncashController::class)->except(['update']);
    Route::post('/incashes/{id}/confirm', [IncashController::class, 'confirm'])->name('incashes.confirm');

    // Cashier Shifts
    Route::get('/cashier-shifts/current', [CashierShiftController::class, 'current'])->name('cashier-shifts.current');
    Route::post('/cashier-shifts/open', [CashierShiftController::class, 'open'])->name('cashier-shifts.open');
    Route::post('/cashier-shifts/{id}/close', [CashierShiftController::class, 'close'])->name('cashier-shifts.close');
    Route::get('/cashier-shifts/{id}/report', [CashierShiftController::class, 'report'])->name('cashier-shifts.report');
    Route::apiResource('cashier-shifts', CashierShiftController::class)->only(['index', 'show']);

    // Rates
    Route::get('/rates/latest', [RateController::class, 'latest'])->name('rates.latest');
    Route::apiResource('rates', RateController::class);

    // Reports
    Route::get('/reports/general', [ReportController::class, 'general'])->name('reports.general');
    Route::get('/reports/cashier-dashboard', [ReportController::class, 'cashierDashboard'])->name('reports.cashier-dashboard');

    // Tickets
    Route::get('/tickets/branches', [TicketController::class, 'branches'])->name('tickets.branches');
    Route::apiResource('tickets', TicketController::class)->only(['index', 'show', 'store', 'update']);
    Route::post('/tickets/{ticket}/messages', [TicketController::class, 'storeMessage'])->name('tickets.messages.store');

    // Users (Employees)
    Route::get('/users/roles', [\App\Http\Controllers\API\UserController::class, 'getRoles'])->name('users.roles');
    Route::get('/users/permissions', [\App\Http\Controllers\API\UserController::class, 'getPermissions'])->name('users.permissions');
    Route::apiResource('users', \App\Http\Controllers\API\UserController::class);
});
