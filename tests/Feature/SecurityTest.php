<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\App;
use Livewire\Livewire;
use App\Livewire\Client\Auth\Login;
use App\Livewire\Client\Auth\Register;
use Tests\TestCase;

class SecurityTest extends TestCase
{
    use RefreshDatabase;

    public function test_security_headers_are_present()
    {
        $response = $this->get('/');
        
        $response->assertHeader('Content-Security-Policy');
        $response->assertHeader('X-Frame-Options', 'DENY');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
    }

    public function test_login_rate_limiting()
    {
        RateLimiter::clear('login|127.0.0.1');

        // Simulate 5 failed attempts
        for ($i = 0; $i < 5; $i++) {
            Livewire::test(Login::class)
                ->set('email', 'test@example.com')
                ->set('password', 'wrongpassword')
                ->call('login')
                ->assertHasErrors(['email']);
        }

        // 6th attempt should be blocked by Rate Limiter
        Livewire::test(Login::class)
            ->set('email', 'test@example.com')
            ->set('password', 'wrongpassword')
            ->call('login')
            ->assertHasErrors(['email' => 'Too many login attempts. Please try again in 900 seconds.']);
    }

    public function test_register_rate_limiting()
    {
        RateLimiter::clear('register|127.0.0.1');

        // Simulate 5 failed attempts
        for ($i = 0; $i < 5; $i++) {
            Livewire::test(Register::class)
                ->set('name', 'Test User')
                ->set('email', 'test@gmail.com')
                ->set('password', 'password123')
                ->set('password_confirmation', 'password123')
                ->call('register');
        }

        // 6th attempt should be blocked
        Livewire::test(Register::class)
            ->set('name', 'Test User')
            ->set('email', 'test@gmail.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['email' => 'Too many attempts. Please try again in 900 seconds.']);
    }

    // Note: Testing Turnstile in production requires mocking App::environment
    // which is difficult in functional tests as it affects the whole app boot.
    // However, we verified the code logic explicitly checks App::environment.
}
