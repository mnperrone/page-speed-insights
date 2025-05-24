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

// Ruta para obtener el historial de métricas
Route::get('/metrics-history', [PageSpeedController::class, 'getMetricsHistory'])->name('getMetricsHistory');

// Ruta para eliminar una métrica
Route::delete('/metrics-history/{id}', [PageSpeedController::class, 'deleteMetric'])->name('deleteMetric');
