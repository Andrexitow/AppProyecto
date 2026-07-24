<?php

namespace Database\Seeders;

use App\Models\PlantillaContable;
use App\Models\ProcesoContable;
use Illuminate\Database\Seeder;

class PlantillaContableSeeder extends Seeder
{
    public function run(): void
    {
        $ventaContado = ProcesoContable::where('codigo', 'VENTA_CONTADO')->firstOrFail();

        $plantillas = [

            [
                'orden' => 1,
                'tipo_movimiento' => 'DEBITO',
                'configuracion_clave' => 'CUENTA_CAJA',
                'origen_valor' => 'TOTAL',
                'descripcion' => 'Ingreso a caja'
            ],

            [
                'orden' => 2,
                'tipo_movimiento' => 'CREDITO',
                'configuracion_clave' => 'CUENTA_VENTAS',
                'origen_valor' => 'SUBTOTAL',
                'descripcion' => 'Registro de venta'
            ],

            [
                'orden' => 3,
                'tipo_movimiento' => 'CREDITO',
                'configuracion_clave' => 'CUENTA_IVA_GENERADO',
                'origen_valor' => 'IVA',
                'descripcion' => 'IVA generado'
            ]

        ];

        foreach ($plantillas as $plantilla) {

            PlantillaContable::updateOrCreate(
                [
                    'proceso_contable_id' => $ventaContado->id,
                    'orden' => $plantilla['orden']
                ],
                array_merge(
                    $plantilla,
                    [
                        'proceso_contable_id' => $ventaContado->id,
                        'estado' => true
                    ]
                )
            );
        }
    }
}