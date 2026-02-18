<?php

namespace App\Http\Controllers\Client\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\ClientRegisterRequest;
use App\Services\Auth\ClientAuthService;
use App\Http\Middleware\IpControlMiddleware;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class RegisterController extends Controller implements HasMiddleware
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
            'guest',
            new Middleware(IpControlMiddleware::class),
            new Middleware('throttle:register', only: ['store']), 
        ];
    }

    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('client.auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(ClientRegisterRequest $request): RedirectResponse
    {
        $this->authService->register(
            $request->validated(),
            $request->ip()
        );

        return redirect()->intended(route('dashboard'));
    }
}
