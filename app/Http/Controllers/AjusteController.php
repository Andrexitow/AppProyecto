<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use App\Models\AjusteDetalle;
use App\Models\Bodega;
use App\Models\Inventario;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AjusteController extends Controller
{

    public function index()
    {
        $bodegas = Bodega::all();
        $ajustes = Ajuste::with('user')->latest()->get();

        return view('ajustes.index', compact('ajustes', 'bodegas'));
    }

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
            'user_id' => Auth::id(),
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

    public function registrar(Request $request, $id)
    {
        $request->validate([
            'detalles' => 'required|array|min:1',
        ]);

        DB::beginTransaction();

        try {
            $ajuste = Ajuste::findOrFail($id);

            // Evitar doble registro
            if ($ajuste->registrado) {
                return response()->json(['error' => 'Este ajuste ya fue registrado'], 422);
            }

            $total = 0;
            AjusteDetalle::where('ajuste_id', $ajuste->id)->delete();
            foreach ($request->detalles as $d) {

                // 🔥 Buscar inventario
                $cantidad = abs($d['cantidad']);
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
                $producto = Producto::find($d['producto_id']);
                $nombre = $producto->descripcion ?? 'Producto';

                // 🔥 calcular exceso
                $exceso = $cantidad - $inventario->stock;

                // 🔥 VALIDAR ANTES DE RESTAR
                if ($d['tipo'] === 'salida' && $inventario->stock < $cantidad) {
                    return response()->json([
                        'error' => [
                            'mensaje' => "Stock insuficiente",
                            'detalle' => "Intentas sacar {$cantidad}, disponible {$inventario->stock}, exceso {$exceso}",
                            'producto' => $nombre
                        ]
                    ], 422);
                }

                // 🔥 GUARDAR DETALLE
                AjusteDetalle::create([
                    'ajuste_id'   => $ajuste->id,
                    'producto_id' => $d['producto_id'],
                    'cantidad'    => $cantidad,
                    'tipo'        => $d['tipo'],
                    'precio'      => $d['precio'] ?? 0,
                ]);

                // 🔥 ACTUALIZAR INVENTARIO
                if ($d['tipo'] === 'entrada') {
                    $inventario->stock += $cantidad;
                } else {
                    $inventario->stock -= $cantidad;
                }

                $inventario->save();

                $total += $cantidad * ($d['precio'] ?? 0);
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

    public function revertir($id)
    {
        $ajuste = Ajuste::with('detalles')->findOrFail($id);

        if (!$ajuste->registrado) {
            return response()->json([
                'error' => 'Este ajuste ya está sin registrar'
            ], 400);
        }

        foreach ($ajuste->detalles as $detalle) {

            $inventario = Inventario::where('producto_id', $detalle->producto_id)
                ->where('bodega_id', $ajuste->bodega_id)
                ->first();

            if (!$inventario) {
                return response()->json([
                    'error' => 'No existe inventario para este producto en la bodega'
                ], 400);
            }

            // 🔥 REVERSA REAL
            if ($detalle->tipo == 'entrada') {
                // antes sumaste → ahora restas
                $inventario->stock -= $detalle->cantidad;
            } else {
                // antes restaste → ahora sumas
                $inventario->stock += $detalle->cantidad;
            }

            // 🔒 evitar negativos
            if ($inventario->stock < 0) {
                return response()->json([
                    'error' => 'Stock negativo no permitido'
                ], 400);
            }

            $inventario->save();
        }

        $ajuste->registrado = 0;
        $ajuste->save();

        return response()->json([
            'success' => true
        ]);
    }
}
