<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Http\Request;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Public API read-only endpoints
        RateLimiter::for(
            'api-public',
            fn(Request $r) =>
            Limit::perMinute(60)->by($r->ip())
        );

        // Authed API user-specific writes/reads
        RateLimiter::for(
            'api-auth',
            fn(Request $r) =>
            Limit::perMinute(30)->by(optional($r->user())->id ?: $r->ip())
        );
    }       
}
