<?php

namespace App\Http\Controllers;

use App\Models\CuentaContable;
use App\Services\ReporteContableService;
use Illuminate\Http\Request;

class InformeContableController extends Controller
{
    public function __construct(private ReporteContableService $reportes)
    {
    }

    public function index()
    {
        return view('informes-contables.index');
    }

    private function rango(Request $request): array
    {
        $hoy = now()->toDateString();
        $primerDia = now()->startOfMonth()->toDateString();

        return [
            $request->query('desde', $primerDia),
            $request->query('hasta', $hoy),
        ];
    }

    public function balancePrueba(Request $request)
    {
        [$desde, $hasta] = $this->rango($request);

        return response()->json($this->reportes->balancePrueba($desde, $hasta) + [
            'desde' => $desde,
            'hasta' => $hasta,
        ]);
    }

    public function libroDiario(Request $request)
    {
        [$desde, $hasta] = $this->rango($request);
        $movimientos = $this->reportes->libroDiario($desde, $hasta, $request->query('tipo') ?: null);

        return response()->json([
            'desde' => $desde,
            'hasta' => $hasta,
            'movimientos' => $movimientos,
            'total_debito' => round($movimientos->sum('debito'), 2),
            'total_credito' => round($movimientos->sum('credito'), 2),
        ]);
    }

    public function libroMayor(Request $request)
    {
        $request->validate(['cuenta_id' => 'required|exists:cuentas_contables,id']);
        [$desde, $hasta] = $this->rango($request);

        return response()->json($this->reportes->libroMayor($desde, $hasta, (int) $request->query('cuenta_id')) + [
            'desde' => $desde,
            'hasta' => $hasta,
        ]);
    }

    public function libroAuxiliar(Request $request)
    {
        $request->validate([
            'cuenta_id' => 'required|exists:cuentas_contables,id',
            'tercero_id' => 'nullable|exists:terceros,id',
        ]);
        [$desde, $hasta] = $this->rango($request);

        return response()->json($this->reportes->libroAuxiliar(
            $desde,
            $hasta,
            (int) $request->query('cuenta_id'),
            $request->query('tercero_id') ? (int) $request->query('tercero_id') : null
        ) + [
            'desde' => $desde,
            'hasta' => $hasta,
        ]);
    }

    public function estadoResultados(Request $request)
    {
        [$desde, $hasta] = $this->rango($request);

        return response()->json($this->reportes->estadoResultados($desde, $hasta) + [
            'desde' => $desde,
            'hasta' => $hasta,
        ]);
    }

    public function balanceGeneral(Request $request)
    {
        $hasta = $request->query('hasta', now()->toDateString());

        return response()->json($this->reportes->balanceGeneral($hasta) + [
            'hasta' => $hasta,
        ]);
    }

    /** Catálogo liviano de cuentas para los selectores de Mayor/Auxiliar. */
    public function catalogoCuentas()
    {
        return response()->json([
            'data' => CuentaContable::where('estado', true)
                ->where('permite_movimientos', true)
                ->orderBy('codigo')
                ->get(['id', 'codigo', 'nombre', 'requiere_tercero']),
        ]);
    }
}
