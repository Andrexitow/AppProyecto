<?php

namespace Database\Seeders;

use App\Models\ConceptoCaja;
use Illuminate\Database\Seeder;

class ConceptoCajaSeeder extends Seeder
{
    /**
     * Conceptos base para que el selector de movimientos de caja no arranque
     * vacío. El administrador puede editar/desactivar/agregar más desde
     * "Conceptos de Caja".
     */
    public function run(): void
    {
        $conceptos = [
            ['nombre' => 'Pago turno meseros', 'tipo' => 'salida', 'descripcion' => 'Pago de propinas o turno a meseros'],
            ['nombre' => 'Compras generales', 'tipo' => 'salida', 'descripcion' => 'Compras menores pagadas desde la caja'],
            ['nombre' => 'Pago a proveedor', 'tipo' => 'salida', 'descripcion' => 'Pago en efectivo a un proveedor'],
            ['nombre' => 'Domicilio / transporte', 'tipo' => 'salida', 'descripcion' => 'Mandados, transporte de mercancía, etc.'],
            ['nombre' => 'Retiro administrativo', 'tipo' => 'salida', 'descripcion' => 'Retiro de efectivo hacia administración'],
            ['nombre' => 'Base de caja', 'tipo' => 'ingreso', 'descripcion' => 'Ingreso de base/fondo inicial de caja'],
            ['nombre' => 'Otro ingreso', 'tipo' => 'ambos', 'descripcion' => 'Ingreso/salida que no aplica a los conceptos anteriores'],
        ];

        foreach ($conceptos as $concepto) {
            ConceptoCaja::firstOrCreate(['nombre' => $concepto['nombre']], $concepto);
        }
    }
}
