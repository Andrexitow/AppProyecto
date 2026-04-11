<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductoSeeder extends Seeder
{
    public function run()
    {
        DB::table('productos')->insert([

            [
                'codigo' => 'P001',
                'descripcion' => 'Arroz 1kg',
                'und_detal' => 'Unidad',
                'categoria' => 'General',
                'precio' => 3500,
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'codigo' => 'P002',
                'descripcion' => 'Azúcar 1kg',
                'und_detal' => 'Unidad',
                'categoria' => 'General',
                'precio' => 3200,
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'codigo' => 'P003',
                'descripcion' => 'Aceite 900ml',
                'und_detal' => 'Unidad',
                'categoria' => 'General',
                'precio' => 8500,
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'codigo' => 'P004',
                'descripcion' => 'Leche Entera',
                'und_detal' => 'Unidad',
                'categoria' => 'Lácteos',
                'precio' => 4000,
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'codigo' => 'P005',
                'descripcion' => 'Pan tajado',
                'und_detal' => 'Unidad',
                'categoria' => 'Panadería',
                'precio' => 5000,
                'created_at' => now(),
                'updated_at' => now()
            ],

        ]);
    }
}