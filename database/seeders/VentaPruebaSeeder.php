<?php

namespace Database\Seeders;

use App\Models\Caja;
use App\Models\Factura;
use App\Models\Mesa;
use App\Models\Producto;
use App\Models\Roles;
use App\Models\User;
use App\Services\LegacyDocumentSyncService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Genera ~50 facturas de venta de prueba (agosto-septiembre 2026) repartidas
 * entre las dos cajas/bodegas, con varios meseros nuevos como vendedor, para
 * poder probar de verdad los informes de "Venta por Producto" y "Propinas
 * por Vendedor" con datos que se comportan como el sistema real:
 *
 * - Los productos con afecta_inventario=1 (cervezas) SÍ descuentan stock real
 *   de `inventarios` y quedan en el kardex, exactamente con la misma
 *   secuencia que usa FacturacionController::cerrarMesa() (decrementar
 *   primero, luego LegacyDocumentSyncService::factura() para dejar el
 *   Documento + movimiento de kardex).
 * - Los productos sin afecta_inventario (comidas) NO tocan inventario.
 *
 * Esto es SOLO para pruebas de los informes de ventas/propinas: a propósito
 * no contabiliza (no crea comprobantes_contables), para no mezclar 50
 * facturas ficticias con los libros contables reales del negocio.
 */
class VentaPruebaSeeder extends Seeder
{
    public function run(): void
    {
        $rolMesero = Roles::where('nombre', 'Mesero')->firstOrFail();
        $rolCajero = Roles::where('nombre', 'Cajero')->first();

        $nombresMeserosNuevos = ['Ana Torres', 'Luis Gómez', 'Sofía Ríos', 'Diego Salas'];
        $meserosNuevos = collect($nombresMeserosNuevos)->map(function ($nombre, $i) use ($rolMesero) {
            $username = 'mesero.prueba' . ($i + 1);
            return User::firstOrCreate(
                ['username' => $username],
                ['name' => $nombre, 'password' => bcrypt('mesero123'), 'rol_id' => $rolMesero->id, 'activo' => true]
            );
        });

        $this->command?->info('Meseros de prueba: ' . $meserosNuevos->pluck('name')->implode(', '));

        // Vendedores posibles = meseros nuevos + todos los meseros/cajeros que ya existían.
        $rolesVendedor = array_filter([$rolMesero->id, $rolCajero?->id]);
        $vendedores = User::whereIn('rol_id', $rolesVendedor)->pluck('id')->all();

        // Aseguramos stock suficiente de cervezas en ambas bodegas para que
        // las 50 ventas no dejen el inventario en negativo.
        $cervezas = Producto::where('afecta_inventario', 1)->get();
        foreach ($cervezas as $producto) {
            foreach ([1, 2] as $bodegaId) {
                $existente = DB::table('inventarios')->where(['producto_id' => $producto->id, 'bodega_id' => $bodegaId])->first();
                if (!$existente) {
                    DB::table('inventarios')->insert([
                        'producto_id' => $producto->id, 'bodega_id' => $bodegaId,
                        'stock' => 300, 'costo_promedio' => round($producto->precio * 0.55, 2),
                        'created_at' => now(), 'updated_at' => now(),
                    ]);
                } elseif ((float) $existente->stock < 150) {
                    DB::table('inventarios')->where('id', $existente->id)->update(['stock' => 300]);
                }
            }
        }

        $productos = Producto::all();
        $cajas = Caja::whereIn('id', [1, 2])->get()->keyBy('id');
        if ($cajas->count() < 2) {
            $this->command?->warn('No se encontraron las cajas 1 y 2 (Restaurante/Discoteca); el seeder de ventas de prueba no puede continuar.');
            return;
        }

        $mesasPorCaja = [
            $cajas[1]->id => Mesa::whereIn('zona_id', [1, 2])->pluck('id')->all(),
            $cajas[2]->id => Mesa::whereIn('zona_id', [3, 4])->pluck('id')->all(),
        ];

        $clientesIds = [null, null, null, 2, 3, 6, 7]; // más peso a "sin cliente" (consumidor final)
        $metodosPago = ['efectivo', 'efectivo', 'tarjeta', 'transferencia'];
        $legacySync = app(LegacyDocumentSyncService::class);

        $inicio = Carbon::create(2026, 8, 1, 8, 0, 0);
        $fin = Carbon::create(2026, 9, 12, 22, 0, 0);

        $creadas = 0;
        for ($i = 1; $i <= 50; $i++) {
            $cajaId = collect($cajas->keys())->random();
            $caja = $cajas[$cajaId];
            $mesasDisponibles = $mesasPorCaja[$cajaId];
            if (empty($mesasDisponibles)) continue;

            $mesaId = collect($mesasDisponibles)->random();
            $vendedorId = collect($vendedores)->random();
            $clienteId = collect($clientesIds)->random();
            $fecha = Carbon::createFromTimestamp(random_int($inicio->timestamp, $fin->timestamp));

            $numItems = random_int(1, 4);
            $itemsElegidos = $productos->random(min($numItems, $productos->count()));
            if (!($itemsElegidos instanceof \Illuminate\Support\Collection)) {
                $itemsElegidos = collect([$itemsElegidos]);
            }

            $subtotalTotal = 0.0;
            $lineas = [];
            foreach ($itemsElegidos as $producto) {
                $cantidad = random_int(1, 3);
                $subtotal = round((float) $producto->precio * $cantidad, 2);
                $lineas[] = ['producto' => $producto, 'cantidad' => $cantidad, 'subtotal' => $subtotal];
                $subtotalTotal += $subtotal;
            }

            // ~40% de las facturas trae propina del 10%, como dejaría un cliente real.
            $propina = random_int(1, 100) <= 40 ? round($subtotalTotal * 0.10) : 0;

            $ultimoNumero = Factura::where('numero_factura', 'like', $caja->prefijo . '-%')
                ->orderByDesc('id')->value('numero_factura');
            $siguiente = $ultimoNumero ? ((int) (explode('-', $ultimoNumero)[1] ?? 0)) + 1 : 1;
            $numeroFactura = $caja->prefijo . '-' . str_pad((string) $siguiente, 5, '0', STR_PAD_LEFT);

            $factura = Factura::create([
                'numero_factura' => $numeroFactura,
                'mesa_id' => $mesaId,
                'user_id' => $vendedorId,
                'cliente_id' => $clienteId,
                'caja_id' => $cajaId,
                'subtotal' => $subtotalTotal,
                'impuestos' => 0,
                'propina' => $propina,
                'total' => $subtotalTotal + $propina,
                'metodo_pago' => collect($metodosPago)->random(),
                'estado' => 'pagada',
                'estado_pago' => 'pagada',
                'total_pagado' => $subtotalTotal + $propina,
                'saldo_pendiente' => 0,
            ]);
            $factura->created_at = $fecha;
            $factura->updated_at = $fecha;
            $factura->save();

            foreach ($lineas as $linea) {
                $factura->detalles()->create([
                    'producto_id' => $linea['producto']->id,
                    'cantidad' => $linea['cantidad'],
                    'precio_unitario' => $linea['producto']->precio,
                    'subtotal' => $linea['subtotal'],
                ]);

                // Misma secuencia que el checkout real: se decrementa el stock
                // ANTES de sincronizar, porque LegacyDocumentSyncService lee el
                // stock ya actualizado para construir el movimiento de kardex.
                if ($linea['producto']->afecta_inventario == 1) {
                    DB::table('inventarios')
                        ->where('producto_id', $linea['producto']->id)
                        ->where('bodega_id', $cajaId)
                        ->decrement('stock', $linea['cantidad']);
                }
            }

            $legacySync->factura($factura->fresh('detalles.producto'));
            $creadas++;
        }

        $this->command?->info("Facturas de prueba creadas: {$creadas}");
    }
}
