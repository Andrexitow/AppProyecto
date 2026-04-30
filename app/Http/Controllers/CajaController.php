<?php

namespace App\Http\Controllers;

use App\Models\Bodega;
use App\Models\Caja;
use App\Models\User;
use Illuminate\Http\Request;

class CajaController extends Controller
{
    public function index()
    {
        // Traemos las cajas con sus relaciones para la tabla/cards
        $cajas = Caja::with(['bodega', 'cajero'])->get();

        // Traemos bodegas y usuarios para los select del modal de creación
        $bodegas = Bodega::all();
        $usuarios = User::all();

        return view('cajas.index', compact('cajas', 'bodegas', 'usuarios'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre'    => 'required|string|max:255',
                'prefijo'   => 'required|string|max:10',
                'bodega_id' => 'required|exists:bodegas,id',
                'user_id'   => 'nullable|exists:users,id'
            ]);

            $caja = \App\Models\Caja::create($request->all());

            // IMPORTANTE: Devolver JSON, no un redirect
            return response()->json([
                'status'  => 'success',
                'message' => 'Caja creada correctamente',
                'data'    => $caja
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 422); // Error de validación o proceso
        }
    }

    public function edit($id)
    {
        $caja = Caja::findOrFail($id);
        return response()->json($caja);
    }

    public function update(Request $request, $id)
    {
        try {
            $caja = Caja::findOrFail($id);

            $request->validate([
                'nombre'    => 'required|string|max:255',
                'prefijo'   => 'required|string|max:10',
                'bodega_id' => 'required|exists:bodegas,id',
                'user_id'   => 'nullable|exists:users,id',
            ]);

            $caja->update([
                'nombre'    => $request->nombre,
                'prefijo'   => $request->prefijo,
                'bodega_id' => $request->bodega_id,
                'user_id'   => $request->user_id ?: null,
                'activa'    => $request->input('activa', '0') === '1' ? 1 : 0,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Caja actualizada correctamente',
                'data'    => $caja
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 422);
        }
    }

    public function destroy($id)
    {
        try {
            $caja = Caja::findOrFail($id);
            $caja->delete();

            return response()->json([
                'status'  => 'success',
                'message' => 'Caja eliminada correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 422);
        }
    }
}
