<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\RefundRequestController;

// API 1: Papuntang Sales and Customer Support (Group 3)
Route::get('/refund-requests/sales', [RefundRequestController::class, 'salesApi']);

// API 2: Papuntang Financing and Accounting (Group 7)
Route::get('/tickets/billing/finance', [TicketController::class, 'financeBillingApi']);