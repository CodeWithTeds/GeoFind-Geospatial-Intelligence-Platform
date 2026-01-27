<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LoginThrottlingTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_attempts_are_throttled()
    {
        // Make 5 requests (the limit)
        for ($i = 0; $i < 5; $i++) {
            $response = $this->post('/admin/login', [
                'email' => 'admin@example.com',
                'password' => 'password',
            ]);
            
            // Should be a validation error (redirect) or success, but definitely not 429
            $this->assertNotEquals(429, $response->status());
        }

        // Make the 6th request
        $response = $this->post('/admin/login', [
            'email' => 'admin@example.com',
            'password' => 'password',
        ]);

        // Should be throttled
        $response->assertStatus(429);
    }

    public function test_api_attempts_are_throttled()
    {
        // API limit is 60 per minute
        for ($i = 0; $i < 60; $i++) {
            $this->getJson('/api/questions');
        }

        // 61st request should fail
        $response = $this->getJson('/api/questions');
        $response->assertStatus(429);
    }
}
