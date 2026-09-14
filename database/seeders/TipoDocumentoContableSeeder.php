<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TipoDocumentoContable;

class TipoDocumentoContableSeeder extends Seeder
{
    public function run(): void
    {
        $documentos = [

            [
                'codigo' => 'FV',
                'nombre' => 'Factura de Venta',
                'prefijo' => 'FV'
            ],

            [
                'codigo' => 'FC',
                'nombre' => 'Factura de Compra',
                'prefijo' => 'FC'
            ],

            [
                'codigo' => 'RC',
                'nombre' => 'Recibo de Caja',
                'prefijo' => 'RC'
            ],

            [
                'codigo' => 'CE',
                'nombre' => 'Comprobante de Egreso',
                'prefijo' => 'CE'
            ],

            [
                'codigo' => 'NC',
                'nombre' => 'Nota Crédito',
                'prefijo' => 'NC'
            ],

            [
                'codigo' => 'ND',
                'nombre' => 'Nota Débito',
                'prefijo' => 'ND'
            ],

            [
                'codigo' => 'AJ',
                'nombre' => 'Ajuste Contable',
                'prefijo' => 'AJ'
            ],

            [
                'codigo' => 'CD',
                'nombre' => 'Comprobante Diario',
                'prefijo' => 'CD'
            ],

            [
                'codigo' => 'AF',
                'nombre' => 'Comprobante de Activos Fijos',
                'prefijo' => 'AF'
            ],

            [
                'codigo' => 'NO',
                'nombre' => 'Comprobante de Nómina',
                'prefijo' => 'NO'
            ],

            [
                'codigo' => 'SI',
                'nombre' => 'Comprobante de Saldos Iniciales',
                'prefijo' => 'SI'
            ],

        ];

        foreach ($documentos as $documento) {

            TipoDocumentoContable::updateOrCreate(

                ['codigo' => $documento['codigo']],

                $documento

            );

        }
    }
}