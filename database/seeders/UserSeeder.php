<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Admin',
            'email' => 'admin@test.com',
            'email_verified_at' => now(),
            'password' => Hash::make('admin'), 
            'remember_token' => null,
        ]);

        User::create([
            'name' => 'Empleado',
            'email' => 'Empleado@test.com',
            'email_verified_at' => now(),
            'password' => Hash::make('empleado'), 
            'remember_token' => null,
        ]);
    }
}
