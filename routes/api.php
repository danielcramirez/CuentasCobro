<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\DashboardApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API routes for dashboard
Route::middleware(['auth'])->prefix('dashboard')->group(function () {
    Route::get('/data', [DashboardApiController::class, 'getDashboardData'])->name('api.dashboard.data');
    Route::get('/activity', [DashboardApiController::class, 'getRecentActivity'])->name('api.dashboard.activity');
});
