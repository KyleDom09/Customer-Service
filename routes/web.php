<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AgentController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\BillingItemController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\SlaController;
use App\Http\Controllers\CommunicationController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\RefundRequestController;

Route::get('/', function () {
    return view('welcome');
});

// ===================== Auth =====================
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/signup', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/signup', [RegisterController::class, 'register']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
// ==================================================

Route::prefix('customer-service')->middleware('auth')->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

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

        Route::get('/refund-requests', [RefundRequestController::class, 'index']);
        Route::post('/refund-requests', [RefundRequestController::class, 'store']);


    });

    // Ticket Management (User / Agent-facing view)
    Route::prefix('ticket-management')->group(function () {

        Route::get('/', [TicketController::class, 'index'])->name('ticketmanagement');
        Route::get('/analytics', [TicketController::class, 'analytics'])->name('tickets.analytics');
        Route::post('/', [TicketController::class, 'store'])->name('tickets.store');
        Route::put('/{ticket}', [TicketController::class, 'update'])->name('tickets.update');
        Route::delete('/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');

        // Admin View (full edit access: status, priority, category, assigned agent)
        // Requires an 'admin' Gate/ability to be defined, e.g. in AuthServiceProvider:
        //   Gate::define('admin', fn ($user) => $user->role === 'admin');
        Route::get('/admin', [TicketController::class, 'adminIndex'])
            ->name('tickets.admin')
            ->middleware('can:admin');

        // Optional: dedicated endpoint for admins to reply/patch a ticket's chat + fields
        // Route::post('/admin/{ticket}/reply', [TicketController::class, 'adminReply'])
        //     ->name('tickets.admin.reply')
        //     ->middleware('can:admin');

    });

    // SLA Tracking
    Route::prefix('sla-tracking')->group(function () {

        Route::get('/', [SlaController::class, 'index']);
        Route::post('/rules', [SlaController::class, 'storeRule']);
        Route::put('/rules/{id}', [SlaController::class, 'updateRule']);
        Route::delete('/rules/{id}', [SlaController::class, 'destroyRule']);
        Route::post('/calendar', [SlaController::class, 'updateCalendar']);

    });

    // Communication History
    Route::get('/communication-history', [CommunicationController::class, 'index'])
        ->name('communication.index');

    Route::post('/communication-history/store', [CommunicationController::class, 'store'])
        ->name('communication.store');

    Route::put('/communication-history/{id}', [CommunicationController::class, 'update'])
        ->name('communication.update');

    Route::get('/dashboard-history', [CommunicationController::class, 'dashboardHistory'])
        ->name('dashboard.history');


});