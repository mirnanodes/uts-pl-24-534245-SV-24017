<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\GearController;
use App\Http\Controllers\Api\HealthController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (prefix: /api)
|--------------------------------------------------------------------------
*/

Route::get('/health', HealthController::class)
    ->withoutMiddleware('throttle:api')
    ->name('health');

Route::post('/login', [AuthController::class, 'login'])
    ->middleware('throttle:login')
    ->name('login');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/me', [AuthController::class, 'me'])->name('me');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/gear-options', [GearController::class, 'options'])->name('gears.options');
    Route::apiResource('gears', GearController::class);
});
