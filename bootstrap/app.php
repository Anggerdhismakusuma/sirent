<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

// ── Fix SSL CA certificates on Windows / XAMPP ──
//     Without this, cURL cannot verify SSL peers and all HTTPS API calls
//     (Midtrans, mail, etc.) fail with "unable to get local issuer certificate".
if (PHP_OS_FAMILY === 'Windows') {
    $cafile = ini_get('curl.cainfo')
        ?: ini_get('openssl.cafile')
        ?: 'C:\\xampp\\php\\extras\\ssl\\cacert.pem';

    if ($cafile && file_exists($cafile)) {
        ini_set('curl.cainfo', $cafile);
        ini_set('openssl.cafile', $cafile);
    }
}
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
        // Trust Railway's proxy/load-balancer so that X-Forwarded-Proto / X-Forwarded-For
        // headers are respected. Without this, the app sees HTTP requests behind the proxy,
        // which breaks signed URL validation (the URL is generated with HTTPS but the
        // request arrives as HTTP → signature mismatch → 403).
        $middleware->trustProxies(at: '*');

        $middleware->alias([
            'auth.guest' => \App\Http\Middleware\RedirectIfGuest::class,
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'account.active' => \App\Http\Middleware\EnsureAccountActive::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);

        // Exclude Midtrans webhook from CSRF protection
        $middleware->validateCsrfTokens(except: [
            'api/midtrans/webhook',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
