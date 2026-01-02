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
| API V1 Routes
|--------------------------------------------------------------------------
|
| Version 1 of the API - Current stable version
|
*/

// Public routes
Route::post('/login', [AuthController::class, 'login'])->name('v1.login');

// Protected routes with rate limiting
Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
    // Auth
    Route::post('/logout', [AuthController::class, 'logout'])->name('v1.logout');
    Route::get('/user', [AuthController::class, 'user'])->name('v1.user');

    // Payments
    Route::apiResource('payments', PaymentController::class);
    Route::post('/payments/{id}/confirm', [PaymentController::class, 'confirm'])->name('v1.payments.confirm');
    Route::post('/payments/{id}/duplicate', [PaymentController::class, 'duplicate'])->name('v1.payments.duplicate');

    // Payment Types
    Route::apiResource('payment-types', PaymentTypeController::class);

    // Exchanges
    Route::apiResource('exchanges', ExchangeController::class)->except(['update']);

    // Credits
    Route::apiResource('credits', CreditController::class);
    Route::post('/credits/{id}/confirm', [CreditController::class, 'confirm'])->name('v1.credits.confirm');
    Route::post('/credits/repay', [CreditController::class, 'repay'])->name('v1.credits.repay');

    // Incashes
    Route::apiResource('incashes', IncashController::class)->except(['update']);

    // Rates
    Route::get('/rates/latest', [RateController::class, 'latest'])->name('v1.rates.latest');
    Route::apiResource('rates', RateController::class);
});
