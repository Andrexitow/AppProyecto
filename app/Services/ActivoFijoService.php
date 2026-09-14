<?php

namespace App\Services;

use App\Models\ActivoFijo;
use App\Models\ComprobanteContable;
use App\Models\ConfiguracionContable;
use App\Models\CuentaContable;
use App\Models\DepreciacionActivoFijo;
use App\Models\MovimientoContable;
use App\Models\ProcesoContable;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Alta, depreciación mensual en línea recta y baja de activos fijos,
 * contabilizadas con el mismo motor de comprobantes que Tesorería/Compras
 * (ProcesoContable + TipoDocumentoContable para la numeración consecutiva).
 */
class ActivoFijoService
{
    public function registrar(array $datos, int $usuarioId): ActivoFijo
    {
        return DB::transaction(function () use ($datos, $usuarioId) {
            $categoria = $datos['categoria'];
            if (!isset(ActivoFijo::CATEGORIAS[$categoria])) {
                throw ValidationException::withMessages(['categoria' => 'Categoría de activo fijo no reconocida.']);
            }
            $mapa = ActivoFijo::CATEGORIAS[$categoria];
            $cuentaActivo = CuentaContable::where('codigo', $mapa['activo'])->firstOrFail();
            $cuentaDepreciacion = CuentaContable::where('codigo', $mapa['depreciacion'])->firstOrFail();
            $cuentaGasto = CuentaContable::where('codigo', $mapa['gasto'])->firstOrFail();
            $contrapartida = CuentaContable::where('estado', true)->where('permite_movimientos', true)->findOrFail($datos['cuenta_contrapartida_id']);

            if ((float) $datos['valor_adquisicion'] <= 0) {
                throw ValidationException::withMessages(['valor_adquisicion' => 'El valor de adquisición debe ser mayor a cero.']);
            }
            if ((float) ($datos['valor_residual'] ?? 0) >= (float) $datos['valor_adquisicion']) {
                throw ValidationException::withMessages(['valor_residual' => 'El valor de salvamento debe ser menor al valor de adquisición.']);
            }
            if ((int) $datos['vida_util_meses'] <= 0) {
                throw ValidationException::withMessages(['vida_util_meses' => 'La vida útil debe ser mayor a cero meses.']);
            }

            $comprobante = $this->crearComprobante('ALTA_ACTIVO_FIJO', 'ALTA_ACTIVO_FIJO', $datos['fecha_adquisicion'], $usuarioId,
                'Alta de activo fijo: ' . $datos['nombre']);
            $this->movimiento($comprobante, $cuentaActivo->id, (float) $datos['valor_adquisicion'], 0, $datos['tercero_id'] ?? null, 'Alta: ' . $datos['nombre']);
            $this->movimiento($comprobante, $contrapartida->id, 0, (float) $datos['valor_adquisicion'], $datos['tercero_id'] ?? null, 'Alta: ' . $datos['nombre']);
            $comprobante->update(['estado' => 'CONTABILIZADO']);

            return ActivoFijo::create([
                'codigo' => $this->siguienteCodigo(),
                'nombre' => $datos['nombre'],
                'descripcion' => $datos['descripcion'] ?? null,
                'categoria' => $categoria,
                'cuenta_activo_id' => $cuentaActivo->id,
                'cuenta_depreciacion_id' => $cuentaDepreciacion->id,
                'cuenta_gasto_id' => $cuentaGasto->id,
                'fecha_adquisicion' => $datos['fecha_adquisicion'],
                'valor_adquisicion' => $datos['valor_adquisicion'],
                'valor_residual' => $datos['valor_residual'] ?? 0,
                'vida_util_meses' => $datos['vida_util_meses'],
                'depreciacion_acumulada' => 0,
                'tercero_id' => $datos['tercero_id'] ?? null,
                'comprobante_alta_id' => $comprobante->id,
                'estado' => 'activo',
                'observaciones' => $datos['observaciones'] ?? null,
            ]);
        });
    }

    /**
     * Corre la depreciación de línea recta de TODOS los activos activos que
     * aún no se hayan depreciado en $periodo ('YYYY-MM'). Es idempotente: un
     * activo ya depreciado ese período (o totalmente depreciado, o dado de
     * baja, o adquirido después del período) simplemente se salta, así que
     * puede volver a ejecutarse sin duplicar el gasto.
     */
    public function depreciarPeriodo(string $periodo, int $usuarioId): array
    {
        return DB::transaction(function () use ($periodo, $usuarioId) {
            $finPeriodo = date('Y-m-t', strtotime($periodo . '-01'));

            $yaDepreciados = DepreciacionActivoFijo::where('periodo', $periodo)->pluck('activo_fijo_id');

            $pendientes = ActivoFijo::where('estado', 'activo')
                ->where('fecha_adquisicion', '<=', $finPeriodo)
                ->whereNotIn('id', $yaDepreciados)
                ->lockForUpdate()
                ->get()
                ->filter(fn (ActivoFijo $a) => $a->depreciacion_acumulada < $a->valor_depreciable - 0.004);

            if ($pendientes->isEmpty()) {
                return ['procesados' => 0, 'total_depreciado' => 0.0, 'activos' => []];
            }

            $comprobante = $this->crearComprobante('DEPRECIACION_ACTIVO_FIJO', 'DEPRECIACION_ACTIVO_FIJO', $finPeriodo, $usuarioId,
                'Depreciación de activos fijos — período ' . $periodo);

            $totalDepreciado = 0.0;
            $detalle = [];

            foreach ($pendientes as $activo) {
                $restante = round($activo->valor_depreciable - (float) $activo->depreciacion_acumulada, 2);
                $cuota = min($activo->depreciacion_mensual, $restante);
                if ($cuota <= 0.004) continue;

                $this->movimiento($comprobante, $activo->cuenta_gasto_id, $cuota, 0, null, 'Depreciación ' . $periodo . ': ' . $activo->nombre);
                $this->movimiento($comprobante, $activo->cuenta_depreciacion_id, 0, $cuota, null, 'Depreciación ' . $periodo . ': ' . $activo->nombre);

                DepreciacionActivoFijo::create([
                    'activo_fijo_id' => $activo->id,
                    'periodo' => $periodo,
                    'valor' => $cuota,
                    'comprobante_contable_id' => $comprobante->id,
                ]);
                $activo->increment('depreciacion_acumulada', $cuota);

                $totalDepreciado += $cuota;
                $detalle[] = ['activo' => $activo->nombre, 'valor' => $cuota];
            }

            if ($totalDepreciado <= 0) {
                // Nada realmente depreciable (todos ya llegaron a su valor de
                // salvamento con cuotas de $0 por redondeo): no dejamos un
                // comprobante vacío en el consecutivo.
                $comprobante->delete();
                return ['procesados' => 0, 'total_depreciado' => 0.0, 'activos' => []];
            }

            $comprobante->update(['estado' => 'CONTABILIZADO']);

            return ['procesados' => count($detalle), 'total_depreciado' => round($totalDepreciado, 2), 'activos' => $detalle];
        });
    }

    public function darDeBaja(ActivoFijo $activo, string $fecha, int $usuarioId, ?string $motivo = null): ActivoFijo
    {
        return DB::transaction(function () use ($activo, $fecha, $usuarioId, $motivo) {
            $activo = ActivoFijo::lockForUpdate()->findOrFail($activo->id);
            if ($activo->estado === 'de_baja') {
                throw ValidationException::withMessages(['activo' => 'Este activo ya fue dado de baja.']);
            }

            $depreciacionAcumulada = round((float) $activo->depreciacion_acumulada, 2);
            $valorLibros = round((float) $activo->valor_adquisicion - $depreciacionAcumulada, 2);

            $comprobante = $this->crearComprobante('BAJA_ACTIVO_FIJO', 'BAJA_ACTIVO_FIJO', $fecha, $usuarioId,
                'Baja de activo fijo: ' . $activo->nombre . ($motivo ? " ({$motivo})" : ''));

            if ($depreciacionAcumulada > 0) {
                $this->movimiento($comprobante, $activo->cuenta_depreciacion_id, $depreciacionAcumulada, 0, null, 'Baja: reverso depreciación acumulada');
            }
            if ($valorLibros > 0.004) {
                $this->movimiento($comprobante, $this->configuracion('CUENTA_PERDIDA_BAJA_ACTIVOS')->cuenta_contable_id, $valorLibros, 0, null, 'Pérdida en baja: ' . $activo->nombre);
            }
            $this->movimiento($comprobante, $activo->cuenta_activo_id, 0, (float) $activo->valor_adquisicion, null, 'Baja: ' . $activo->nombre);
            $comprobante->update(['estado' => 'CONTABILIZADO']);

            $activo->update([
                'estado' => 'de_baja',
                'fecha_baja' => $fecha,
                'comprobante_baja_id' => $comprobante->id,
            ]);

            return $activo->fresh();
        });
    }

    private function configuracion(string $clave): ConfiguracionContable
    {
        $c = ConfiguracionContable::where('clave', $clave)->where('estado', true)->first();
        if (!$c?->cuenta_contable_id) throw ValidationException::withMessages(['contabilidad' => "La configuración contable {$clave} no tiene una cuenta asignada."]);
        return $c;
    }

    private function siguienteCodigo(): string
    {
        $ultimo = ActivoFijo::orderByDesc('id')->value('id') ?? 0;
        return 'BIEN-' . str_pad((string) ($ultimo + 1), 4, '0', STR_PAD_LEFT);
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
