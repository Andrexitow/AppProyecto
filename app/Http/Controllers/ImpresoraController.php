<?php

namespace App\Http\Controllers;

use App\Models\Impresora;
use Illuminate\Http\Request;

class ImpresoraController extends Controller
{
    public function index()
    {
        $impresoras = Impresora::all();
        return view('impresoras.index', compact('impresoras'));
    }

    // Retorna solo los datos en JSON (para refrescar la tabla)
    public function listar()
    {
        return response()->json(Impresora::all());
    }

    public function store(Request $request)
    {
        // 1. Validaciones
        $validated = $request->validate([
            'id'     => 'nullable|exists:impresoras,id',
            'nombre' => 'required|string|max:50',
            'ip'     => 'required|ip',
            'puerto' => 'required|integer|between:1,65535',
        ]);

        try {
            // 2. Guardar o Actualizar
            \App\Models\Impresora::updateOrCreate(
                ['id' => $request->id],
                [
                    'nombre' => strtoupper($request->nombre), // Guardamos en mayúsculas
                    'ip'     => $request->ip,
                    'puerto' => $request->puerto,
                    'activa' => 1
                ]
            );

            // 3. RESPUESTA JSON PURA (Sin HTML)
            return response()->json([
                'status' => 'success',
                'message' => 'Impresora guardada correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $impresora = Impresora::findOrFail($id);
            $impresora->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Impresora eliminada correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'No se puede eliminar una impresora que está asignada a una caja. Reasigna la caja primero.'
            ], 422);
        }
    }
}
