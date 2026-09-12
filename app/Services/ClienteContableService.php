<?php

namespace App\Services;

use App\Models\ComprobanteContable;
use App\Models\ConfiguracionContable;
use App\Models\Factura;
use App\Models\MetodoPagoContable;
use App\Models\MovimientoContable;
use App\Models\PagoCliente;
use App\Models\ProcesoContable;
use Illuminate\Validation\ValidationException;

/**
 * Cuentas por cobrar de clientes: abonos y saldos de ventas a crédito.
 * Espejo de CompraContableService, pero en la dirección contraria
 * (aquí se cobra: débito Caja/Banco, crédito Clientes).
 */
class ClienteContableService
{
    public function registrarAbono(Factura $factura, array $datos, int $usuarioId): PagoCliente
    {
        if ($factura->estado_pago === 'pagada') {
            throw ValidationException::withMessages(['factura' => 'Esta factura ya está totalmente pagada.']);
        }

        $this->actualizarSaldo($factura);
        $valor = (float) $datos['valor'];
        if ($valor > (float) $factura->saldo_pendiente + .01) {
            throw ValidationException::withMessages(['valor' => 'El abono supera el saldo pendiente de la factura.']);
        }

        $metodo = MetodoPagoContable::whereKey($datos['metodo_pago_contable_id'])->where('estado', true)->firstOrFail();
        if ($metodo->metodo_pago === 'credito') {
            throw ValidationException::withMessages(['metodo_pago_contable_id' => 'Crédito no es un medio de pago válido para recibir un abono.']);
        }
        $this->configuracion($metodo->configuracion_clave);

        $pago = PagoCliente::create([
            'cliente_id' => $factura->cliente_id,
            'fecha' => $datos['fecha'],
            'valor' => $valor,
            'metodo_pago_contable_id' => $metodo->id,
            'referencia' => $datos['referencia'] ?? null,
            'origen' => 'abono',
            'estado' => 'registrado',
            'usuario_id' => $usuarioId,
        ]);
        $pago->aplicaciones()->create(['factura_id' => $factura->id, 'valor' => $valor]);

        $movimientos = [];
        $this->agregar($movimientos, $metodo->configuracion_clave, $valor, 0, null, 'Recaudo de cartera');
        $this->agregar($movimientos, 'CUENTA_CLIENTES', 0, $valor, $factura->cliente_id, 'Abono a cuenta por cobrar');

        $comprobante = $this->crearComprobante('RECAUDO_CLIENTE', 'PAGO_CLIENTE', $pago->id, $datos['fecha'], $usuarioId, "Abono a {$factura->numero_factura}");
        $this->guardarMovimientos($comprobante, $movimientos);
        $pago->update(['comprobante_contable_id' => $comprobante->id]);
        $this->actualizarSaldo($factura);

        return $pago;
    }

    public function actualizarSaldo(Factura $factura): void
    {
        $pagado = (float) $factura->pagosCliente()->whereHas('pago', fn ($q) => $q->where('estado', 'registrado'))->sum('valor');
        $saldo = max(0, round((float) $factura->total - $pagado, 2));
        $factura->update([
            'total_pagado' => $pagado,
            'saldo_pendiente' => $saldo,
            'estado_pago' => $saldo <= .009 ? 'pagada' : ($pagado > 0 ? 'parcialmente_pagada' : 'pendiente'),
        ]);
    }

    private function configuracion(string $clave): ConfiguracionContable
    {
        $c = ConfiguracionContable::with('cuenta')->where('clave', $clave)->where('estado', true)->first();
        if (!$c?->cuenta) {
            throw ValidationException::withMessages(['contabilidad' => "La configuración contable {$clave} no tiene una cuenta asignada."]);
        }

        return $c;
    }

    private function agregar(array &$lineas, string $clave, float $debito, float $credito, ?int $terceroId, string $detalle): void
    {
        if (round($debito + $credito, 2) <= 0) return;
        if (!$clave) throw ValidationException::withMessages(['pagos' => 'Un medio de pago no tiene cuenta contable configurada.']);
        $lineas[] = compact('clave', 'debito', 'credito', 'terceroId', 'detalle');
    }

    private function crearComprobante(string $procesoCodigo, string $origen, int $origenId, string $fecha, int $usuarioId, string $observacion): ComprobanteContable
    {
        $proceso = ProcesoContable::with('tipoDocumento')->where('codigo', $procesoCodigo)->where('estado', true)->firstOrFail();
        $tipo = $proceso->tipoDocumento;
        if (!$tipo) throw ValidationException::withMessages(['contabilidad' => "El proceso {$procesoCodigo} no tiene tipo de comprobante."]);
        $tipo = $tipo->newQuery()->lockForUpdate()->findOrFail($tipo->id);
        $numero = $tipo->prefijo . str_pad($tipo->consecutivo, $tipo->longitud, '0', STR_PAD_LEFT);
        $comprobante = ComprobanteContable::create([
            'tipo_documento_contable_id' => $tipo->id,
            'proceso_contable_id' => $proceso->id,
            'numero' => $numero,
            'fecha' => $fecha,
            'observacion' => $observacion,
            'usuario_id' => $usuarioId,
            'documento_origen' => $origen,
            'documento_origen_id' => $origenId,
            'estado' => 'BORRADOR',
        ]);
        $tipo->increment('consecutivo');

        return $comprobante;
    }

    private function guardarMovimientos(ComprobanteContable $comprobante, array $lineas): void
    {
        $debitos = 0;
        $creditos = 0;
        foreach ($lineas as $linea) {
            $cuenta = $this->configuracion($linea['clave'])->cuenta;
            MovimientoContable::create([
                'comprobante_contable_id' => $comprobante->id,
                'cuenta_contable_id' => $cuenta->id,
                'tercero_id' => $linea['terceroId'],
                'referencia' => $comprobante->documento_origen,
                'detalle' => $linea['detalle'],
                'debito' => $linea['debito'],
                'credito' => $linea['credito'],
            ]);
            $debitos += $linea['debito'];
            $creditos += $linea['credito'];
        }
        if (round($debitos, 2) !== round($creditos, 2)) {
            throw ValidationException::withMessages(['contabilidad' => 'El comprobante de abono quedó descuadrado.']);
        }
        $comprobante->update(['estado' => 'CONTABILIZADO']);
    }
}
