<?php

namespace App\Http\Controllers;

use App\Models\Bodega;
use App\Services\KardexReporteService;
use Illuminate\Http\Request;

class KardexController extends Controller
{
    public function __construct(private KardexReporteService $reportes)
    {
    }

    public function index()
    {
        return view('kardex.index');
    }

    public function bodegas()
    {
        return response()->json(['data' => Bodega::orderBy('descripcion')->get(['id', 'descripcion'])]);
    }

    public function productos(Request $request)
    {
        $buscar = trim((string) $request->string('buscar'));

        $productos = \App\Models\Producto::query()
            ->when($request->filled('bodega_id'), function ($query) use ($request) {
                $query->whereHas('inventarios', fn ($q) => $q->where('bodega_id', $request->integer('bodega_id')));
            })
            ->when($buscar !== '', function ($query) use ($buscar) {
                $query->where(function ($sub) use ($buscar) {
                    $sub->where('descripcion', 'like', '%' . $buscar . '%')
                        ->orWhere('codigo', 'like', '%' . $buscar . '%')
                        ->orWhere('codigo_barras', 'like', '%' . $buscar . '%');
                });
            })
            ->orderBy('descripcion')
            ->limit(20)
            ->get(['id', 'codigo', 'descripcion']);

        return response()->json(['data' => $productos]);
    }

    public function movimientos(Request $request)
    {
        $datos = $request->validate([
            'producto_id' => 'required|exists:productos,id',
            'bodega_id' => 'nullable|exists:bodegas,id',
            'desde' => 'required|date',
            'hasta' => 'required|date|after_or_equal:desde',
        ]);

        $bodegaId = $request->filled('bodega_id') ? (int) $datos['bodega_id'] : null;

        return response()->json($this->reportes->movimientos((int) $datos['producto_id'], $bodegaId, $datos['desde'], $datos['hasta']));
    }

    public function valorizacion(Request $request)
    {
        $bodegaId = $request->filled('bodega_id') ? $request->integer('bodega_id') : null;

        return response()->json($this->reportes->valorizacion($bodegaId));
    }
}
