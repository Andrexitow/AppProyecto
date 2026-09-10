<?php

namespace App\Http\Controllers;

use App\Models\Bodega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BodegaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $bodegas = Bodega::all();
        return view('bodegas.index', compact('bodegas'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'descripcion' => 'required|string|max:255'
        ]);
        $bodega = Bodega::create([
            'descripcion' => $request->descripcion
        ]);
        return response()->json($bodega);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return response()->json(Bodega::findOrFail($id));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'descripcion' => 'required|string|max:255',
        ]);

        $bodega = Bodega::findOrFail($id);
        $bodega->update($validated);

        return response()->json([
            'success' => true,
            'data' => $bodega,
            'message' => 'Bodega actualizada correctamente.',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $bodega = Bodega::findOrFail($id);

        $dependencias = [
            ['tabla' => 'inventarios', 'mensaje' => 'tiene existencias de inventario'],
            ['tabla' => 'cajas', 'mensaje' => 'está asignada a una caja'],
            ['tabla' => 'ajustes', 'mensaje' => 'tiene ajustes de inventario'],
        ];

        foreach ($dependencias as $dependencia) {
            if (DB::table($dependencia['tabla'])->where('bodega_id', $bodega->id)->exists()) {
                return response()->json([
                    'message' => 'No se puede eliminar la bodega porque ' . $dependencia['mensaje'] . '.',
                ], 422);
            }
        }

        $bodega->delete();

        return response()->json([
            'success' => true,
            'message' => 'Bodega eliminada correctamente.',
        ]);
    }
}
