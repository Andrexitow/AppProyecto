<?php

namespace Database\Seeders;

use App\Models\Permisos;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PermisoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permisos = [
            // MÓDULO: PRODUCTOS
            ['nombre' => 'Ver Productos',      'slug' => 'productos.ver'],
            ['nombre' => 'Crear Productos',    'slug' => 'productos.crear'],
            ['nombre' => 'Editar Productos',   'slug' => 'productos.editar'],
            ['nombre' => 'Eliminar Productos', 'slug' => 'productos.eliminar'],
            ['nombre' => 'Cambiar Estado',     'slug' => 'productos.estado'],

            // MÓDULO: USUARIOS Y ROLES
            ['nombre' => 'Administrar Usuarios', 'slug' => 'usuarios.index'],
            ['nombre' => 'Crear Usuarios',       'slug' => 'usuarios.crear'],
            ['nombre' => 'Eliminar Usuarios',       'slug' => 'usuarios.eliminar'],
            ['nombre' => 'Administrar Roles',    'slug' => 'roles.index'],

            // MÓDULO: AJUSTES / INVENTARIO
            ['nombre' => 'Ver Inventario',     'slug' => 'inventario.ver'],
            ['nombre' => 'Realizar Ajustes',   'slug' => 'inventario.ajuste'],
            
            // MÓDULO: TERCEROS
            ['nombre' => 'Administrar Terceros', 'slug' => 'terceros.index'],
        ];

        foreach ($permisos as $p) {
            Permisos::updateOrCreate(['slug' => $p['slug']], $p);
        }
    }
}
