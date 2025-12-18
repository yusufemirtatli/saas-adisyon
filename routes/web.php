<?php

use App\Http\Controllers\KitchenController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Kitchen API Routes
Route::prefix('api/kitchen')->middleware(['web'])->group(function () {
    Route::post('/start-preparing/{shopcartId}', [KitchenController::class, 'startPreparing']);
    Route::post('/mark-ready/{shopcartId}', [KitchenController::class, 'markReady']);
    Route::post('/cancel/{shopcartId}', [KitchenController::class, 'cancelOrder']);
});
