<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\BillingItemController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\SlaController;

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

    // Self-Service Portal
    Route::prefix('self-service')->group(function () {

        Route::get('/', function () {
            return view('selfserviceportal');
        });

        Route::get('/billing-items', [BillingItemController::class, 'index']);
        Route::post('/billing-items', [BillingItemController::class, 'store']);
        Route::patch('/billing-items/{billingItem}/rate', [BillingItemController::class, 'rate']);

        Route::get('/articles', [ArticleController::class, 'index']);
        Route::post('/articles', [ArticleController::class, 'store']);
        Route::patch('/articles/{article}/rate', [ArticleController::class, 'rate']);

    });

    // Ticket Management
    Route::prefix('ticket-management')->group(function () {

        Route::get('/', [TicketController::class, 'index'])->name('ticketmanagement');
        Route::post('/', [TicketController::class, 'store'])->name('tickets.store');
        Route::put('/{ticket}', [TicketController::class, 'update'])->name('tickets.update');
        Route::delete('/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');

    });

    // SLA Tracking
    Route::prefix('sla-tracking')->group(function () {
 
    Route::get('/', [SlaController::class, 'index']);
    Route::post('/rules', [SlaController::class, 'storeRule']);
    Route::put('/rules/{id}', [SlaController::class, 'updateRule']);
    Route::delete('/rules/{id}', [SlaController::class, 'destroyRule']);
    Route::post('/calendar', [SlaController::class, 'updateCalendar']);
 
    });

});