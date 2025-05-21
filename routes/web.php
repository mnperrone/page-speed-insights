<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PageSpeedController;

// Ruta para mostrar el formulario
Route::get('/pagespeed', function () {
    return view('pagespeed');
})->name('pagespeed');

// Ruta para obtener métricas
Route::post('/get-metrics', [PageSpeedController::class, 'getMetrics'])->name('getMetrics');

// Ruta para guardar resultados
Route::post('/save-results', [PageSpeedController::class, 'saveResults'])->name('saveResults');
