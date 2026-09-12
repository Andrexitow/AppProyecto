<?php

namespace App\Http\Controllers;

use App\Models\PeriodoContable;
use App\Services\AuditoriaService;
use App\Services\PeriodoContableService;
use Illuminate\Http\Request;

class PeriodoContableController extends Controller
{
    public function __construct(private PeriodoContableService $periodos)
    {
    }

    public function index()
    {
        return view('periodos-contables.index');
    }

    public function data()
    {
        $periodos = PeriodoContable::with(['cerradoPor:id,name', 'reabiertoPor:id,name'])->orderByDesc('fecha_inicio')->get();

        return response()->json(['data' => $periodos]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'nombre' => ['required', 'string', 'max:60'],
        ]);

        $periodo = $this->periodos->cerrarPeriodo($datos['fecha_inicio'], $datos['fecha_fin'], $datos['nombre'], $request->user()->id);

        AuditoriaService::registrar(
            $request->user(),
            'Períodos contables',
            'Cierre de período',
            "Cerró el período contable '{$periodo->nombre}' ({$periodo->fecha_inicio->format('d/m/Y')} - {$periodo->fecha_fin->format('d/m/Y')}).",
            $request,
            $periodo->nombre
        );

        return response()->json(['success' => true, 'data' => $periodo], 201);
    }

    public function reabrir(Request $request, PeriodoContable $periodo)
    {
        $this->periodos->reabrirPeriodo($periodo, $request->user()->id);

        AuditoriaService::registrar(
            $request->user(),
            'Períodos contables',
            'Reapertura de período',
            "Reabrió el período contable '{$periodo->nombre}' ({$periodo->fecha_inicio->format('d/m/Y')} - {$periodo->fecha_fin->format('d/m/Y')}), permitiendo modificar comprobantes en ese rango de nuevo.",
            $request,
            $periodo->nombre
        );

        return response()->json(['success' => true]);
    }
}
