<?php

namespace Database\Seeders;

use App\Models\Mesa;
use App\Models\Zona;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class GastrobarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear las Zonas
        $restaurante = Zona::create(['nombre' => 'Restaurante']);
        $terraza = Zona::create(['nombre' => 'Terraza']);
        $discoteca = Zona::create(['nombre' => 'Discoteca VIP']);
        $barra = Zona::create(['nombre' => 'Barra']);

        // 2. Crear Mesas para el Restaurante (Mesas familiares)
        for ($i = 1; $i <= 8; $i++) {
            Mesa::create([
                'zona_id' => $restaurante->id,
                'numero' => "Mesa " . str_pad($i, 2, '0', STR_PAD_LEFT),
                'capacidad' => 4,
                'estado' => 'disponible'
            ]);
        }

        // 3. Crear Mesas para la Terraza
        for ($i = 1; $i <= 5; $i++) {
            Mesa::create([
                'zona_id' => $terraza->id,
                'numero' => "Terraza " . $i,
                'capacidad' => 2,
                'estado' => 'disponible'
            ]);
        }

        // 4. Crear Mesas para la Discoteca (Mesas ocupadas para probar diseño)
        for ($i = 1; $i <= 6; $i++) {
            Mesa::create([
                'zona_id' => $discoteca->id,
                'numero' => "VIP " . $i,
                'capacidad' => 6,
                'estado' => $i < 3 ? 'ocupada' : 'disponible' // Las primeras 2 aparecerán azules
            ]);
        }

        // 5. Crear puestos en la Barra
        for ($i = 1; $i <= 10; $i++) {
            Mesa::create([
                'zona_id' => $barra->id,
                'numero' => "Spot " . $i,
                'capacidad' => 1,
                'estado' => 'disponible'
            ]);
        }
    }
}
