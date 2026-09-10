<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Seeder;

class InventarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $p = fn(string $codigo) => Producto::where('codigo', $codigo)->value('id');
        DB::table('inventarios')->insert([

            [
                'producto_id' => $p('CV003'), // Águila
                'bodega_id' => 1,
                'stock' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'producto_id' => $p('CV003'), // Águila
                'bodega_id' => 2,
                'stock' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'producto_id' => $p('CV004'), // Águila Light
                'bodega_id' => 1,
                'stock' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'producto_id' => $p('CV002'), // Poker
                'bodega_id' => 1,
                'stock' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],

            [
                'producto_id' => $p('CV001'), // Club Colombia
                'bodega_id' => 2,
                'stock' => 100,
                'created_at' => now(),
                'updated_at' => now(),
            ],

        ]);
    }
    //     DB::table('inventarios')->insert([

    //         [
    //             'producto_id' => 80, // Aguila
    //             'bodega_id' => 1,
    //             'stock' => 30,
    //             'created_at' => now(),
    //             'updated_at' => now()
    //         ],

    //         [
    //             'producto_id' => 80, // Aguila
    //             'bodega_id' => 2,
    //             'stock' => 10,
    //             'created_at' => now(),
    //             'updated_at' => now()
    //         ],

    //         [
    //             'producto_id' => 81, // Aguila Light
    //             'bodega_id' => 1,
    //             'stock' => 25,
    //             'created_at' => now(),
    //             'updated_at' => now()
    //         ],

    //         [
    //             'producto_id' => 79, // Poker
    //             'bodega_id' => 1,
    //             'stock' => 40,
    //             'created_at' => now(),
    //             'updated_at' => now()
    //         ],

    //         [
    //             'producto_id' => 78, // Club Colombia
    //             'bodega_id' => 2,
    //             'stock' => 15,
    //             'created_at' => now(),
    //             'updated_at' => now()
    //         ],

    //         [
    //             'producto_id' => 86, // Heineken
    //             'bodega_id' => 1,
    //             'stock' => 20,
    //             'created_at' => now(),
    //             'updated_at' => now()
    //         ],

    //     ]);
    // }
}
