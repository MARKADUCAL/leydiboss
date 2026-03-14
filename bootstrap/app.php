<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Support\Facades\Auth;
use App\Http\Middleware\RequireCustomerAuth;
use App\Http\Middleware\RequireAdminAuth;
use App\Http\Middleware\EnsureAdminRole;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'customer.auth' => RequireCustomerAuth::class,
            'admin.auth' => RequireAdminAuth::class,
            'admin.role' => EnsureAdminRole::class,
        ]);

        // When an already-logged-in user visits login/register, send them to their dashboard
        $middleware->redirectUsersTo(function ($request) {
            if (Auth::guard('customer')->check()) {
                return route('customer.index');
            }
            if (Auth::guard('admin')->check()) {
                return route('admin.dashboard');
            }
            return url('/');
        });
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
