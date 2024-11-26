<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\PageSpeedController;

Route::post('/get-metrics', [PageSpeedController::class, 'getMetrics'])->name('getMetrics');
Route::get('/pagespeed', function () {
    return view('pagespeed');
});
