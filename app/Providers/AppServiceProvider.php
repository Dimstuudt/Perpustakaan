<?php

namespace App\Providers;

use Anhskohbo\NoCaptcha\NoCaptcha;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use Inertia\Inertia;

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
        // Validasi captcha
        Validator::extend('captcha', function ($attribute, $value, $parameters, $validator) {
            return app(NoCaptcha::class)->verifyResponse($value);
        });

        // Super Admin bisa semua ability
        Gate::before(function ($user, $ability) {
            return $user->hasRole('Super Admin') ? true : null;
        });

        // 🔥 Share flash ke Inertia
        Inertia::share([
            'flash' => function () {
                return [
                    'message' => session('message'),
                    'error'   => session('error'),
                ];
            },

            // 🔥 Share auth user ke semua Inertia views
            'auth' => function () {
                return [
                    'user' => auth()->check() ? [
                        'id' => auth()->user()->id,
                        'name' => auth()->user()->name,
                        'email' => auth()->user()->email,
                        'role' => auth()->user()->role ?? 'user',
                        'avatar' => auth()->user()->avatar ?? null,
                    ] : null,
                ];
            },
        ]);
    }
}
