<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\IntegracionContable;

class IntegracionContableSeeder extends Seeder
{
    /**
     * ⚠️ TASAS PROVISIONALES — PENDIENTE REVISIÓN CONTABLE ⚠️
     * Estos porcentajes son valores por defecto razonables (IVA general
     * colombiano 19%) para poder operar el sistema mientras un contador
     * confirma el tratamiento fiscal real de cada categoría (IVA vs INC,
     * exenciones, régimen del establecimiento, etc.).
     *
     * Antes de facturar en producción con clientes reales:
     * 1. Un contador debe revisar cada fila de esta tabla.
     * 2. Ajustar porcentaje_iva / porcentaje_inc según corresponda.
     * 3. Los cambios se hacen directo en la tabla integraciones_contables,
     *    sin tocar código ni el motor contable.
     */
    public function run(): void
    {
        $integraciones = [
            [
                'codigo' => 'CERVEZAS',
                'nombre' => 'Cervezas',
                'porcentaje_iva' => 19.00,
                'porcentaje_inc' => 0,
                'proceso_contable_id' => 1,
                'estado' => true,
            ],
            [
                'codigo' => 'LICORES',
                'nombre' => 'Licores',
                'porcentaje_iva' => 19.00,
                'porcentaje_inc' => 0,
                'proceso_contable_id' => 1,
                'estado' => true,
            ],
            [
                'codigo' => 'COCTELES',
                'nombre' => 'Cocteles',
                'porcentaje_iva' => 19.00,
                'porcentaje_inc' => 0,
                'proceso_contable_id' => 1,
                'estado' => true,
            ],
            [
                'codigo' => 'COMIDA',
                'nombre' => 'Comida / Cocina',
                'porcentaje_iva' => 19.00, // ⚠️ revisar: podría ser INC 8% en vez de IVA
                'porcentaje_inc' => 0,
                'proceso_contable_id' => 1,
                'estado' => true,
            ],
            [
                'codigo' => 'BEBIDAS_NO_ALCOHOLICAS',
                'nombre' => 'Bebidas no alcohólicas',
                'porcentaje_iva' => 19.00,
                'porcentaje_inc' => 0,
                'proceso_contable_id' => 1,
                'estado' => true,
            ],
            [
                'codigo' => 'OTROS',
                'nombre' => 'Otros',
                'porcentaje_iva' => 19.00,
                'porcentaje_inc' => 0,
                'proceso_contable_id' => 1,
                'estado' => true,
            ],
        ];

        foreach ($integraciones as $integracion) {
            IntegracionContable::updateOrCreate(
                ['codigo' => $integracion['codigo']],
                $integracion
            );
        }
    }
}