<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\GrupoMenu;

class ProductoSeeder extends Seeder
{
    public function run()
    {
        // Helper para obtener grupo_menu_id por nombre
        $g = fn(string $nombre) => GrupoMenu::where('nombre', $nombre)->value('id');

        DB::table('productos')->insert([

            ['codigo' => 'BG001', 'descripcion' => 'Burger Master',  'und_detal' => 'Unidad', 'categoria' => 'Burger Mania', 'afecta_inventario' => 0, 'grupo_menu_id' => $g('BURGER'), 'Precio' => 35000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'BG002', 'descripcion' => 'Mexicana',       'und_detal' => 'Unidad', 'categoria' => 'Burger Mania', 'afecta_inventario' => 0, 'grupo_menu_id' => $g('BURGER'), 'Precio' => 35000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'BG003', 'descripcion' => 'Doble',          'und_detal' => 'Unidad', 'categoria' => 'Burger Mania', 'afecta_inventario' => 0, 'grupo_menu_id' => $g('BURGER'), 'Precio' => 35000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'BG004', 'descripcion' => 'Burger 360',     'und_detal' => 'Unidad', 'categoria' => 'Burger Mania', 'afecta_inventario' => 0, 'grupo_menu_id' => $g('BURGER'), 'Precio' => 29900, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'BG005', 'descripcion' => 'Burger Clásica', 'und_detal' => 'Unidad', 'categoria' => 'Burger Mania', 'afecta_inventario' => 0, 'grupo_menu_id' => $g('BURGER'), 'Precio' => 22000, 'created_at' => now(), 'updated_at' => now()],

            ['codigo' => 'CV001', 'descripcion' => 'Club Colombia', 'und_detal' => 'Unidad', 'categoria' => 'Cervezas', 'afecta_inventario' => 1, 'grupo_menu_id' => $g('CERVEZAS'), 'Precio' => 8000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CV002', 'descripcion' => 'Poker',         'und_detal' => 'Unidad', 'categoria' => 'Cervezas', 'afecta_inventario' => 1, 'grupo_menu_id' => $g('CERVEZAS'), 'Precio' => 7000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CV003', 'descripcion' => 'Águila',        'und_detal' => 'Unidad', 'categoria' => 'Cervezas', 'afecta_inventario' => 1, 'grupo_menu_id' => $g('CERVEZAS'), 'Precio' => 7000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CV004', 'descripcion' => 'Águila Light',  'und_detal' => 'Unidad', 'categoria' => 'Cervezas', 'afecta_inventario' => 1, 'grupo_menu_id' => $g('CERVEZAS'), 'Precio' => 7000, 'created_at' => now(), 'updated_at' => now()],
            ['codigo' => 'CV005', 'descripcion' => 'Costeña',       'und_detal' => 'Unidad', 'categoria' => 'Cervezas', 'afecta_inventario' => 1, 'grupo_menu_id' => $g('CERVEZAS'), 'Precio' => 7000, 'created_at' => now(), 'updated_at' => now()],

        ]);
    }
}