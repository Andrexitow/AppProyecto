<?php
namespace App\Services;
use App\Models\{ComprobanteContable,CuentaContable,MovimientoContable,TipoDocumentoContable};
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
class AccountingService {
 public function crearBorrador(array $data,int $usuario): ComprobanteContable { return DB::transaction(function()use($data,$usuario){
  $tipo=TipoDocumentoContable::lockForUpdate()->findOrFail($data['tipo_documento_contable_id']);
  $numero=$tipo->prefijo.str_pad($tipo->consecutivo,$tipo->longitud,'0',STR_PAD_LEFT);$tipo->increment('consecutivo');
  return ComprobanteContable::create(['tipo_documento_contable_id'=>$tipo->id,'tipo'=>$tipo->nombre,'prefijo'=>$tipo->prefijo,'numero'=>$numero,'fecha'=>$data['fecha'],'tercero_id'=>$data['tercero_id']??null,'descripcion'=>$data['descripcion']??null,'observacion'=>$data['descripcion']??null,'usuario_id'=>$usuario,'estado'=>'BORRADOR']);
 });}
 public function actualizarBorrador(ComprobanteContable $c,array $data,array $lineas): ComprobanteContable {if($c->estado!=='BORRADOR')$this->fail('Solo se editan borradores.');return DB::transaction(function()use($c,$data,$lineas){$c->update(['fecha'=>$data['fecha'],'tercero_id'=>$data['tercero_id']??null,'descripcion'=>$data['descripcion']??null,'observacion'=>$data['descripcion']??null]);$this->guardarLineas($c,$lineas,false);return $c->fresh();});}
 public function registrar(ComprobanteContable $c,array $lineas,int $usuario): ComprobanteContable{return DB::transaction(function()use($c,$lineas,$usuario){
  if($c->estado!=='BORRADOR')$this->fail('Solo se registran borradores.');[$d,$h]=$this->guardarLineas($c,$lineas,true);
  $c->update(['total_debito'=>$d,'total_credito'=>$h,'estado'=>'REGISTRADO','registrado_por'=>$usuario,'registrado_at'=>now()]);return $c->fresh();
 });}
 private function guardarLineas(ComprobanteContable $c,array $lineas,bool $cuadrar): array {$c->movimientos()->delete();$d=0;$h=0;
  foreach($lineas as $l){$cuenta=CuentaContable::findOrFail($l['cuenta_id']);$de=(float)($l['debito']??0);$cr=(float)($l['credito']??0);
   if(!$cuenta->estado||!$cuenta->permite_movimientos)$this->fail('La cuenta no está habilitada para movimientos.');
   if(($de<=0&&$cr<=0)||($de>0&&$cr>0))$this->fail('Cada línea debe tener solo débito o crédito positivo.');
   if($cuenta->requiere_tercero&&empty($l['tercero_id']))$this->fail('La cuenta requiere tercero.');
   if($cuenta->requiere_centro_costo&&empty($l['centro_costo_id']))$this->fail('La cuenta requiere centro de costo.');
   MovimientoContable::create(['comprobante_contable_id'=>$c->id,'cuenta_contable_id'=>$cuenta->id,'tercero_id'=>$l['tercero_id']??null,'centro_costo_id'=>$l['centro_costo_id']??null,'detalle'=>$l['descripcion']??null,'referencia'=>$l['documento_referencia']??null,'documento_referencia'=>$l['documento_referencia']??null,'debito'=>$de,'credito'=>$cr]);$d+=$de;$h+=$cr;}
  if($cuadrar&&(round($d,2)!==round($h,2)||$d<=0))$this->fail('El comprobante está descuadrado.');return [$d,$h];}
 public function anular(ComprobanteContable $c,string $motivo,int $usuario): ComprobanteContable{return DB::transaction(function()use($c,$motivo,$usuario){
  if($c->estado!=='REGISTRADO'||$c->comprobante_reversion_id)$this->fail('El comprobante no puede anularse.');
  $tipo=TipoDocumentoContable::lockForUpdate()->findOrFail($c->tipo_documento_contable_id);$n=$tipo->prefijo.str_pad($tipo->consecutivo,$tipo->longitud,'0',STR_PAD_LEFT);$tipo->increment('consecutivo');
  $r=ComprobanteContable::create(['tipo_documento_contable_id'=>$tipo->id,'tipo'=>$tipo->nombre,'prefijo'=>$tipo->prefijo,'numero'=>$n,'fecha'=>now(),'tercero_id'=>$c->tercero_id,'descripcion'=>'Reversión '.$c->numero,'usuario_id'=>$usuario,'estado'=>'REGISTRADO','registrado_por'=>$usuario,'registrado_at'=>now(),'total_debito'=>$c->total_credito,'total_credito'=>$c->total_debito]);
  foreach($c->movimientos as $m)MovimientoContable::create(['comprobante_contable_id'=>$r->id,'cuenta_contable_id'=>$m->cuenta_contable_id,'tercero_id'=>$m->tercero_id,'centro_costo_id'=>$m->centro_costo_id,'detalle'=>'Reversión: '.$m->detalle,'debito'=>$m->credito,'credito'=>$m->debito,'referencia'=>$c->numero]);
  $c->update(['estado'=>'ANULADO','anulado_por'=>$usuario,'anulado_at'=>now(),'motivo_anulacion'=>$motivo,'comprobante_reversion_id'=>$r->id]);return $c->fresh();
 });}
 /**
  * Deshace anular(): antes esto lo hacía el controlador con un simple
  * $comprobante->update(['estado'=>'CONTABILIZADO']) que (a) dejaba vivo el
  * comprobante de reversión con las líneas invertidas, así que en el mayor
  * quedaban original+reversión sumando neto CERO (no se restituía nada, solo
  * cambiaba una etiqueta), y (b) usaba un estado 'CONTABILIZADO' que este
  * comprobante nunca tiene en su propio ciclo de vida (BORRADOR/REGISTRADO/
  * ANULADO), dejándolo fuera del alcance de anular() para siempre porque esa
  * guarda exige estado==='REGISTRADO'.
  */
 public function revertirAnulacion(ComprobanteContable $c,int $usuario): ComprobanteContable{return DB::transaction(function()use($c,$usuario){
  if($c->estado!=='ANULADO'||!$c->comprobante_reversion_id)$this->fail('Solo puede revertirse un comprobante anulado con una reversión asociada.');
  $reversion=ComprobanteContable::lockForUpdate()->find($c->comprobante_reversion_id);
  if(!$reversion||$reversion->estado!=='REGISTRADO')$this->fail('La reversión de este comprobante ya no está activa; no se puede deshacer automáticamente.');
  $reversion->update(['estado'=>'ANULADO','anulado_por'=>$usuario,'anulado_at'=>now(),'motivo_anulacion'=>'Reversión deshecha al reactivar '.$c->numero]);
  $c->update(['estado'=>'REGISTRADO','anulado_por'=>null,'anulado_at'=>null,'motivo_anulacion'=>null,'comprobante_reversion_id'=>null]);
  return $c->fresh();
 });}
 private function fail($m):never{throw ValidationException::withMessages(['comprobante'=>$m]);}
}
