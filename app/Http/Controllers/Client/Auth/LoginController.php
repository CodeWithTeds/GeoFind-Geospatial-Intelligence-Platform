<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ClientLoginRequest;
use App\Services\Auth\ClientAuthService;
use App\Http\Middleware\IpControlMiddleware;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class LoginController extends Controller implements HasMiddleware
{
    public function __construct(
        protected ClientAuthService $authService
    ) {}

    /**
     * Get the middleware that should be assigned to the controller.
     */
    public static function middleware(): array
    {
        return [
            new Middleware('guest', only: ['create', 'store']), // Apply guest only to login page/action
            // Custom IP Control Layer
            new Middleware(IpControlMiddleware::class),
            // Rate Limiting (Throttle) - defined in AppServiceProvider
            new Middleware('throttle:login', only: ['store']),
        ];
    }

    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('client.auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(ClientLoginRequest $request): RedirectResponse
    {
        $this->authService->login(
            $request->only(['email', 'password']),
            $request->boolean('remember'),
            $request->ip()
        );

        // Redirect to the dashboard after successful login
        return redirect()->route('dashboard');
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(): RedirectResponse
    {
        $this->authService->logout();

        return redirect('/');
    }
}
