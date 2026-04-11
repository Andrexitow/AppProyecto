<?php

namespace App\Http\Controllers;

use App\Models\Tercero;
use Illuminate\Http\Request;

class TerceroController extends Controller
{
    public function buscar(Request $request)
    {
        $query = $request->query('query');

        $terceros = Tercero::query()
            ->where(function ($q) use ($query) {
                $q->where('cedula', 'like', "%$query%")
                    ->orWhere('nit', 'like', "%$query%")
                    ->orWhere('nombre', 'like', "%$query%")
                    ->orWhere('apellido', 'like', "%$query%")
                    ->orWhere('razon_social', 'like', "%$query%");
            })
            ->limit(10)
            ->get();

        return response()->json($terceros);
    }

    public function buscarPorDocumento(Request $request)
    {
        $doc = $request->doc;

        $tercero = Tercero::where('cedula', $doc)
            ->orWhere('nit', $doc)
            ->first();

        return response()->json($tercero);
    }
}
