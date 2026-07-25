<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 'admin' Gate — ginagamit ng ->middleware('can:admin') sa routes/web.php
        // (para lang sa 'ticket-management/admin' na route)
        Gate::define('admin', function ($user) {
            return $user->role === 'admin';
        });
    }
}