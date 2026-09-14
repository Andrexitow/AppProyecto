<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ConfiguracionContable;

class ConfiguracionContableSeeder extends Seeder
{
    public function run(): void
    {
        $configuraciones = [

            [
                'clave' => 'CUENTA_CAJA',
                'nombre' => 'Cuenta Caja General'
            ],

            [
                'clave' => 'CUENTA_BANCO',
                'nombre' => 'Cuenta Banco Principal'
            ],

            [
                'clave' => 'CUENTA_CLIENTES',
                'nombre' => 'Cuenta Clientes'
            ],

            [
                'clave' => 'CUENTA_PROVEEDORES',
                'nombre' => 'Cuenta Proveedores'
            ],

            [
                'clave' => 'CUENTA_INVENTARIO',
                'nombre' => 'Cuenta Inventario'
            ],

            [
                'clave' => 'CUENTA_COSTO_VENTAS',
                'nombre' => 'Cuenta Costo de Ventas'
            ],

            [
                'clave' => 'CUENTA_VENTAS',
                'nombre' => 'Cuenta Ventas'
            ],

            [
                'clave' => 'CUENTA_IVA_GENERADO',
                'nombre' => 'Cuenta IVA Generado'
            ],

            [
                'clave' => 'CUENTA_IVA_DESCONTABLE',
                'nombre' => 'Cuenta IVA Descontable'
            ],

            [
                'clave' => 'CUENTA_RETEFUENTE',
                'nombre' => 'Cuenta Retención en la Fuente'
            ],

            [
                'clave' => 'CUENTA_RETEIVA',
                'nombre' => 'Cuenta Retención de IVA'
            ],

            [
                'clave' => 'CUENTA_RETEICA',
                'nombre' => 'Cuenta Retención de ICA'
            ],

            [
                'clave' => 'CUENTA_PERDIDA_BAJA_ACTIVOS',
                'nombre' => 'Cuenta Pérdida en Baja de Activos Fijos'
            ],

            [
                'clave' => 'CUENTA_PROVISION_CARTERA',
                'nombre' => 'Cuenta Provisión Clientes (contra-activo)'
            ],

            [
                'clave' => 'CUENTA_GASTO_PROVISION_CARTERA',
                'nombre' => 'Cuenta Gasto Provisión Cartera'
            ],

        ];

        foreach ($configuraciones as $configuracion) {

            ConfiguracionContable::updateOrCreate(
                ['clave' => $configuracion['clave']],
                $configuracion
            );

        }
    }
}