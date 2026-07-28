<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Producto;
use App\Models\IntegracionContable;
use App\Models\ProcesoContable;

class ProductoIntegracionSeeder extends Seeder
{
    public function run(): void
    {
        $venta = ProcesoContable::where(
            'codigo',
            'VENTA_CONTADO'
        )->firstOrFail();

        $cervezas = IntegracionContable::firstOrCreate(

            [
                'codigo' => 'CERVEZAS'
            ],

            [
                'nombre' => 'Cervezas',

                'proceso_contable_id' => $venta->id,

                'porcentaje_iva' => 19,

                'porcentaje_inc' => 0,

                'estado' => true
            ]

        );

        Producto::whereIn(

            'categoria',

            [

                'Cervezas',

                'Cervezas Importadas'

            ]

        )->update([

            'integracion_contable_id' => $cervezas->id

        ]);

    }
}