<?php

namespace App\Http\Controllers;

use App\Models\Tercero;
use Illuminate\Http\Request;

class TerceroController extends Controller
{
    public function index(Request $request)
    {
        $query = Tercero::query();

        // Filtro de búsqueda (Buscador general)
        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nombre', 'like', "%$search%")
                    ->orWhere('apellido', 'like', "%$search%")
                    ->orWhere('razon_social', 'like', "%$search%")
                    ->orWhere('cedula', 'like', "%$search%")
                    ->orWhere('nit', 'like', "%$search%")
                    ->orWhere('celular', 'like', "%$search%");
            });
        }

        // Ordenamos por los más recientes
        $terceros = $query->orderBy('id', 'desc')->get();

        // Si la petición es AJAX (para el buscador en vivo), devolvemos solo la tabla
        if ($request->ajax()) {
            return view('terceros.partials.tabla', compact('terceros'));
        }

        return view('terceros.index', compact('terceros'));
    }

    public function store(Request $request)
    {
        $rules = [
            'tipo' => 'required|in:persona,empresa',
            'email' => 'nullable|email',
            'celular' => 'required',
            'direccion' => 'nullable|string',

            // persona
            'nombre' => 'required_if:tipo,persona',
            'apellido' => 'required_if:tipo,persona',
            'cedula' => 'required_if:tipo,persona|unique:terceros,cedula',

            // empresa
            'razon_social' => 'required_if:tipo,empresa',
            'nit' => 'required_if:tipo,empresa|unique:terceros,nit',
        ];

        $messages = [
            'tipo.required' => 'Debe seleccionar el tipo de tercero',
            'celular.required' => 'El celular es obligatorio',
            'cedula.unique' => 'La cédula ya está registrada',
            'nit.unique' => 'El NIT ya está registrado',
            'nombre.required_if' => 'El nombre es obligatorio',
            'razon_social.required_if' => 'La razón social es obligatoria',
        ];

        $validated = $request->validate($rules, $messages);

        $tercero = Tercero::create($validated);

        return response()->json([
            'success' => true,
            'data' => $tercero,
            'message' => 'Tercero guardado correctamente'
        ]);
    }

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
