<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\MetodoPagoContable;

class MetodoPagoContableSeeder extends Seeder
{
    public function run(): void
    {
        $mapeos = [
            ['metodo_pago' => 'efectivo',      'configuracion_clave' => 'CUENTA_CAJA'],
            ['metodo_pago' => 'tarjeta',       'configuracion_clave' => 'CUENTA_BANCO'],
            ['metodo_pago' => 'transferencia', 'configuracion_clave' => 'CUENTA_BANCO'],
            ['metodo_pago' => 'nequi',         'configuracion_clave' => 'CUENTA_BANCO'],
            ['metodo_pago' => 'daviplata',     'configuracion_clave' => 'CUENTA_BANCO'],
            ['metodo_pago' => 'credito',       'configuracion_clave' => 'CUENTA_CLIENTES'],
        ];

        foreach ($mapeos as $mapeo) {
            MetodoPagoContable::updateOrCreate(
                ['metodo_pago' => $mapeo['metodo_pago']],
                $mapeo + ['estado' => true]
            );
        }
    }
}