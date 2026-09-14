<?php

namespace App\Http\Controllers;

use App\Models\ActivoFijo;
use App\Models\CuentaContable;
use App\Services\ActivoFijoService;
use Illuminate\Http\Request;

class ActivoFijoController extends Controller
{
    public function __construct(private ActivoFijoService $activos)
    {
    }

    public function index()
    {
        return view('activos-fijos.index');
    }

    public function data(Request $request)
    {
        $query = ActivoFijo::with(['cuentaActivo:id,codigo,nombre', 'tercero']);

        if ($request->filled('estado')) {
            $query->where('estado', $request->query('estado'));
        }
        if ($request->filled('categoria')) {
            $query->where('categoria', $request->query('categoria'));
        }

        $activos = $query->orderByDesc('id')->get()->map(function (ActivoFijo $a) {
            return [
                'id' => $a->id,
                'codigo' => $a->codigo,
                'nombre' => $a->nombre,
                'categoria' => $a->categoria,
                'categoria_nombre' => ActivoFijo::CATEGORIAS[$a->categoria]['nombre'] ?? $a->categoria,
                'cuenta' => $a->cuentaActivo->codigo . ' - ' . $a->cuentaActivo->nombre,
                'fecha_adquisicion' => $a->fecha_adquisicion->toDateString(),
                'valor_adquisicion' => (float) $a->valor_adquisicion,
                'valor_residual' => (float) $a->valor_residual,
                'vida_util_meses' => $a->vida_util_meses,
                'depreciacion_mensual' => $a->depreciacion_mensual,
                'depreciacion_acumulada' => (float) $a->depreciacion_acumulada,
                'valor_libros' => $a->valor_libros,
                'estado' => $a->estado,
                'fecha_baja' => $a->fecha_baja?->toDateString(),
                'tercero' => $a->tercero?->nombre_completo,
            ];
        });

        $metricas = [
            'total' => ActivoFijo::count(),
            'activos' => ActivoFijo::where('estado', 'activo')->count(),
            'de_baja' => ActivoFijo::where('estado', 'de_baja')->count(),
            'valor_libros_total' => round((float) ActivoFijo::where('estado', 'activo')->get()->sum('valor_libros'), 2),
        ];

        return response()->json(['data' => $activos, 'metricas' => $metricas, 'categorias' => ActivoFijo::CATEGORIAS]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:150'],
            'descripcion' => ['nullable', 'string'],
            'categoria' => ['required', 'in:' . implode(',', array_keys(ActivoFijo::CATEGORIAS))],
            'fecha_adquisicion' => ['required', 'date'],
            'valor_adquisicion' => ['required', 'numeric', 'gt:0'],
            'valor_residual' => ['nullable', 'numeric', 'gte:0'],
            'vida_util_meses' => ['required', 'integer', 'gt:0'],
            'cuenta_contrapartida_id' => ['required', 'exists:cuentas_contables,id'],
            'tercero_id' => ['nullable', 'exists:terceros,id'],
            'observaciones' => ['nullable', 'string'],
        ]);

        $activo = $this->activos->registrar($datos, $request->user()->id);

        return response()->json(['success' => true, 'data' => $activo, 'message' => 'Activo fijo registrado y contabilizado correctamente.'], 201);
    }

    public function depreciar(Request $request)
    {
        $datos = $request->validate([
            'periodo' => ['required', 'date_format:Y-m'],
        ]);

        $resultado = $this->activos->depreciarPeriodo($datos['periodo'], $request->user()->id);

        $mensaje = $resultado['procesados'] > 0
            ? "Depreciación de {$datos['periodo']} contabilizada: {$resultado['procesados']} activo(s) por " . number_format($resultado['total_depreciado'], 2)
            : 'No hay activos pendientes de depreciar para este período (ya se procesaron o no tienen valor depreciable restante).';

        return response()->json(['success' => true] + $resultado + ['message' => $mensaje]);
    }

    public function baja(Request $request, ActivoFijo $activoFijo)
    {
        $datos = $request->validate([
            'fecha' => ['required', 'date'],
            'motivo' => ['nullable', 'string', 'max:255'],
        ]);

        $activo = $this->activos->darDeBaja($activoFijo, $datos['fecha'], $request->user()->id, $datos['motivo'] ?? null);

        return response()->json(['success' => true, 'data' => $activo, 'message' => 'Activo dado de baja correctamente.']);
    }

    public function show(ActivoFijo $activoFijo)
    {
        $activoFijo->load(['depreciaciones' => fn ($q) => $q->orderBy('periodo')]);

        return response()->json($activoFijo);
    }

    /** Catálogo liviano de cuentas de contrapartida (caja/bancos/proveedores) para el formulario de alta. */
    public function cuentasContrapartida()
    {
        return response()->json([
            'data' => CuentaContable::where('estado', true)->where('permite_movimientos', true)
                ->orderBy('codigo')->get(['id', 'codigo', 'nombre']),
        ]);
    }
}
