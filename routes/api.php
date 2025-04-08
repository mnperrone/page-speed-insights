<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Http\Controllers\PageSpeedController;

Route::post('/getMetrics', [PageSpeedController::class, 'getMetrics']);

