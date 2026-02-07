<?php

namespace App\Livewire\Client\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Services\Auth\ClientAuthService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use App\Rules\Turnstile;

#[Layout('components.layouts.auth', [
    'title' => 'Login - Oxbit Access',
    'header' => 'Secure Uplink',
    'pageTitle' => 'Identify'
])]
class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;
    public string $turnstileToken = '';
    public int $secondsRemaining = 0;

    public function render()
    {
        // Pre-check rate limits for UI feedback
        $ipThrottleKey = 'login.ip.' . request()->ip();
        $emailThrottleKey = 'login.email.' . strtolower($this->email);

        if (RateLimiter::tooManyAttempts($ipThrottleKey, 5)) {
            $this->secondsRemaining = RateLimiter::availableIn($ipThrottleKey);
        } elseif (!empty($this->email) && RateLimiter::tooManyAttempts($emailThrottleKey, 5)) {
            $this->secondsRemaining = RateLimiter::availableIn($emailThrottleKey);
        } else {
            $this->secondsRemaining = 0;
        }

        return view('livewire.client.auth.login');
    }

    protected function rules()
    {
        $rules = [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];

        if (!App::environment('local', 'testing')) {
            $rules['turnstileToken'] = ['required', new Turnstile];
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'turnstileToken.required' => 'Please complete the CAPTCHA verification.',
        ];
    }

    public function login()
    {
        $this->turnstileToken = trim($this->turnstileToken);
        
        // 1. Global IP Rate Limit (5 attempts per minute)
        $ipThrottleKey = 'login.ip.' . request()->ip();
        
        // 2. Email Rate Limit (5 attempts per minute per email) - prevents brute forcing a specific account
        $emailThrottleKey = 'login.email.' . strtolower($this->email);

        if (RateLimiter::tooManyAttempts($ipThrottleKey, 5)) {
            $seconds = RateLimiter::availableIn($ipThrottleKey);
            $this->secondsRemaining = $seconds;
            Log::warning("Login IP rate limit exceeded: " . request()->ip());
            $this->addError('email', "Too many requests. Please try again in {$seconds} seconds.");
            return;
        }

        if (RateLimiter::tooManyAttempts($emailThrottleKey, 5)) {
            $seconds = RateLimiter::availableIn($emailThrottleKey);
            $this->secondsRemaining = $seconds;
            Log::warning("Login Email rate limit exceeded: {$this->email}");
            $this->addError('email', "Too many login attempts for this account. Please try again in {$seconds} seconds.");
            return;
        }

        // Explicit server-side check for empty token in production
        // We use !App::environment('local', 'testing') to ensure it runs in production/staging but not tests
        if (!App::environment('local', 'testing') && empty($this->turnstileToken)) {
            $this->addError('turnstileToken', 'Please complete the CAPTCHA verification.');
            $this->dispatch('reset-turnstile');
            return;
        }

        try {
            $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            RateLimiter::hit($ipThrottleKey, 60); // 1 minute decay for validation errors
            RateLimiter::hit($emailThrottleKey, 60);
            
            // ALWAYS reset Turnstile on any validation error.
            // Cloudflare tokens are one-time use. Once sent to the server (even if validation fails elsewhere),
            // the token is consumed/invalidated by Cloudflare.
            // We must force the user to get a new token for the next attempt.
            $this->dispatch('reset-turnstile');
            $this->turnstileToken = ''; // Clear the used token from state
            throw $e;
        }

        $authService = app(ClientAuthService::class);
        
        try {
            $authService->login(
                ['email' => $this->email, 'password' => $this->password],
                $this->remember,
                request()->ip()
            );

            RateLimiter::clear($ipThrottleKey);
            RateLimiter::clear($emailThrottleKey);
            return redirect()->route('dashboard');
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Hit rate limiter on failed login attempt (wrong credentials)
            RateLimiter::hit($ipThrottleKey, 900); // 15 minutes penalty
            RateLimiter::hit($emailThrottleKey, 900);
            $this->dispatch('reset-turnstile'); // Force reset here too
            $this->turnstileToken = ''; 
            $this->addError('email', $e->getMessage());
        } catch (\Exception $e) {
            RateLimiter::hit($ipThrottleKey, 900);
            RateLimiter::hit($emailThrottleKey, 900);
            $this->dispatch('reset-turnstile'); // Force reset here too
            $this->turnstileToken = '';
            Log::error("Login error: " . $e->getMessage());
            $this->addError('email', 'Authentication failed. Please try again.');
        }
    }


}
