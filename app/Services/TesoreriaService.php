<?php

namespace App\Services;

use App\Models\ComprobanteContable;
use App\Models\CuentaContable;
use App\Models\CuentaTesoreria;
use App\Models\MovimientoContable;
use App\Models\MovimientoTesoreria;
use App\Models\ProcesoContable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Ingresos, egresos y transferencias entre cuentas de tesorería (caja/bancos),
 * contabilizados con el mismo motor de comprobantes del resto del sistema.
 */
class TesoreriaService
{
    private const ESTADOS_VALIDOS = ['REGISTRADO', 'CONTABILIZADO'];

    /**
     * Saldo actual (o a una fecha de corte) de una cuenta de tesorería, según su cuenta contable.
     *
     * @param  bool  $paraActualizar  true cuando se va a decidir si hay saldo
     *         suficiente para un egreso/transferencia: convierte la lectura en un
     *         SELECT ... FOR UPDATE para que, combinada con el lockForUpdate() de
     *         la fila de CuentaTesoreria, lea de verdad los movimientos ya
     *         comprometidos por otra transacción que se soltó justo antes — no la
     *         foto vieja que un SELECT normal podría devolver bajo REPEATABLE READ.
     *         Nunca se activa en una simple consulta de saldo para mostrar en pantalla.
     */
    public function saldo(CuentaTesoreria $cuenta, ?string $hasta = null, bool $paraActualizar = false): float
    {
        $query = DB::table('movimientos_contables as m')
            ->join('comprobantes_contables as c', 'c.id', '=', 'm.comprobante_contable_id')
            ->where('m.cuenta_contable_id', $cuenta->cuenta_contable_id)
            ->whereIn('c.estado', self::ESTADOS_VALIDOS);

        if ($paraActualizar) {
            $query->lockForUpdate();
        }

        if ($hasta) {
            $query->where('c.fecha', '<=', $hasta);
        }

        $fila = $query->selectRaw('COALESCE(SUM(m.debito),0) as db, COALESCE(SUM(m.credito),0) as cr')->first();

        return round((float) $fila->db - (float) $fila->cr, 2);
    }

    public function registrarIngreso(array $datos, int $usuarioId): MovimientoTesoreria
    {
        return DB::transaction(function () use ($datos, $usuarioId) {
            $cuenta = CuentaTesoreria::where('activa', true)->findOrFail($datos['cuenta_tesoreria_id']);
            $contrapartida = CuentaContable::where('estado', true)->where('permite_movimientos', true)->findOrFail($datos['cuenta_contrapartida_id']);

            $comprobante = $this->crearComprobante('INGRESO_TESORERIA', 'INGRESO_TESORERIA', $datos['fecha'], $usuarioId, $datos['descripcion']);
            $this->movimiento($comprobante, $cuenta->cuenta_contable_id, (float) $datos['valor'], 0, $datos['tercero_id'] ?? null, $datos['descripcion']);
            $this->movimiento($comprobante, $contrapartida->id, 0, (float) $datos['valor'], $datos['tercero_id'] ?? null, $datos['descripcion']);
            $comprobante->update(['estado' => 'CONTABILIZADO']);

            return MovimientoTesoreria::create([
                'cuenta_tesoreria_id' => $cuenta->id,
                'tipo' => 'INGRESO',
                'valor' => $datos['valor'],
                'fecha' => $datos['fecha'],
                'descripcion' => $datos['descripcion'],
                'tercero_id' => $datos['tercero_id'] ?? null,
                'cuenta_contrapartida_id' => $contrapartida->id,
                'comprobante_contable_id' => $comprobante->id,
                'usuario_id' => $usuarioId,
            ]);
        });
    }

    public function registrarEgreso(array $datos, int $usuarioId): MovimientoTesoreria
    {
        return DB::transaction(function () use ($datos, $usuarioId) {
            // lockForUpdate() serializa dos egresos concurrentes sobre la MISMA
            // cuenta: sin esto, ambos podían leer el mismo saldo "suficiente" y
            // pasar la validación en paralelo, dejando la cuenta en negativo.
            $cuenta = CuentaTesoreria::where('activa', true)->lockForUpdate()->findOrFail($datos['cuenta_tesoreria_id']);
            $contrapartida = CuentaContable::where('estado', true)->where('permite_movimientos', true)->findOrFail($datos['cuenta_contrapartida_id']);

            $saldoActual = $this->saldo($cuenta, null, true);
            if ((float) $datos['valor'] > $saldoActual + 0.01) {
                throw ValidationException::withMessages(['valor' => 'El egreso supera el saldo disponible de la cuenta (' . number_format($saldoActual, 2) . ').']);
            }

            $comprobante = $this->crearComprobante('EGRESO_TESORERIA', 'EGRESO_TESORERIA', $datos['fecha'], $usuarioId, $datos['descripcion']);
            $this->movimiento($comprobante, $contrapartida->id, (float) $datos['valor'], 0, $datos['tercero_id'] ?? null, $datos['descripcion']);
            $this->movimiento($comprobante, $cuenta->cuenta_contable_id, 0, (float) $datos['valor'], $datos['tercero_id'] ?? null, $datos['descripcion']);
            $comprobante->update(['estado' => 'CONTABILIZADO']);

            return MovimientoTesoreria::create([
                'cuenta_tesoreria_id' => $cuenta->id,
                'tipo' => 'EGRESO',
                'valor' => $datos['valor'],
                'fecha' => $datos['fecha'],
                'descripcion' => $datos['descripcion'],
                'tercero_id' => $datos['tercero_id'] ?? null,
                'cuenta_contrapartida_id' => $contrapartida->id,
                'comprobante_contable_id' => $comprobante->id,
                'usuario_id' => $usuarioId,
            ]);
        });
    }

    public function registrarTransferencia(array $datos, int $usuarioId): array
    {
        return DB::transaction(function () use ($datos, $usuarioId) {
            if ((int) $datos['cuenta_origen_id'] === (int) $datos['cuenta_destino_id']) {
                throw ValidationException::withMessages(['cuenta_destino_id' => 'La cuenta de destino debe ser diferente a la de origen.']);
            }

            // Se bloquean ambas cuentas de una sola vez, ordenadas por id (siempre
            // el mismo orden, para no arriesgar un deadlock con otra transferencia
            // cruzada concurrente) — por la misma razón que en registrarEgreso():
            // sin esto, dos operaciones simultáneas sobre la misma cuenta podían
            // validar contra el mismo saldo "antes" de que la otra lo descontara.
            $idsOrdenados = collect([(int) $datos['cuenta_origen_id'], (int) $datos['cuenta_destino_id']])->sort()->values();
            $cuentas = CuentaTesoreria::where('activa', true)->whereIn('id', $idsOrdenados)->lockForUpdate()->orderBy('id')->get()->keyBy('id');
            if ($cuentas->count() < 2) {
                throw ValidationException::withMessages(['cuenta_destino_id' => 'Una de las cuentas seleccionadas no existe o no está activa.']);
            }
            $origen = $cuentas->get((int) $datos['cuenta_origen_id']);
            $destino = $cuentas->get((int) $datos['cuenta_destino_id']);

            $saldoActual = $this->saldo($origen, null, true);
            if ((float) $datos['valor'] > $saldoActual + 0.01) {
                throw ValidationException::withMessages(['valor' => 'La transferencia supera el saldo disponible en ' . $origen->nombre . ' (' . number_format($saldoActual, 2) . ').']);
            }

            $descripcion = $datos['descripcion'] ?? ('Transferencia de ' . $origen->nombre . ' a ' . $destino->nombre);
            $comprobante = $this->crearComprobante('TRANSFERENCIA_TESORERIA', 'TRANSFERENCIA_TESORERIA', $datos['fecha'], $usuarioId, $descripcion);
            $this->movimiento($comprobante, $destino->cuenta_contable_id, (float) $datos['valor'], 0, null, $descripcion);
            $this->movimiento($comprobante, $origen->cuenta_contable_id, 0, (float) $datos['valor'], null, $descripcion);
            $comprobante->update(['estado' => 'CONTABILIZADO']);

            $salida = MovimientoTesoreria::create([
                'cuenta_tesoreria_id' => $origen->id,
                'tipo' => 'TRANSFERENCIA_SALIDA',
                'valor' => $datos['valor'],
                'fecha' => $datos['fecha'],
                'descripcion' => $descripcion,
                'cuenta_tesoreria_relacionada_id' => $destino->id,
                'comprobante_contable_id' => $comprobante->id,
                'usuario_id' => $usuarioId,
            ]);
            $entrada = MovimientoTesoreria::create([
                'cuenta_tesoreria_id' => $destino->id,
                'tipo' => 'TRANSFERENCIA_ENTRADA',
                'valor' => $datos['valor'],
                'fecha' => $datos['fecha'],
                'descripcion' => $descripcion,
                'cuenta_tesoreria_relacionada_id' => $origen->id,
                'comprobante_contable_id' => $comprobante->id,
                'usuario_id' => $usuarioId,
            ]);

            return [$salida, $entrada];
        });
    }

    private function crearComprobante(string $procesoCodigo, string $origen, string $fecha, int $usuarioId, string $observacion): ComprobanteContable
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
            'documento_origen_id' => null,
            'estado' => 'BORRADOR',
        ]);
        $tipo->increment('consecutivo');

        return $comprobante;
    }

    private function movimiento(ComprobanteContable $comprobante, int $cuentaContableId, float $debito, float $credito, ?int $terceroId, string $detalle): void
    {
        if (round($debito + $credito, 2) <= 0) return;
        MovimientoContable::create([
            'comprobante_contable_id' => $comprobante->id,
            'cuenta_contable_id' => $cuentaContableId,
            'tercero_id' => $terceroId,
            'referencia' => $comprobante->documento_origen,
            'detalle' => $detalle,
            'debito' => $debito,
            'credito' => $credito,
        ]);
    }
}
