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

    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #1D4ED8;
        color: #fff;
        border: none;
        border-radius: 7px;
        padding: 7px 14px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        white-space: nowrap;
    }

    .btn-primary:hover {
        background: #1e40af;
    }

    .btn-outline {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: #fff;
        color: #374151;
        border: 1px solid #D1D5DB;
        border-radius: 7px;
        padding: 7px 12px;
        font-size: 12px;
        font-weight: 500;
        cursor: pointer;
    }

    .btn-outline:hover {
        background: #F3F4F6;
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

    .fi-input {
        flex: 1;
        min-width: 200px;
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

    .table-wrapper {
        background: #fff;
        border: 1px solid #EAECF0;
        border-radius: 10px;
        overflow: hidden;
    }

    .table-scroll {
        overflow-x: auto;
    }

    .acomp-tbl {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
    }

    .acomp-tbl th {
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

    .acomp-tbl td {
        padding: 11px 14px;
        border-bottom: 1px solid #F3F4F6;
        vertical-align: middle;
        color: #374151;
    }

    .acomp-tbl tr:hover td {
        background: #FAFAFF;
    }

    .td-mono {
        font-family: 'IBM Plex Mono', monospace;
        color: #7C3AED;
        font-weight: 600;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 3px 9px;
        border-radius: 999px;
        font-size: 11px;
        font-weight: 600;
    }

    .badge-blue {
        background: #EFF6FF;
        color: #1D4ED8;
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

    .modal-bg {
        position: fixed;
        inset: 0;
        background: rgba(17, 24, 39, .5);
        z-index: 50;
        align-items: center;
        justify-content: center;
    }

    .modal-card {
        background: #fff;
        border-radius: 12px;
        max-width: 440px;
        width: 94%;
        max-height: 85vh;
        overflow-y: auto;
    }

    .modal-card.wide {
        max-width: 560px;
    }

    .modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid #EAECF0;
    }

    .modal-title {
        font-weight: 700;
        font-size: 15px;
        color: #111827;
        margin: 0;
    }

    .modal-close {
        border: none;
        background: transparent;
        font-size: 18px;
        cursor: pointer;
        color: #6B7280;
    }

    .modal-body {
        padding: 18px 20px;
    }

    .field {
        margin-bottom: 14px;
    }

    .field label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 5px;
    }

    .field input {
        width: 100%;
        border: 1px solid #D1D5DB;
        border-radius: 7px;
        padding: 8px 10px;
        font-size: 13px;
    }

    .modal-foot {
        display: flex;
        justify-content: flex-end;
        gap: 8px;
        padding: 14px 20px;
        border-top: 1px solid #EAECF0;
    }

    .opcion-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 10px;
        border: 1px solid #F3F4F6;
        border-radius: 8px;
        margin-bottom: 6px;
        font-size: 12.5px;
    }

    .opcion-buscar-wrap {
        position: relative;
        margin-bottom: 14px;
    }

    .opcion-resultados {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 8px;
        box-shadow: 0 8px 20px -6px rgba(0, 0, 0, .15);
        max-height: 200px;
        overflow-y: auto;
        z-index: 20;
    }
</style>

<div>
    <div class="sec-header">
        <div>
            <div class="sec-title">🍹 Acompañamientos</div>
            <p class="sec-subtitle">Grupos de productos elegibles para combos/mezclas (ej. "Servicio de Cubetazo": hasta
                10 unidades a repartir entre varias cervezas).</p>
        </div>
        <button class="btn-primary" onclick="abrirModalNuevoAcompanamiento()">+ Nuevo grupo</button>
    </div>

    <div class="filter-bar">
        <input type="text" id="acomp-buscar" class="fi-input" placeholder="Buscar por código o descripción..."
            oninput="filtrarAcompanamientos()">
        <button type="button" class="btn-outline"
            onclick="document.getElementById('acomp-buscar').value='';filtrarAcompanamientos();">✕ Limpiar</button>
    </div>

    <div class="table-wrapper" id="tablaAcompanamientos">
        @include('acompanamientos.partials.tabla', ['grupos' => $grupos])
    </div>
</div>

{{-- Modal: crear/editar grupo --}}
<div id="modalAcompanamiento" class="modal-bg hidden">
    <div class="modal-card">
        <div class="modal-head">
            <p class="modal-title" id="acomp-modal-titulo">Nuevo grupo de acompañamiento</p>
            <button class="modal-close" onclick="cerrarModalAcompanamiento()">✕</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="acomp-id">
            <div class="field">
                <label>Código</label>
                <input type="text" id="acomp-codigo" placeholder="Ej: CUBETAZO">
            </div>
            <div class="field">
                <label>Descripción</label>
                <input type="text" id="acomp-descripcion" placeholder="Ej: Servicio de Cubetazo">
            </div>
            <div class="field">
                <label>Cantidad máxima a repartir</label>
                <input type="number" id="acomp-cantidad-maxima" min="1" placeholder="Ej: 10">
            </div>
        </div>
        <div class="modal-foot">
            <button class="btn-outline" onclick="cerrarModalAcompanamiento()">Cancelar</button>
            <button class="btn-primary" onclick="guardarAcompanamiento()">💾 Guardar</button>
        </div>
    </div>
</div>

{{-- Modal: gestionar productos elegibles del grupo --}}
<div id="modalOpcionesAcompanamiento" class="modal-bg hidden">
    <div class="modal-card wide">
        <div class="modal-head">
            <p class="modal-title" id="opciones-modal-titulo">Productos del grupo</p>
            <button class="modal-close" onclick="cerrarModalOpciones()">✕</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="opciones-grupo-id">

            <div class="opcion-buscar-wrap">
                <label style="display:block;font-size:12px;font-weight:600;color:#374151;margin-bottom:5px;">Agregar
                    producto al grupo</label>
                <input type="text" id="opciones-buscar" class="fi-input" style="width:100%;" autocomplete="off"
                    placeholder="Buscar por nombre o código…" oninput="buscarProductoParaOpcion()">
                <div id="opciones-resultados" class="opcion-resultados hidden"></div>
            </div>

            <p style="font-size:11.5px;color:#9CA3AF;margin:0 0 8px;">Productos elegibles en este grupo:</p>
            <div id="opciones-lista"></div>
        </div>
        <div class="modal-foot">
            <button class="btn-outline" onclick="cerrarModalOpciones()">Cerrar</button>
        </div>
    </div>
</div>

<script>
    var acompPagina = 1;

    function cargarTablaAcompanamientos() {
        var buscar = document.getElementById('acomp-buscar').value.trim();
        var contenedor = document.getElementById('tablaAcompanamientos');
        var params = new URLSearchParams({
            buscar: buscar,
            page: acompPagina
        });

        fetch('/views/acompanamientos?' + params.toString(), {
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
                contenedor.innerHTML = '<div class="spinner-cell">⚠️ No fue posible cargar los grupos.</div>';
            });
    }

    window.filtrarAcompanamientos = function() {
        acompPagina = 1;
        cargarTablaAcompanamientos();
    };
    window.irAPaginaAcompanamiento = function(p) {
        if (p < 1) return;
        acompPagina = p;
        cargarTablaAcompanamientos();
    };

    function tokenCsrf() {
        return document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    }

    // --- Modal crear/editar ---
    window.abrirModalNuevoAcompanamiento = function() {
        document.getElementById('acomp-modal-titulo').textContent = 'Nuevo grupo de acompañamiento';
        document.getElementById('acomp-id').value = '';
        document.getElementById('acomp-codigo').value = '';
        document.getElementById('acomp-descripcion').value = '';
        document.getElementById('acomp-cantidad-maxima').value = '';
        var m = document.getElementById('modalAcompanamiento');
        m.classList.remove('hidden');
        m.classList.add('flex');
    };

    window.editarAcompanamiento = function(id, codigo, descripcion, cantidadMaxima) {
        document.getElementById('acomp-modal-titulo').textContent = 'Editar grupo de acompañamiento';
        document.getElementById('acomp-id').value = id;
        document.getElementById('acomp-codigo').value = codigo;
        document.getElementById('acomp-descripcion').value = descripcion;
        document.getElementById('acomp-cantidad-maxima').value = cantidadMaxima;
        var m = document.getElementById('modalAcompanamiento');
        m.classList.remove('hidden');
        m.classList.add('flex');
    };

    window.cerrarModalAcompanamiento = function() {
        var m = document.getElementById('modalAcompanamiento');
        m.classList.add('hidden');
        m.classList.remove('flex');
    };

    window.guardarAcompanamiento = function() {
        var id = document.getElementById('acomp-id').value;
        var payload = {
            codigo: document.getElementById('acomp-codigo').value.trim(),
            descripcion: document.getElementById('acomp-descripcion').value.trim(),
            cantidad_maxima: document.getElementById('acomp-cantidad-maxima').value,
        };

        if (!payload.codigo || !payload.descripcion || !payload.cantidad_maxima) {
            mostrarNotificacion('Completa código, descripción y cantidad máxima.', 'error');
            return;
        }

        var url = id ? '/acompanamientos/' + id : '/acompanamientos';
        if (id) payload._method = 'PUT';

        fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': tokenCsrf(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(payload),
            })
            .then(async function(r) {
                var data = await r.json();
                if (!r.ok) throw new Error(data.message || (data.errors && Object.values(data.errors)[0][
                    0]) || 'Error al guardar.');
                return data;
            })
            .then(function(data) {
                mostrarNotificacion(data.message, 'success');
                cerrarModalAcompanamiento();
                cargarTablaAcompanamientos();
            })
            .catch(function(e) {
                mostrarNotificacion(e.message, 'error');
            });
    };

    window.eliminarAcompanamiento = function(id) {
        abrirConfirm('¿Eliminar este grupo de acompañamiento?', function() {
            fetch('/acompanamientos/' + id, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': tokenCsrf(),
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: new URLSearchParams({
                        _method: 'DELETE'
                    }),
                })
                .then(async function(r) {
                    var data = await r.json();
                    if (!r.ok) throw new Error(data.message || 'Error al eliminar.');
                    return data;
                })
                .then(function(data) {
                    mostrarNotificacion(data.message, 'success');
                    cargarTablaAcompanamientos();
                })
                .catch(function(e) {
                    mostrarNotificacion(e.message, 'error');
                });
        });
    };

    // --- Modal opciones (productos del grupo) ---
    window.gestionarOpciones = function(grupoId, grupoDescripcion) {
        document.getElementById('opciones-grupo-id').value = grupoId;
        document.getElementById('opciones-modal-titulo').textContent = 'Productos de "' + grupoDescripcion + '"';
        document.getElementById('opciones-buscar').value = '';
        document.getElementById('opciones-resultados').classList.add('hidden');
        cargarOpcionesDelGrupo();
        var m = document.getElementById('modalOpcionesAcompanamiento');
        m.classList.remove('hidden');
        m.classList.add('flex');
    };

    window.cerrarModalOpciones = function() {
        var m = document.getElementById('modalOpcionesAcompanamiento');
        m.classList.add('hidden');
        m.classList.remove('flex');
        cargarTablaAcompanamientos(); // por si cambió el conteo de productos
    };

    function cargarOpcionesDelGrupo() {
        var grupoId = document.getElementById('opciones-grupo-id').value;
        fetch('/acompanamientos/' + grupoId + '/opciones')
            .then(function(r) {
                return r.json();
            })
            .then(function(data) {
                var lista = document.getElementById('opciones-lista');
                if (!data.opciones.length) {
                    lista.innerHTML =
                        '<p style="font-size:12px;color:#9CA3AF;">Todavía no hay productos en este grupo.</p>';
                    return;
                }
                lista.innerHTML = data.opciones.map(function(p) {
                    return '<div class="opcion-row">' +
                        '<span><span class="td-mono">' + p.codigo + '</span> &nbsp; ' + p.descripcion +
                        '</span>' +
                        '<button class="act-btn" title="Quitar" onclick="quitarOpcion(' + grupoId + ', ' + p
                        .id + ')">🗑️</button>' +
                        '</div>';
                }).join('');
            });
    }

    window.buscarProductoParaOpcion = debounce(function() {
        var query = document.getElementById('opciones-buscar').value.trim();
        var contenedor = document.getElementById('opciones-resultados');
        if (!query || query.length < 2) {
            contenedor.classList.add('hidden');
            return;
        }

        fetch('/productos/buscar?query=' + encodeURIComponent(query))
            .then(function(r) {
                return r.json();
            })
            .then(function(data) {
                if (!data.length) {
                    contenedor.innerHTML =
                        '<div style="padding:10px;color:#9CA3AF;font-size:12px;">Sin resultados</div>';
                } else {
                    contenedor.innerHTML = data.map(function(p) {
                        var descripcion = (p.descripcion || '').replace(/'/g, "\\'");
                        return '<div onclick="agregarOpcion(' + p.id + ', \'' + descripcion +
                            '\')" ' +
                            'style="display:flex;gap:10px;padding:9px 12px;cursor:pointer;border-bottom:1px solid #F3F4F6;font-size:12.5px;">' +
                            '<span class="td-mono" style="width:80px;flex-shrink:0;">' + (p
                                .codigo ?? '-') + '</span>' +
                            '<span>' + p.descripcion + '</span></div>';
                    }).join('');
                }
                contenedor.classList.remove('hidden');
            });
    }, 300);

    window.agregarOpcion = function(productoId) {
        var grupoId = document.getElementById('opciones-grupo-id').value;
        fetch('/acompanamientos/' + grupoId + '/opciones', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': tokenCsrf(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    producto_id: productoId
                }),
            })
            .then(async function(r) {
                var data = await r.json();
                if (!r.ok) throw new Error(data.message || 'No se pudo agregar.');
                return data;
            })
            .then(function() {
                document.getElementById('opciones-buscar').value = '';
                document.getElementById('opciones-resultados').classList.add('hidden');
                cargarOpcionesDelGrupo();
            })
            .catch(function(e) {
                mostrarNotificacion(e.message, 'error');
            });
    };

    window.quitarOpcion = function(grupoId, productoId) {
        fetch('/acompanamientos/' + grupoId + '/opciones/' + productoId, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': tokenCsrf(),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    _method: 'DELETE'
                }),
            })
            .then(function() {
                cargarOpcionesDelGrupo();
            })
            .catch(function(e) {
                mostrarNotificacion('No se pudo quitar', 'error');
            });
    };
</script>
