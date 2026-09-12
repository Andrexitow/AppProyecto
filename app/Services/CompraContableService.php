<?php

namespace App\Services;

use App\Models\Compra;
use App\Models\ComprobanteContable;
use App\Models\ConfiguracionContable;
use App\Models\MetodoPagoContable;
use App\Models\MovimientoContable;
use App\Models\PagoProveedor;
use App\Models\ProcesoContable;
use Illuminate\Validation\ValidationException;

class CompraContableService
{
    public function registrarPagosIniciales(Compra $compra, int $usuarioId): void
    {
        if ($compra->pagosProveedor()->whereHas('pago', fn ($q) => $q->where('origen', 'inicial')->where('estado', 'registrado'))->exists()) { $this->actualizarSaldo($compra); return; }
        $pagos = $this->validarFormasPago($compra);
        foreach ($pagos as $linea) {
            if ($this->esCreditoProveedor($linea->metodo_pago)) continue;
            $metodo = $this->metodo($linea->metodo_pago);
            $pago = PagoProveedor::create(['proveedor_id'=>$compra->proveedor_id,'fecha'=>$compra->fecha,'valor'=>$linea->valor,'metodo_pago_contable_id'=>$metodo->id,'referencia'=>$linea->referencia,'origen'=>'inicial','estado'=>'registrado','usuario_id'=>$usuarioId]);
            $pago->aplicaciones()->create(['compra_id'=>$compra->id,'valor'=>$linea->valor]);
        }
        $this->actualizarSaldo($compra);
    }

    /** Verifica que la forma de pago (incluido el crédito) cubra exactamente la factura. */
    public function validarFormasPago(Compra $compra)
    {
        $pagos = $compra->pagos()->get()->filter(fn ($p) => (float) $p->valor > 0)->values();
        if ($pagos->isEmpty()) throw ValidationException::withMessages(['pagos' => 'Debe indicar al menos una forma de pago o Crédito proveedores.']);
        $totalFormas = (float) $pagos->sum('valor');
        if (abs($totalFormas - (float) $compra->total) > .01) throw ValidationException::withMessages(['pagos' => 'La suma de las formas de pago debe ser exactamente igual al total de la factura.']);
        foreach ($pagos as $linea) if (!$this->esCreditoProveedor($linea->metodo_pago)) $this->metodo($linea->metodo_pago);
        return $pagos;
    }

    public function contabilizarCompra(Compra $compra, int $usuarioId): ComprobanteContable
    {
        $compra->loadMissing('pagosProveedor.pago.metodoPago');
        $existente = ComprobanteContable::where('documento_origen','COMPRA')->where('documento_origen_id',$compra->id)->where('estado','CONTABILIZADO')->first();
        if ($existente) return $existente;
        $movimientos=[];
        $this->agregar($movimientos,'CUENTA_INVENTARIO',(float)$compra->total+(float)$compra->retenciones-(float)$compra->iva,0,null,'Ingreso de inventario');
        $this->agregar($movimientos,'CUENTA_IVA_DESCONTABLE',(float)$compra->iva,0,$compra->proveedor_id,'IVA descontable de compra');
        foreach ($compra->pagosProveedor as $aplicacion) if ($aplicacion->pago->estado === 'registrado') $this->agregar($movimientos,$aplicacion->pago->metodoPago->configuracion_clave ?? '',0,(float)$aplicacion->valor,null,'Pago inmediato de compra');
        $this->agregar($movimientos,'CUENTA_PROVEEDORES',0,(float)$compra->saldo_pendiente,$compra->proveedor_id,'Saldo pendiente al proveedor');
        $this->agregar($movimientos,'CUENTA_RETEFUENTE',0,(float)$compra->retenciones,$compra->proveedor_id,'Retenciones practicadas');
        $comprobante=$this->crearComprobante('COMPRA','COMPRA',$compra->id,$compra->fecha,$usuarioId,"Compra {$compra->prefijo}-{$compra->numero_factura}");
        $this->guardarMovimientos($comprobante,$movimientos);
        $compra->update(['contabilizado_at'=>now()]);
        return $comprobante;
    }

    public function registrarAbono(Compra $compra, array $datos, int $usuarioId): PagoProveedor
    {
        if ($compra->estado !== 'confirmada') throw ValidationException::withMessages(['compra'=>'Solo se pueden abonar compras confirmadas.']);
        $this->actualizarSaldo($compra); $valor=(float)$datos['valor'];
        if ($valor > (float)$compra->saldo_pendiente + .01) throw ValidationException::withMessages(['valor'=>'El abono supera el saldo pendiente de la compra.']);
        $metodo=MetodoPagoContable::whereKey($datos['metodo_pago_contable_id'])->where('estado',true)->firstOrFail();
        // Mismo bug que ya se corrigió del lado de Cuentas por Cobrar: "crédito" no
        // es un medio de pago real para SALDAR algo — es precisamente lo que se
        // está saldando. Sin este chequeo, un abono a proveedor pagado "a crédito"
        // terminaba acreditando CUENTA_CLIENTES (la cuenta de cartera de clientes,
        // sin ninguna relación con este proveedor) en vez de Caja/Banco.
        if ($metodo->metodo_pago === 'credito') throw ValidationException::withMessages(['metodo_pago_contable_id'=>'Crédito no es un medio de pago válido para pagar un abono.']);
        $this->configuracion($metodo->configuracion_clave);
        $pago=PagoProveedor::create(['proveedor_id'=>$compra->proveedor_id,'fecha'=>$datos['fecha'],'valor'=>$valor,'metodo_pago_contable_id'=>$metodo->id,'referencia'=>$datos['referencia']??null,'origen'=>'abono','estado'=>'registrado','usuario_id'=>$usuarioId]);
        $pago->aplicaciones()->create(['compra_id'=>$compra->id,'valor'=>$valor]);
        $movimientos=[]; $this->agregar($movimientos,'CUENTA_PROVEEDORES',$valor,0,$compra->proveedor_id,'Abono a cuenta por pagar'); $this->agregar($movimientos,$metodo->configuracion_clave,0,$valor,null,'Salida por pago a proveedor');
        $comprobante=$this->crearComprobante('PAGO_PROVEEDOR','PAGO_PROVEEDOR',$pago->id,$datos['fecha'],$usuarioId,"Abono a {$compra->prefijo}-{$compra->numero_factura}");
        $this->guardarMovimientos($comprobante,$movimientos); $pago->update(['comprobante_contable_id'=>$comprobante->id]); $this->actualizarSaldo($compra); return $pago;
    }

    public function prepararReversion(Compra $compra): void
    {
        if ($compra->pagosProveedor()->whereHas('pago',fn($q)=>$q->where('origen','abono')->where('estado','registrado'))->exists()) throw ValidationException::withMessages(['compra'=>'No puede revertirse: la compra tiene abonos posteriores. Anule primero esos pagos desde Cuentas por Pagar.']);
        // Update() masivo: no dispara los eventos de Eloquent, así que el
        // bloqueo de período cerrado del modelo no se activaría solo aquí.
        app(\App\Services\PeriodoContableService::class)->assertAbierto($compra->fecha);
        ComprobanteContable::where('documento_origen','COMPRA')->where('documento_origen_id',$compra->id)->where('estado','CONTABILIZADO')->update(['estado'=>'ANULADO']);
        PagoProveedor::whereHas('aplicaciones',fn($q)=>$q->where('compra_id',$compra->id))->where('origen','inicial')->update(['estado'=>'anulado']);
        $compra->update(['total_pagado'=>0,'saldo_pendiente'=>0,'estado_pago'=>'pendiente','contabilizado_at'=>null]);
    }

    public function actualizarSaldo(Compra $compra): void
    {
        $pagado=(float)$compra->pagosProveedor()->whereHas('pago',fn($q)=>$q->where('estado','registrado'))->sum('valor'); $saldo=max(0,round((float)$compra->total-$pagado,2));
        $compra->update(['total_pagado'=>$pagado,'saldo_pendiente'=>$saldo,'estado_pago'=>$saldo<=.009?'pagada':($pagado>0?'parcialmente_pagada':'pendiente')]);
    }

    private function metodo(string $nombre): MetodoPagoContable { $metodo=MetodoPagoContable::whereRaw('LOWER(metodo_pago)=?',[mb_strtolower(trim($nombre))])->where('estado',true)->first(); if(!$metodo) throw ValidationException::withMessages(['pagos'=>"El medio de pago '{$nombre}' no está parametrizado contablemente."]); $this->configuracion($metodo->configuracion_clave); return $metodo; }
    private function esCreditoProveedor(?string $metodo): bool { return in_array(mb_strtolower(trim((string) $metodo)), ['credito', 'credito_proveedores'], true); }
    private function agregar(array &$lineas,string $clave,float $debito,float $credito,?int $terceroId,string $detalle): void { if(round($debito+$credito,2)<=0)return; if(!$clave)throw ValidationException::withMessages(['pagos'=>'Un medio de pago no tiene cuenta contable configurada.']); $lineas[]=compact('clave','debito','credito','terceroId','detalle'); }
    private function configuracion(string $clave): ConfiguracionContable { $c=ConfiguracionContable::with('cuenta')->where('clave',$clave)->where('estado',true)->first(); if(!$c?->cuenta)throw ValidationException::withMessages(['contabilidad'=>"La configuración contable {$clave} no tiene una cuenta asignada."]); return $c; }
    private function crearComprobante(string $procesoCodigo,string $origen,int $origenId,string $fecha,int $usuarioId,string $observacion): ComprobanteContable { $proceso=ProcesoContable::with('tipoDocumento')->where('codigo',$procesoCodigo)->where('estado',true)->firstOrFail(); $tipo=$proceso->tipoDocumento; if(!$tipo)throw ValidationException::withMessages(['contabilidad'=>"El proceso {$procesoCodigo} no tiene tipo de comprobante."]); $tipo=$tipo->newQuery()->lockForUpdate()->findOrFail($tipo->id); $numero=$tipo->prefijo.str_pad($tipo->consecutivo,$tipo->longitud,'0',STR_PAD_LEFT); $c=ComprobanteContable::create(['tipo_documento_contable_id'=>$tipo->id,'proceso_contable_id'=>$proceso->id,'numero'=>$numero,'fecha'=>$fecha,'observacion'=>$observacion,'usuario_id'=>$usuarioId,'documento_origen'=>$origen,'documento_origen_id'=>$origenId,'estado'=>'BORRADOR']); $tipo->increment('consecutivo'); return $c; }
    private function guardarMovimientos(ComprobanteContable $c,array $lineas): void { $d=0;$h=0;foreach($lineas as $l){$cuenta=$this->configuracion($l['clave'])->cuenta;MovimientoContable::create(['comprobante_contable_id'=>$c->id,'cuenta_contable_id'=>$cuenta->id,'tercero_id'=>$l['terceroId'],'referencia'=>$c->documento_origen,'detalle'=>$l['detalle'],'debito'=>$l['debito'],'credito'=>$l['credito']]);$d+=$l['debito'];$h+=$l['credito'];}if(round($d,2)!==round($h,2))throw ValidationException::withMessages(['contabilidad'=>'El comprobante de compra quedó descuadrado. Revise impuestos, retenciones y pagos.']);$c->update(['estado'=>'CONTABILIZADO']); }
}
