<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Roles;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buscamos los roles que creó el RolSeeder (o el DatabaseSeeder)
        $rolAdmin = Roles::where('nombre', 'Administrador')->first();
        $rolMesero = Roles::where('nombre', 'Mesero')->first();

        // 2. Usuario Administrador
        User::create([
            'name'              => 'Administrador del Sistema',
            'username'          => 'admin',
            'password'          => Hash::make('admin123'),
            'rol_id'            => $rolAdmin->id, // Usamos el ID del rol
            'activo'            => true,
            'email_verified_at' => now(),
        ]);

        // 3. Usuario Empleado / Mesero
        User::create([
            'name'              => 'Personal de Ventas',
            'username'          => 'meseros',
            'password'          => Hash::make('ventas2024'),
            'rol_id'            => $rolMesero->id, // Usamos el ID del rol
            'activo'            => true,
            'email_verified_at' => now(),
        ]);

        // 4. Usuario Cocina (Si no creaste el rol 'Cocina', puedes usar el de Mesero o crear el rol primero)
        // Por ahora lo asignaré como Mesero para evitar errores si el rol Cocina no existe
        User::create([
            'name'              => 'Personal de Cocina',
            'username'          => 'cocina',
            'password'          => Hash::make('cocina123'),
            'rol_id'            => $rolMesero->id, 
            'activo'            => true,
            'email_verified_at' => now(),
        ]);
    }
}