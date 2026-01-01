<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\PaymentController;
use App\Http\Controllers\API\PaymentTypeController;
use App\Http\Controllers\API\ExchangeController;
use App\Http\Controllers\API\CreditController;
use App\Http\Controllers\API\IncashController;
use App\Http\Controllers\API\RateController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/login', [AuthController::class, 'login'])->name('login');

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
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
    Route::post('/credits/repay', [CreditController::class, 'repay'])->name('credits.repay');

    // Incashes
    Route::apiResource('incashes', IncashController::class)->except(['update']);

    // Rates
    Route::get('/rates/latest', [RateController::class, 'latest'])->name('rates.latest');
    Route::apiResource('rates', RateController::class);
});
