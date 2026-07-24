<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProcesoContable;
use App\Models\TipoDocumentoContable;

class ProcesoContableSeeder extends Seeder
{
    public function run(): void
    {
        // $tipos = TipoDocumentoContable::pluck('id', 'codigo');
        $tipos = TipoDocumentoContable::where('codigo', 'FV')->firstOrFail();

        $procesos = [

            [
                'codigo' => 'VENTA_CONTADO',
                'nombre' => 'Venta de contado',
                'tipo_documento_contable_id' => $tipos['FV'],
                'estado' => true,
            ],

            [
                'codigo' => 'VENTA_CREDITO',
                'nombre' => 'Venta a crédito',
                'tipo_documento_contable_id' => $tipos['FV'],
                'estado' => true,
            ],

            [
                'codigo' => 'COMPRA',
                'nombre' => 'Compra',
                'tipo_documento_contable_id' => $tipos['FC'],
                'estado' => true,
            ],

            [
                'codigo' => 'PAGO_PROVEEDOR',
                'nombre' => 'Pago a proveedor',
                'tipo_documento_contable_id' => $tipos['CE'],
                'estado' => true,
            ],

            [
                'codigo' => 'RECAUDO_CLIENTE',
                'nombre' => 'Recaudo de cliente',
                'tipo_documento_contable_id' => $tipos['RC'],
                'estado' => true,
            ],

            [
                'codigo' => 'AJUSTE_INVENTARIO',
                'nombre' => 'Ajuste de inventario',
                'tipo_documento_contable_id' => $tipos['CD'],
                'estado' => true,
            ],

            [
                'codigo' => 'SALIDA_CONSUMO',
                'nombre' => 'Salida por consumo',
                'tipo_documento_contable_id' => $tipos['CD'],
                'estado' => true,
            ],

            [
                'codigo' => 'ENTRADA_INVENTARIO',
                'nombre' => 'Entrada de inventario',
                'tipo_documento_contable_id' => $tipos['CD'],
                'estado' => true,
            ],

            [
                'codigo' => 'NOTA_CREDITO',
                'nombre' => 'Nota crédito',
                'tipo_documento_contable_id' => $tipos['NC'],
                'estado' => true,
            ],

            [
                'codigo' => 'NOTA_DEBITO',
                'nombre' => 'Nota débito',
                'tipo_documento_contable_id' => $tipos['ND'],
                'estado' => true,
            ],

        ];

        foreach ($procesos as $proceso) {

            ProcesoContable::updateOrCreate(

                [
                    'codigo' => $proceso['codigo']
                ],

                [
                    'nombre' => $proceso['nombre'],
                    'tipo_documento_contable_id' => $proceso['tipo_documento_contable_id'],
                    'estado' => $proceso['estado'],
                ]

            );
        }
    }
}