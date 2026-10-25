<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    /**
     * Create the demo administrator once (credentials come from .env).
     */
    public function run(): void
    {
        $admin = config('summitgear.admin');

        User::firstOrCreate(
            ['email' => $admin['email']],
            [
                'name' => $admin['name'],
                'password' => $admin['password'],
                'email_verified_at' => now(),
            ],
        );
    }
}
