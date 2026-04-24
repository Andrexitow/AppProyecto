<?php

namespace Database\Seeders;

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
        DB::table('inventarios')->insert([

            [
                'producto_id' => 1, // Aguila
                'bodega_id' => 1,
                'stock' => 30,
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'producto_id' => 1, // Aguila
                'bodega_id' => 2,
                'stock' => 10,
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'producto_id' => 2, // Aguila Light
                'bodega_id' => 1,
                'stock' => 25,
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'producto_id' => 3, // Poker
                'bodega_id' => 1,
                'stock' => 40,
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'producto_id' => 4, // Club Colombia
                'bodega_id' => 2,
                'stock' => 15,
                'created_at' => now(),
                'updated_at' => now()
            ],

            [
                'producto_id' => 6, // Heineken
                'bodega_id' => 1,
                'stock' => 20,
                'created_at' => now(),
                'updated_at' => now()
            ],

        ]);
    }
}
