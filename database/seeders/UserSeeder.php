<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Admin', 'password' => 'password', 'role' => 'admin', 'status' => 'active']
        );

        User::updateOrCreate(
            ['email' => 'user@example.com'],
            ['name' => 'Hussein Basma', 'password' => 'password', 'role' => 'employee', 'status' => 'active']
        );
    }
}
