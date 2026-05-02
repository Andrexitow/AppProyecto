<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductoSeeder extends Seeder
{
    public function run()
    {
        DB::table('productos')->insert([

            // ── CERVEZAS ──
            ['codigo' => 'C001', 'descripcion' => 'Cerveza Aguila 330ml',           'und_detal' => 'Unidad', 'categoria' => 'Cervezas', 'precio' => 3500, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'C002', 'descripcion' => 'Cerveza Aguila Light 330ml',     'und_detal' => 'Unidad', 'categoria' => 'Cervezas', 'precio' => 3500, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'C003', 'descripcion' => 'Cerveza Poker 330ml',            'und_detal' => 'Unidad', 'categoria' => 'Cervezas', 'precio' => 3500, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'C004', 'descripcion' => 'Cerveza Club Colombia Dorada',   'und_detal' => 'Unidad', 'categoria' => 'Cervezas', 'precio' => 4500, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'C005', 'descripcion' => 'Cerveza Club Colombia Negra',    'und_detal' => 'Unidad', 'categoria' => 'Cervezas', 'precio' => 4500, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'C006', 'descripcion' => 'Cerveza Heineken 330ml',         'und_detal' => 'Unidad', 'categoria' => 'Cervezas', 'precio' => 5000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'C007', 'descripcion' => 'Cerveza Corona 355ml',           'und_detal' => 'Unidad', 'categoria' => 'Cervezas', 'precio' => 5500, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'C008', 'descripcion' => 'Cerveza Budweiser 330ml',        'und_detal' => 'Unidad', 'categoria' => 'Cervezas', 'precio' => 4500, 'created_at' => now(), 'updated_at' => now()],

            // ── COCTELES ──
            ['codigo' => 'K001', 'descripcion' => 'Mojito Clásico',                 'und_detal' => 'Unidad', 'categoria' => 'Cocteles', 'precio' => 18000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'K002', 'descripcion' => 'Mojito de Maracuyá',             'und_detal' => 'Unidad', 'categoria' => 'Cocteles', 'precio' => 19000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'K003', 'descripcion' => 'Piña Colada',                    'und_detal' => 'Unidad', 'categoria' => 'Cocteles', 'precio' => 18000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'K004', 'descripcion' => 'Margarita Clásica',              'und_detal' => 'Unidad', 'categoria' => 'Cocteles', 'precio' => 20000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'K005', 'descripcion' => 'Aperol Spritz',                  'und_detal' => 'Unidad', 'categoria' => 'Cocteles', 'precio' => 22000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'K006', 'descripcion' => 'Gin Tonic',                      'und_detal' => 'Unidad', 'categoria' => 'Cocteles', 'precio' => 20000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'K007', 'descripcion' => 'Daiquiri de Fresa',              'und_detal' => 'Unidad', 'categoria' => 'Cocteles', 'precio' => 19000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'K008', 'descripcion' => 'Tequila Sunrise',                'und_detal' => 'Unidad', 'categoria' => 'Cocteles', 'precio' => 20000, 'created_at' => now(), 'updated_at' => now()],

            // ── COMIDA ──
            ['codigo' => 'F001', 'descripcion' => 'Hamburguesa Clásica',            'und_detal' => 'Unidad', 'categoria' => 'Comida',   'precio' => 22000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'F002', 'descripcion' => 'Hamburguesa Doble Carne',        'und_detal' => 'Unidad', 'categoria' => 'Comida',   'precio' => 28000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'F003', 'descripcion' => 'Alitas BBQ x6',                  'und_detal' => 'Porción', 'categoria' => 'Comida',   'precio' => 24000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'F004', 'descripcion' => 'Papas Fritas',                   'und_detal' => 'Porción', 'categoria' => 'Comida',   'precio' => 10000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'F005', 'descripcion' => 'Papas con Carne Desmechada',     'und_detal' => 'Porción', 'categoria' => 'Comida',   'precio' => 18000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'F006', 'descripcion' => 'Nachos con Queso y Guacamole',   'und_detal' => 'Porción', 'categoria' => 'Comida',   'precio' => 20000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'F007', 'descripcion' => 'Chorizo Santarrosano',           'und_detal' => 'Porción', 'categoria' => 'Comida',   'precio' => 15000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'F008', 'descripcion' => 'Bandeja Paisa',                  'und_detal' => 'Unidad', 'categoria' => 'Comida',   'precio' => 32000, 'created_at' => now(), 'updated_at' => now()],

        ]);
    }
}
