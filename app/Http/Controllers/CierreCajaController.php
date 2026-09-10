<?php

namespace App\Http\Controllers;

use App\Models\CierreCaja;
use Illuminate\Http\Request;

class CierreCajaController extends Controller
{
    public function index()
    {
        return view('cierres-caja.index');
    }

    public function data(Request $request)
    {
        $cierres = CierreCaja::with(['caja:id,nombre', 'usuario:id,name'])
            ->when($request->desde, fn ($query, $desde) => $query->whereDate('fecha_inicio', '>=', $desde))
            ->when($request->hasta, fn ($query, $hasta) => $query->whereDate('fecha_inicio', '<=', $hasta))
            ->latest('fecha_inicio')
            ->get();

        return response()->json(['data' => $cierres]);
    }
}
