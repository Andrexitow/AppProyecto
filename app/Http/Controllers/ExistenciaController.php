<?php

namespace App\Http\Controllers;

use App\Models\Bodega;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExistenciaController extends Controller
{

    public function index()
    {
        // Esto arreglará que no se vean las bodegas al cargar la página
        $bodegas = Bodega::all();
        return view('existencias.index', compact('bodegas'));
    }

    public function data(Request $request)
    {
        try {
            $bodega_id = $request->bodega_id;

            if (!$bodega_id) {
                return response()->json(['message' => 'Bodega no válida'], 400);
            }

            $bodega = Bodega::find($bodega_id);

            if (!$bodega) {
                return response()->json(['message' => 'Bodega no encontrada'], 404);
            }

            $existencias = DB::table('inventarios')
                ->join('productos', 'inventarios.producto_id', '=', 'productos.id')
                ->where('inventarios.bodega_id', $bodega_id)
                ->select(
                    'productos.id',
                    'productos.codigo',
                    'productos.descripcion',
                    'productos.categoria',
                    'productos.precio',
                    'inventarios.stock'
                )
                ->orderBy('productos.descripcion')
                ->get();

            return response()->json([
                'bodega' => ['id' => $bodega->id, 'descripcion' => $bodega->descripcion],
                'data' => $existencias,
            ]);
        } catch (\Exception $e) {
            // Esto devolverá el error real en lugar de un simple "500"
            return response()->json(['message' => 'Error en controlador: ' . $e->getMessage()], 500);
        }
    }
}
