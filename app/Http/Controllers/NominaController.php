<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use App\Models\LiquidacionNomina;
use App\Models\ParametroNomina;
use App\Services\NominaService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class NominaController extends Controller
{
    public function __construct(private NominaService $nomina)
    {
    }

    public function index()
    {
        return view('nomina.index');
    }

    /* ── Empleados ── */

    public function empleados(Request $request)
    {
        $query = Empleado::query();
        if ($request->filled('estado')) $query->where('estado', $request->query('estado'));

        return response()->json(['data' => $query->orderBy('nombre')->get()]);
    }

    public function storeEmpleado(Request $request)
    {
        $datos = $this->validarEmpleado($request);
        $datos['codigo'] = $this->siguienteCodigoEmpleado();
        $empleado = Empleado::create($datos);

        return response()->json(['success' => true, 'data' => $empleado, 'message' => 'Empleado registrado correctamente.'], 201);
    }

    public function updateEmpleado(Request $request, Empleado $empleado)
    {
        $empleado->update($this->validarEmpleado($request, $empleado));

        return response()->json(['success' => true, 'data' => $empleado->fresh(), 'message' => 'Empleado actualizado correctamente.']);
    }

    public function cambiarEstadoEmpleado(Request $request, Empleado $empleado)
    {
        $nuevoEstado = $empleado->estado === 'activo' ? 'inactivo' : 'activo';
        $empleado->update([
            'estado' => $nuevoEstado,
            'fecha_retiro' => $nuevoEstado === 'inactivo' ? ($request->input('fecha_retiro') ?: now()->toDateString()) : null,
        ]);

        return response()->json(['success' => true, 'message' => $nuevoEstado === 'activo' ? 'Empleado reactivado.' : 'Empleado retirado.']);
    }

    private function validarEmpleado(Request $request, ?Empleado $empleado = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'cedula' => ['required', 'string', 'max:20', Rule::unique('empleados', 'cedula')->ignore($empleado?->id)],
            'cargo' => ['nullable', 'string', 'max:100'],
            'fecha_ingreso' => ['required', 'date'],
            'salario_base' => ['required', 'numeric', 'gt:0'],
            'arl_tarifa' => ['nullable', 'numeric', 'gte:0'],
            'email' => ['nullable', 'email'],
            'celular' => ['nullable', 'string', 'max:30'],
            'cuenta_bancaria' => ['nullable', 'string', 'max:60'],
        ]);
    }

    private function siguienteCodigoEmpleado(): string
    {
        $ultimo = Empleado::orderByDesc('id')->value('id') ?? 0;
        return 'EMP-' . str_pad((string) ($ultimo + 1), 4, '0', STR_PAD_LEFT);
    }

    /* ── Parámetros legales ── */

    public function parametros()
    {
        return response()->json(ParametroNomina::vigente());
    }

    public function updateParametros(Request $request)
    {
        $datos = $request->validate([
            'smmlv' => ['required', 'numeric', 'gt:0'],
            'auxilio_transporte' => ['required', 'numeric', 'gte:0'],
            'salud_empleado_pct' => ['required', 'numeric', 'gte:0'],
            'pension_empleado_pct' => ['required', 'numeric', 'gte:0'],
            'salud_patronal_pct' => ['required', 'numeric', 'gte:0'],
            'pension_patronal_pct' => ['required', 'numeric', 'gte:0'],
            'cesantias_pct' => ['required', 'numeric', 'gte:0'],
            'intereses_cesantias_pct' => ['required', 'numeric', 'gte:0'],
            'prima_pct' => ['required', 'numeric', 'gte:0'],
            'vacaciones_pct' => ['required', 'numeric', 'gte:0'],
            'sena_pct' => ['required', 'numeric', 'gte:0'],
            'icbf_pct' => ['required', 'numeric', 'gte:0'],
            'caja_compensacion_pct' => ['required', 'numeric', 'gte:0'],
        ]);

        $parametros = ParametroNomina::vigente();
        $parametros->update($datos);

        return response()->json(['success' => true, 'data' => $parametros->fresh(), 'message' => 'Parámetros de nómina actualizados.']);
    }

    /* ── Liquidaciones ── */

    public function liquidaciones()
    {
        return response()->json([
            'data' => LiquidacionNomina::orderByDesc('periodo')->withCount('detalles')->get(),
        ]);
    }

    public function liquidar(Request $request)
    {
        $datos = $request->validate([
            'periodo' => ['required', 'date_format:Y-m'],
            'fecha_pago' => ['required', 'date'],
        ]);

        $liquidacion = $this->nomina->liquidarPeriodo($datos['periodo'], $datos['fecha_pago'], $request->user()->id);

        return response()->json(['success' => true, 'data' => $liquidacion, 'message' => "Nómina de {$datos['periodo']} liquidada y contabilizada correctamente."], 201);
    }

    public function anularLiquidacion(Request $request, LiquidacionNomina $liquidacion)
    {
        $motivo = $request->validate(['motivo' => ['required', 'string', 'max:255']])['motivo'];
        $this->nomina->anular($liquidacion, $motivo, $request->user()->id);

        return response()->json(['success' => true, 'message' => 'Liquidación anulada correctamente.']);
    }

    /** Detalle completo de una liquidación (para el desprendible de pago imprimible). */
    public function show(LiquidacionNomina $liquidacion)
    {
        $liquidacion->load('detalles.empleado');

        return response()->json($liquidacion);
    }
}
