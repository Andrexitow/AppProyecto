<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    AjusteController,
    BodegaController,
    CajaController,
    CategoriaPosController,
    ProductoController,
    TerceroController,
    ExistenciaController,
    FacturacionController,
    GrupomenuController,
    ImpresoraController,
    UsuarioController
};
use Illuminate\Support\Facades\Auth;


// LOGIN
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth')->group(function () {

    Route::get('/', function () {
        $user = Auth::user();
        if ($user && in_array($user->rol_id, [2, 4])) {
            return redirect()->route('facturacion.index');
        }

        return view('home');
    })->name('home');

    Route::get('/facturacion', [FacturacionController::class, 'index'])->name('facturacion.index');
    Route::post('/mesas/{id}/bloquear', [FacturacionController::class, 'bloquearMesa']);
    Route::post('/mesas/{id}/liberar', [FacturacionController::class, 'liberarMesa']);
    Route::get('/mesas/actualizar', [FacturacionController::class, 'obtenerEstadoMesas']);
    Route::post('/pedidos/guardar', [FacturacionController::class, 'guardarPedido']);
    Route::get('/pedidos/mesa/{mesaId}/pendiente', [FacturacionController::class, 'obtenerPedidoPendiente']);
    Route::post('/pedidos/eliminar-item', [FacturacionController::class, 'eliminarItemPedido']);

    Route::post('/pedidos/cerrar-mesa', [FacturacionController::class, 'cerrarMesa'])->name('pedidos.cerrar');

    // Productos
    Route::get('/views/productos', [ProductoController::class, 'index'])->name('productos.index');
    Route::post('/productos', [ProductoController::class, 'store']);
    Route::get('/productos/buscar', [ProductoController::class, 'buscar']);
    Route::get('/productos/{id}/edit', [ProductoController::class, 'edit'])->name('productos.edit');
    Route::put('/productos/{id}', [ProductoController::class, 'update'])->name('productos.update');
    Route::put('/productos/{id}/estado', [ProductoController::class, 'cambiarEstado']);
    Route::delete('/productos/{id}', [ProductoController::class, 'destroy'])
        ->name('productos.destroy');
    Route::get('/productos/buscar-admin', [ProductoController::class, 'buscarAdmin']);

    // Bodegas
    Route::get('/views/bodegas', [BodegaController::class, 'index'])->name('bodegas.index');
    Route::post('/bodegas', [BodegaController::class, 'store']);

    // Ajustes
    Route::get('/views/ajustes', [AjusteController::class, 'index'])->name('ajustes.index');
    Route::get('/ajustes/siguiente-numero', [AjusteController::class, 'siguienteNumero']);
    Route::post('/ajustes/{id}/revertir', [AjusteController::class, 'revertir']);
    Route::post('/ajustes/{id}/detalles', [AjusteController::class, 'registrar']);
    Route::resource('ajustes', AjusteController::class)->only(['store', 'show', 'update', 'destroy']);

    // Terceros
    Route::get('/views/terceros', [TerceroController::class, 'index'])->name('terceros.index');
    Route::get('/terceros/buscar', [TerceroController::class, 'buscar']);
    Route::get('/terceros/buscar-doc', [TerceroController::class, 'buscarPorDocumento']);
    Route::post('/terceros', [TerceroController::class, 'store'])->name('terceros.store');

    // Existencias
    Route::get('/views/existencias', [ExistenciaController::class, 'index'])->name('existencias.index');
    Route::get('/existencias/data', [ExistenciaController::class, 'data'])->name('existencias.data');

    // Usuarios
    Route::get('/views/usuarios', [UsuarioController::class, 'index'])->name('usuarios.index');
    Route::post('/usuarios/store', [UsuarioController::class, 'store'])->name('usuarios.store');
    Route::get('/usuarios/{id}/edit', [UsuarioController::class, 'edit']);
    Route::post('/usuarios/update/{id}', [UsuarioController::class, 'update']);
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy']);

    Route::get('/roles/{id}/edit', [UsuarioController::class, 'editRole']);
    Route::put('/roles/{id}', [UsuarioController::class, 'updateRole']);

    Route::get('/views/cajas', [CajaController::class, 'index'])->name('cajas.index');
    Route::post('/cajas/store', [CajaController::class, 'store'])->name('cajas.store');
    Route::get('/cajas/{id}/edit', [CajaController::class, 'edit'])->name('cajas.edit');
    Route::post('/cajas/update/{id}', [CajaController::class, 'update'])->name('cajas.update');
    Route::delete('/cajas/{id}', [CajaController::class, 'destroy'])->name('cajas.destroy');

    Route::get('/views/impresoras', [ImpresoraController::class, 'index'])->name('impresoras.index');


    // // Rutas de API para el CRUD
    Route::get('/api/impresoras', [ImpresoraController::class, 'listar']);
    Route::post('/api/impresoras/guardar', [ImpresoraController::class, 'store']);
    Route::delete('/api/impresoras/{id}', [ImpresoraController::class, 'destroy']);

    // Grupos de Menú
    Route::get('/views/grupos', [GrupomenuController::class, 'index'])->name('grupos.index');
    Route::post('/grupos/store', [GrupomenuController::class, 'store'])->name('grupos.store');
    Route::post('/grupos/update/{id}', [GrupomenuController::class, 'update'])->name('grupos.update');
    Route::delete('/grupos/{id}', [GrupomenuController::class, 'destroy'])->name('grupos.destroy');

    // routes/web.php
    Route::get('/views/categorias-pos',              [CategoriaPosController::class, 'index']);
    Route::post('/categorias-pos',             [CategoriaPosController::class, 'store']);
    Route::put('/categorias-pos/{categoriaPos}',    [CategoriaPosController::class, 'update']);
    Route::delete('/categorias-pos/{categoriaPos}', [CategoriaPosController::class, 'destroy']);
});
