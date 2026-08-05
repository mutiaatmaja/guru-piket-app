<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Admin Utama
        User::updateOrCreate(
            ['email' => 'admin@smkn7ptk.sch.id'],
            [
                'name'     => 'Administrator Utama',
                'password' => Hash::make('password123'),
                'role'     => 'admin',
            ]
        );
    }
}