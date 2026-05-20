<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // This app uses token-based API auth (Bearer tokens), not cookie-based SPA auth.
        // Keeping the API stateless avoids CSRF token requirements on /api/auth/* endpoints.
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })
    ->create();
