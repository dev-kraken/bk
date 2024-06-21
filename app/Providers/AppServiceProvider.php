<?php

namespace App\Providers;

use App\Services\AuthService;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton('authFacade', function () {
            return new AuthService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
//        RateLimiter::for('profile', static function ($request) {
//            return Limit::perMinute(2)->by('profile');
//        });
    }

}
