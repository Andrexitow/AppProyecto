<?php

namespace Database\Seeders;

use App\Models\Bodega;
use App\Models\Caja;
use App\Models\User;
use Illuminate\Database\Seeder;

class CajaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cajeroPrincipal = User::where('username', 'cajero1')->first();
        $cajeroNocturno  = User::where('username', 'cajero2')->first();

        // Ajusta los nombres de bodega según tus registros reales
        $bodegaRestaurante = Bodega::where('descripcion', 'Bodega Restaurante')->first();
        $bodegaDiscoteca   = Bodega::where('descripcion', 'Bodega Discoteca')->first();

        Caja::create([
            'nombre'    => 'CajaRestaurante',
            'prefijo'   => 'FR',
            'bodega_id' => 1,
            'user_id'   => $cajeroPrincipal?->id,
            'activa'    => true,
        ]);

        Caja::create([
            'nombre'    => 'CajaDiscoteca',
            'prefijo'   => 'FD',
            'bodega_id' => 2,
            'user_id'   => $cajeroNocturno?->id,
            'activa'    => true,
        ]);
    }
}
