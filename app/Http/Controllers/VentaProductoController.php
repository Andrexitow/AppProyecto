<?php

namespace App\Http\Controllers;

use App\Models\Bodega;
use App\Models\Caja;
use App\Models\GrupoMenu;
use App\Models\Producto;
use App\Models\User;
use App\Services\PropinaReporteService;
use App\Services\VentaProductoReporteService;
use Illuminate\Http\Request;

class VentaProductoController extends Controller
{
    public function __construct(private VentaProductoReporteService $reportes, private PropinaReporteService $propinas)
    {
    }

    public function index()
    {
        return view('venta-productos.index');
    }

    /** Todos los catálogos que alimentan los selectores del filtro, en una sola llamada. */
    public function filtros()
    {
        return response()->json([
            'bodegas' => Bodega::orderBy('descripcion')->get(['id', 'descripcion']),
            'usuarios' => User::orderBy('name')->get(['id', 'name']),
            'cajas' => Caja::orderBy('nombre')->get(['id', 'nombre']),
            'grupos_menu' => GrupoMenu::orderBy('nombre')->get(['id', 'nombre']),
            'categorias' => Producto::whereNotNull('categoria')->where('categoria', '!=', '')->distinct()->orderBy('categoria')->pluck('categoria'),
        ]);
    }

    public function reporte(Request $request)
    {
        $datos = $request->validate([
            'bodega_id' => 'nullable|exists:bodegas,id',
            'user_id' => 'nullable|exists:users,id',
            'cliente_id' => 'nullable|exists:terceros,id',
            'caja_id' => 'nullable|exists:cajas,id',
            'prefijo' => 'nullable|string|max:10',
            'categoria' => 'nullable|string',
            'grupo_menu_id' => 'nullable|exists:grupo_menus,id',
            'producto_id' => 'nullable|exists:productos,id',
            'desde' => 'required|date',
            'hasta' => 'required|date|after_or_equal:desde',
            'con_iva' => 'nullable|boolean',
        ]);

        return response()->json($this->reportes->ventaPorProducto($datos) + [
            'desde' => $datos['desde'],
            'hasta' => $datos['hasta'],
        ]);
    }

    public function propinas()
    {
        return view('venta-productos.propinas');
    }

    public function reportePropinas(Request $request)
    {
        $datos = $request->validate([
            'bodega_id' => 'nullable|exists:bodegas,id',
            'caja_id' => 'nullable|exists:cajas,id',
            'user_id' => 'nullable|exists:users,id',
            'desde' => 'required|date',
            'hasta' => 'required|date|after_or_equal:desde',
        ]);

        return response()->json($this->propinas->porVendedor($datos) + [
            'desde' => $datos['desde'],
            'hasta' => $datos['hasta'],
        ]);
    }
}
