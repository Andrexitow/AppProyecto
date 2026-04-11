<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('productos.index');
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
            'codigo' => 'required|unique:productos',
            'descripcion' => 'required',
            'und_detal' => 'required',
        ]);

        $producto = Producto::create($request->all());

        return response()->json($producto);
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
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
