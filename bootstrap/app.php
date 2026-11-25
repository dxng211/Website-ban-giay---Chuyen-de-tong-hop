<?php

use App\Http\Middleware\EnsureAccountNotLocked;
use App\Http\Middleware\EnsureUserHasRole;
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
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'role' => EnsureUserHasRole::class,
            'not.locked' => EnsureAccountNotLocked::class,
        ]);

        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->expectsJson()) {
                return null;
            }

            if ($request->is('admin') || $request->is('admin/*')) {
                return route('admin.login');
            }

            return route('account');
        });

        $middleware->redirectUsersTo(function (Request $request) {
            $user = $request->user();
            $role = $user && is_object($user->role) ? $user->role->value : ($user->role ?? 'customer');

            return $role === 'admin'
                ? route('admin.dashboard')
                : route('account');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();