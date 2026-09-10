<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    AjusteController,
    BodegaController,
    CajaController,
    CategoriaPosController,
    CompraController,
    CierreCajaController,
    CocinaController,
    DashboardController,
    ComprobanteController,
    CuentaContableController,
    ProductoController,
    TerceroController,
    ExistenciaController,
    FacturacionController,
    FacturaController,
    GrupomenuController,
    ImpresoraController,
    NotificacionPedidoController,
    UsuarioController
};
use App\Models\ComandaPendiente;
use Illuminate\Http\Request;
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

        return app(DashboardController::class)->index();
    })->name('home');
    Route::get('/dashboard/resumen', [DashboardController::class, 'resumen'])
        ->middleware('role:Administrador,Contabilidad');

    Route::get('/facturacion', [FacturacionController::class, 'index'])->name('facturacion.index');
    Route::post('/mesas/{id}/bloquear', [FacturacionController::class, 'bloquearMesa']);
    Route::post('/mesas/{id}/liberar', [FacturacionController::class, 'liberarMesa']);
    Route::get('/mesas/actualizar', [FacturacionController::class, 'obtenerEstadoMesas']);
    Route::post('/pedidos/guardar', [FacturacionController::class, 'guardarPedido']);
    Route::post('/pedidos/cliente', [FacturacionController::class, 'actualizarClientePedido']);
    Route::get('/pedidos/mesa/{mesaId}/pendiente', [FacturacionController::class, 'obtenerPedidoPendiente']);
    Route::post('/pedidos/eliminar-item', [FacturacionController::class, 'eliminarItemPedido']);

    Route::get('/cocina', [CocinaController::class, 'index'])->middleware('role:Cocina,Administrador')->name('cocina.index');
    Route::get('/cocina/comandas', [CocinaController::class, 'comandas'])->middleware('role:Cocina,Administrador');
    Route::post('/cocina/comandas/{id}/finalizar', [CocinaController::class, 'finalizar'])->middleware('role:Cocina,Administrador');
    Route::get('/notificaciones-pedidos/pendientes', [NotificacionPedidoController::class, 'pendientes']);
    Route::post('/notificaciones-pedidos/{notificacion}/leer', [NotificacionPedidoController::class, 'marcarLeida']);

    Route::get('/views/facturas', [FacturaController::class, 'index'])->middleware('role:Administrador,Contabilidad')->name('factura.index');
    Route::get('/facturas', [FacturaController::class, 'data'])->middleware('role:Administrador,Contabilidad');
    Route::get('/facturas/{id}', [FacturaController::class, 'show'])->middleware('role:Administrador,Contabilidad');
    Route::post('/facturas/{id}/anular', [FacturaController::class, 'anular'])->middleware('role:Administrador');
    Route::post('/facturas/{id}/revertir', [FacturaController::class, 'revertirAnulacion'])->middleware('role:Administrador');
    Route::post('/facturas/{id}/imprimir', [FacturaController::class, 'imprimir'])->middleware('role:Administrador,Contabilidad');

    Route::get('/views/comprobantes', [ComprobanteController::class, 'index'])->middleware('role:Administrador,Contabilidad')->name('comprobantes.index');
    Route::get('/comprobantes', [ComprobanteController::class, 'data'])->middleware('role:Administrador,Contabilidad');
    Route::post('/comprobantes', [ComprobanteController::class, 'store'])->middleware('role:Administrador,Contabilidad');
    Route::get('/comprobantes/{comprobante}/edit', [ComprobanteController::class, 'edit'])->middleware('role:Administrador,Contabilidad');
    Route::put('/comprobantes/{comprobante}', [ComprobanteController::class, 'update'])->middleware('role:Administrador,Contabilidad');
    Route::post('/comprobantes/{comprobante}/anular', [ComprobanteController::class, 'anular'])->middleware('role:Administrador,Contabilidad');
    Route::post('/comprobantes/{comprobante}/revertir', [ComprobanteController::class, 'revertir'])->middleware('role:Administrador,Contabilidad');
    Route::delete('/comprobantes/{comprobante}', [ComprobanteController::class, 'destroy'])->middleware('role:Administrador,Contabilidad');
    Route::get('/comprobantes/{id}', [ComprobanteController::class, 'show'])->middleware('role:Administrador,Contabilidad');



    Route::get('/views/cuentas-contables', [CuentaContableController::class, 'index'])
        ->middleware('role:Administrador,Contabilidad')
        ->name('cuentas-contables.index');

    Route::get('/cuentas-contables/data', [CuentaContableController::class, 'data'])
        ->middleware('role:Administrador,Contabilidad')
        ->name('cuentas-contables.data');

    Route::post('/cuentas-contables', [CuentaContableController::class, 'store'])
        ->middleware('role:Administrador')
        ->name('cuentas-contables.store');

    Route::get('/cuentas-contables/{cuentaContable}', [CuentaContableController::class, 'show'])
        ->middleware('role:Administrador,Contabilidad')
        ->name('cuentas-contables.show');

    Route::put('/cuentas-contables/{cuentaContable}', [CuentaContableController::class, 'update'])
        ->middleware('role:Administrador')
        ->name('cuentas-contables.update');

    Route::delete('/cuentas-contables/{cuentaContable}', [CuentaContableController::class, 'destroy'])
        ->middleware('role:Administrador')
        ->name('cuentas-contables.destroy');

    Route::get('/terceros', [FacturaController::class, 'catalogoTerceros']);
    Route::get('/cajas', [FacturaController::class, 'catalogoCajas']);
    Route::get('/bodegas', [FacturaController::class, 'catalogoBodegas']);
    Route::get('/productos', [FacturaController::class, 'catalogoProductos']);

    Route::post('/pedidos/imprimir-inventario-pos', [FacturacionController::class, 'imprimirInventarioPos']);
    Route::post('/pedidos/procesar-cierre-caja', [FacturacionController::class, 'procesarCierreCaja']);

    Route::post('/pedidos/cerrar-mesa', [FacturacionController::class, 'cerrarMesa'])->name('pedidos.cerrar');

    // Productos
    Route::get('/views/productos', [ProductoController::class, 'index'])->middleware('role:Administrador')->name('productos.index');
    Route::post('/productos', [ProductoController::class, 'store'])->middleware('role:Administrador');
    Route::get('/productos/buscar', [ProductoController::class, 'buscar'])->middleware('role:Administrador');
    Route::get('/productos/{id}/edit', [ProductoController::class, 'edit'])->middleware('role:Administrador')->name('productos.edit');
    Route::put('/productos/{id}', [ProductoController::class, 'update'])->middleware('role:Administrador')->name('productos.update');
    Route::put('/productos/{id}/estado', [ProductoController::class, 'cambiarEstado'])->middleware('role:Administrador');
    Route::delete('/productos/{id}', [ProductoController::class, 'destroy'])->middleware('role:Administrador')
        ->name('productos.destroy');
    Route::get('/productos/buscar-admin', [ProductoController::class, 'buscarAdmin']);

    // Bodegas
    Route::get('/views/bodegas', [BodegaController::class, 'index'])->middleware('role:Administrador')->name('bodegas.index');
    Route::post('/bodegas', [BodegaController::class, 'store'])->middleware('role:Administrador');
    Route::get('/bodegas/{id}/edit', [BodegaController::class, 'edit'])->middleware('role:Administrador');
    Route::put('/bodegas/{id}', [BodegaController::class, 'update'])->middleware('role:Administrador');
    Route::delete('/bodegas/{id}', [BodegaController::class, 'destroy'])->middleware('role:Administrador');

    // Ajustes
    Route::get('/views/ajustes', [AjusteController::class, 'index'])->middleware('role:Administrador')->name('ajustes.index');
    Route::get('/ajustes/siguiente-numero', [AjusteController::class, 'siguienteNumero'])->middleware('role:Administrador');
    Route::post('/ajustes/{id}/revertir', [AjusteController::class, 'revertir'])->middleware('role:Administrador');
    Route::post('/ajustes/{id}/detalles', [AjusteController::class, 'registrar'])->middleware('role:Administrador');
    Route::resource('ajustes', AjusteController::class)->only(['store', 'show', 'update', 'destroy'])->middleware('role:Administrador');

    // Terceros
    Route::get('/views/terceros', [TerceroController::class, 'index'])->middleware('role:Administrador,Contabilidad')->name('terceros.index');
    Route::get('/terceros/buscar', [TerceroController::class, 'buscar'])->middleware('role:Administrador,Contabilidad');
    Route::get('/terceros/buscar-doc', [TerceroController::class, 'buscarPorDocumento'])->middleware('role:Administrador,Contabilidad');
    Route::post('/terceros', [TerceroController::class, 'store'])->middleware('role:Administrador,Contabilidad')->name('terceros.store');
    Route::get('/terceros/{tercero}', [TerceroController::class, 'show'])->middleware('role:Administrador,Contabilidad');
    Route::put('/terceros/{tercero}', [TerceroController::class, 'update'])->middleware('role:Administrador,Contabilidad');
    Route::delete('/terceros/{tercero}', [TerceroController::class, 'destroy'])->middleware('role:Administrador,Contabilidad');

    // Existencias
    Route::get('/views/existencias', [ExistenciaController::class, 'index'])->middleware('role:Administrador')->name('existencias.index');
    Route::get('/existencias/data', [ExistenciaController::class, 'data'])->middleware('role:Administrador')->name('existencias.data');

    // Usuarios
    Route::get('/views/usuarios', [UsuarioController::class, 'index'])->middleware('role:Administrador')->name('usuarios.index');
    Route::post('/usuarios/store', [UsuarioController::class, 'store'])->middleware('role:Administrador')->name('usuarios.store');
    Route::get('/usuarios/{id}/edit', [UsuarioController::class, 'edit'])->middleware('role:Administrador');
    Route::post('/usuarios/update/{id}', [UsuarioController::class, 'update'])->middleware('role:Administrador');
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy'])->middleware('role:Administrador');

    Route::get('/roles/{id}/edit', [UsuarioController::class, 'editRole'])->middleware('role:Administrador');
    Route::put('/roles/{id}', [UsuarioController::class, 'updateRole'])->middleware('role:Administrador');

    Route::get('/views/cajas', [CajaController::class, 'index'])->middleware('role:Administrador')->name('cajas.index');
    Route::get('/views/compras', [CompraController::class, 'index'])->middleware('role:Administrador,Contabilidad')->name('compras.index');
    Route::get('/compras', [CompraController::class, 'data'])->middleware('role:Administrador,Contabilidad');
    Route::post('/compras', [CompraController::class, 'store'])->middleware('role:Administrador,Contabilidad');
    Route::get('/compras/{compra}', [CompraController::class, 'show'])->middleware('role:Administrador,Contabilidad');
    Route::put('/compras/{compra}', [CompraController::class, 'update'])->middleware('role:Administrador,Contabilidad');
    Route::post('/compras/{compra}/registrar', [CompraController::class, 'registrar'])->middleware('role:Administrador,Contabilidad');
    Route::post('/compras/{compra}/revertir-registro', [CompraController::class, 'revertirRegistro'])->middleware('role:Administrador,Contabilidad');
    Route::post('/compras/{compra}/anular', [CompraController::class, 'anular'])->middleware('role:Administrador,Contabilidad');
    Route::post('/compras/{compra}/revertir', [CompraController::class, 'revertir'])->middleware('role:Administrador,Contabilidad');
    Route::get('/views/cierres-caja', [CierreCajaController::class, 'index'])->middleware('role:Administrador,Contabilidad')->name('cierres-caja.index');
    Route::get('/cierres-caja/data', [CierreCajaController::class, 'data'])->middleware('role:Administrador,Contabilidad');
    Route::post('/cajas/store', [CajaController::class, 'store'])->middleware('role:Administrador')->name('cajas.store');
    Route::get('/cajas/{id}/edit', [CajaController::class, 'edit'])->middleware('role:Administrador')->name('cajas.edit');
    Route::post('/cajas/update/{id}', [CajaController::class, 'update'])->middleware('role:Administrador')->name('cajas.update');
    Route::delete('/cajas/{id}', [CajaController::class, 'destroy'])->middleware('role:Administrador')->name('cajas.destroy');

    Route::get('/views/impresoras', [ImpresoraController::class, 'index'])->middleware('role:Administrador')->name('impresoras.index');


    // // Rutas de API para el CRUD (estas SÍ usan sesión de navegador, son del panel admin)
    Route::get('/api/impresoras', [ImpresoraController::class, 'listar'])->middleware('role:Administrador');
    Route::post('/api/impresoras/guardar', [ImpresoraController::class, 'store'])->middleware('role:Administrador');
    Route::delete('/api/impresoras/{id}', [ImpresoraController::class, 'destroy'])->middleware('role:Administrador');

    // Grupos de Menú
    Route::get('/views/grupos', [GrupomenuController::class, 'index'])->middleware('role:Administrador')->name('grupos.index');
    Route::post('/grupos/store', [GrupomenuController::class, 'store'])->middleware('role:Administrador')->name('grupos.store');
    Route::post('/grupos/update/{id}', [GrupomenuController::class, 'update'])->middleware('role:Administrador')->name('grupos.update');
    Route::delete('/grupos/{id}', [GrupomenuController::class, 'destroy'])->middleware('role:Administrador')->name('grupos.destroy');

    // routes/web.php
    Route::get('/views/categorias_pos',              [CategoriaPosController::class, 'index'])->middleware('role:Administrador');
    Route::get('/categorias-pos',                    [CategoriaPosController::class, 'data'])->middleware('role:Administrador');
    Route::post('/categorias-pos',             [CategoriaPosController::class, 'store'])->middleware('role:Administrador');
    Route::put('/categorias-pos/{categoriaPos}',    [CategoriaPosController::class, 'update'])->middleware('role:Administrador');
    Route::delete('/categorias-pos/{categoriaPos}', [CategoriaPosController::class, 'destroy'])->middleware('role:Administrador');
});


/*
|--------------------------------------------------------------------------
| RUTAS PARA EL AGENTE DE IMPRESIÓN LOCAL
|--------------------------------------------------------------------------
| Estas rutas NO usan sesión de navegador (middleware 'auth'), porque las
| consume el programa Node.js instalado en la PC del negocio, que no
| tiene cookies ni login. Se protegen con un token fijo que va en el
| header "Authorization: Bearer <token>" — ver AutenticarAgenteImpresion.
|
| Deja estas rutas en web.php (no routes/api.php) porque tu proyecto no
| usa el archivo api.php por defecto; si en tu caso sí existe y está
| cargado, puedes moverlas allí sin el prefijo /agente que pongo aquí.
*/
Route::prefix('agente')->middleware('auth.agente')->group(function () {

    // El agente pregunta cada 2-3 segundos: "¿hay algo nuevo para imprimir?"
    Route::get('/comandas/pendientes', function () {
        return ComandaPendiente::where('estado', 'pendiente')
            ->with('impresora')
            ->orderBy('id')
            ->limit(50)
            ->get();
    });

    // El agente confirma que ya imprimió correctamente
    Route::post('/comandas/{id}/marcar-impreso', function ($id) {
        $comanda = ComandaPendiente::find($id);
        if (!$comanda) {
            return response()->json(['ok' => false, 'message' => 'No existe'], 404);
        }
        $comanda->update(['estado' => 'impreso']);
        return response()->json(['ok' => true]);
    });

    // Si la impresora falla (apagada, sin papel, sin red local, etc.)
    Route::post('/comandas/{id}/marcar-error', function ($id, Request $request) {
        $comanda = ComandaPendiente::find($id);
        if (!$comanda) {
            return response()->json(['ok' => false], 404);
        }
        $comanda->update([
            'estado' => 'error',
            'error_mensaje' => $request->input('mensaje', 'Error desconocido'),
        ]);
        return response()->json(['ok' => true]);
    });
});
