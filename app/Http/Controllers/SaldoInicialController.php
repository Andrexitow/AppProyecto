<?php

namespace App\Http\Controllers;

use App\Models\ComprobanteContable;
use App\Models\CuentaContable;
use App\Services\SaldoInicialService;
use Illuminate\Http\Request;

class SaldoInicialController extends Controller
{
    public function __construct(private SaldoInicialService $saldos)
    {
    }

    public function index()
    {
        return view('saldos-iniciales.index');
    }

    /** Cuentas de detalle disponibles para cargar saldo (todas las que admiten movimientos). */
    public function cuentas()
    {
        return response()->json([
            'data' => CuentaContable::where('estado', true)->where('permite_movimientos', true)
                ->orderBy('codigo')->get(['id', 'codigo', 'nombre', 'naturaleza', 'requiere_tercero']),
        ]);
    }

    /** Historial de cargas de saldos iniciales ya contabilizadas. */
    public function historial()
    {
        $comprobantes = ComprobanteContable::whereHas('tipoDocumento', fn ($q) => $q->where('codigo', 'SI'))
            ->whereIn('estado', ['REGISTRADO', 'ANULADO'])
            ->withCount('movimientos')
            ->orderByDesc('fecha')
            ->get(['id', 'prefijo', 'numero', 'fecha', 'descripcion', 'estado', 'total_debito', 'total_credito']);

        return response()->json(['data' => $comprobantes]);
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'fecha' => ['required', 'date'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            'lineas' => ['required', 'array', 'min:2'],
            'lineas.*.cuenta_id' => ['required', 'exists:cuentas_contables,id'],
            'lineas.*.tercero_id' => ['nullable', 'exists:terceros,id'],
            'lineas.*.debito' => ['nullable', 'numeric', 'min:0'],
            'lineas.*.credito' => ['nullable', 'numeric', 'min:0'],
        ]);

        $comprobante = $this->saldos->registrar($datos, $request->user()->id);

        return response()->json([
            'success' => true,
            'data' => $comprobante,
            // numero ya incluye el prefijo (lo arma AccountingService al numerar).
            'message' => "Saldos iniciales contabilizados en el comprobante {$comprobante->numero}.",
        ], 201);
    }
}
