<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FacturaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $cajeroId = 4;
        $cajaId = 1;
        // Forzamos la fecha al 6 de Mayo de 2026
        $hoy = Carbon::create(2026, 5, 6);

        for ($i = 1; $i <= 10; $i++) {
            $metodo = ($i % 3 == 0) ? 'transferencia' : 'efectivo';
            $subtotal = rand(30000, 150000);
            $propina = $subtotal * 0.10;
            $total = $subtotal + $propina;

            DB::table('facturas')->insert([
                'numero_factura' => 'FD-' . str_pad($i + 10, 5, '0', STR_PAD_LEFT), // +10 para no chocar IDs
                'user_id'        => $cajeroId,
                'caja_id'        => $cajaId,
                'cliente_id'     => 1,
                'mesa_id'        => rand(1, 5),
                'subtotal'       => $subtotal,
                'impuestos'      => 0.00,
                'propina'        => $propina,
                'total'          => $total,
                'metodo_pago'    => $metodo,
                'estado'         => 'pagada',
                'created_at'     => $hoy->copy()->setHour(rand(10, 20))->setMinute(rand(0, 59)),
                'updated_at'     => $hoy->copy()->setHour(21),
            ]);
        }
    }
}
