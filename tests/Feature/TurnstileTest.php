<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

use App\Models\User;

use RyanChandler\LaravelCloudflareTurnstile\Facades\Turnstile;

class TurnstileTest extends TestCase
{
    use RefreshDatabase;

    public function test_turnstile_validation_passes_with_valid_token()
    {
        $user = User::factory()->create([
            'email' => 'test@example.com',
            'password' => bcrypt('password'),
        ]);

        Turnstile::fake();

        $response = $this->post('/admin/login', [
            'email' => 'test@example.com',
            'password' => 'password',
            'cf-turnstile-response' => Turnstile::dummy(),
        ]);

        $response->assertSessionHasNoErrors();
        $response->assertRedirect(route('admin.dashboard'));
    }

    public function test_turnstile_validation_fails_with_invalid_token()
    {
        Turnstile::fake()->fail();

        $response = $this->post('/admin/login', [
            'email' => 'test@example.com',
            'password' => 'password',
            'cf-turnstile-response' => 'invalid-token',
        ]);

        $response->assertSessionHasErrors(['cf-turnstile-response']);
    }
}
