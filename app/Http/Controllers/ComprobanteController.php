<?php

namespace App\Http\Controllers;

use App\Models\ComprobanteContable;
use App\Models\MovimientoContable;
use App\Models\TipoDocumentoContable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ComprobanteController extends Controller
{
    public function index()
    {
        return view('comprobantes.index');
    }

    /**
     * Listado agrupado por documento origen (Factura POS)
     */
    public function data()
    {
        $documentos = DB::table('comprobantes_contables')
            ->join(
                'tipos_documento_contable',
                'tipos_documento_contable.id',
                '=',
                'comprobantes_contables.tipo_documento_contable_id'
            )
            ->leftJoin(
                'users',
                'users.id',
                '=',
                'comprobantes_contables.usuario_id'
            )
            ->select(
                DB::raw('MIN(comprobantes_contables.id) as id'),
                'comprobantes_contables.documento_origen',
                DB::raw('MAX(comprobantes_contables.fecha) as fecha'),
                DB::raw('MAX(comprobantes_contables.estado) as estado'),
                DB::raw('MAX(comprobantes_contables.observacion) as observacion'),
                DB::raw('MAX(tipos_documento_contable.nombre) as tipo_nombre'),
                DB::raw('MAX(tipos_documento_contable.prefijo) as tipo_prefijo'),
                DB::raw('MAX(users.name) as usuario_nombre')
            )
            ->groupBy('comprobantes_contables.documento_origen')
            ->orderByDesc(DB::raw('MIN(comprobantes_contables.id)'))
            ->get();

        $data = [];

        foreach ($documentos as $doc) {

            $ids = DB::table('comprobantes_contables')
                ->where('documento_origen', $doc->documento_origen)
                ->pluck('id');

            $totales = DB::table('movimientos_contables')
                ->whereIn('comprobante_contable_id', $ids)
                ->selectRaw('SUM(debito) as debitos, SUM(credito) as creditos')
                ->first();

            $data[] = [
                'id' => $doc->id,
                'tipo' => $doc->tipo_nombre,
                'prefijo' => $doc->tipo_prefijo ?? '',
                'numero' => Str::startsWith((string) $doc->documento_origen, 'MANUAL-')
                    ? Str::afterLast((string) $doc->documento_origen, '-')
                    : $doc->documento_origen,
                'fecha' => $doc->fecha,
                'observaciones' => $doc->observacion,
                'debito' => (float) ($totales->debitos ?? 0),
                'credito' => (float) ($totales->creditos ?? 0),
                'verificado' => $doc->estado === 'CONTABILIZADO',
                'anulado' => $doc->estado === 'ANULADO',
                'manual' => $this->esManual($doc->documento_origen),
                'documento_origen' => $doc->documento_origen,
                'usuario' => $doc->usuario_nombre ?? '-',
            ];
        }

        return response()->json([
            'data' => $data
        ]);
    }

    /**
     * Detalle de una factura (todos los comprobantes del documento origen)
     */
    public function show($id)
    {
        $principal = DB::table('comprobantes_contables')
            ->where('id', $id)
            ->first();

        if (!$principal) {
            return response()->json([
                'message' => 'Documento no encontrado'
            ], 404);
        }

        $comprobantes = DB::table('comprobantes_contables')
            ->join(
                'tipos_documento_contable',
                'tipos_documento_contable.id',
                '=',
                'comprobantes_contables.tipo_documento_contable_id'
            )
            ->where(
                'comprobantes_contables.documento_origen',
                $principal->documento_origen
            )
            ->select(
                'comprobantes_contables.*',
                'tipos_documento_contable.nombre as tipo_nombre',
                'tipos_documento_contable.prefijo as tipo_prefijo'
            )
            ->orderBy('comprobantes_contables.id')
            ->get();

        $detalle = [];

        foreach ($comprobantes as $comprobante) {

            $movimientos = DB::table('movimientos_contables')
                ->join(
                    'cuentas_contables',
                    'cuentas_contables.id',
                    '=',
                    'movimientos_contables.cuenta_contable_id'
                )
                ->where(
                    'movimientos_contables.comprobante_contable_id',
                    $comprobante->id
                )
                ->select(
                    'cuentas_contables.codigo as cuenta_codigo',
                    'cuentas_contables.nombre as cuenta_nombre',
                    'movimientos_contables.detalle',
                    'movimientos_contables.debito',
                    'movimientos_contables.credito'
                )
                ->orderBy('movimientos_contables.id')
                ->get();

            $detalle[] = [
                'id' => $comprobante->id,
                'numero' => $comprobante->numero,
                'grupo' => $comprobante->referencia_grupo,
                'observacion' => $comprobante->observacion,
                'movimientos' => $movimientos
            ];
        }

        return response()->json([
            'documento_origen' => $principal->documento_origen,
            'fecha' => $principal->fecha,
            'estado' => $principal->estado,
            'comprobantes' => $detalle
        ]);
    }

    public function edit(ComprobanteContable $comprobante)
    {
        $this->asegurarManualActivo($comprobante);
        $comprobante->load(['tipoDocumento', 'movimientos']);

        return response()->json([
            'id' => $comprobante->id,
            'tipo' => $comprobante->tipoDocumento->nombre,
            'prefijo' => $comprobante->tipoDocumento->prefijo,
            'numero' => $comprobante->numero,
            'fecha' => $comprobante->fecha->format('Y-m-d'),
            'observaciones' => $comprobante->observacion,
            'items' => $comprobante->movimientos->map(fn ($movimiento) => [
                'cuenta_id' => $movimiento->cuenta_contable_id,
                'tercero_id' => $movimiento->tercero_id,
                'detalle' => $movimiento->detalle,
                'debito' => (float) $movimiento->debito,
                'credito' => (float) $movimiento->credito,
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $datos = $this->validarComprobante($request);

        $comprobante = DB::transaction(function () use ($datos) {
            $tipo = $this->resolverTipo($datos['tipo'], $datos['prefijo']);
            $numero = $this->siguienteNumero($tipo);
            $tipo->increment('consecutivo');
            $comprobante = ComprobanteContable::create([
                'tipo_documento_contable_id' => $tipo->id,
                'numero' => $numero,
                'fecha' => $datos['fecha'],
                'observacion' => $datos['observaciones'] ?? null,
                'usuario_id' => Auth::id(),
                'documento_origen' => 'MANUAL-' . $tipo->id . '-' . $numero,
                'referencia_grupo' => 'Comprobante manual',
                'estado' => 'CONTABILIZADO',
            ]);
            $this->guardarMovimientos($comprobante, $datos['items']);

            return $comprobante;
        });

        return response()->json(['message' => 'Comprobante creado correctamente.', 'id' => $comprobante->id], 201);
    }

    public function update(Request $request, ComprobanteContable $comprobante)
    {
        $this->asegurarManualActivo($comprobante);
        $datos = $this->validarComprobante($request);

        DB::transaction(function () use ($comprobante, $datos) {
            $tipo = $this->resolverTipo($datos['tipo'], $datos['prefijo']);
            $comprobante->update([
                'tipo_documento_contable_id' => $tipo->id,
                'fecha' => $datos['fecha'],
                'observacion' => $datos['observaciones'] ?? null,
            ]);
            $comprobante->movimientos()->delete();
            $this->guardarMovimientos($comprobante, $datos['items']);
        });

        return response()->json(['message' => 'Comprobante actualizado correctamente.']);
    }

    public function anular(ComprobanteContable $comprobante)
    {
        $this->asegurarManual($comprobante);
        if ($comprobante->estado === 'ANULADO') {
            return response()->json(['message' => 'El comprobante ya está anulado.'], 422);
        }
        $comprobante->update(['estado' => 'ANULADO']);

        return response()->json(['message' => 'Comprobante anulado correctamente.']);
    }

    public function revertir(ComprobanteContable $comprobante)
    {
        $this->asegurarManual($comprobante);
        if ($comprobante->estado !== 'ANULADO') {
            return response()->json(['message' => 'Solo puede revertirse un comprobante anulado.'], 422);
        }
        $comprobante->update(['estado' => 'CONTABILIZADO']);

        return response()->json(['message' => 'Anulación revertida correctamente.']);
    }

    public function destroy(ComprobanteContable $comprobante)
    {
        $this->asegurarManualActivo($comprobante);
        $comprobante->delete();

        return response()->json(['message' => 'Comprobante eliminado correctamente.']);
    }

    private function validarComprobante(Request $request): array
    {
        $datos = $request->validate([
            'tipo' => ['required', 'string', 'max:100'],
            'prefijo' => ['required', 'string', 'max:10'],
            'fecha' => ['required', 'date'],
            'observaciones' => ['nullable', 'string', 'max:2000'],
            'items' => ['required', 'array', 'min:2'],
            'items.*.cuenta_id' => ['required', 'integer', 'exists:cuentas_contables,id'],
            'items.*.tercero_id' => ['nullable', 'integer', 'exists:terceros,id'],
            'items.*.detalle' => ['nullable', 'string', 'max:1000'],
            'items.*.debito' => ['nullable', 'numeric', 'min:0'],
            'items.*.credito' => ['nullable', 'numeric', 'min:0'],
        ]);

        $debitos = collect($datos['items'])->sum(fn ($item) => (float) ($item['debito'] ?? 0));
        $creditos = collect($datos['items'])->sum(fn ($item) => (float) ($item['credito'] ?? 0));
        foreach ($datos['items'] as $indice => $item) {
            $debito = (float) ($item['debito'] ?? 0);
            $credito = (float) ($item['credito'] ?? 0);
            if (($debito <= 0 && $credito <= 0) || ($debito > 0 && $credito > 0)) {
                throw ValidationException::withMessages(["items.$indice" => 'Cada movimiento debe tener débito o crédito, pero no ambos.']);
            }
        }
        if ($debitos <= 0 || round($debitos, 2) !== round($creditos, 2)) {
            throw ValidationException::withMessages(['items' => 'El comprobante debe estar cuadrado y tener valores mayores a cero.']);
        }

        return $datos;
    }

    private function guardarMovimientos(ComprobanteContable $comprobante, array $items): void
    {
        foreach ($items as $item) {
            MovimientoContable::create([
                'comprobante_contable_id' => $comprobante->id,
                'cuenta_contable_id' => $item['cuenta_id'],
                'tercero_id' => $item['tercero_id'] ?? null,
                'detalle' => $item['detalle'] ?? null,
                'debito' => $item['debito'] ?? 0,
                'credito' => $item['credito'] ?? 0,
            ]);
        }
    }

    private function resolverTipo(string $nombre, string $prefijo): TipoDocumentoContable
    {
        $tipo = TipoDocumentoContable::where('nombre', $nombre)->first();
        if ($tipo) {
            return $tipo;
        }

        return TipoDocumentoContable::create([
            'codigo' => 'MAN-' . strtoupper(Str::random(6)),
            'nombre' => $nombre,
            'prefijo' => $prefijo,
            'consecutivo' => 1,
            'longitud' => 6,
            'estado' => true,
        ]);
    }

    private function siguienteNumero(TipoDocumentoContable $tipo): string
    {
        return str_pad((string) $tipo->consecutivo, $tipo->longitud ?: 6, '0', STR_PAD_LEFT);
    }

    private function esManual(?string $origen): bool
    {
        return Str::startsWith((string) $origen, 'MANUAL-');
    }

    private function asegurarManual(ComprobanteContable $comprobante): void
    {
        abort_unless($this->esManual($comprobante->documento_origen), 422, 'Los comprobantes automáticos se administran desde su documento de origen.');
    }

    private function asegurarManualActivo(ComprobanteContable $comprobante): void
    {
        $this->asegurarManual($comprobante);
        abort_if($comprobante->estado !== 'CONTABILIZADO', 422, 'Solo se pueden editar o eliminar comprobantes manuales activos.');
    }
}
