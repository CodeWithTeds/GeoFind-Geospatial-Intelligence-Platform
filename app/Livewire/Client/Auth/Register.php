<?php

namespace App\Livewire\Client\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Services\Auth\ClientAuthService;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules\Password;
use App\Rules\Turnstile;

#[Layout('components.layouts.auth', [
    'title' => 'Register - Oxbit Access',
    'header' => 'New Connection',
    'pageTitle' => 'Initialize'
])]
class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $turnstileToken = '';
    public int $secondsRemaining = 0;

    public function render()
    {
        // Pre-check rate limits for UI feedback
        $throttleKey = 'register.ip.' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $this->secondsRemaining = RateLimiter::availableIn($throttleKey);
        } else {
            $this->secondsRemaining = 0;
        }

        return view('livewire.client.auth.register');
    }

    public function updated($propertyName)
    {
        // Skip validation for Turnstile token to prevent consuming the one-time token
        // Validation will happen on form submission
        if ($propertyName === 'turnstileToken') {
            return;
        }

        if ($propertyName === 'password') {
            // If confirmation is empty, skip 'confirmed' check to avoid premature error
            if (empty($this->password_confirmation)) {
                $this->validateOnly($propertyName, [
                    'password' => ['required', 'min:10', 'max:20', Password::defaults()],
                ]);
                return;
            }
        }

        if ($propertyName === 'password_confirmation') {
            // When confirmation changes, re-validate password to check 'confirmed' status
            $this->validateOnly('password');
            return;
        }

        $this->validateOnly($propertyName);
    }

    protected function rules()
    {
        $rules = [
            'name' => ['required', 'string', 'min:10', 'max:20', 'regex:/^\S*$/u'], // No spaces allowed
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users',
                'regex:/^[a-zA-Z0-9._%+-]+@gmail\.com$/i' // Gmail validation
            ],
            'password' => ['required', 'confirmed', 'min:10', 'max:20', Password::defaults()],
        ];

        // Apply Turnstile only in production/non-local environments
        if (!App::environment('local', 'testing')) {
            $rules['turnstileToken'] = ['required', new Turnstile];
        }

        return $rules;
    }

    protected function messages()
    {
        return [
            'name.regex' => 'The codename cannot contain spaces.',
            'email.regex' => 'Only Gmail addresses (@gmail.com) are authorized for access.',
            'turnstileToken.required' => 'Please complete the CAPTCHA verification.',
        ];
    }

    public function register()
    {
        $this->turnstileToken = trim($this->turnstileToken);
        
        // Rate limit by IP (5 attempts per minute)
        $throttleKey = 'register.ip.' . request()->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            $this->secondsRemaining = $seconds;
            Log::warning("Registration rate limit exceeded for IP: " . request()->ip());
            $this->addError('email', "Too many attempts. Please try again in {$seconds} seconds.");
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
            $validatedData = $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            RateLimiter::hit($throttleKey, 60); // 1 minute decay for validation errors
            $this->dispatch('reset-turnstile');
            throw $e;
        }

        try {
            // Resolve service from container manually or use method injection if supported by Livewire version
            $authService = app(ClientAuthService::class);

            $authService->register(
                $validatedData,
                request()->ip()
            );

            RateLimiter::clear($throttleKey);
            return redirect()->route('dashboard');
        } catch (\Exception $e) {
            RateLimiter::hit($throttleKey, 900); // 15 minutes penalty for failed registration
            Log::error("Registration error: " . $e->getMessage());
            $this->addError('email', 'Registration failed. Please try again.');
        }
    }


}
