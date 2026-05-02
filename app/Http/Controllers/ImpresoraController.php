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
            'nombre' => 'required|string|max:50',
            'ip'     => 'required|ip',
            'puerto' => 'required|integer',
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
}
