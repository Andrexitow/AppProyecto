<?php

namespace App\Http\Controllers;

use App\Models\CategoriaPos;
use Illuminate\Http\Request;

class CategoriaPosController extends Controller
{
    public function index()
    {
        return response()->json(CategoriaPos::orderBy('orden')->get());
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'icono'  => 'required|string|max:10',
            'orden'  => 'nullable|integer',
        ]);

        $cat = CategoriaPos::create($request->only('nombre', 'icono', 'orden'));
        return response()->json($cat);
    }

    public function update(Request $request, CategoriaPos $categoriaPos)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'icono'  => 'required|string|max:10',
            'orden'  => 'nullable|integer',
        ]);

        $categoriaPos->update($request->only('nombre', 'icono', 'orden'));
        return response()->json($categoriaPos);
    }

    public function destroy(CategoriaPos $categoriaPos)
    {
        $categoriaPos->delete();
        return response()->json(['ok' => true]);
    }
}
