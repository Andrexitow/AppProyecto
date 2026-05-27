<?php

namespace Database\Seeders;

use App\Models\CategoriaPos;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriasPosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [

            // COMIDA
            ['nombre' => 'Entradas y Antojos',     'icono' => '🍽️', 'orden' => 1],
            ['nombre' => 'Del Mar y Patacones',    'icono' => '🐟', 'orden' => 2],
            ['nombre' => 'Cortes Premium',         'icono' => '🥩', 'orden' => 3],
            ['nombre' => 'Cerdo y Pollo',          'icono' => '🍗', 'orden' => 4],
            ['nombre' => 'Típicos Festivos',       'icono' => '🍲', 'orden' => 5],
            ['nombre' => 'Picadas y Pinchos',      'icono' => '🍢', 'orden' => 6],
            ['nombre' => 'Burger Mania',           'icono' => '🍔', 'orden' => 7],
            ['nombre' => 'Infantil y Rápidos',     'icono' => '🍟', 'orden' => 8],

            // BEBIDAS
            ['nombre' => 'Cocteles',               'icono' => '🍹', 'orden' => 9],
            ['nombre' => 'Cocteles de la Casa',    'icono' => '🔥', 'orden' => 10],
            ['nombre' => 'Peceras',                'icono' => '🫧', 'orden' => 11],
            ['nombre' => 'Sodas Italianas',        'icono' => '🥤', 'orden' => 12],
            ['nombre' => 'Cervezas',               'icono' => '🍺', 'orden' => 13],
            ['nombre' => 'Cervezas Importadas',    'icono' => '🍻', 'orden' => 14],
            ['nombre' => 'Micheladas',             'icono' => '🍺', 'orden' => 15],
            ['nombre' => 'Vinos',                  'icono' => '🍷', 'orden' => 16],

            // LICORES
            ['nombre' => 'Aguardiente',            'icono' => '🥃', 'orden' => 17],
            ['nombre' => 'Ron',                    'icono' => '🥃', 'orden' => 18],
            ['nombre' => 'Vodka',                  'icono' => '🍸', 'orden' => 19],
            ['nombre' => 'Whisky',                 'icono' => '🥃', 'orden' => 20],
            ['nombre' => 'Ginebra',                'icono' => '🍸', 'orden' => 21],
            ['nombre' => 'Licores Cremosos',       'icono' => '🥛', 'orden' => 22],
            ['nombre' => 'Tequilas',               'icono' => '🌵', 'orden' => 23],
            ['nombre' => 'Cubetazos',              'icono' => '🧊', 'orden' => 24],
        ];

        foreach ($categorias as $cat) {
            CategoriaPos::firstOrCreate(
                ['nombre' => $cat['nombre']],
                [
                    'icono' => $cat['icono'],
                    'orden' => $cat['orden']
                ]
            );
        }
    }
}
