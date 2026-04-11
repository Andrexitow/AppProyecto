<?php

use App\Http\Controllers\AjusteController;
use App\Http\Controllers\BodegaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\TerceroController;
use App\Models\Bodega;
use App\Models\Producto;

Route::get('/', function () {
    return view('home');
});


Route::get('/views/productos', function () {
    $productos = Producto::all();
    return view('productos.index', compact('productos'));
});

Route::post('/productos', [ProductoController::class, 'store']);
Route::get('/productos/buscar', [ProductoController::class, 'buscar']);



//Bodega
Route::get('/views/bodegas', function () {

    // return view('bodegas.index');
    $bodegas = Bodega::all();
    return view('bodegas.index', compact('bodegas'));
});

Route::get('/views/ajustes', function () {

    $ajustes = \App\Models\Ajuste::with('user')
        ->latest()
        ->get();

    return view('ajustes.index', compact('ajustes'));
});

// Route::get('/ajustes', [AjusteController::class, 'index']);

Route::post('/bodegas', [BodegaController::class, 'store']);

Route::get('/terceros/buscar', [TerceroController::class, 'buscar']);
Route::get('/terceros/buscar-doc', [TerceroController::class, 'buscarPorDocumento']);

Route::post('/ajustes', [AjusteController::class, 'store']);
Route::get('/ajustes/siguiente-numero', [AjusteController::class, 'siguienteNumero']);
