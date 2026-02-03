<?php

namespace App\Livewire\Client\Auth;

use Livewire\Component;
use App\Services\Auth\ClientAuthService;
use Illuminate\Support\Facades\App;
use Illuminate\Validation\Rules\Password;
use App\Rules\Turnstile;

class Register extends Component
{
    public string $name = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';
    public string $turnstileToken = '';

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
        try {
            $validatedData = $this->validate();
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->dispatch('reset-turnstile');
            throw $e;
        }

        // Resolve service from container manually or use method injection if supported by Livewire version
        $authService = app(ClientAuthService::class);

        $authService->register(
            $validatedData,
            request()->ip()
        );

        return redirect()->route('dashboard');
    }

    public function render()
    {
        return view('livewire.client.auth.register')
            ->layout('components.layouts.auth', [
                'title' => 'Register - Oxbit Access',
                'header' => 'New Connection',
                'pageTitle' => 'Initialize'
            ]);
    }
}
