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
use App\Services\AccountingService;

class ComprobanteController extends Controller
{
    public function index()
    {
        return view('comprobantes.nuevo');
    }

    /**
     * Listado agrupado por documento origen (Factura POS)
     */
    public function data()
    {
        $q=ComprobanteContable::with(['tercero','usuario','tipoDocumento'])->orderByDesc('id');
        foreach(['desde'=>'fecha','hasta'=>'fecha'] as $key=>$col){if(request($key))$key==='desde'?$q->whereDate($col,'>=',request($key)):$q->whereDate($col,'<=',request($key));}
        if(request('estado'))$q->where('estado',request('estado')); if(request('tercero'))$q->where('tercero_id',request('tercero')); if(request('tipo'))$q->where('tipo',request('tipo')); if(request('numero'))$q->where(fn($x)=>$x->where('numero','like','%'.request('numero').'%')->orWhere('prefijo','like','%'.request('numero').'%'));
        return response()->json(['data'=>$q->get()->map(fn($c)=>['id'=>$c->id,'tipo'=>$c->tipo ?: ($c->tipoDocumento?->nombre ?: 'Comprobante contable'),'prefijo'=>$c->prefijo ?: ($c->tipoDocumento?->prefijo ?: '—'),'numero'=>$c->numero ?: '—','fecha'=>$c->fecha?->format('Y-m-d'),'tercero'=>$c->tercero?->nombre_completo ?: '—','descripcion'=>$c->descripcion ?: $c->observacion ?: '—','debito'=>(float)$c->total_debito,'credito'=>(float)$c->total_credito,'estado'=>$c->estado,'usuario'=>$c->usuario?->name ?: '—'])]);

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
                'comprobantes_contables.documento_origen_id',
                DB::raw('MAX(comprobantes_contables.fecha) as fecha'),
                DB::raw('MAX(comprobantes_contables.estado) as estado'),
                DB::raw('MAX(comprobantes_contables.observacion) as observacion'),
                DB::raw('MAX(tipos_documento_contable.nombre) as tipo_nombre'),
                DB::raw('MAX(tipos_documento_contable.prefijo) as tipo_prefijo'),
                DB::raw('MAX(users.name) as usuario_nombre')
            )
            ->groupBy('comprobantes_contables.documento_origen', 'comprobantes_contables.documento_origen_id')
            ->orderByDesc(DB::raw('MIN(comprobantes_contables.id)'))
            ->get();

        $data = [];

        foreach ($documentos as $doc) {

            $idsQuery = DB::table('comprobantes_contables')->where('documento_origen', $doc->documento_origen);
            if ($doc->documento_origen_id === null) $idsQuery->whereNull('documento_origen_id');
            else $idsQuery->where('documento_origen_id', $doc->documento_origen_id);
            $ids = $idsQuery->pluck('id');

            $totales = DB::table('movimientos_contables')
                ->whereIn('comprobante_contable_id', $ids)
                ->selectRaw('SUM(debito) as debitos, SUM(credito) as creditos')
                ->first();

            $data[] = [
                'id' => $doc->id,
                'tipo' => $doc->tipo_nombre,
                'prefijo' => $doc->tipo_prefijo ?? '',
                'numero' => in_array($doc->documento_origen, ['COMPRA', 'PAGO_PROVEEDOR', 'AJUSTE'], true)
                    ? ($doc->observacion ?: $doc->documento_origen . ' #' . $doc->documento_origen_id)
                    : (Str::startsWith((string) $doc->documento_origen, 'MANUAL-')
                    ? Str::afterLast((string) $doc->documento_origen, '-')
                    : $doc->documento_origen),
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
        $c=ComprobanteContable::with(['tercero','usuario','registradoPor','anuladoPor','reversion','movimientos.cuenta','movimientos.tercero','movimientos.centroCosto'])->findOrFail($id);
        $original=ComprobanteContable::where('comprobante_reversion_id',$c->id)->first();
        return response()->json(['id'=>$c->id,'tipo'=>$c->tipo,'prefijo'=>$c->prefijo,'numero'=>$c->numero,'fecha'=>$c->fecha?->format('Y-m-d'),'estado'=>$c->estado,'descripcion'=>$c->descripcion,'tercero'=>$c->tercero?->nombre_completo,'usuario'=>$c->usuario?->name,'registrado_por'=>$c->registradoPor?->name,'registrado_at'=>$c->registrado_at,'anulado_por'=>$c->anuladoPor?->name,'anulado_at'=>$c->anulado_at,'motivo'=>$c->motivo_anulacion,'reversion'=>$c->reversion?['id'=>$c->reversion->id,'documento'=>$c->reversion->prefijo.'-'.$c->reversion->numero]:null,'original'=>$original?['id'=>$original->id,'documento'=>$original->prefijo.'-'.$original->numero]:null,'total_debito'=>$c->total_debito,'total_credito'=>$c->total_credito,'lineas'=>$c->movimientos->map(fn($m)=>['cuenta_id'=>$m->cuenta_contable_id,'codigo'=>$m->cuenta?->codigo,'cuenta'=>$m->cuenta?->nombre,'descripcion'=>$m->detalle,'tercero_id'=>$m->tercero_id,'tercero'=>$m->tercero?->nombre_completo,'centro_costo_id'=>$m->centro_costo_id,'centro_costo'=>$m->centroCosto?->nombre,'debito'=>(float)$m->debito,'credito'=>(float)$m->credito])]);

        $principal = DB::table('comprobantes_contables')
            ->where('id', $id)
            ->first();

        if (!$principal) {
            return response()->json([
                'message' => 'Documento no encontrado'
            ], 404);
        }

        $comprobantesQuery = DB::table('comprobantes_contables')
            ->join(
                'tipos_documento_contable',
                'tipos_documento_contable.id',
                '=',
                'comprobantes_contables.tipo_documento_contable_id'
            )
            ->where('comprobantes_contables.documento_origen', $principal->documento_origen);

        if ($principal->documento_origen_id === null) $comprobantesQuery->whereNull('comprobantes_contables.documento_origen_id');
        else $comprobantesQuery->where('comprobantes_contables.documento_origen_id', $principal->documento_origen_id);

        $comprobantes = $comprobantesQuery
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
        abort_if($comprobante->estado !== 'BORRADOR',422,'Solo se editan borradores.');
        $comprobante->load('movimientos');
        return response()->json(['id'=>$comprobante->id,'tipo'=>$comprobante->tipo,'prefijo'=>$comprobante->prefijo,'numero'=>$comprobante->numero,'fecha'=>$comprobante->fecha?->format('Y-m-d'),'descripcion'=>$comprobante->descripcion,'items'=>$comprobante->movimientos->map(fn($m)=>['cuenta_id'=>$m->cuenta_contable_id,'tercero_id'=>$m->tercero_id,'centro_costo_id'=>$m->centro_costo_id,'detalle'=>$m->detalle,'debito'=>(float)$m->debito,'credito'=>(float)$m->credito])]);

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

    public function store(Request $request, AccountingService $accounting)
    {
        $datos = $request->validate(['tipo'=>'required|string|max:100','prefijo'=>'required|string|max:10','fecha'=>'required|date','tercero_id'=>'nullable|exists:terceros,id','descripcion'=>'nullable|string|max:2000']);
        $tipo = $this->resolverTipo($datos['tipo'], $datos['prefijo']);
        $comprobante = $accounting->crearBorrador($datos + ['tipo_documento_contable_id'=>$tipo->id], Auth::id());

        return response()->json(['message' => 'Comprobante creado correctamente.', 'id' => $comprobante->id], 201);
    }

    public function update(Request $request, ComprobanteContable $comprobante, AccountingService $accounting)
    {
        $datos = $request->validate(['fecha'=>'required|date','tercero_id'=>'nullable|exists:terceros,id','descripcion'=>'nullable|string|max:2000','items'=>'array','items.*.cuenta_id'=>'required|exists:cuentas_contables,id','items.*.tercero_id'=>'nullable|integer|exists:terceros,id','items.*.centro_costo_id'=>'nullable|integer|exists:centros_costo,id','items.*.descripcion'=>'nullable|string|max:1000','items.*.debito'=>'nullable|numeric|min:0','items.*.credito'=>'nullable|numeric|min:0']);
        $accounting->actualizarBorrador($comprobante,$datos,$datos['items']??[]);

        return response()->json(['message' => 'Comprobante actualizado correctamente.']);
    }

    public function anular(Request $request, ComprobanteContable $comprobante, AccountingService $accounting)
    {
        $motivo=$request->validate(['motivo_anulacion'=>'required|string|max:2000'])['motivo_anulacion'];
        $accounting->anular($comprobante,$motivo,Auth::id());

        return response()->json(['message' => 'Comprobante anulado correctamente.']);
    }

    public function registrar(Request $request, ComprobanteContable $comprobante, AccountingService $accounting)
    {
        $items = $request->validate(['items'=>'required|array|min:2','items.*.cuenta_id'=>'required|exists:cuentas_contables,id','items.*.tercero_id'=>'nullable|integer|exists:terceros,id','items.*.centro_costo_id'=>'nullable|integer|exists:centros_costo,id','items.*.descripcion'=>'nullable|string|max:1000','items.*.debito'=>'nullable|numeric|min:0','items.*.credito'=>'nullable|numeric|min:0'])['items'];
        $accounting->registrar($comprobante, $items, Auth::id());
        return response()->json(['message'=>'Comprobante registrado correctamente.']);
    }

    public function revertir(ComprobanteContable $comprobante, AccountingService $accounting)
    {
        $this->asegurarManual($comprobante);
        $accounting->revertirAnulacion($comprobante, Auth::id());

        return response()->json(['message' => 'Anulación revertida correctamente.']);
    }

    public function destroy(ComprobanteContable $comprobante)
    {
        abort_if($comprobante->estado !== 'BORRADOR',422,'Solo se eliminan borradores.');
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
        // Ningún comprobante en el sistema lleva realmente el prefijo "MANUAL-"
        // (nada lo genera al crearlo) — con Str::startsWith() esto era siempre
        // false, así que asegurarManual() rechazaba el revertir()/destroy() de
        // CUALQUIER comprobante manual, incluidos los legítimos. Los comprobantes
        // que sí vienen de un módulo (ventas, compras, ajustes...) siempre traen
        // documento_origen con un valor; los creados a mano vía
        // AccountingService::crearBorrador() nunca lo asignan, así que null es la
        // señal real de "es manual".
        return $origen === null;
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
