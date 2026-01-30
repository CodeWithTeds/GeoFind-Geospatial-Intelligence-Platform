<?php

namespace Tests\Feature\Client\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ClientRegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful registration redirects to /play.
     */
    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('play'));

        $this->assertDatabaseHas('users', [
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);
    }

    /**
     * Test duplicate email registration fails.
     */
    public function test_users_cannot_register_with_existing_email(): void
    {
        User::factory()->create([
            'email' => 'test@example.com',
        ]);

        $response = $this->post('/register', [
            'name' => 'Another User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors(['email']);
        $this->assertGuest();
    }

    /**
     * Test weak password registration fails.
     */
    public function test_users_cannot_register_with_weak_password(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'pass',
            'password_confirmation' => 'pass',
        ]);

        $response->assertSessionHasErrors(['password']);
        $this->assertGuest();
    }

    /**
     * Test throttling registration attempts.
     */
    public function test_registration_is_throttled(): void
    {
        // Limit is 6 per minute
        for ($i = 0; $i < 6; $i++) {
            $this->post('/register', [
                'name' => 'User ' . $i,
                'email' => "user{$i}@example.com",
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);
        }

        // The 7th attempt should fail
        $response = $this->post('/register', [
            'name' => 'Spammer',
            'email' => 'spammer@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(429);
    }

    /**
     * Test Blacklisted IP cannot register.
     */
    public function test_blacklisted_ip_cannot_register(): void
    {
        Config::set('security.ip_blacklist', ['123.45.67.89']);

        $response = $this->withServerVariables(['REMOTE_ADDR' => '123.45.67.89'])
                         ->post('/register', [
                             'name' => 'Hacker',
                             'email' => 'hacker@example.com',
                             'password' => 'password',
                             'password_confirmation' => 'password',
                         ]);

        $response->assertStatus(403);
    }
}
