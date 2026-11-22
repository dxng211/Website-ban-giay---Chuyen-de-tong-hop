<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

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

        // Guest vào trang cần đăng nhập -> về trang Tài khoản; riêng khu vực /admin/* -> về trang đăng nhập admin
        $middleware->redirectGuestsTo(fn (Request $request) =>
            $request->is('admin', 'admin/*') ? route('admin.login') : '/account'
        );

        // Đã đăng nhập mà vào route chỉ dành cho Guest (POST /login, POST /register, /admin/login):
        // Admin -> dashboard admin; Customer -> /account (không bị 403 khi thử vào /admin/login)
        $middleware->redirectUsersTo(fn (Request $request) =>
            $request->user()?->isAdmin() ? route('admin.dashboard') : '/account'
        );
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();