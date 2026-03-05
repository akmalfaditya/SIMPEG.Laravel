<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        if (User::count() > 0) return;

        User::create([
            'name' => 'Super Admin',
            'email' => 'admin@simpeg.go.id',
            'password' => Hash::make('password'),
            'role' => 'SuperAdmin',
        ]);

        User::create([
            'name' => 'HR Staff',
            'email' => 'hr@simpeg.go.id',
            'password' => Hash::make('password'),
            'role' => 'HR',
        ]);
    }
}
