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
use App\Http\Controllers\SelfServiceController;

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

// ============== Regular User (Customer) Area ==============
// Landing page after login for anyone with role = 'user'. Shows their own
// tickets + own communication history, and lets them file a new concern.
// Protected by 'auth' only — NOT 'can:admin' — any logged-in account can
// see their OWN data here (that's the whole point of this view).
Route::middleware('auth')->group(function () {
    Route::get('/my-dashboard', [TicketController::class, 'userDashboard'])->name('user.home');
    Route::patch('/tickets/{ticket}/cancel', [TicketController::class, 'cancel'])->name('tickets.cancel');
    Route::post('/tickets/mine', [TicketController::class, 'store'])->name('tickets.storeUser');
});
// ============================================================

Route::middleware('auth')->group(function () {
    Route::get('/tickets/{ticket}/messages', [TicketController::class, 'messages']);
    Route::post('/tickets/{ticket}/messages', [TicketController::class, 'sendMessage']);
});

Route::prefix('customer-service')->middleware('auth')->group(function () {

    // ---------------------------------------------------------------
    // ADMIN-ONLY CORE (Dashboard, Agents, Activity Logs, Notifications)
    // These have no user-facing equivalent, so the whole block is
    // locked behind the 'admin' Gate.
    // ---------------------------------------------------------------
    Route::middleware('can:admin')->group(function () {

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

        // Agents
        Route::get('/agents', [AgentController::class, 'index']);
        Route::post('/agents', [AgentController::class, 'store'])->name('agents.store');
        Route::put('/agents/{agent}', [AgentController::class, 'update']);
        Route::patch('/agents/{agent}/rating', [AgentController::class, 'updateRating'])->name('agents.updateRating');
        Route::delete('/agents/{agent}', [AgentController::class, 'destroy']);

        // Activity Logs
        Route::get('/logs/{filter?}', [ActivityLogController::class, 'index']);
        Route::post('/logs', [ActivityLogController::class, 'store']);
        Route::put('/logs/{activityLog}', [ActivityLogController::class, 'update']);
        Route::delete('/logs/{activityLog}', [ActivityLogController::class, 'destroy']);

        // Notifications
        Route::post('/notifications/mark-read', [ActivityLogController::class, 'markAllRead']);

    });

    // ---------------------------------------------------------------
    Route::prefix('self-service')->group(function () {

        // Iisang URL na lang — controller ang magde-decide kung admin o user view
        Route::get('/', [SelfServiceController::class, 'index'])->name('self-service.index');

        Route::get('/billing-items', [BillingItemController::class, 'index']);
        Route::post('/billing-items', [BillingItemController::class, 'store'])->middleware('can:admin');
        Route::patch('/billing-items/{billingItem}/rate', [BillingItemController::class, 'rate']);
        Route::put('/billing-items/{billingItem}', [BillingItemController::class, 'update'])->middleware('can:admin');
        Route::delete('/billing-items/{billingItem}', [BillingItemController::class, 'destroy'])->middleware('can:admin');

        Route::get('/articles', [ArticleController::class, 'index']);
        Route::post('/articles', [ArticleController::class, 'store'])->middleware('can:admin');
        Route::patch('/articles/{article}/rate', [ArticleController::class, 'rate']);
        Route::put('/articles/{article}', [ArticleController::class, 'update'])->middleware('can:admin');
        Route::delete('/articles/{article}', [ArticleController::class, 'destroy'])->middleware('can:admin');

        // Iisang URL din para sa refund requests — data returned depende sa role
        Route::get('/refund-requests', [RefundRequestController::class, 'index']);
        Route::post('/refund-requests', [RefundRequestController::class, 'store']);
        Route::delete('/refund-requests/{refundRequest}', [RefundRequestController::class, 'destroy']);

        // Approve/reject — admin-only actions, pero hindi na kailangan ng separate "admin" URL
        // dahil buttons na lang ito na visible sa admin view
        Route::middleware('can:admin')->group(function () {
            Route::patch('/refund-requests/{refundRequest}/approve', [RefundRequestController::class, 'approve']);
            Route::patch('/refund-requests/{refundRequest}/reject', [RefundRequestController::class, 'reject']);
            Route::delete('/refund-requests/{refundRequest}/admin', [RefundRequestController::class, 'adminDestroy']);
        });

    });

    // ---------------------------------------------------------------
    // Ticket Management — Agent/Admin-facing view ONLY.
    // Customers now file + view their own tickets through /my-dashboard
    // instead, so the old duplicate '/admin' full-edit route has been
    // removed — this single route IS the admin/agent view, protected
    // by the 'admin' Gate.
    // ---------------------------------------------------------------
    Route::prefix('ticket-management')->middleware('can:admin')->group(function () {

        Route::get('/', [TicketController::class, 'adminIndex'])->name('ticketmanagement');
        Route::get('/analytics', [TicketController::class, 'analytics'])->name('tickets.analytics');
        Route::post('/', [TicketController::class, 'store'])->name('tickets.store');
        Route::put('/{ticket}', [TicketController::class, 'update'])->name('tickets.update');
        Route::delete('/{ticket}', [TicketController::class, 'destroy'])->name('tickets.destroy');

    });

    // ---------------------------------------------------------------
    // SLA Tracking — admin/agent-only, no customer-facing equivalent.
    // ---------------------------------------------------------------
    Route::prefix('sla-tracking')->middleware('can:admin')->group(function () {

        Route::get('/', [SlaController::class, 'index']);
        Route::post('/rules', [SlaController::class, 'storeRule']);
        Route::put('/rules/{id}', [SlaController::class, 'updateRule']);
        Route::delete('/rules/{id}', [SlaController::class, 'destroyRule']);
        Route::post('/calendar', [SlaController::class, 'updateCalendar']);

    });

    // ---------------------------------------------------------------
    // Communication History — admin/agent-only full view (all customers'
    // records). Customers see only their OWN communication history
    // through /my-dashboard, which queries the data directly instead
    // of going through this route.
    // ---------------------------------------------------------------
    Route::middleware('can:admin')->group(function () {

        Route::get('/communication-history', [CommunicationController::class, 'index'])
            ->name('communication.index');

        Route::post('/communication-history/store', [CommunicationController::class, 'store'])
            ->name('communication.store');

        Route::put('/communication-history/{id}', [CommunicationController::class, 'update'])
            ->name('communication.update');

        Route::get('/dashboard-history', [CommunicationController::class, 'dashboardHistory'])
            ->name('dashboard.history');

    });

});
