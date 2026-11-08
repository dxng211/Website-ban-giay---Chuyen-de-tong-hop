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
    $middleware->alias([
            'role'       => \App\Http\Middleware\EnsureUserHasRole::class,
            'not.locked' => \App\Http\Middleware\EnsureAccountNotLocked::class,
        ]);

        $middleware->redirectGuestsTo('/account');  // Guest vào trang cần đăng nhập -> về trang Tài khoản
        $middleware->redirectUsersTo('/account');   // Đã đăng nhập mà vào route chỉ dành cho Guest (login/register)
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
