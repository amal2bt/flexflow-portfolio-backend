<?php

use App\Http\Middleware\AuthenticateApiToken;
use App\Http\Middleware\EnforceCorsAllowList;
use App\Http\Middleware\SecurityHeaders;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // CORS is handled globally by Illuminate\Http\Middleware\HandleCors,
        // which reads configuration from config/cors.php.
        $middleware->alias([
            'auth.apikey' => AuthenticateApiToken::class,
        ]);

        // Conservative security headers on every response (nosniff, DENY
        // framing, no-referrer, restrictive CSP, ...).
        $middleware->append(SecurityHeaders::class);

        // Wraps HandleCors so an untrusted Origin never receives CORS headers
        // (including preflight responses, which HandleCors returns early).
        $middleware->prepend(EnforceCorsAllowList::class);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
