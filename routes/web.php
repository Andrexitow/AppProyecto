<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{
    AuthController,
    AjusteController,
    BodegaController,
    CajaController,
    CategoriaPosController,
    CompraController,
    CompraAvanzadaController,
    CuentaPorCobrarController,
    TesoreriaController,
    PeriodoContableController,
    DocumentoController,
    CierreCajaController,
    CocinaController,
    DashboardController,
    ComprobanteController,
    CuentaContableController,
    ProductoController,
    TerceroController,
    TrasladoBodegaController,
    ExistenciaController,
    FacturacionController,
    FacturaController,
    GrupomenuController,
    ImpresoraController,
    InformeContableController,
    KardexController,
    LogActividadController,
    MetodoPagoContableController,
    NotificacionPedidoController,
    UsuarioController
};
use App\Models\ComandaPendiente;
use App\Models\CentroCosto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


// LOGIN
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware(['auth', 'auditar'])->group(function () {

    Route::get('/', function () {
        $user = Auth::user();
        if ($user && in_array($user->rol?->nombre, ['Mesero', 'Cajero'], true)) {
            return redirect()->route('facturacion.index');
        }

        return app(DashboardController::class)->index();
    })->name('home');
    Route::get('/dashboard/resumen', [DashboardController::class, 'resumen'])
        ->middleware('role:Administrador,Contabilidad');
    Route::post('/dashboard/hora-corte', [DashboardController::class, 'actualizarHoraCorte'])
        ->middleware('role:Administrador');

    Route::get('/views/logs', [LogActividadController::class, 'index'])
        ->middleware('role:Administrador')
        ->name('logs.index');
    Route::get('/logs/data', [LogActividadController::class, 'data'])
        ->middleware('role:Administrador');

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
    Route::get('/cocina/historial', [CocinaController::class, 'historial'])->middleware('role:Cocina,Administrador')->name('cocina.historial');
    Route::get('/cocina/historial/datos', [CocinaController::class, 'historialDatos'])->middleware('role:Cocina,Administrador');
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
    Route::post('/comprobantes/{comprobante}/registrar', [ComprobanteController::class, 'registrar'])->middleware('role:Administrador,Contabilidad');
    Route::post('/comprobantes/{comprobante}/anular', [ComprobanteController::class, 'anular'])->middleware('role:Administrador,Contabilidad');
    Route::post('/comprobantes/{comprobante}/revertir', [ComprobanteController::class, 'revertir'])->middleware('role:Administrador,Contabilidad');
    Route::delete('/comprobantes/{comprobante}', [ComprobanteController::class, 'destroy'])->middleware('role:Administrador,Contabilidad');
    Route::get('/comprobantes/{id}', [ComprobanteController::class, 'show'])->middleware('role:Administrador,Contabilidad');
    Route::get('/centros-costo/catalogo', fn () => response()->json(['data' => CentroCosto::where('estado', true)->orderBy('codigo')->get(['id','codigo','nombre'])]))->middleware('role:Administrador,Contabilidad');

    Route::get('/views/informes-contables', [InformeContableController::class, 'index'])->middleware('role:Administrador,Contabilidad')->name('informes-contables.index');
    Route::get('/informes-contables/cuentas', [InformeContableController::class, 'catalogoCuentas'])->middleware('role:Administrador,Contabilidad');
    Route::get('/informes-contables/balance-prueba', [InformeContableController::class, 'balancePrueba'])->middleware('role:Administrador,Contabilidad');
    Route::get('/informes-contables/libro-diario', [InformeContableController::class, 'libroDiario'])->middleware('role:Administrador,Contabilidad');
    Route::get('/informes-contables/libro-mayor', [InformeContableController::class, 'libroMayor'])->middleware('role:Administrador,Contabilidad');
    Route::get('/informes-contables/libro-auxiliar', [InformeContableController::class, 'libroAuxiliar'])->middleware('role:Administrador,Contabilidad');
    Route::get('/informes-contables/estado-resultados', [InformeContableController::class, 'estadoResultados'])->middleware('role:Administrador,Contabilidad');
    Route::get('/informes-contables/balance-general', [InformeContableController::class, 'balanceGeneral'])->middleware('role:Administrador,Contabilidad');

    Route::get('/views/kardex', [KardexController::class, 'index'])->middleware('role:Administrador,Contabilidad')->name('kardex.index');
    Route::get('/kardex/bodegas', [KardexController::class, 'bodegas'])->middleware('role:Administrador,Contabilidad');
    Route::get('/kardex/productos', [KardexController::class, 'productos'])->middleware('role:Administrador,Contabilidad');
    Route::get('/kardex/movimientos', [KardexController::class, 'movimientos'])->middleware('role:Administrador,Contabilidad');
    Route::get('/kardex/valorizacion', [KardexController::class, 'valorizacion'])->middleware('role:Administrador,Contabilidad');

    Route::get('/views/cuentas-por-cobrar', [CuentaPorCobrarController::class, 'index'])->middleware('role:Administrador,Contabilidad')->name('cuentas-por-cobrar.index');
    Route::get('/cuentas-por-cobrar', [CuentaPorCobrarController::class, 'data'])->middleware('role:Administrador,Contabilidad');
    Route::get('/cuentas-por-cobrar/resumen', [CuentaPorCobrarController::class, 'resumen'])->middleware('role:Administrador,Contabilidad');
    Route::get('/cuentas-por-cobrar/{factura}', [CuentaPorCobrarController::class, 'show'])->middleware('role:Administrador,Contabilidad');
    Route::post('/cuentas-por-cobrar/{factura}/abonos', [CuentaPorCobrarController::class, 'registrarAbono'])->middleware('role:Administrador,Contabilidad');

    Route::get('/views/tesoreria', [TesoreriaController::class, 'index'])->middleware('role:Administrador,Contabilidad')->name('tesoreria.index');
    Route::get('/tesoreria/cuentas', [TesoreriaController::class, 'cuentas'])->middleware('role:Administrador,Contabilidad');
    Route::post('/tesoreria/cuentas', [TesoreriaController::class, 'storeCuenta'])->middleware('role:Administrador');
    Route::get('/tesoreria/movimientos', [TesoreriaController::class, 'movimientos'])->middleware('role:Administrador,Contabilidad');
    Route::post('/tesoreria/ingresos', [TesoreriaController::class, 'registrarIngreso'])->middleware('role:Administrador,Contabilidad');
    Route::post('/tesoreria/egresos', [TesoreriaController::class, 'registrarEgreso'])->middleware('role:Administrador,Contabilidad');
    Route::post('/tesoreria/transferencias', [TesoreriaController::class, 'registrarTransferencia'])->middleware('role:Administrador,Contabilidad');

    Route::get('/views/periodos-contables', [PeriodoContableController::class, 'index'])->middleware('role:Administrador,Contabilidad')->name('periodos-contables.index');
    Route::get('/periodos-contables', [PeriodoContableController::class, 'data'])->middleware('role:Administrador,Contabilidad');
    Route::post('/periodos-contables', [PeriodoContableController::class, 'store'])->middleware('role:Administrador,Contabilidad');
    Route::post('/periodos-contables/{periodo}/reabrir', [PeriodoContableController::class, 'reabrir'])->middleware('role:Administrador');



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

    Route::get('/views/metodos-pago-contables', [MetodoPagoContableController::class, 'index'])->middleware('role:Administrador')->name('metodos-pago-contables.index');
    Route::get('/metodos-pago-contables', [MetodoPagoContableController::class, 'data'])->middleware('role:Administrador,Contabilidad');
    Route::get('/metodos-pago-contables/opciones', [MetodoPagoContableController::class, 'opciones'])->middleware('role:Administrador,Contabilidad');
    Route::get('/metodos-pago-contables/cuentas', [MetodoPagoContableController::class, 'catalogoCuentas'])->middleware('role:Administrador');
    Route::post('/metodos-pago-contables', [MetodoPagoContableController::class, 'store'])->middleware('role:Administrador');
    Route::put('/metodos-pago-contables/{metodoPagoContable}', [MetodoPagoContableController::class, 'update'])->middleware('role:Administrador');

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
    Route::put('/terceros/{tercero}/estado', [TerceroController::class, 'cambiarEstado'])->middleware('role:Administrador,Contabilidad');
    Route::delete('/terceros/{tercero}', [TerceroController::class, 'destroy'])->middleware('role:Administrador,Contabilidad');

    // Existencias
    Route::get('/views/existencias', [ExistenciaController::class, 'index'])->middleware('role:Administrador')->name('existencias.index');
    Route::get('/existencias/data', [ExistenciaController::class, 'data'])->middleware('role:Administrador')->name('existencias.data');

    // Traslados entre bodegas
    Route::get('/views/traslados-bodega', [TrasladoBodegaController::class, 'index'])->middleware('role:Administrador')->name('traslados-bodega.index');
    Route::get('/traslados-bodega', [TrasladoBodegaController::class, 'data'])->middleware('role:Administrador');
    Route::get('/traslados-bodega/siguiente-consecutivo', [TrasladoBodegaController::class, 'siguienteConsecutivo'])->middleware('role:Administrador');
    Route::get('/traslados-bodega/productos', [TrasladoBodegaController::class, 'productos'])->middleware('role:Administrador');
    Route::post('/traslados-bodega', [TrasladoBodegaController::class, 'store'])->middleware('role:Administrador');
    Route::post('/traslados-bodega/{traslado}/registrar', [TrasladoBodegaController::class, 'registrar'])->middleware('role:Administrador');
    Route::post('/traslados-bodega/{traslado}/revertir', [TrasladoBodegaController::class, 'revertir'])->middleware('role:Administrador');
    Route::delete('/traslados-bodega/{traslado}', [TrasladoBodegaController::class, 'destroy'])->middleware('role:Administrador');

    // Usuarios
    Route::get('/views/usuarios', [UsuarioController::class, 'index'])->middleware('role:Administrador')->name('usuarios.index');
    Route::post('/usuarios/store', [UsuarioController::class, 'store'])->middleware('role:Administrador')->name('usuarios.store');
    Route::get('/usuarios/{id}/edit', [UsuarioController::class, 'edit'])->middleware('role:Administrador');
    Route::post('/usuarios/update/{id}', [UsuarioController::class, 'update'])->middleware('role:Administrador');
    Route::delete('/usuarios/{id}', [UsuarioController::class, 'destroy'])->middleware('role:Administrador');

    Route::get('/roles/{id}/edit', [UsuarioController::class, 'editRole'])->middleware('role:Administrador');
    Route::put('/roles/{id}', [UsuarioController::class, 'updateRole'])->middleware('role:Administrador');

    Route::get('/views/cajas', [CajaController::class, 'index'])->middleware('role:Administrador')->name('cajas.index');
    Route::get('/views/compras', [CompraAvanzadaController::class, 'index'])->middleware('role:Administrador,Contabilidad')->name('compras.index');
    Route::get('/compras', [CompraAvanzadaController::class, 'data'])->middleware('role:Administrador,Contabilidad');
    Route::get('/compras/siguiente-consecutivo', [CompraAvanzadaController::class, 'siguienteConsecutivo'])->middleware('role:Administrador,Contabilidad');
    Route::post('/compras', [CompraAvanzadaController::class, 'store'])->middleware('role:Administrador,Contabilidad');
    Route::get('/compras/{compra}', [CompraAvanzadaController::class, 'show'])->middleware('role:Administrador,Contabilidad');
    Route::put('/compras/{compra}', [CompraAvanzadaController::class, 'update'])->middleware('role:Administrador,Contabilidad');
    Route::post('/compras/{compra}/registrar', [CompraAvanzadaController::class, 'registrar'])->middleware('role:Administrador,Contabilidad');
    Route::post('/compras/{compra}/revertir-registro', [CompraAvanzadaController::class, 'revertirRegistro'])->middleware('role:Administrador,Contabilidad');
    Route::post('/compras/{compra}/anular', [CompraAvanzadaController::class, 'anular'])->middleware('role:Administrador,Contabilidad');
    Route::post('/compras/{compra}/revertir', [CompraAvanzadaController::class, 'revertir'])->middleware('role:Administrador,Contabilidad');
    Route::post('/compras/{compra}/pagos', [CompraAvanzadaController::class, 'registrarPago'])->middleware('role:Administrador,Contabilidad');
    Route::get('/views/cierres-caja', [CierreCajaController::class, 'index'])->middleware('role:Administrador,Contabilidad')->name('cierres-caja.index');
    Route::get('/cierres-caja/data', [CierreCajaController::class, 'data'])->middleware('role:Administrador,Contabilidad');
    Route::post('/cajas/store', [CajaController::class, 'store'])->middleware('role:Administrador')->name('cajas.store');
    Route::get('/cajas/{id}/edit', [CajaController::class, 'edit'])->middleware('role:Administrador')->name('cajas.edit');
    Route::post('/cajas/update/{id}', [CajaController::class, 'update'])->middleware('role:Administrador')->name('cajas.update');
    Route::delete('/cajas/{id}', [CajaController::class, 'destroy'])->middleware('role:Administrador')->name('cajas.destroy');
    Route::get('/cajas/data', [CajaController::class, 'data'])->middleware('role:Administrador')->name('cajas.data');
    Route::post('/cajas', [CajaController::class, 'store'])->middleware('role:Administrador');
    Route::get('/cajas/{caja}', [CajaController::class, 'show'])->middleware('role:Administrador');
    Route::put('/cajas/{id}', [CajaController::class, 'update'])->middleware('role:Administrador');

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
    Route::get('/views/documentos', [DocumentoController::class, 'index'])->middleware('role:Administrador,Contabilidad')->name('documentos.index');
    Route::get('/documentos', [DocumentoController::class, 'data'])->middleware('role:Administrador,Contabilidad');
    Route::get('/documentos/{documento}', [DocumentoController::class, 'show'])->middleware('role:Administrador,Contabilidad');
