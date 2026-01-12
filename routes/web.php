<?php

use Illuminate\Support\Facades\Route;

Route::get('/{any}', function () {
    return view('app');
})->where('any', '^(?!admin|cabinet).*$'); // Exclude /admin and /cabinet from SPA routing
