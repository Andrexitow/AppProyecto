<?php

namespace App\Http\Controllers;

use App\Models\Ajuste;
use App\Models\AjusteDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class AjusteController extends Controller
{
    public function store(Request $request)
    {
        DB::beginTransaction();

        try {

            // 🔹 GENERAR NÚMERO AUTOMÁTICO
            $ultimo = Ajuste::where('prefijo', $request->prefijo)->max('numero');
            $numero = ($ultimo ?? 0) + 1;

            // 🔹 GUARDAR CABECERA
            $ajuste = Ajuste::create([
                'prefijo' => $request->prefijo,
                'numero' => $numero,
                'fecha' => $request->fecha,
                'tercero_id' => $request->tercero_id,
                'contraparte' => $request->contraparte,
                'observaciones' => $request->observaciones,
                'total' => $request->total,
                'registrado' => 1,
                'user_id' => 1
            ]);

            // 🔹 GUARDAR DETALLES
            foreach ($request->detalles as $d) {

                AjusteDetalle::create([
                    'ajuste_id' => $ajuste->id,
                    'producto_id' => $d['producto_id'],
                    'cantidad' => $d['cantidad'],
                    'precio' => $d['precio'] ?? 0,
                ]);
            }

            DB::commit();

            return response()->json([
                'success' => true
            ]);
        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
    public function siguienteNumero(Request $request)
    {
        $prefijo = $request->prefijo;

        $ultimo = Ajuste::where('prefijo', $prefijo)->max('numero');

        $numero = ($ultimo ?? 0) + 1;

        return response()->json([
            'numero' => $numero
        ]);
    }
}
