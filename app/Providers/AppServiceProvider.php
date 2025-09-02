<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Support\CartManager;


class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(CartManager::class, fn() => new CartManager());
    }

    public function boot(): void
    {
        // Admin-only gate
        Gate::define('admin-only', fn(User $user) => ($user->role ?? 'customer') === 'admin');
    }
}
