<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use App\Models\AjusteDetalle;
use App\Models\Inventario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AjusteController extends Controller
{
    // PASO 1 — Solo guarda la cabecera
    public function store(Request $request)
    {
        $request->validate([
            'prefijo'   => 'required',
            'fecha'     => 'required|date',
            'bodega_id' => 'required|exists:bodegas,id',
        ]);

        $ultimo = Ajuste::where('prefijo', $request->prefijo)->max('numero');
        $numero = ($ultimo ?? 0) + 1;

        $ajuste = Ajuste::create([
            'prefijo'       => $request->prefijo,
            'numero'        => $numero,
            'fecha'         => $request->fecha,
            'tercero_id'    => $request->tercero_id,
            'bodega_id'     => $request->bodega_id,
            'contraparte'   => $request->contraparte,
            'observaciones' => $request->observaciones,
            'total'         => 0,
            'registrado'    => false,
            'user_id'       => auth()->id(),
        ]);

        return response()->json($ajuste);
    }

    public function update(Request $request, $id)
    {
        $ajuste = Ajuste::findOrFail($id);

        if ($ajuste->registrado) {
            return response()->json(['error' => 'No se puede editar un ajuste registrado'], 422);
        }

        $ajuste->update($request->only([
            'prefijo',
            'fecha',
            'tercero_id',
            'bodega_id',
            'contraparte',
            'observaciones',
        ]));

        return response()->json(['ok' => true]);
    }

    // PASO 2 — Guarda detalles, actualiza inventario y marca como registrado
    public function registrar(Request $request, $id)
    {
        $request->validate([
            'detalles'   => 'required|array|min:1',
        ]);

        DB::beginTransaction();

        try {
            $ajuste = Ajuste::findOrFail($id);

            // Evitar doble registro
            if ($ajuste->registrado) {
                return response()->json(['error' => 'Este ajuste ya fue registrado'], 422);
            }

            $total = 0;

            foreach ($request->detalles as $d) {
                AjusteDetalle::create([
                    'ajuste_id'   => $ajuste->id,
                    'producto_id' => $d['producto_id'],
                    'cantidad'    => $d['cantidad'],
                    'precio'      => $d['precio'] ?? 0,
                ]);

                $total += $d['cantidad'] * ($d['precio'] ?? 0);

                // Actualizar inventario
                $inventario = Inventario::where('producto_id', $d['producto_id'])
                    ->where('bodega_id', $ajuste->bodega_id)
                    ->first();

                if (!$inventario) {
                    $inventario = Inventario::create([
                        'producto_id' => $d['producto_id'],
                        'bodega_id'   => $ajuste->bodega_id,
                        'stock'       => 0,
                    ]);
                }

                $inventario->stock += $d['cantidad'];
                $inventario->save();
            }

            $ajuste->update([
                'total'      => $total,
                'registrado' => true,
            ]);

            DB::commit();

            return response()->json(['ok' => true]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function siguienteNumero(Request $request)
    {
        $prefijo = $request->prefijo;
        $ultimo  = Ajuste::where('prefijo', $prefijo)->max('numero');
        $numero  = ($ultimo ?? 0) + 1;

        return response()->json(['numero' => $numero]);
    }

    public function show($id)
    {
        $ajuste = Ajuste::with(['tercero', 'bodega', 'detalles.producto'])->findOrFail($id);

        return response()->json($ajuste);
    }

    public function destroy($id)
    {
        $ajuste = Ajuste::findOrFail($id);

        if ($ajuste->registrado) {
            return response()->json([
                'error' => 'No puedes eliminar un ajuste registrado'
            ], 422);
        }

        $ajuste->delete();

        return response()->json(['ok' => true]);
    }
}
