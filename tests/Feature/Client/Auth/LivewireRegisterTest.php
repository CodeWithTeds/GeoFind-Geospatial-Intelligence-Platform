<?php

namespace Tests\Feature\Client\Auth;

use App\Livewire\Client\Auth\Register;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class LivewireRegisterTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function can_register_with_valid_gmail_and_no_spaces_in_name()
    {
        $component = Livewire::test(Register::class)
            ->set('name', 'ValidUser123')
            ->set('email', 'test@gmail.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register');
            
        if ($component->errors()->isNotEmpty()) {
            dump($component->errors());
        }

        $component->assertHasNoErrors(); // Ensure no validation errors

        $this->assertDatabaseHas('users', [
            'email' => 'test@gmail.com',
        ]);
    }

    /** @test */
    public function name_cannot_contain_spaces()
    {
        Livewire::test(Register::class)
            ->set('name', 'Invalid User')
            ->set('email', 'test@gmail.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['name']);
    }

    /** @test */
    public function name_must_be_between_10_and_20_chars()
    {
        // Too short
        Livewire::test(Register::class)
            ->set('name', 'ShortUser')
            ->set('email', 'test@gmail.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['name']);

        // Too long
        Livewire::test(Register::class)
            ->set('name', 'ThisUserIsWayTooLongForTheLimit')
            ->set('email', 'test@gmail.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['name']);
    }

    /** @test */
    public function email_must_be_gmail()
    {
        Livewire::test(Register::class)
            ->set('name', 'ValidUser123')
            ->set('email', 'test@yahoo.com')
            ->set('password', 'password123')
            ->set('password_confirmation', 'password123')
            ->call('register')
            ->assertHasErrors(['email']);
    }

    /** @test */
    public function email_is_required_realtime_validation()
    {
        Livewire::test(Register::class)
            ->set('email', '')
            ->assertHasErrors(['email']);
    }
}
