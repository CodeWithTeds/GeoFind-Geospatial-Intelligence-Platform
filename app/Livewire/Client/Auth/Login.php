<?php

namespace App\Livewire\Client\Auth;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Services\Auth\ClientAuthService;
use Illuminate\Support\Facades\App;
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
        $this->validate();

        $authService = app(ClientAuthService::class);

        // We need to pass data in the format expected by the service
        // The service expects array $credentials, bool $remember, string $ip
        
        try {
            $authService->login(
                ['email' => $this->email, 'password' => $this->password],
                $this->remember,
                request()->ip()
            );

            return redirect()->intended(route('play'));
        } catch (\Illuminate\Validation\ValidationException $e) {
            $this->addError('email', $e->getMessage());
        } catch (\Exception $e) {
            $this->addError('email', 'Authentication failed. Please try again.');
        }
    }

    public function render()
    {
        return view('livewire.client.auth.login');
    }
}
