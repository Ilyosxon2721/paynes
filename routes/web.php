<?php

use Illuminate\Support\Facades\Route;

Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!admin|client).*$'); // Exclude /admin and /client from SPA routing
