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
            ['nombre' => 'Cervezas', 'icono' => '🍺', 'orden' => 1],
            ['nombre' => 'Cocteles', 'icono' => '🍹', 'orden' => 2],
            ['nombre' => 'Comida',   'icono' => '🍔', 'orden' => 3],
        ];

        foreach ($categorias as $cat) {
            CategoriaPos::firstOrCreate(
                ['nombre' => $cat['nombre']],
                ['icono'  => $cat['icono'], 'orden' => $cat['orden']]
            );
        }
    }
}
