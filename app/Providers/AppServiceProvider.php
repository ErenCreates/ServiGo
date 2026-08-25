<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Auth\Middleware\RedirectIfAuthenticated;
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
        RedirectIfAuthenticated::redirectUsing(function ($request) {
            $user = $request->user();

            if ($user instanceof User) {
                return route($user->homeRoute());
            }

            return route('customer.dashboard');
        });
    }
}
