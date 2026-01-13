<?php

use Illuminate\Support\Facades\Route;

Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!admin|merchant).*$'); // Exclude /admin and /merchant from SPA routing
