<?php

use App\Http\Middleware\ApiTokenExists;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'api.token.exists' => ApiTokenExists::class,
            'guest' => RedirectIfAuthenticated::class,
        ]);

        $middleware->web(append: [
            \Livewire\Features\SupportDisablingBackButtonCache\DisableBackButtonCacheMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();