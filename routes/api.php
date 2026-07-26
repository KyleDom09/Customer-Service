<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\RefundRequestController;

Route::get('/tickets/billing', [TicketController::class, 'billingApi']);
Route::get('/refund-requests/finance', [RefundRequestController::class, 'financeApi']);