<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use Illuminate\Http\Request;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Support\Facades\DB;
use App\Services\LegacyDocumentSyncService;
use App\Services\CompraContableService;
use Illuminate\Validation\ValidationException;

class CompraAvanzadaController extends Controller
{
    public function index() { return view('compras.index'); }
    public function data() { return response()->json(['data' => Compra::with(['proveedor', 'usuario:id,name'])->latest()->get()]); }
    public function siguienteConsecutivo(Request $r) { $p = strtoupper(trim((string) $r->string('prefijo', 'FC'))); return response()->json(['consecutivo' => (Compra::where('prefijo', $p)->max('consecutivo') ?? 0) + 1]); }
    public function show(Compra $compra) { return response()->json($compra->load('proveedor', 'usuario', 'detalles.producto', 'detalles.bodega', 'pagos', 'pagosProveedor.pago.metodoPago', 'pagosProveedor.pago.comprobante')); }

    public function store(Request $r)
    {
        $d = $this->validar($r);
        try {
            return DB::transaction(function () use ($d, $r) {
                $prefijo = strtoupper($d['prefijo']);
                // siguienteConsecutivo() solo sugiere un número (lectura suelta, sin
                // bloqueo); dos compras con el mismo prefijo creadas casi a la vez
                // podían recibir la misma sugerencia. El unique(prefijo,consecutivo)
                // de la tabla ya evita el duplicado silencioso, pero antes reventaba
                // como un 500 crudo — con este lockForUpdate() dentro de la misma
                // transacción, el segundo request ve el conflicto y recibe un 422 claro.
                if (Compra::where('prefijo', $prefijo)->where('consecutivo', $d['consecutivo'])->lockForUpdate()->exists()) {
                    throw ValidationException::withMessages(['consecutivo' => 'Ya existe una compra con este prefijo y consecutivo. Actualiza el número e intenta de nuevo.']);
                }
                $c = Compra::create(['prefijo' => $prefijo, 'consecutivo' => $d['consecutivo'], 'numero_factura' => $d['numero_factura'], 'proveedor_id' => $d['proveedor_id'], 'user_id' => $r->user()->id, 'fecha' => $d['fecha'], 'observaciones' => $d['observaciones'] ?? null, 'estado' => !empty($d['confirmar']) ? 'confirmada' : 'borrador', 'registrado_at' => !empty($d['confirmar']) ? now() : null]);
                $this->detalles($c, $d);
                if ($c->estado === 'confirmada') { $this->confirmarCompra($c, $r->user()->id); }
                return response()->json(['success' => true, 'data' => $c->load('detalles', 'pagos')], 201);
            });
        } catch (\Illuminate\Database\QueryException $e) {
            // Backstop final: si dos requests pasan el lockForUpdate() casi a la
            // vez (o en un motor sin soporte real de bloqueo de rango), el
            // unique(prefijo,consecutivo) de la tabla sigue siendo quien de
            // verdad impide el duplicado — esto solo evita que el segundo
            // request reciba un 500 crudo en vez de un mensaje claro.
            if ($e->getCode() === '23000') {
                throw ValidationException::withMessages(['consecutivo' => 'Ya existe una compra con este prefijo y consecutivo. Actualiza el número e intenta de nuevo.']);
            }
            throw $e;
        }
    }

    public function update(Request $r, Compra $compra)
    {
        if ($compra->estado !== 'borrador') return response()->json(['message' => 'Solo se pueden editar compras en borrador'], 422);
        $d = $this->validar($r);
        return DB::transaction(function () use ($compra, $d, $r) {
            $compra->update(['prefijo' => strtoupper($d['prefijo']), 'consecutivo' => $d['consecutivo'], 'numero_factura' => $d['numero_factura'], 'proveedor_id' => $d['proveedor_id'], 'fecha' => $d['fecha'], 'observaciones' => $d['observaciones'] ?? null]);
            $compra->detalles()->delete(); $compra->pagos()->delete(); $this->detalles($compra, $d);
            if (!empty($d['confirmar'])) { $this->confirmarCompra($compra, $r->user()->id); }
            return response()->json(['success' => true]);
        });
    }

    public function registrar(Request $request, Compra $compra) { if ($compra->estado !== 'borrador') return response()->json(['message' => 'La compra ya fue registrada'], 422); return DB::transaction(function () use ($compra, $request) { $this->confirmarCompra($compra, $request->user()->id); return response()->json(['success' => true]); }); }
    public function revertirRegistro(Compra $compra)
    {
        if ($compra->estado === 'borrador') {
            return response()->json(['success' => true, 'message' => 'La compra ya estaba en borrador; no se descontó inventario nuevamente.']);
        }
        if ($compra->estado !== 'confirmada') return response()->json(['message' => 'Solo se puede revertir una compra confirmada'], 422);
        return DB::transaction(function () use ($compra) {
            $this->validarStockParaSalida($compra, 'revertir el registro');
            app(CompraContableService::class)->prepararReversion($compra);
            app(\App\Services\ReversionInventarioService::class)->ejecutar($compra->documento_id);
            $compra->update(['estado' => 'borrador', 'registrado_at' => null]);
            return response()->json(['success' => true, 'message' => 'Compra revertida a borrador e inventario descontado correctamente.']);
        });
    }
    public function anular(Compra $compra)
    {
        if ($compra->estado !== 'confirmada') return response()->json(['message' => 'Solo se puede anular una compra confirmada'], 422);
        return DB::transaction(function () use ($compra) {
            $this->validarStockParaSalida($compra, 'anular');
            // Antes esto no revertía el comprobante contable ni los pagos iniciales:
            // quedaba una compra "anulada" con su contabilidad y sus pagos a proveedor
            // activos, igual que hace revertirRegistro() (mismo servicio, mismo efecto).
            app(CompraContableService::class)->prepararReversion($compra);
            app(\App\Services\ReversionInventarioService::class)->ejecutar($compra->documento_id);
            $compra->update(['estado' => 'anulada']);
            return response()->json(['success' => true]);
        });
    }

    public function revertir(Compra $compra)
    {
        if ($compra->estado !== 'anulada') return response()->json(['message' => 'Solo se puede revertir una compra anulada'], 422);
        return DB::transaction(function () use ($compra) {
            $this->confirmarCompra($compra, request()->user()->id);
            return response()->json(['success' => true]);
        });
    }

    public function registrarPago(Request $request, Compra $compra)
    {
        $datos = $request->validate([
            'fecha' => ['required', 'date'],
            'valor' => ['required', 'numeric', 'gt:0'],
            'metodo_pago_contable_id' => ['required', 'exists:metodos_pago_contables,id'],
            'referencia' => ['nullable', 'string', 'max:120'],
        ]);
        return DB::transaction(function () use ($compra, $datos, $request) {
            $pago = app(CompraContableService::class)->registrarAbono($compra, $datos, $request->user()->id);
            return response()->json(['success' => true, 'data' => $pago]);
        });
    }

    private function confirmarCompra(Compra $compra, int $usuarioId): void
    {
        // Una compra confirmada siempre debe declarar cómo se cubre el total,
        // incluso cuando el saldo se deja explícitamente a Crédito proveedores.
        app(CompraContableService::class)->validarFormasPago($compra);
        $this->inventario($compra, 1);
        $compra->update(['estado' => 'confirmada', 'registrado_at' => now()]);
        app(LegacyDocumentSyncService::class)->compra($compra);
        $contabilidad = app(CompraContableService::class);
        $contabilidad->registrarPagosIniciales($compra, $usuarioId);
        $contabilidad->contabilizarCompra($compra->fresh(), $usuarioId);
    }

    private function validar(Request $r): array { return $r->validate(['prefijo'=>'required|max:10','consecutivo'=>'required|integer|min:1','numero_factura'=>'required|max:50','proveedor_id'=>'required|exists:terceros,id','fecha'=>'required|date','observaciones'=>'nullable','confirmar'=>'boolean','otros_cargos'=>'nullable|numeric|min:0','retefuente_porcentaje'=>'nullable|numeric|min:0','reteiva_porcentaje'=>'nullable|numeric|min:0','reteica_porcentaje'=>'nullable|numeric|min:0','pagos'=>'nullable|array','pagos.*.metodo_pago'=>'required_with:pagos','pagos.*.valor'=>'required_with:pagos|numeric|gt:0','pagos.*.referencia'=>'nullable','items'=>'required|array|min:1','items.*.producto_id'=>'required|exists:productos,id','items.*.bodega_id'=>'required|exists:bodegas,id','items.*.cantidad'=>'required|numeric|gt:0','items.*.costo_unitario'=>'required|numeric|min:0','items.*.descuento_porcentaje'=>'nullable|numeric|min:0','items.*.descuento_2_porcentaje'=>'nullable|numeric|min:0','items.*.descuento_financiero_porcentaje'=>'nullable|numeric|min:0','items.*.iva_porcentaje'=>'nullable|numeric|min:0','items.*.ico_porcentaje'=>'nullable|numeric|min:0','items.*.valor_ico'=>'nullable|numeric|min:0','items.*.imp_saludable_porcentaje'=>'nullable|numeric|min:0','items.*.unidad'=>'nullable','items.*.observacion'=>'nullable','items.*.bonificado'=>'boolean','items.*.entrada_pos'=>'boolean']); }

    private function detalles(Compra $c, array $d): void
    {
        $baseTotal=0;$descuentos=0;$ivaT=0;$icoT=0;$saludT=0;$retT=0;$totalT=0;$otros=(float)($d['otros_cargos']??0);$lineas=[];
        foreach($d['items'] as $i){$base=!empty($i['bonificado']) ? 0 : $i['cantidad']*$i['costo_unitario'];$d1=$base*(($i['descuento_porcentaje']??0)/100);$d2=($base-$d1)*(($i['descuento_2_porcentaje']??0)/100);$df=($base-$d1-$d2)*(($i['descuento_financiero_porcentaje']??0)/100);$i['_base']=$base;$i['_desc']=$d1+$d2+$df;$i['_grav']=$base-$i['_desc'];$lineas[]=$i;$baseTotal+=$base;$descuentos+=$i['_desc'];}
        $gravTotal=max(0.01,$baseTotal-$descuentos);$rf=$gravTotal*(($d['retefuente_porcentaje']??0)/100);$ri=$gravTotal*(($d['reteiva_porcentaje']??0)/100);$rc=$gravTotal*(($d['reteica_porcentaje']??0)/100);
        foreach($lineas as $i){$f=$i['_grav']/$gravTotal;$iva=$i['_grav']*(($i['iva_porcentaje']??0)/100);$ico=array_key_exists('valor_ico',$i) && $i['valor_ico'] !== null && $i['valor_ico'] !== '' ? (float)$i['valor_ico'] : $i['_grav']*(($i['ico_porcentaje']??0)/100);$sal=$i['_grav']*(($i['imp_saludable_porcentaje']??0)/100);$ret=($rf+$ri+$rc)*$f;$add=$otros*$f;$tot=$i['_grav']+$iva+$ico+$sal+$add-$ret;$detalle=$i;unset($detalle['_base'],$detalle['_desc'],$detalle['_grav']);$c->detalles()->create(array_merge($detalle,['subtotal'=>$i['_grav'],'total'=>$tot,'retefuente'=>$rf*$f,'reteiva'=>$ri*$f,'reteica'=>$rc*$f,'otros_cargos'=>$add]));$ivaT+=$iva;$icoT+=$ico;$saludT+=$sal;$retT+=$ret;$totalT+=$tot;}
        $c->update(['subtotal'=>$baseTotal,'descuentos'=>$descuentos,'iva'=>$ivaT,'ico'=>$icoT,'imp_saludable'=>$saludT,'retenciones'=>$retT,'otros_cargos'=>$otros,'total'=>$totalT,'retefuente_porcentaje'=>$d['retefuente_porcentaje']??0,'reteiva_porcentaje'=>$d['reteiva_porcentaje']??0,'reteica_porcentaje'=>$d['reteica_porcentaje']??0]);
        foreach($d['pagos']??[] as $p) if(($p['valor']??0)>0)$c->pagos()->create($p);
    }

    private function inventario(Compra $c, int $signo): void
    {
        // Antes usaba updateOrInsert() con COALESCE(stock,0)+N en el propio
        // INSERT: en un alta nueva no hay fila que leer todavía, así que esa
        // referencia a "stock" es inválida en cualquier motor — solo "funcionaba"
        // en MySQL por su tolerancia a resolver la columna inexistente como NULL;
        // en SQLite (los tests) falla directo con "no such column: stock".
        foreach ($c->detalles as $d) {
            if ($signo > 0) {
                \App\Models\Inventario::firstOrCreate(['producto_id' => $d->producto_id, 'bodega_id' => $d->bodega_id], ['stock' => 0])
                    ->increment('stock', $d->cantidad);
            } else {
                DB::table('inventarios')->where(['producto_id' => $d->producto_id, 'bodega_id' => $d->bodega_id])->decrement('stock', $d->cantidad);
            }
        }
    }
    private function validarStockParaSalida(Compra $c, string $accion): void
    {
        foreach ($c->detalles as $d) {
            $stock = DB::table('inventarios')->where(['producto_id' => $d->producto_id, 'bodega_id' => $d->bodega_id])->lockForUpdate()->value('stock');
            if ($stock === null || (float) $stock + 0.0001 < (float) $d->cantidad) {
                throw new HttpResponseException(response()->json([
                    'message' => "No se puede {$accion}: la compra requiere {$d->cantidad} unidades de este producto en {$d->bodega->descripcion}, pero actualmente hay " . number_format((float) ($stock ?? 0), 3, ',', '.') . '. Parte del inventario ya fue vendido o trasladado.',
                ], 422));
            }
        }
    }
}
