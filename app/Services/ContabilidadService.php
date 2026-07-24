<?php

namespace App\Services;

use App\DataTransferObjects\ContabilidadData;
use App\Models\ComprobanteContable;
use App\Models\ConfiguracionContable;
use App\Models\MovimientoContable;
use App\Models\PlantillaContable;
use App\Models\ProcesoContable;
use App\Models\TipoDocumentoContable;
use Illuminate\Support\Facades\DB;

class ContabilidadService
{
    public function procesar(string $codigoProceso, ContabilidadData $datos)
    {
        return DB::transaction(function () use ($codigoProceso, $datos) {


            // Buscar proceso
            $proceso = ProcesoContable::where('codigo', $codigoProceso)
                ->where('estado', true)
                ->firstOrFail();

            $existe = ComprobanteContable::where('documento_origen', $datos->documento)
                ->where('documento_origen_id', $datos->documentoId)
                ->where('estado', 'CONTABILIZADO')
                ->exists();

            if ($existe) {
                throw new \Exception(
                    "El documento {$datos->documento} ya está contabilizado."
                );
            }

            // Obtener plantilla
            $plantillas = PlantillaContable::where(
                'proceso_contable_id',
                $proceso->id
            )
                ->where('estado', true)
                ->orderBy('orden')
                ->get();

            if ($plantillas->isEmpty()) {
                throw new \Exception(
                    "El proceso {$codigoProceso} no tiene plantilla contable."
                );
            }

            $comprobante = $this->crearComprobante(
                $proceso,
                $datos
            );

            $this->crearMovimientos(
                $comprobante,
                $plantillas,
                $datos
            );

            $this->validarPartidaDoble($comprobante);

            $comprobante->update([
                'estado' => 'CONTABILIZADO'
            ]);

            return $comprobante;
        });
    }

    private function obtenerValor(
        string $origen,
        ContabilidadData $datos,
        ?float $valorFijo
    ): float {

        if ($origen === 'VALOR_FIJO') {
            return $valorFijo ?? 0;
        }

        return $datos->valor($origen);
    }

    private function obtenerConfiguracion(string $clave): ConfiguracionContable
    {
        $configuracion = ConfiguracionContable::where('clave', $clave)
            ->where('estado', true)
            ->first();

        if (!$configuracion) {
            throw new \Exception("No existe la configuración {$clave}");
        }

        if (!$configuracion->cuenta) {
            throw new \Exception("La configuración {$clave} no tiene cuenta asignada.");
        }

        return $configuracion;
    }

    private function crearComprobante(
        ProcesoContable $proceso,
        ContabilidadData $datos
    ): ComprobanteContable {

        $tipo = $proceso->tipoDocumento()->first();

        if (!$tipo) {
            throw new \Exception(
                "El proceso {$proceso->codigo} no tiene tipo de documento contable asignado."
            );
        }

        $numero = $tipo->prefijo .
            str_pad(
                $tipo->consecutivo,
                $tipo->longitud,
                '0',
                STR_PAD_LEFT
            );

        $numero = $tipo->prefijo .
            str_pad(
                $tipo->consecutivo,
                $tipo->longitud,
                '0',
                STR_PAD_LEFT
            );

        $comprobante = ComprobanteContable::create([

            'tipo_documento_contable_id' => $tipo->id,

            'numero' => $numero,

            'fecha' => now(),

            'observacion' => $datos->observacion,

            'usuario_id' => $datos->usuarioId,

            'documento_origen' => $datos->documento,

            'documento_origen_id' => $datos->documentoId,

            'estado' => 'BORRADOR'

        ]);

        $tipo->increment('consecutivo');

        return $comprobante;
    }

    private function crearMovimientos(
        ComprobanteContable $comprobante,
        $plantillas,
        ContabilidadData $datos
    ): void {

        foreach ($plantillas as $plantilla) {

            $configuracion = $this->obtenerConfiguracion(
                $plantilla->configuracion_clave
            );

            $valor = $this->obtenerValor(
                $plantilla->origen_valor,
                $datos,
                $plantilla->valor_fijo
            );

            if ($plantilla->omitir_si_cero && $valor == 0) {
                continue;
            }

            MovimientoContable::create([

                'comprobante_contable_id' => $comprobante->id,

                'cuenta_contable_id' => $configuracion->cuenta->id,

                'tercero_id' => $plantilla->requiere_tercero
                    ? $datos->terceroId
                    : null,

                'referencia' => $datos->documento,

                'detalle' => $plantilla->descripcion,

                'debito' => $plantilla->tipo_movimiento === 'DEBITO'
                    ? $valor
                    : 0,

                'credito' => $plantilla->tipo_movimiento === 'CREDITO'
                    ? $valor
                    : 0,

            ]);
        }
    }

    private function validarPartidaDoble(
        ComprobanteContable $comprobante
    ): void {

        $debito = $comprobante
            ->movimientos()
            ->sum('debito');

        $credito = $comprobante
            ->movimientos()
            ->sum('credito');

        if (round($debito, 2) != round($credito, 2)) {

            throw new \Exception(

                "El comprobante {$comprobante->numero} está descuadrado.
            Débitos: {$debito}
            Créditos: {$credito}"

            );
        }
    }
    private function validarNaturaleza(
        MovimientoContable $movimiento
    ): void {
        $naturaleza = $movimiento->cuenta->naturaleza;

        if (
            $naturaleza === 'DEBITO'
            &&
            $movimiento->credito > 0
        ) {

            logger()->warning(

                "La cuenta {$movimiento->cuenta->codigo}
            está siendo acreditada."

            );
        }

        if (
            $naturaleza === 'CREDITO'
            &&
            $movimiento->debito > 0
        ) {

            logger()->warning(

                "La cuenta {$movimiento->cuenta->codigo}
            está siendo debitada."

            );
        }
    }
}
