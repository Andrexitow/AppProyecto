<?php

use App\Http\Controllers\AjusteController;
use App\Http\Controllers\BodegaController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\TerceroController;
use App\Models\Bodega;
use App\Models\Producto;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExistenciaController;

// LOGIN
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        return view('home');
    });

    Route::get('/views/productos', function () {
        $productos = Producto::all();
        return view('productos.index', compact('productos'));
    });

    Route::post('/productos', [ProductoController::class, 'store']);
    Route::get('/productos/buscar', [ProductoController::class, 'buscar']);

    // Bodegas
    Route::get('/views/bodegas', function () {
        $bodegas = Bodega::all();
        return view('bodegas.index', compact('bodegas'));
    });

    // Ajustes
    Route::get('/views/ajustes', function () {
        $bodegas = Bodega::all();
        $ajustes = \App\Models\Ajuste::with('user')->latest()->get();

        return view('ajustes.index', compact('ajustes', 'bodegas'));
    });

    Route::post('/bodegas', [BodegaController::class, 'store']);

    Route::post('/ajustes', [AjusteController::class, 'store']);
    Route::get('/ajustes/siguiente-numero', [AjusteController::class, 'siguienteNumero']);
    Route::get('/ajustes/{id}', [AjusteController::class, 'show']);
    Route::put('/ajustes/{id}', [AjusteController::class, 'update']);
    Route::delete('/ajustes/{id}', [AjusteController::class, 'destroy']);
    Route::post('/ajustes/{id}/revertir', [AjusteController::class, 'revertir']);
    Route::post('/ajustes/{id}/detalles', [AjusteController::class, 'registrar']);


    Route::get('/terceros/buscar', [TerceroController::class, 'buscar']);
    Route::get('/terceros/buscar-doc', [TerceroController::class, 'buscarPorDocumento']);

    Route::get('/views/existencias', [ExistenciaController::class, 'index'])->name('existencias.index');
    Route::get('/existencias/data', [ExistenciaController::class, 'data'])->name('existencias.data');

    Route::post('/terceros', [TerceroController::class, 'store'])->name('terceros.store');
    Route::get('/views/terceros', [TerceroController::class, 'index'])->name('terceros.index');


});
