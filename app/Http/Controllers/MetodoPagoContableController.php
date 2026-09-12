<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionContable;
use App\Models\CuentaContable;
use App\Models\MetodoPagoContable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\PlanCuentasService;

class MetodoPagoContableController extends Controller
{
    public function index() { return view('metodos-pago-contables.index'); }

    public function data()
    {
        return response()->json(['data' => MetodoPagoContable::with('configuracion.cuenta')->orderBy('metodo_pago')->get()]);
    }

    public function opciones()
    {
        return response()->json(['data' => MetodoPagoContable::where('estado', true)->orderBy('metodo_pago')->get(['id','metodo_pago','configuracion_clave'])]);
    }

    public function catalogoCuentas()
    {
        return response()->json(['data' => CuentaContable::where('estado', true)->where('permite_movimientos', true)->orderBy('codigo')->get(['id','codigo','nombre'])]);
    }

    public function store(Request $request, PlanCuentasService $plan)
    {
        $datos = $this->validar($request);
        $plan->cuentaOperable((int) $datos['cuenta_contable_id']);
        $clave = $this->guardarConfiguracion($datos);
        $metodo = MetodoPagoContable::create(['metodo_pago' => mb_strtolower($datos['metodo_pago']), 'configuracion_clave' => $clave, 'estado' => $datos['estado'] ?? true]);
        return response()->json($metodo, 201);
    }

    public function update(Request $request, MetodoPagoContable $metodoPagoContable, PlanCuentasService $plan)
    {
        $datos = $this->validar($request, $metodoPagoContable->id);
        $plan->cuentaOperable((int) $datos['cuenta_contable_id']);
        $clave = $this->guardarConfiguracion($datos, $metodoPagoContable->configuracion_clave);
        $metodoPagoContable->update(['metodo_pago' => mb_strtolower($datos['metodo_pago']), 'configuracion_clave' => $clave, 'estado' => $datos['estado'] ?? true]);
        return response()->json($metodoPagoContable->fresh());
    }

    private function validar(Request $request, ?int $id = null): array
    {
        return $request->validate(['metodo_pago' => ['required','string','max:50',Rule::unique('metodos_pago_contables')->ignore($id)], 'cuenta_contable_id' => ['required','exists:cuentas_contables,id'], 'estado' => ['boolean']]);
    }

    private function guardarConfiguracion(array $datos, ?string $claveExistente = null): string
    {
        $nombreNormalizado = strtoupper($datos['metodo_pago']);
        $clave = $claveExistente ?: 'MEDIO_PAGO_' . preg_replace('/[^A-Z0-9]+/', '_', $nombreNormalizado);
        ConfiguracionContable::updateOrCreate(['clave' => $clave], ['nombre' => 'Medio de pago: ' . $datos['metodo_pago'], 'cuenta_contable_id' => $datos['cuenta_contable_id'], 'estado' => true]);
        return $clave;
    }
}
