<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'profalex@admin.com'],
            [
                'name' => 'Professor Alex',
                'password' => 'password', // The model casts this to hashed
                'email_verified_at' => now(),
                'role' => 'admin',
            ]
        );

        // Ensure role is admin if user already existed
        if ($user->role !== 'admin') {
            $user->update(['role' => 'admin']);
        }
    }
}
