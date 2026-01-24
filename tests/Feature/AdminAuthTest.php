<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\RateLimiter;
use Tests\TestCase;

class AdminAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Clear rate limiter before each test
        RateLimiter::clear('login:'.request()->ip());
    }

    public function test_admin_can_login_with_valid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password', // Factory hashes this
        ]);

        $response = $this->post(route('admin.login.store'), [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response->assertRedirect(route('admin.dashboard'));
        $this->assertAuthenticatedAs($user);
    }

    public function test_admin_cannot_login_with_invalid_credentials()
    {
        $user = User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        $response = $this->post(route('admin.login.store'), [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_login_is_rate_limited()
    {
        // Make 5 failed attempts (the limit)
        for ($i = 0; $i < 5; $i++) {
            $this->post(route('admin.login.store'), [
                'email' => 'admin@example.com',
                'password' => 'wrong-password',
            ]);
        }

        // The 6th attempt should be blocked
        $response = $this->post(route('admin.login.store'), [
            'email' => 'admin@example.com',
            'password' => 'wrong-password',
        ]);

        // Laravel's throttle middleware returns 429 Too Many Requests
        $response->assertStatus(429);
    }

    public function test_sql_injection_attempt_is_neutralized()
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        // Attempt SQL injection in email field
        // If vulnerable, this might log in as the first user or throw a DB error
        // Eloquent/PDO should treat this as a literal string
        $response = $this->post(route('admin.login.store'), [
            'email' => "' OR '1'='1",
            'password' => 'password',
        ]);

        // Should fail validation (email format) or authentication
        // Since ' OR '1'='1 is not a valid email format, Laravel's email validation might catch it first.
        // Let's try a valid email format that contains SQL injection syntax if we bypass email validation,
        // but since we have email validation, that's layer 1.
        // Let's assume the attacker tries to inject via password or a raw query.
        // The most important thing is that it doesn't log us in.
        
        $this->assertGuest();
        $response->assertSessionHasErrors(['email']);
    }
    
    public function test_sql_injection_in_password_field()
    {
        User::factory()->create([
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        // Attempt injection in password
        $response = $this->post(route('admin.login.store'), [
            'email' => 'admin@example.com',
            'password' => "' OR '1'='1",
        ]);

        // Should simply fail authentication because the hash won't match
        $this->assertGuest();
        $response->assertSessionHasErrors('email');
    }
}
