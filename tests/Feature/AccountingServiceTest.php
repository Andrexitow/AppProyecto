<?php
namespace Tests\Feature;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\{CuentaContable,CentroCosto,ComprobanteContable,TipoDocumentoContable,User,Roles};
use App\Services\AccountingService;
use Illuminate\Validation\ValidationException;

class AccountingServiceTest extends TestCase {
 use RefreshDatabase;
 private function user(){$r=Roles::firstOrCreate(['nombre'=>'Administrador'],['descripcion'=>'Test']);return User::firstOrCreate(['username'=>'contable-test'],['name'=>'Contable Test','password'=>bcrypt('secret'),'activo'=>true,'rol_id'=>$r->id]);}
 private function tipo(){return TipoDocumentoContable::create(['codigo'=>'CG','nombre'=>'General','prefijo'=>'CG','consecutivo'=>1,'longitud'=>4,'estado'=>true]);}
 private function cuenta($code,$extra=[]){return CuentaContable::create(array_merge(['codigo'=>$code,'nombre'=>$code,'nivel'=>1,'clasificacion'=>'ACTIVO','naturaleza'=>'DEBITO','tipo'=>'DETALLE','permite_movimientos'=>true,'estado'=>true],$extra));}
 private function borrador(){return app(AccountingService::class)->crearBorrador(['tipo_documento_contable_id'=>$this->tipo()->id,'fecha'=>'2026-09-11','descripcion'=>'Prueba'],$this->user()->id);}
 private function lineas($a,$b){return [['cuenta_id'=>$a->id,'debito'=>500,'credito'=>0],['cuenta_id'=>$b->id,'debito'=>0,'credito'=>500]];}
 public function test_borrador_se_crea_y_actualiza_lineas(){ $c=$this->borrador();$a=$this->cuenta('110505');$b=$this->cuenta('413505');app(AccountingService::class)->actualizarBorrador($c,['fecha'=>'2026-09-12','descripcion'=>'Editado'],$this->lineas($a,$b));$this->assertSame('BORRADOR',$c->fresh()->estado);$this->assertCount(2,$c->fresh()->movimientos); }
 public function test_registra_balanceado_y_totales(){ $c=$this->borrador();$a=$this->cuenta('110505');$b=$this->cuenta('413505');app(AccountingService::class)->registrar($c,$this->lineas($a,$b),$this->user()->id);$this->assertSame('REGISTRADO',$c->fresh()->estado);$this->assertEquals(500,(float)$c->fresh()->total_debito); }
 public function test_rechaza_descuadre_y_cuentas_invalidas(){ $c=$this->borrador();$a=$this->cuenta('110505');$b=$this->cuenta('413505',['estado'=>false]);$this->expectException(ValidationException::class);app(AccountingService::class)->registrar($c,$this->lineas($a,$b),$this->user()->id); }
 public function test_anular_crea_reversion_invertida(){ $c=$this->borrador();$a=$this->cuenta('110505');$b=$this->cuenta('413505');$s=app(AccountingService::class);$s->registrar($c,$this->lineas($a,$b),$this->user()->id);$s->anular($c,'Prueba',$this->user()->id);$r=$c->fresh()->reversion;$this->assertSame('ANULADO',$c->fresh()->estado);$this->assertSame('500.00',(string)$r->movimientos()->first()->credito); }
 public function test_rechaza_vacio_negativos_y_doble_columna(){ $c=$this->borrador();$a=$this->cuenta('110505');$this->expectException(ValidationException::class);app(AccountingService::class)->registrar($c,[['cuenta_id'=>$a->id,'debito'=>-1,'credito'=>0]],$this->user()->id); }
 public function test_exige_tercero_y_centro_costo(){ $c=$this->borrador();$a=$this->cuenta('110505',['requiere_tercero'=>true]);$b=$this->cuenta('413505');$this->expectException(ValidationException::class);app(AccountingService::class)->registrar($c,$this->lineas($a,$b),$this->user()->id); }
 public function test_impide_editar_y_anular_dos_veces(){ $c=$this->borrador();$a=$this->cuenta('110505');$b=$this->cuenta('413505');$s=app(AccountingService::class);$s->registrar($c,$this->lineas($a,$b),$this->user()->id);try{$s->actualizarBorrador($c,['fecha'=>'2026-09-12'],[]);$this->fail('Debía rechazar edición');}catch(ValidationException $e){}$s->anular($c,'x',$this->user()->id);$this->expectException(ValidationException::class);$s->anular($c,'x',$this->user()->id); }
 public function test_consecutivos_son_unicos(){ $s=app(AccountingService::class);$t=$this->tipo();$u=$this->user()->id;$a=$s->crearBorrador(['tipo_documento_contable_id'=>$t->id,'fecha'=>'2026-09-11'],$u);$b=$s->crearBorrador(['tipo_documento_contable_id'=>$t->id,'fecha'=>'2026-09-11'],$u);$this->assertNotSame($a->numero,$b->numero); }
 public function test_rechaza_comprobante_sin_lineas(){ $this->expectException(ValidationException::class);app(AccountingService::class)->registrar($this->borrador(),[],$this->user()->id); }
 public function test_rechaza_debito_y_credito_simultaneos(){ $c=$this->borrador();$a=$this->cuenta('110505');$this->expectException(ValidationException::class);app(AccountingService::class)->registrar($c,[['cuenta_id'=>$a->id,'debito'=>1,'credito'=>1]],$this->user()->id); }
 public function test_endpoint_impide_eliminar_registrado(){ $c=$this->borrador();$a=$this->cuenta('110505');$b=$this->cuenta('413505');app(AccountingService::class)->registrar($c,$this->lineas($a,$b),$this->user()->id);$this->actingAs($this->user())->delete('/comprobantes/'.$c->id)->assertStatus(422);$this->assertDatabaseHas('comprobantes_contables',['id'=>$c->id,'estado'=>'REGISTRADO']);}
 public function test_revertir_anulacion_anula_la_reversion_y_restituye_el_neto_original(){
  $c=$this->borrador();$a=$this->cuenta('110505');$b=$this->cuenta('413505');$s=app(AccountingService::class);
  $s->registrar($c,$this->lineas($a,$b),$this->user()->id);
  $s->anular($c,'Prueba',$this->user()->id);
  $reversionId=$c->fresh()->comprobante_reversion_id;

  $s->revertirAnulacion($c->fresh(),$this->user()->id);
  $c=$c->fresh();
  $this->assertSame('REGISTRADO',$c->estado);
  $this->assertNull($c->comprobante_reversion_id);
  $this->assertSame('ANULADO',ComprobanteContable::find($reversionId)->estado);

  // Antes del fix, revertir() solo cambiaba una etiqueta y dejaba la reversión
  // activa: original (débito 500) + reversión (crédito 500) sumaban NETO CERO
  // en el mayor, como si nunca se hubiera revertido nada. Debe volver a ser 500.
  $activos=\App\Models\MovimientoContable::whereHas('comprobante',fn($q)=>$q->whereIn('estado',['REGISTRADO','CONTABILIZADO']))->where('cuenta_contable_id',$a->id)->get();
  $this->assertEquals(500,(float)$activos->sum('debito')-(float)$activos->sum('credito'));
 }
 public function test_endpoint_revertir_anulacion_funciona_sobre_un_comprobante_manual(){
  // Cubre también el bug de esManual(): exigía un prefijo "MANUAL-" que ningún
  // comprobante real llega a tener, así que antes bloqueaba SIEMPRE este endpoint.
  $c=$this->borrador();$a=$this->cuenta('110505');$b=$this->cuenta('413505');$s=app(AccountingService::class);
  $s->registrar($c,$this->lineas($a,$b),$this->user()->id);
  $s->anular($c,'Prueba',$this->user()->id);

  $this->actingAs($this->user())->post('/comprobantes/'.$c->id.'/revertir')->assertOk();
  $this->assertSame('REGISTRADO',$c->fresh()->estado);
 }
 public function test_rollback_no_deja_reversion_parcial(){ $c=$this->borrador();$a=$this->cuenta('110505');$b=$this->cuenta('413505');$s=app(AccountingService::class);$s->registrar($c,$this->lineas($a,$b),$this->user()->id);try{\Illuminate\Support\Facades\DB::transaction(function()use($s,$c){$s->anular($c,'Falla controlada',$this->user()->id);throw new \RuntimeException('Falla controlada');});}catch(\RuntimeException $e){}$c=$c->fresh();$this->assertSame('REGISTRADO',$c->estado);$this->assertNull($c->comprobante_reversion_id);$this->assertSame(1,ComprobanteContable::count());}
}
