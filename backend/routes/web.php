<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return response()->json([
        'app' => config('app.name'),
        'message' => 'SummitGear API. Gunakan endpoint di bawah /api.',
        'health' => url('/api/health'),
    ]);
});
