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

        $this->aplicar($ventaContado, [

            // Ingreso de la venta, repartido entre Caja/Banco/Clientes según las
            // formas de pago reales de la factura (FacturacionContableService::
            // repartirPagosPorGrupo). Antes había una sola línea a CUENTA_CAJA
            // con override por método de pago; un método 'mixto' (o cualquiera
            // sin parametrizar) caía silenciosamente a Caja. Ahora cada línea
            // solo aparece si esa forma de pago realmente se usó (omitir_si_cero),
            // y un método sin parametrizar contable HACE FALLAR la venta en vez
            // de registrarse mal.
            [
                'orden' => 1,
                'tipo_movimiento' => 'DEBITO',
                'configuracion_clave' => 'CUENTA_CAJA',
                'origen_valor' => 'CUENTA_CAJA',
                'descripcion' => 'Ingreso a caja'
            ],

            [
                'orden' => 2,
                'tipo_movimiento' => 'DEBITO',
                'configuracion_clave' => 'CUENTA_BANCO',
                'origen_valor' => 'CUENTA_BANCO',
                'descripcion' => 'Ingreso a banco/digital'
            ],

            [
                'orden' => 3,
                'tipo_movimiento' => 'DEBITO',
                'configuracion_clave' => 'CUENTA_CLIENTES',
                'origen_valor' => 'CUENTA_CLIENTES',
                'descripcion' => 'Venta a crédito de cliente'
            ],

            [
                'orden' => 4,
                'tipo_movimiento' => 'CREDITO',
                'configuracion_clave' => 'CUENTA_VENTAS',
                'origen_valor' => 'SUBTOTAL',
                'descripcion' => 'Registro de venta'
            ],

            [
                'orden' => 5,
                'tipo_movimiento' => 'CREDITO',
                'configuracion_clave' => 'CUENTA_IVA_GENERADO',
                'origen_valor' => 'IVA',
                'descripcion' => 'IVA generado'
            ],

            // Costo de ventas: sin estas dos líneas el Kardex baja el stock
            // físico pero la contabilidad nunca sacaba el inventario del activo,
            // inflando utilidad e inventario contable en cada venta.
            // origen_valor 'COSTO' se calcula en FacturacionContableService a
            // partir de movimientos_inventario (costo promedio al momento de la
            // venta). omitir_si_cero (default true en la tabla) se deja así para
            // no generar el par de líneas cuando el grupo no tuvo salida de
            // inventario (p. ej. productos con afecta_inventario = false).
            [
                'orden' => 6,
                'tipo_movimiento' => 'DEBITO',
                'configuracion_clave' => 'CUENTA_COSTO_VENTAS',
                'origen_valor' => 'COSTO',
                'descripcion' => 'Costo de ventas'
            ],

            [
                'orden' => 7,
                'tipo_movimiento' => 'CREDITO',
                'configuracion_clave' => 'CUENTA_INVENTARIO',
                'origen_valor' => 'COSTO',
                'descripcion' => 'Salida de inventario por venta'
            ]

        ]);

        // Nota crédito: reversa una venta (total o parcialmente). Es el
        // espejo exacto de VENTA_CONTADO — mismas cuentas, tipo_movimiento
        // invertido — para que NotaFacturaService pueda reutilizar
        // FacturacionContableService::calcularDesglose()/repartirPagosPorGrupo()
        // sin duplicar la lógica de cálculo fiscal ni de reparto de pagos.
        $notaCredito = ProcesoContable::where('codigo', 'NOTA_CREDITO')->firstOrFail();
        $this->aplicar($notaCredito, [
            [
                'orden' => 1,
                'tipo_movimiento' => 'CREDITO',
                'configuracion_clave' => 'CUENTA_CAJA',
                'origen_valor' => 'CUENTA_CAJA',
                'descripcion' => 'Devolución en efectivo'
            ],
            [
                'orden' => 2,
                'tipo_movimiento' => 'CREDITO',
                'configuracion_clave' => 'CUENTA_BANCO',
                'origen_valor' => 'CUENTA_BANCO',
                'descripcion' => 'Devolución a banco/digital'
            ],
            [
                'orden' => 3,
                'tipo_movimiento' => 'CREDITO',
                'configuracion_clave' => 'CUENTA_CLIENTES',
                'origen_valor' => 'CUENTA_CLIENTES',
                'descripcion' => 'Reduce la cartera del cliente'
            ],
            [
                'orden' => 4,
                'tipo_movimiento' => 'DEBITO',
                'configuracion_clave' => 'CUENTA_VENTAS',
                'origen_valor' => 'SUBTOTAL',
                'descripcion' => 'Reversa de venta'
            ],
            [
                'orden' => 5,
                'tipo_movimiento' => 'DEBITO',
                'configuracion_clave' => 'CUENTA_IVA_GENERADO',
                'origen_valor' => 'IVA',
                'descripcion' => 'Reversa de IVA generado'
            ],
            // Solo se generan si la nota marca restaura_inventario = true
            // (un producto realmente devuelto a la bodega); omitir_si_cero
            // se encarga de no crearlas cuando es una simple corrección de
            // valor sin devolución física.
            [
                'orden' => 6,
                'tipo_movimiento' => 'CREDITO',
                'configuracion_clave' => 'CUENTA_COSTO_VENTAS',
                'origen_valor' => 'COSTO',
                'descripcion' => 'Reversa de costo de ventas'
            ],
            [
                'orden' => 7,
                'tipo_movimiento' => 'DEBITO',
                'configuracion_clave' => 'CUENTA_INVENTARIO',
                'origen_valor' => 'COSTO',
                'descripcion' => 'Reingreso a inventario'
            ],
        ]);

        // Nota débito: cobro adicional sobre una venta ya facturada (p. ej.
        // corrección de precio al alza). Misma dirección que VENTA_CONTADO;
        // sin líneas de inventario porque no representa producto adicional
        // entregado, solo un mayor valor a cobrar.
        $notaDebito = ProcesoContable::where('codigo', 'NOTA_DEBITO')->firstOrFail();
        $this->aplicar($notaDebito, [
            [
                'orden' => 1,
                'tipo_movimiento' => 'DEBITO',
                'configuracion_clave' => 'CUENTA_CAJA',
                'origen_valor' => 'CUENTA_CAJA',
                'descripcion' => 'Cobro adicional en efectivo'
            ],
            [
                'orden' => 2,
                'tipo_movimiento' => 'DEBITO',
                'configuracion_clave' => 'CUENTA_BANCO',
                'origen_valor' => 'CUENTA_BANCO',
                'descripcion' => 'Cobro adicional a banco/digital'
            ],
            [
                'orden' => 3,
                'tipo_movimiento' => 'DEBITO',
                'configuracion_clave' => 'CUENTA_CLIENTES',
                'origen_valor' => 'CUENTA_CLIENTES',
                'descripcion' => 'Aumenta la cartera del cliente'
            ],
            [
                'orden' => 4,
                'tipo_movimiento' => 'CREDITO',
                'configuracion_clave' => 'CUENTA_VENTAS',
                'origen_valor' => 'SUBTOTAL',
                'descripcion' => 'Mayor valor de venta'
            ],
            [
                'orden' => 5,
                'tipo_movimiento' => 'CREDITO',
                'configuracion_clave' => 'CUENTA_IVA_GENERADO',
                'origen_valor' => 'IVA',
                'descripcion' => 'IVA generado adicional'
            ],
        ]);
    }

    private function aplicar(ProcesoContable $proceso, array $plantillas): void
    {
        foreach ($plantillas as $plantilla) {
            PlantillaContable::updateOrCreate(
                [
                    'proceso_contable_id' => $proceso->id,
                    'orden' => $plantilla['orden']
                ],
                array_merge(
                    $plantilla,
                    [
                        'proceso_contable_id' => $proceso->id,
                        'estado' => true
                    ]
                )
            );
        }
    }
}
