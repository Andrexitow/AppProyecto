<?php

namespace Database\Seeders;

use App\Models\Permisos;
use App\Models\Roles;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    // database/seeders/RolSeeder.php
    public function run(): void
    {
        // Crear Rol Admin
        $admin = Roles::create([
            'nombre' => 'Administrador',
            'descripcion' => 'Acceso total'
        ]);

        // Asignar TODOS los permisos existentes al admin
        $permisos = Permisos::all();
        $admin->permisos()->attach($permisos);

        // Crear Rol Mesero (Sin permisos por ahora, se los das en el modal)
        Roles::create([
            'nombre' => 'Mesero',
            'descripcion' => 'Solo ventas y pedidos'
        ]);
    }
}
