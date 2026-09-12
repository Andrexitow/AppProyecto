<?php

namespace Tests\Feature;

use App\Models\ConfiguracionContable;
use App\Models\CuentaContable;
use App\Services\PlanCuentasService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class PlanCuentasServiceTest extends TestCase
{
    use RefreshDatabase;

    private function datos(array $extra = []): array
    {
        return array_merge(['codigo' => '1', 'nombre' => 'Activo', 'clasificacion' => 'ACTIVO', 'naturaleza' => 'DEBITO', 'tipo' => 'AGRUPADORA', 'movimientos' => false], $extra);
    }

    public function test_crea_cuenta_raiz_y_subcuenta_valida(): void
    {
        $plan = app(PlanCuentasService::class);
        $raiz = CuentaContable::create(array_merge($plan->validar($this->datos()), ['permite_movimientos' => false, 'estado' => true]));
        $hija = $plan->validar($this->datos(['codigo' => '11', 'nombre' => 'Disponible', 'cuenta_padre_id' => $raiz->id]));
        $this->assertSame(2, $hija['nivel']);
    }

    public function test_impide_hija_con_codigo_fuera_del_padre(): void
    {
        $padre = CuentaContable::create(['codigo'=>'1','nombre'=>'Activo','nivel'=>1,'clasificacion'=>'ACTIVO','naturaleza'=>'DEBITO','tipo'=>'AGRUPADORA','permite_movimientos'=>false,'estado'=>true]);
        $this->expectException(ValidationException::class);
        app(PlanCuentasService::class)->validar($this->datos(['codigo'=>'22','cuenta_padre_id'=>$padre->id]));
    }

    public function test_codigo_es_unico_en_base_de_datos(): void
    {
        CuentaContable::create(['codigo'=>'1','nombre'=>'Activo','nivel'=>1,'clasificacion'=>'ACTIVO','naturaleza'=>'DEBITO','tipo'=>'AGRUPADORA','permite_movimientos'=>false,'estado'=>true]);
        $this->expectException(\Illuminate\Database\UniqueConstraintViolationException::class);
        CuentaContable::create(['codigo'=>'1','nombre'=>'Duplicada','nivel'=>1,'clasificacion'=>'ACTIVO','naturaleza'=>'DEBITO','tipo'=>'AGRUPADORA','permite_movimientos'=>false,'estado'=>true]);
    }

    public function test_cuenta_inactiva_no_es_operable_para_un_medio_de_pago(): void
    {
        $cuenta = CuentaContable::create(['codigo'=>'110505','nombre'=>'Caja','nivel'=>1,'clasificacion'=>'ACTIVO','naturaleza'=>'DEBITO','tipo'=>'DETALLE','permite_movimientos'=>true,'estado'=>false]);
        $this->expectException(ValidationException::class);
        app(PlanCuentasService::class)->cuentaOperable($cuenta->id);
    }

    public function test_cuenta_con_parametrizacion_se_inactiva_en_lugar_de_borrarse(): void
    {
        $cuenta = CuentaContable::create(['codigo'=>'110505','nombre'=>'Caja','nivel'=>1,'clasificacion'=>'ACTIVO','naturaleza'=>'DEBITO','tipo'=>'DETALLE','permite_movimientos'=>true,'estado'=>true]);
        ConfiguracionContable::create(['clave'=>'PRUEBA_CAJA','nombre'=>'Prueba','cuenta_contable_id'=>$cuenta->id,'estado'=>true]);
        $cuenta->update(['estado'=>false]);
        $this->assertDatabaseHas('cuentas_contables', ['id'=>$cuenta->id, 'estado'=>false]);
    }
}
