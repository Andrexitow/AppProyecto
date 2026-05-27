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
        // Buscar roles
        $rolAdmin   = Roles::where('nombre', 'Administrador')->first();
        $rolMesero  = Roles::where('nombre', 'Mesero')->first();
        $rolCajero  = Roles::where('nombre', 'Cajero')->first();
        $rolCocina  = Roles::where('nombre', 'Cocina')->first();

        // ADMIN
        User::create([
            'name'              => 'Administrador del Sistema',
            'username'          => 'admin',
            'password'          => Hash::make('admin'),
            'rol_id'            => $rolAdmin->id,
            'activo'            => true,
            'email_verified_at' => now(),
        ]);

        // ===== MESEROS =====
        User::create([
            'name'              => 'Mesero Restaurante',
            'username'          => 'mesero1',
            'password'          => Hash::make('mesero1'),
            'rol_id'            => $rolMesero->id,
            'activo'            => true,
            'email_verified_at' => now(),
            'caja_id'          => 1,
        ]);

        User::create([
            'name'              => 'Mesero Discoteca',
            'username'          => 'mesero2',
            'password'          => Hash::make('mesero2'),
            'rol_id'            => $rolMesero->id,
            'activo'            => true,
            'email_verified_at' => now(),
            'caja_id'          => 2,
        ]);

        // ===== CAJEROS =====
        User::create([
            'name'              => 'Cajero Restaurante',
            'username'          => 'cajero1',
            'password'          => Hash::make('cajero1'),
            'rol_id'            => $rolCajero->id,
            'activo'            => true,
            'email_verified_at' => now(),
            'caja_id'          => 1,
        ]);

        User::create([
            'name'              => 'Cajero Discoteca',
            'username'          => 'cajero2',
            'password'          => Hash::make('cajero2'),
            'rol_id'            => $rolCajero->id,
            'activo'            => true,
            'email_verified_at' => now(),
            'caja_id'          => 2,
        ]);

        // ===== COCINA =====
        User::create([
            'name'              => 'Chef Principal',
            'username'          => 'cocina1',
            'password'          => Hash::make('cocina1'),
            'rol_id'            => $rolCocina->id,
            'activo'            => true,
            'email_verified_at' => now(),
        ]);

        User::create([
            'name'              => 'Auxiliar Cocina',
            'username'          => 'cocina2',
            'password'          => Hash::make('cocina2'),
            'rol_id'            => $rolCocina->id,
            'activo'            => true,
            'email_verified_at' => now(),
        ]);
    }
}