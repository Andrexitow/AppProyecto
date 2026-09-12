<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProcesoContable;
use App\Models\TipoDocumentoContable;

class ProcesoContableSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = TipoDocumentoContable::pluck('id', 'codigo');

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
                'tipo_documento_contable_id' => $tipos['AJ'],
                'estado' => true,
            ],

            [
                'codigo' => 'SALIDA_CONSUMO',
                'nombre' => 'Salida por consumo',
                'tipo_documento_contable_id' => $tipos['AJ'],
                'estado' => true,
            ],

            [
                'codigo' => 'ENTRADA_INVENTARIO',
                'nombre' => 'Entrada de inventario',
                'tipo_documento_contable_id' => $tipos['AJ'],
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

            [
                'codigo' => 'INGRESO_TESORERIA',
                'nombre' => 'Ingreso de tesorería',
                'tipo_documento_contable_id' => $tipos['RC'],
                'estado' => true,
            ],

            [
                'codigo' => 'EGRESO_TESORERIA',
                'nombre' => 'Egreso de tesorería',
                'tipo_documento_contable_id' => $tipos['CE'],
                'estado' => true,
            ],

            [
                'codigo' => 'TRANSFERENCIA_TESORERIA',
                'nombre' => 'Transferencia entre cuentas de tesorería',
                'tipo_documento_contable_id' => $tipos['CD'],
                'estado' => true,
            ],

        ];

        foreach ($procesos as $proceso) {

            ProcesoContable::updateOrCreate(
                ['codigo' => $proceso['codigo']],
                $proceso
            );
        }
    }
}
