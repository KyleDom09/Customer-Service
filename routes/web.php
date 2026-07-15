<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\ActivityLogController;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('customer-service')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Agents
    Route::get('/agents', [AgentController::class, 'index']);
    Route::post('/agents', [AgentController::class, 'store'])->name('agents.store');
    Route::put('/agents/{agent}', [AgentController::class, 'update']);
    Route::delete('/agents/{agent}', [AgentController::class, 'destroy']);

    // Activity Logs
    Route::get('/logs/{filter?}', [ActivityLogController::class, 'index']);
    Route::post('/logs', [ActivityLogController::class, 'store']);
    Route::put('/logs/{activityLog}', [ActivityLogController::class, 'update']);
    Route::delete('/logs/{activityLog}', [ActivityLogController::class, 'destroy']);

    // Notifications
    Route::post('/notifications/mark-read', [ActivityLogController::class, 'markAllRead']);

});