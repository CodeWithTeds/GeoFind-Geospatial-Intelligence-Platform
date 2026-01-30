<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;

use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Auth\Events\Registered;

class ClientAuthService
{
    /**
     * Register a new user.
     *
     * @param array $data
     * @param string $ip
     * @return User
     */
    public function register(array $data, string $ip): User
    {
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'client',
        ]);

        event(new Registered($user));

        Log::info("New user registered: {$user->email} from IP: {$ip}");

        // Auto login after registration
        Auth::login($user);
        
        return $user;
    }

    /**
     * Attempt to authenticate a client user.
     *
     * @param array $credentials
     * @param bool $remember
     * @param string $ip
     * @return void
     * @throws ValidationException
     */
    public function login(array $credentials, bool $remember, string $ip): void
    {
        // Attempt to authenticate
        if (!Auth::guard('web')->attempt($credentials, $remember)) {
            
            Log::warning("Failed login attempt for email: {$credentials['email']} from IP: {$ip}");
            
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // Regenerate session to prevent session fixation
        Session::regenerate();

        Log::info("Successful client login for email: {$credentials['email']} from IP: {$ip}");
    }

    /**
     * Logout the current user.
     *
     * @return void
     */
    public function logout(): void
    {
        $user = Auth::guard('web')->user();
        $ip = request()->ip();

        Auth::guard('web')->logout();

        Session::invalidate();
        Session::regenerateToken();

        if ($user) {
            Log::info("Client logout for user ID: {$user->id} from IP: {$ip}");
        }
    }
}
