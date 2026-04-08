<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@ukk2026.com'],
            [
                'password' => Hash::make('123456'),
                'role' => 'admin',
            ]
        );
    }
}