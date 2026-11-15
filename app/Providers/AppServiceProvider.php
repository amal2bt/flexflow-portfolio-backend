<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Schema;
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
        // WAMP's MySQL config caps index key length at 1000 bytes, so keep
        // indexed varchar columns (users.email, services.slug, ...) within it.
        Schema::defaultStringLength(191);

        /*
         * Login throttling lives in AuthController::login so the lockout has a
         * strict, fixed 600-second duration and every 429 carries
         * `Retry-After: 600` (Laravel's throttle middleware reports the
         * remainder of a sliding window instead).
         */

        // Throttle public contact submissions per IP to prevent spam/flooding.
        RateLimiter::for('contact', function (Request $request) {
            return [
                Limit::perMinute(5)->by('contact:'.$request->ip()),
                Limit::perHour(30)->by('contact-hour:'.$request->ip()),
            ];
        });
    }
}
