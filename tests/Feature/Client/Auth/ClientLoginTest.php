<?php

namespace Tests\Feature\Client\Auth;

use App\Livewire\Client\Auth\Login;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;
use Tests\TestCase;

class ClientLoginTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test successful login redirects to /play.
     */
    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        $component = Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'password')
            ->call('login');
            
        if ($component->errors()->isNotEmpty()) {
             dump($component->errors());
        }

        $component->assertHasNoErrors()
            ->assertRedirect(route('play'));

        $this->assertAuthenticatedAs($user);
    }

    /**
     * Test failed login does not authenticate.
     */
    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create([
            'password' => bcrypt('password'),
        ]);

        Livewire::test(Login::class)
            ->set('email', $user->email)
            ->set('password', 'wrong-password')
            ->call('login')
            ->assertHasErrors(['email']);

        $this->assertGuest();
    }

    /**
     * Test IP Control Middleware blocks blacklisted IPs.
     * This is a middleware test, so it should still use HTTP request to the route.
     */
    public function test_blacklisted_ip_cannot_access_login(): void
    {
        // Mock the config to blacklist a specific IP
        Config::set('security.ip_blacklist', ['123.45.67.89']);

        // Simulate request from that IP
        $response = $this->withServerVariables(['REMOTE_ADDR' => '123.45.67.89'])
            ->get('/login');

        $response->assertStatus(403);
    }

    /**
     * Test Unauthenticated users are redirected from /play to /login.
     */
    public function test_unauthenticated_users_redirected_from_play(): void
    {
        $response = $this->get('/play');
        $response->assertRedirect(route('login'));
    }
}
