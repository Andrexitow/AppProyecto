<?php

namespace App\Http\Controllers;

use App\Models\Bodega;
use App\Models\Caja;
use App\Models\Impresora;
use App\Models\User;
use Illuminate\Http\Request;

class CajaController extends Controller
{
    public function index()
    {
        // Traemos las cajas con sus relaciones para la tabla/cards
        $cajas = Caja::with(['bodega', 'cajero'])->get();
        $impresoras = Impresora::all();

        // Traemos bodegas y usuarios para los select del modal de creación
        $bodegas = Bodega::all();
        $usuarios = User::all();

        return view('cajas.index', compact('cajas', 'bodegas', 'usuarios', 'impresoras'));
    }

    public function store(Request $request)
    {
        try {
            // 1. Validaciones incluyendo los nuevos campos del formulario
            $request->validate([
                'nombre'         => 'required|string|max:255',
                'prefijo'        => 'required|string|max:10',
                'proximo_numero' => 'required|integer|min:1', // Nuevo campo validado
                'bodega_id'      => 'required|exists:bodegas,id',
                'impresora_id'   => 'nullable|exists:impresoras,id', // Nuevo campo validado
                'user_id'        => 'nullable|exists:users,id',
            ]);

            // 2. Extraer los datos y estandarizar el prefijo a MAYÚSCULAS
            $datos = $request->all();
            $datos['prefijo'] = strtoupper($request->prefijo);

            // Manejo del checkbox 'activa' (si viene en el request toma 1, sino 0)
            $datos['activa'] = $request->has('activa') ? 1 : 0;

            // 3. Crear la caja con todos los parámetros
            $caja = \App\Models\Caja::create($datos);

            // Devolver JSON para la integración asíncrona (AJAX/Fetch)
            return response()->json([
                'status'  => 'success',
                'message' => 'Caja creada y configurada correctamente',
                'data'    => $caja
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 422);
        }
    }

    public function edit($id)
    {
        // Cargamos la caja junto con sus relaciones para tener la info completa en JS
        $caja = Caja::with(['bodega', 'impresora', 'cajero'])->findOrFail($id);
        return response()->json($caja);
    }

    public function update(Request $request, $id)
    {
        try {
            $caja = Caja::findOrFail($id);

            // 1. Validaciones incluyendo los nuevos campos del formulario
            $request->validate([
                'nombre'         => 'required|string|max:255',
                'prefijo'        => 'required|string|max:10',
                'proximo_numero' => 'required|integer|min:1', // Nuevo campo
                'bodega_id'      => 'required|exists:bodegas,id',
                'impresora_id'   => 'nullable|exists:impresoras,id', // Nuevo campo
                'user_id'        => 'nullable|exists:users,id',
            ]);

            // 2. Actualizar el registro mapeando todo correctamente
            $caja->update([
                'nombre'         => $request->nombre,
                'prefijo'        => strtoupper($request->prefijo), // Estandariza a mayúsculas
                'proximo_numero' => $request->proximo_numero,      // Nuevo campo
                'bodega_id'      => $request->bodega_id,
                'impresora_id'   => $request->impresora_id ?: null, // Nuevo campo
                'user_id'        => $request->user_id ?: null,
                // Evaluamos correctamente el checkbox si viene marcado (1) o desmarcado (0)
                'activa'         => $request->has('activa') ? 1 : 0,
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Caja actualizada y configurada correctamente',
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
