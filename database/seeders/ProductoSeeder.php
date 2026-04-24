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
                'codigo' => 'C001',
                'descripcion' => 'Cerveza Aguila 330ml',
                'und_detal' => 'Unidad',
                'categoria' => 'Cervezas',
                'precio' => 3500,
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'codigo' => 'C002',
                'descripcion' => 'Cerveza Aguila Light 330ml',
                'und_detal' => 'Unidad',
                'categoria' => 'Cervezas',
                'precio' => 3500,
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'codigo' => 'C003',
                'descripcion' => 'Cerveza Poker 330ml',
                'und_detal' => 'Unidad',
                'categoria' => 'Cervezas',
                'precio' => 3500,
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'codigo' => 'C004',
                'descripcion' => 'Cerveza Club Colombia Dorada 330ml',
                'und_detal' => 'Unidad',
                'categoria' => 'Cervezas',
                'precio' => 4500,
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'codigo' => 'C005',
                'descripcion' => 'Cerveza Club Colombia Negra 330ml',
                'und_detal' => 'Unidad',
                'categoria' => 'Cervezas',
                'precio' => 4500,
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'codigo' => 'C006',
                'descripcion' => 'Cerveza Heineken 330ml',
                'und_detal' => 'Unidad',
                'categoria' => 'Cervezas',
                'precio' => 5000,
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'codigo' => 'C007',
                'descripcion' => 'Cerveza Corona 355ml',
                'und_detal' => 'Unidad',
                'categoria' => 'Cervezas',
                'precio' => 5500,
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'codigo' => 'C008',
                'descripcion' => 'Cerveza Budweiser 330ml',
                'und_detal' => 'Unidad',
                'categoria' => 'Cervezas',
                'precio' => 4500,
                'created_at' => now(),
                'updated_at' => now()
            ],

        ]);
    }
}
