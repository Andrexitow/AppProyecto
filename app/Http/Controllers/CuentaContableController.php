<?php

namespace App\Http\Controllers;

use App\Models\CuentaContable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Services\PlanCuentasService;

class CuentaContableController extends Controller
{
    /**
     * Muestra la vista del Plan de Cuentas.
     */
    public function index(Request $request)
    {
        return view('cuentascontable.index');
    }

    /**
     * Devuelve el listado plano de cuentas contables en JSON.
     * El árbol se construye en el frontend a partir de cuenta_padre_id.
     */
    public function data(Request $request)
    {
        $cuentas = CuentaContable::query()
            ->orderBy('codigo')
            ->get()
            ->map(fn ($c) => $this->transformar($c));

        return response()->json([
            'data' => $cuentas,
        ]);
    }

    /**
     * Muestra el detalle de una sola cuenta.
     */
    public function show(CuentaContable $cuentaContable)
    {
        return response()->json(
            $this->transformar($cuentaContable->load('padre'))
        );
    }

    /**
     * Crea una nueva cuenta contable.
     */
    public function store(Request $request, PlanCuentasService $plan)
    {
        $datos = $this->validarYMapear($request, null, $plan);

        $cuenta = CuentaContable::create($datos);

        return response()->json($this->transformar($cuenta), 201);
    }

    /**
     * Actualiza una cuenta contable existente.
     */
    public function update(Request $request, CuentaContable $cuentaContable, PlanCuentasService $plan)
    {
        $datos = $this->validarYMapear($request, $cuentaContable->id, $plan);

        $cuentaContable->update($datos);

        return response()->json($this->transformar($cuentaContable->fresh()));
    }

    /**
     * Elimina una cuenta contable, siempre que no tenga subcuentas asociadas.
     */
    public function destroy(CuentaContable $cuentaContable)
    {
        if ($cuentaContable->hijos()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar: la cuenta tiene subcuentas asociadas.',
            ], 422);
        }

        if ($cuentaContable->movimientos()->exists() || $cuentaContable->configuraciones()->exists()) {
            $cuentaContable->update(['estado' => false]);
            return response()->json(['message' => 'La cuenta tiene historial; fue inactivada para conservar la trazabilidad.']);
        }
        $cuentaContable->delete();

        return response()->json([
            'message' => 'Cuenta eliminada correctamente',
        ]);
    }

    /**
     * Valida el request (que usa los nombres del frontend: 'movimientos', 'activa')
     * y devuelve el array ya mapeado a las columnas reales de la BD
     * ('permite_movimientos', 'estado'). Calcula además el nivel según el padre.
     */
    private function validarYMapear(Request $request, $idActual = null, ?PlanCuentasService $plan = null): array
    {
        $validado = $request->validate([
            'codigo' => [
                'required', 'string', 'max:20',
                Rule::unique('cuentas_contables', 'codigo')->ignore($idActual),
            ],
            'nombre' => ['required', 'string', 'max:150'],
            'cuenta_padre_id' => ['nullable', 'exists:cuentas_contables,id'],
            'clasificacion' => ['required', Rule::in(['ACTIVO', 'PASIVO', 'PATRIMONIO', 'INGRESO', 'COSTO', 'GASTO'])],
            'naturaleza' => ['required', Rule::in(['DEBITO', 'CREDITO'])],
            'tipo' => ['required', Rule::in(['AGRUPADORA', 'DETALLE'])],
            'movimientos' => ['boolean'],
            'requiere_tercero' => ['boolean'],
            'requiere_centro_costo' => ['boolean'],
            'activa' => ['boolean'],
        ]);

        $validado = $plan->validar($validado, $idActual ? CuentaContable::find($idActual) : null);

        return [
            'codigo' => $validado['codigo'],
            'nombre' => $validado['nombre'],
            'cuenta_padre_id' => $validado['cuenta_padre_id'] ?? null,
            'clasificacion' => $validado['clasificacion'],
            'naturaleza' => $validado['naturaleza'],
            'tipo' => $validado['tipo'],
            'permite_movimientos' => $validado['movimientos'] ?? false,
            'requiere_tercero' => $validado['requiere_tercero'] ?? false,
            'requiere_centro_costo' => $validado['requiere_centro_costo'] ?? false,
            'estado' => $validado['activa'] ?? true,
            'nivel' => $validado['nivel'],
        ];
    }

    /**
     * Transforma el modelo a la forma exacta que espera el frontend.
     */
    private function transformar(CuentaContable $c): array
    {
        return [
            'id' => $c->id,
            'codigo' => $c->codigo,
            'nombre' => $c->nombre,
            'clasificacion' => $c->clasificacion,
            'naturaleza' => $c->naturaleza,
            'tipo' => $c->tipo,
            'cuenta_padre_id' => $c->cuenta_padre_id,
            'nivel' => $c->nivel,
            'movimientos' => (bool) $c->permite_movimientos,
            'requiere_tercero' => (bool) $c->requiere_tercero,
            'requiere_centro_costo' => (bool) $c->requiere_centro_costo,
            'activa' => (bool) $c->estado,
        ];
    }
}
