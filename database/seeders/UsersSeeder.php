<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        User::create([
            'name' => 'admin',
            'role' => UserRole::Admin->value,
            'login' => 'admin',
            'password' => '1234567',
        ]);
    }
}
