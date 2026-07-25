<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withBroadcasting(
        __DIR__.'/../routes/channels.php',
        ['middleware' => ['auth']],
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.guest' => \App\Http\Middleware\RedirectIfGuest::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'account.active' => \App\Http\Middleware\EnsureAccountActive::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        $middleware->redirectGuestsTo(
            fn (Request $request) => route('home')
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
