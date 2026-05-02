<?php

namespace App\Http\Controllers;

use App\Models\GrupoMenu;
use App\Models\Impresora;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $productos = Producto::all();

        $grupos = GrupoMenu::all();

        return view('productos.index', compact('productos', 'grupos'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'codigo' => 'required|unique:productos,codigo',
            'descripcion' => 'required',
            'precio' => 'required|numeric|min:0',
            'afecta_inventario' => 'required|in:0,1',
            'grupo_menu_id' => 'required|exists:grupo_menus,id' // Validación del nuevo campo
        ]);

        Producto::create([
            'codigo' => $request->codigo,
            'descripcion' => $request->descripcion,
            'categoria' => $request->categoria,
            'grupo_menu_id' => $request->grupo_menu_id, // Guardamos el grupo
            'und_detal' => $request->und_detal,
            'precio' => $request->precio,
            'caracteristicas' => $request->caracteristicas,
            'afecta_inventario' => $request->afecta_inventario,
            'inactivo' => 0
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Producto guardado correctamente'
        ]);
    }

    public function buscar(Request $request)
    {
        $query = $request->query('query');

        $productos = Producto::query()
            ->where(function ($q) use ($query) {
                $q->where('descripcion', 'like', "%$query%")
                    ->orWhere('codigo', 'like', "%$query%");
            })
            ->select('id', 'codigo', 'descripcion', 'precio')
            ->limit(10)
            ->get();

        return response()->json($productos);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $producto = Producto::findOrFail($id);

        return response()->json($producto);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $producto = Producto::findOrFail($id);

        $request->validate([
            'codigo' => 'required|unique:productos,codigo,' . $id,
            'descripcion' => 'required',
            'precio' => 'required|numeric|min:0',
            'afecta_inventario' => 'required|in:0,1',
            'grupo_menu_id' => 'required|exists:grupo_menus,id' // Validación del nuevo campo
        ]);

        $producto->update([
            'codigo' => $request->codigo,
            'descripcion' => $request->descripcion,
            'categoria' => $request->categoria,
            'grupo_menu_id' => $request->grupo_menu_id, // Actualizamos el grupo
            'und_detal' => $request->und_detal,
            'precio' => $request->precio,
            'caracteristicas' => $request->caracteristicas,
            'afecta_inventario' => $request->afecta_inventario
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Producto actualizado correctamente'
        ]);
    }

    public function cambiarEstado($id)
    {
        $producto = Producto::findOrFail($id);

        // alternar valor
        $producto->inactivo = $producto->inactivo == 0 ? 1 : 0;

        $producto->save();

        return response()->json([
            'success' => true,
            'message' => $producto->inactivo == 1
                ? 'Producto desactivado correctamente'
                : 'Producto activado correctamente'
        ]);
    }

    public function destroy($id)
    {
        $producto = Producto::findOrFail($id);

        // Validar stock en inventario
        $tieneStock = DB::table('inventarios')
            ->where('producto_id', $id)
            ->where('stock', '>', 0)
            ->exists();

        if ($tieneStock) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar. El producto tiene existencias en inventario.'
            ], 422);
        }

        // // Validar movimientos
        // $tieneMovimientos = DB::table('movimientos')
        //     ->where('producto_id', $id)
        //     ->exists();

        // if ($tieneMovimientos) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => 'No se puede eliminar. El producto ya tiene movimientos registrados.'
        //     ], 422);
        // }

        $producto->delete();

        return response()->json([
            'success' => true,
            'message' => 'Producto eliminado correctamente.'
        ]);
    }

    public function buscarAdmin(Request $request)
    {
        $texto = $request->texto;

        $productos = Producto::with('inventarios')
            ->where('codigo', 'like', "%{$texto}%")
            ->orWhere('descripcion', 'like', "%{$texto}%")
            ->get();

        return view('Productos.partials.tabla', compact('productos'));
    }
}
