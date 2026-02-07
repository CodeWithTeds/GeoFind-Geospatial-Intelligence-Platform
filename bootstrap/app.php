<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Append Security Headers Middleware
        $middleware->append(\App\Http\Middleware\SecurityHeadersMiddleware::class);

        // Security: Do not trust all proxies in production unless behind a specific load balancer.
        // $middleware->trustProxies(at: '*'); 
        $middleware->redirectGuestsTo(function (Request $request) {
            if ($request->is('admin*')) {
                return route('admin.login');
            }
            return route('login');
        });

        // Redirect authenticated users
        $middleware->redirectTo(
            guests: '/login',
            users: function (Request $request) {
                if ($request->user() && $request->user()->role === 'admin') {
                    return route('admin.dashboard');
                }
                return route('dashboard');
            }
        );
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
