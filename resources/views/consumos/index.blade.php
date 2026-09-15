<style>
    .sec-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 16px;
    }

    .sec-title {
        font-size: 17px;
        font-weight: 600;
        color: #111827;
        letter-spacing: -0.3px;
    }

    .sec-subtitle {
        font-size: 12px;
        color: #6B7280;
        margin-top: 2px;
    }

    .metrics-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 10px;
        margin-bottom: 16px;
    }

    .metric-card {
        background: #fff;
        border: 1px solid #EAECF0;
        border-radius: 10px;
        padding: 12px 14px;
        position: relative;
        overflow: hidden;
    }

    .metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: #7C3AED;
    }

    .metric-label {
        font-size: 11px;
        font-weight: 500;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .metric-value {
        font-size: 20px;
        font-weight: 700;
        color: #111827;
        margin-top: 4px;
        letter-spacing: -0.5px;
    }

    .metric-sub {
        font-size: 11px;
        color: #6B7280;
        margin-top: 2px;
    }

    .filter-bar {
        background: #fff;
        border: 1px solid #EAECF0;
        border-radius: 10px;
        padding: 12px 14px;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
        margin-bottom: 12px;
    }

    .filter-bar .fi-group {
        display: flex;
        align-items: center;
        gap: 6px;
        flex: 1;
        min-width: 160px;
    }

    .fi-label {
        font-size: 12px;
        color: #6B7280;
        white-space: nowrap;
    }

    .fi-input {
        flex: 1;
        border: 1px solid #D1D5DB;
        border-radius: 7px;
        padding: 6px 10px;
        font-size: 12px;
        color: #111827;
        background: #F9FAFB;
        outline: none;
    }

    .fi-input:focus {
        border-color: #7C3AED;
        background: #fff;
    }

    .fi-btn {
        border: 1px solid #D1D5DB;
        background: #fff;
        color: #374151;
        font-size: 12px;
        font-weight: 600;
        border-radius: 7px;
        padding: 6px 12px;
        cursor: pointer;
    }

    .fi-btn:hover {
        background: #F9FAFB;
    }

    .table-wrapper {
        background: #fff;
        border: 1px solid #EAECF0;
        border-radius: 10px;
        overflow: hidden;
    }

    .table-scroll {
        overflow-x: auto;
    }

    .cons-tbl {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
    }

    .cons-tbl th {
        text-align: left;
        padding: 10px 14px;
        background: #F9FAFB;
        color: #6B7280;
        font-size: 10.5px;
        text-transform: uppercase;
        letter-spacing: .04em;
        border-bottom: 1px solid #EAECF0;
        white-space: nowrap;
    }

    .cons-tbl td {
        padding: 11px 14px;
        border-bottom: 1px solid #F3F4F6;
        vertical-align: top;
        color: #374151;
    }

    .cons-tbl tr:hover td {
        background: #FAFAFF;
    }

    .td-mono {
        font-family: 'IBM Plex Mono', monospace;
        color: #7C3AED;
        font-weight: 600;
    }

    .td-money {
        font-family: 'IBM Plex Mono', monospace;
        font-weight: 600;
        color: #111827;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .badge-purple {
        background: #F3E8FF;
        color: #7C3AED;
    }

    .badge-green {
        background: #ECFDF5;
        color: #065F46;
    }

    .badge-amber {
        background: #FFFBEB;
        color: #92400E;
    }

    .badge-red {
        background: #FEF2F2;
        color: #991B1B;
    }

    .act-btn {
        border: none;
        background: #F3F4F6;
        border-radius: 6px;
        padding: 5px 8px;
        cursor: pointer;
        font-size: 12px;
    }

    .act-btn:hover {
        background: #E5E7EB;
    }

    .spinner-cell {
        padding: 40px 20px;
        text-align: center;
        color: #9CA3AF;
        font-size: 13px;
    }

    .fi-select {
        border: 1px solid #D1D5DB;
        border-radius: 7px;
        padding: 6px 10px;
        font-size: 12px;
        color: #111827;
        background: #F9FAFB;
        outline: none;
    }

    .fi-select:focus {
        border-color: #7C3AED;
        background: #fff;
    }

    .pend-alert {
        background: #FFFBEB;
        border: 1px solid #FDE68A;
        border-radius: 10px;
        padding: 12px 14px;
        font-size: 12px;
        color: #92400E;
        margin-bottom: 14px;
    }

    .pend-linea {
        display: grid;
        grid-template-columns: 1fr auto auto auto;
        gap: 10px;
        align-items: center;
        padding: 9px 0;
        border-bottom: 1px solid #F3F4F6;
    }

    .pend-linea input[type="number"] {
        width: 78px;
        border: 1px solid #D1D5DB;
        border-radius: 6px;
        padding: 5px 7px;
        font-size: 12.5px;
    }

    .pend-falta {
        color: #B91C1C;
        font-weight: 700;
        font-size: 11px;
    }

    .pend-ok {
        color: #065F46;
        font-weight: 700;
        font-size: 11px;
    }

    .btn-registrar {
        border: none;
        border-radius: 8px;
        padding: 9px 14px;
        font-size: 12.5px;
        font-weight: 700;
        cursor: pointer;
        background: #7C3AED;
        color: #fff;
        width: 100%;
        margin-top: 12px;
    }

    .btn-registrar:hover {
        background: #6D28D9;
    }

    .btn-registrar:disabled {
        opacity: .55;
        cursor: not-allowed;
    }

    @media (max-width:640px) {
        .filter-bar .fi-group {
            min-width: 100%;
        }
    }
</style>

<div>
    <div class="sec-header">
        <div>
            <div class="sec-title">🥣 Consumos de materia prima</div>
            <p class="sec-subtitle">Insumos descontados automáticamente al vender productos ensamblados (ej. un Cubetazo
                consume varias unidades de Poker).</p>
        </div>
    </div>

    <div class="metrics-row">
        <div class="metric-card">
            <div class="metric-label">Total consumos</div>
            <div class="metric-value">{{ $metricas['total'] }}</div>
            <div class="metric-sub">Registrados en total</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Valor total</div>
            <div class="metric-value">${{ number_format($metricas['valor_total'], 0, ',', '.') }}</div>
            <div class="metric-sub">Costo acumulado de insumos consumidos</div>
        </div>
        <div class="metric-card">
            <div class="metric-label">Este mes</div>
            <div class="metric-value">${{ number_format($metricas['este_mes'], 0, ',', '.') }}</div>
            <div class="metric-sub">{{ now()->translatedFormat('F Y') }}</div>
        </div>
        <div class="metric-card" style="{{ $metricas['pendientes'] > 0 ? '--accent:#D97706;' : '' }}">
            <div class="metric-label">No registrados</div>
            <div class="metric-value" style="{{ $metricas['pendientes'] > 0 ? 'color:#B45309;' : '' }}">
                {{ $metricas['pendientes'] }}</div>
            <div class="metric-sub">Esperando que un administrador ajuste inventario o descarte la línea</div>
        </div>
    </div>

    <div class="filter-bar">
        <div class="fi-group">
            <span class="fi-label">🔍</span>
            <input type="text" id="cons-buscar" class="fi-input" placeholder="Número de factura..."
                oninput="filtrarConsumos()">
        </div>
        <div class="fi-group" style="flex:none;">
            <span class="fi-label">Estado</span>
            <select id="cons-estado" class="fi-select" onchange="filtrarConsumos()">
                <option value="todos">Todos</option>
                <option value="no_registrado">⚠️ No registrados</option>
                <option value="registrado">✓ Registrados</option>
            </select>
        </div>
        <div class="fi-group" style="flex:none;">
            <span class="fi-label">Desde</span>
            <input type="date" id="cons-desde" class="fi-input" onchange="filtrarConsumos()">
        </div>
        <div class="fi-group" style="flex:none;">
            <span class="fi-label">Hasta</span>
            <input type="date" id="cons-hasta" class="fi-input" onchange="filtrarConsumos()">
        </div>
        <button type="button" class="fi-btn" onclick="limpiarFiltrosConsumos()">✕ Limpiar</button>
    </div>

    <div class="table-wrapper" id="tablaConsumos">
        @include('consumos.partials.tabla', ['consumos' => $consumos])
    </div>
</div>

<div id="modalConsumoDetalle" class="fixed inset-0 hidden items-center justify-center z-50"
    style="background:rgba(17,24,39,.5);">
    <div style="background:#fff;border-radius:12px;max-width:520px;width:94%;max-height:85vh;overflow-y:auto;">
        <div
            style="display:flex;align-items:center;justify-content:space-between;padding:16px 20px;border-bottom:1px solid #EAECF0;">
            <p style="font-weight:700;font-size:15px;color:#111827;margin:0;" id="cons-detalle-titulo">Detalle del
                consumo</p>
            <button onclick="cerrarModalConsumo()"
                style="border:none;background:transparent;font-size:18px;cursor:pointer;color:#6B7280;">✕</button>
        </div>
        <div style="padding:18px 20px;" id="cons-detalle-body"></div>
    </div>
</div>

<script>
    var consPagina = 1;

    function tokenConsumos() {
        return document.querySelector('meta[name="csrf-token"]')?.content;
    }

    function notifConsumos(msg, tipo) {
        if (typeof mostrarNotificacion === 'function') {
            mostrarNotificacion(msg, tipo || 'success');
            return;
        }
        window.alert(msg);
    }

    function cargarTablaConsumos() {
        var buscar = document.getElementById('cons-buscar').value.trim();
        var estado = document.getElementById('cons-estado').value;
        var desde = document.getElementById('cons-desde').value;
        var hasta = document.getElementById('cons-hasta').value;
        var contenedor = document.getElementById('tablaConsumos');

        var params = new URLSearchParams({
            buscar: buscar,
            estado: estado,
            desde: desde,
            hasta: hasta,
            page: consPagina
        });

        fetch('/views/consumos?' + params.toString(), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'text/html'
                }
            })
            .then(function(r) {
                return r.text();
            })
            .then(function(html) {
                contenedor.innerHTML = html;
            })
            .catch(function() {
                contenedor.innerHTML = '<div class="spinner-cell">⚠️ No fue posible cargar los consumos.</div>';
            });
    }

    window.filtrarConsumos = function() {
        consPagina = 1;
        cargarTablaConsumos();
    };

    window.irAPaginaConsumo = function(pagina) {
        if (pagina < 1) return;
        consPagina = pagina;
        cargarTablaConsumos();
    };

    window.limpiarFiltrosConsumos = function() {
        document.getElementById('cons-buscar').value = '';
        document.getElementById('cons-estado').value = 'todos';
        document.getElementById('cons-desde').value = '';
        document.getElementById('cons-hasta').value = '';
        filtrarConsumos();
    };

    window.verConsumo = function(id) {
        fetch('/consumos/' + id, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(function(r) {
                return r.json();
            })
            .then(renderConsumoDetalle)
            .catch(function() {
                notifConsumos('No se pudo cargar el detalle del consumo', 'error');
            });
    };

    function renderConsumoDetalle(c) {
        document.getElementById('cons-detalle-titulo').textContent = 'Consumo · Factura ' + c.numero_factura;
        var pendiente = c.estado === 'no_registrado';

        var filas = (c.detalles || []).map(function(d) {
            if (!pendiente) {
                return '<tr>' +
                    '<td style="padding:8px 0;color:#374151;">' + (d.producto_base?.descripcion || '—') +
                    '<div style="font-size:11px;color:#9CA3AF;">generado por: ' + (d.producto_ensamblado
                        ?.descripcion || '—') +
                    (d.bodega ? ' · bodega: ' + d.bodega.descripcion : '') + '</div></td>' +
                    '<td style="padding:8px 0;text-align:center;">' + Number(d.cantidad) + ' ' + (d
                        .producto_base?.und_detal || '') + '</td>' +
                    '<td style="padding:8px 0;text-align:right;font-family:\'IBM Plex Mono\',monospace;">$' +
                    Number(d.subtotal).toLocaleString('es-CO') + '</td>' +
                    '</tr>';
            }

            // Pendiente: además de ver, se puede editar la cantidad o
            // eliminar la línea — ver ConsumoController::actualizarDetalle()/eliminarDetalle().
            var disponible = Number(d.stock_disponible ?? 0);
            var alcanza = disponible >= Number(d.cantidad);
            return '<div class="pend-linea">' +
                '<div>' + (d.producto_base?.descripcion || '—') +
                '<div style="font-size:11px;color:#9CA3AF;">generado por: ' + (d.producto_ensamblado
                    ?.descripcion || '—') +
                (d.bodega ? ' · bodega: ' + d.bodega.descripcion : '') + '</div>' +
                '<div class="' + (alcanza ? 'pend-ok' : 'pend-falta') + '">' + (alcanza ? '✓ hay ' +
                    disponible + ' disponibles' : '✗ solo hay ' + disponible + ' (faltan ' + (Number(d
                        .cantidad) - disponible) + ')') + '</div>' +
                '</div>' +
                '<input type="number" min="0.01" step="0.01" value="' + Number(d.cantidad) +
                '" id="pend-cant-' + d.id + '">' +
                '<button class="act-btn" title="Guardar cantidad" onclick="guardarCantidadConsumo(' + d.id +
                ', ' + c.id + ')">💾</button>' +
                '<button class="act-btn" title="Eliminar línea" onclick="eliminarLineaConsumo(' + d.id + ', ' +
                c.id + ')">🗑️</button>' +
                '</div>';
        }).join('');

        var alertaPendiente = pendiente ?
            '<div class="pend-alert">⚠️ <b>No registrado:</b> a este consumo le faltó stock de al menos un insumo cuando se hizo la venta. La factura ya se cobró y quedó bien — esto NO ha descontado inventario todavía. Ajusta el inventario (Existencias/Ajustes) o elimina la línea problemática, y luego pulsa "Registrar".</div>' :
            '';

        var cuerpoTabla = pendiente ?
            filas :
            '<table style="width:100%;border-collapse:collapse;font-size:12.5px;">' +
            '<thead><tr style="color:#9CA3AF;font-size:10.5px;text-transform:uppercase;"><th style="text-align:left;padding-bottom:6px;border-bottom:1px solid #EAECF0;">Insumo</th><th style="padding-bottom:6px;border-bottom:1px solid #EAECF0;">Cantidad</th><th style="text-align:right;padding-bottom:6px;border-bottom:1px solid #EAECF0;">Costo</th></tr></thead>' +
            '<tbody>' + filas + '</tbody>' +
            '<tfoot><tr><td colspan="2" style="padding-top:10px;font-weight:700;color:#111827;">Total</td><td style="padding-top:10px;text-align:right;font-weight:700;color:#111827;font-family:\'IBM Plex Mono\',monospace;">$' +
            Number(c.total).toLocaleString('es-CO') + '</td></tr></tfoot>' +
            '</table>';

        var botonRegistrar = pendiente ?
            '<button class="btn-registrar" id="btn-registrar-consumo" onclick="registrarConsumo(' + c.id +
            ')">✓ Registrar (descontar inventario ahora)</button>' :
            '';

        document.getElementById('cons-detalle-body').innerHTML =
            '<div style="font-size:12.5px;color:#6B7280;margin-bottom:14px;">' +
            '<div><b style="color:#111827;">Fecha:</b> ' + String(c.fecha).slice(0, 10).split('-').reverse().join('/') +
            '</div>' +
            '<div><b style="color:#111827;">Observación:</b> ' + c.observacion + '</div>' +
            '<div><b style="color:#111827;">Generado por:</b> ' + (c.user?.name || '—') + '</div>' +
            (pendiente ? '' : '<div><b style="color:#111827;">Registrado por:</b> ' + (c.registrado_por?.name || '—') +
                '</div>') +
            '</div>' +
            alertaPendiente + cuerpoTabla + botonRegistrar;

        var modal = document.getElementById('modalConsumoDetalle');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    window.guardarCantidadConsumo = function(detalleId, consumoId) {
        var input = document.getElementById('pend-cant-' + detalleId);
        var cantidad = Number(input.value);
        if (!cantidad || cantidad <= 0) {
            notifConsumos('Ingresa una cantidad válida.', 'error');
            return;
        }

        fetch('/consumos/detalles/' + detalleId, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': tokenConsumos(),
                    'Content-Type': 'application/json',
                    Accept: 'application/json'
                },
                body: JSON.stringify({
                    cantidad: cantidad
                }),
            }).then(function(r) {
                return r.json().then(function(d) {
                    return {
                        ok: r.ok,
                        d: d
                    };
                });
            })
            .then(function(res) {
                if (!res.ok) throw new Error(res.d.errors ? Object.values(res.d.errors).flat().join('<br>') :
                    res.d.message);
                notifConsumos('Cantidad actualizada.', 'success');
                verConsumo(consumoId);
                cargarTablaConsumos();
            }).catch(function(e) {
                notifConsumos(e.message, 'error');
            });
    };

    window.eliminarLineaConsumo = function(detalleId, consumoId) {
        var confirmar = window.Swal ?
            Swal.fire({
                title: '¿Eliminar esta línea del consumo?',
                text: 'No se descontará inventario por ella.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Eliminar'
            }) :
            Promise.resolve({
                isConfirmed: confirm('¿Eliminar esta línea del consumo? No se descontará inventario por ella.')
            });

        confirmar.then(function(res) {
            if (!res.isConfirmed) return;
            fetch('/consumos/detalles/' + detalleId, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': tokenConsumos(),
                        Accept: 'application/json'
                    },
                }).then(function(r) {
                    return r.json().then(function(d) {
                        return {
                            ok: r.ok,
                            d: d
                        };
                    });
                })
                .then(function(res2) {
                    if (!res2.ok) throw new Error(res2.d.message);
                    notifConsumos(res2.d.message, 'success');
                    cargarTablaConsumos();
                    if (res2.d.data && res2.d.data.detalles && res2.d.data.detalles.length === 0) {
                        cerrarModalConsumo();
                    } else {
                        verConsumo(consumoId);
                    }
                }).catch(function(e) {
                    notifConsumos(e.message, 'error');
                });
        });
    };

    window.registrarConsumo = function(consumoId) {
        var boton = document.getElementById('btn-registrar-consumo');
        if (boton) {
            boton.disabled = true;
            boton.textContent = 'Registrando…';
        }

        fetch('/consumos/' + consumoId + '/registrar', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': tokenConsumos(),
                    Accept: 'application/json'
                },
            }).then(function(r) {
                return r.json().then(function(d) {
                    return {
                        ok: r.ok,
                        d: d
                    };
                });
            })
            .then(function(res) {
                if (!res.ok) throw new Error(res.d.errors ? Object.values(res.d.errors).flat().join('<br>') :
                    res.d.message);
                notifConsumos(res.d.message, 'success');
                cerrarModalConsumo();
                cargarTablaConsumos();
            }).catch(function(e) {
                notifConsumos(e.message, 'error');
                if (boton) {
                    boton.disabled = false;
                    boton.textContent = '✓ Registrar (descontar inventario ahora)';
                }
            });
    };

    window.cerrarModalConsumo = function() {
        var modal = document.getElementById('modalConsumoDetalle');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    };
</script>
