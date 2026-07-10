<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 1. Mendaftarkan Alias Middleware
        $middleware->alias([
            'admin' => \App\Http\Middleware\IsAdmin::class,
        ]);

        // 2. Bypass CSRF Token Check untuk Endpoint Tertentu (Midtrans Callback)
        // 2. Bypass CSRF Token Check untuk Endpoint Tertentu (Midtrans Callback)
$middleware->validateCsrfTokens(except: [
    'midtrans/callback',   /* Hapus tanda / di depan */
    'api/midtrans/callback' /* Tambahkan ini juga sebagai cadangan jika rutenya lewat api.php */
]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();