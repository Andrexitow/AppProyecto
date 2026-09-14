<?php

namespace App\Http\Controllers;

use App\Models\Bodega;
use App\Models\Impresora;
use App\Services\PrintService;
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

    /**
     * Encola el reporte de existencias (ya formateado como texto de ticket
     * por el frontend) hacia una impresora de red concreta — el agente local
     * instalado en el negocio es quien realmente lo envía por IP, igual que
     * hace con las comandas de cocina/barra.
     */
    public function imprimirRed(Request $request, PrintService $printService)
    {
        $datos = $request->validate([
            'impresora_id' => 'required|exists:impresoras,id',
            'contenido' => 'required|string',
        ]);

        $impresora = Impresora::where('activa', true)->findOrFail($datos['impresora_id']);
        $resultado = $printService->imprimirInventario($datos['contenido'], $impresora);

        if ($resultado['status'] !== 'success') {
            return response()->json(['message' => $resultado['message'] ?? 'No se pudo encolar el reporte.'], 500);
        }

        return response()->json([
            'success' => true,
            'message' => "Reporte enviado a la cola de \"{$impresora->nombre}\". Se imprimirá en unos segundos.",
        ]);
    }
}
