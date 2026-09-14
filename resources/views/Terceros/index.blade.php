<style>
    .sec-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 16px;
    }

    .sec-title { font-size: 17px; font-weight: 600; color: #111827; letter-spacing: -0.3px; }
    .sec-subtitle { font-size: 12px; color: #6B7280; margin-top: 2px; }

    .metrics-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
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
        top: 0; left: 0; right: 0;
        height: 3px;
        background: var(--accent, #1D4ED8);
    }

    .metric-label {
        font-size: 11px;
        font-weight: 500;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .metric-value { font-size: 20px; font-weight: 700; color: #111827; margin-top: 4px; letter-spacing: -0.5px; }
    .metric-sub { font-size: 11px; color: #6B7280; margin-top: 2px; }

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

    .filter-bar .fi-group { display: flex; align-items: center; gap: 6px; flex: 1; min-width: 160px; }
    .fi-label { font-size: 12px; color: #6B7280; white-space: nowrap; }

    .fi-input {
        flex: 1;
        border: 1px solid #D1D5DB;
        border-radius: 7px;
        padding: 6px 10px;
        font-size: 12px;
        color: #111827;
        background: #F9FAFB;
        outline: none;
        transition: border 0.15s;
    }

    .fi-select {
        border: 1px solid #D1D5DB;
        border-radius: 7px;
        padding: 6px 10px;
        font-size: 12px;
        color: #111827;
        background: #F9FAFB;
        outline: none;
        cursor: pointer;
        flex: none;
    }

    .fi-input:focus, .fi-select:focus { border-color: #1D4ED8; background: #fff; }
    .fi-input::placeholder { color: #9CA3AF; }

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
        transition: background 0.15s;
        white-space: nowrap;
    }

    .btn-primary:hover { background: #1e40af; }
    .btn-primary:disabled { opacity: .5; cursor: not-allowed; }

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
        transition: all 0.15s;
        white-space: nowrap;
    }

    .btn-outline:hover { background: #F3F4F6; border-color: #9CA3AF; }

    .table-wrapper { background: #fff; border: 1px solid #EAECF0; border-radius: 10px; overflow: hidden; }
    .table-scroll { overflow-x: auto; }

    table.tc-tbl { width: 100%; border-collapse: collapse; font-size: 12.5px; }
    table.tc-tbl thead { background: #F8FAFC; border-bottom: 1px solid #EAECF0; }

    table.tc-tbl thead th {
        padding: 10px 12px;
        text-align: left;
        font-size: 11px;
        font-weight: 600;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    table.tc-tbl tbody tr { border-bottom: 1px solid #F3F4F6; transition: background 0.1s; }
    table.tc-tbl tbody tr:last-child { border-bottom: none; }
    table.tc-tbl tbody tr:hover { background: #F8FAFC; }
    table.tc-tbl tbody tr.inactivo { opacity: 0.6; }
    table.tc-tbl td { padding: 9px 12px; color: #374151; vertical-align: middle; }

    .td-mono { font-family: 'JetBrains Mono', 'Fira Mono', monospace; font-size: 12px; color: #1D4ED8; font-weight: 600; }

    .tc-avatar {
        width: 32px;
        height: 32px;
        border-radius: 50%;
        background: #EFF6FF;
        color: #1D4ED8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 12px;
        text-transform: uppercase;
        border: 1px solid #DBEAFE;
        flex-shrink: 0;
    }

    .badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .badge-green { background: #ECFDF5; color: #065F46; }
    .badge-red { background: #FEF2F2; color: #991B1B; }
    .badge-purple { background: #EDE9FE; color: #4C1D95; }
    .badge-blue { background: #EFF6FF; color: #1e3a8a; }
    .badge-gray { background: #F3F4F6; color: #374151; }
    .dot { width: 5px; height: 5px; border-radius: 50%; display: inline-block; background: currentColor; }

    .tbl-actions { display: flex; align-items: center; justify-content: flex-end; gap: 4px; }

    .act-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 28px;
        height: 28px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-size: 13px;
        transition: all 0.15s;
        background: transparent;
        color: #6B7280;
        position: relative;
    }

    .act-btn:hover { background: #F3F4F6; color: #111827; transform: scale(1.05); }
    .act-btn.view:hover { background: #EFF6FF; color: #1D4ED8; }
    .act-btn.edit:hover { background: #ECFDF5; color: #059669; }
    .act-btn.state:hover { background: #FFFBEB; color: #D97706; }
    .act-btn.del:hover { background: #FEF2F2; color: #DC2626; }

    .act-btn::after {
        content: attr(title);
        position: absolute;
        bottom: calc(100% + 6px);
        left: 50%;
        transform: translateX(-50%);
        background: #1F2937;
        color: #fff;
        font-size: 11px;
        padding: 3px 7px;
        border-radius: 5px;
        white-space: nowrap;
        pointer-events: none;
        opacity: 0;
        transition: opacity 0.15s;
        z-index: 50;
    }

    .act-btn:hover::after { opacity: 1; }

    .spinner-cell {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
        color: #6B7280;
        font-size: 13px;
        gap: 10px;
    }

    @keyframes spin-tc { to { transform: rotate(360deg); } }

    .spinner {
        width: 18px;
        height: 18px;
        border: 2px solid #E5E7EB;
        border-top-color: #1D4ED8;
        border-radius: 50%;
        animation: spin-tc 0.7s linear infinite;
    }

    /* ── Modal ── */
    .modal-backdrop-tc { background: rgba(17, 24, 39, 0.5); backdrop-filter: blur(4px); }

    .modal-tc {
        background: #fff;
        border-radius: 16px;
        width: 100%;
        max-width: 640px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }

    .modal-head-tc {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid #EAECF0;
        flex-shrink: 0;
    }

    .modal-head-title { font-size: 15px; font-weight: 600; color: #111827; }
    .modal-head-sub { font-size: 12px; color: #6B7280; margin-top: 2px; }
    .modal-body-tc { flex: 1; overflow-y: auto; padding: 20px; }

    .modal-foot-tc {
        padding: 14px 20px;
        border-top: 1px solid #EAECF0;
        display: flex;
        gap: 8px;
        justify-content: flex-end;
        flex-shrink: 0;
    }

    .tc-tabs { display: flex; gap: 6px; padding: 4px; background: #F3F4F6; border-radius: 10px; margin-bottom: 16px; }

    .tc-tab {
        flex: 1;
        text-align: center;
        padding: 8px;
        border-radius: 8px;
        font-size: 12.5px;
        font-weight: 600;
        color: #6B7280;
        cursor: pointer;
        transition: all 0.15s;
    }

    .tc-tab.activo { background: #fff; color: #1D4ED8; box-shadow: 0 1px 2px rgba(16,24,40,.06); }

    .tc-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    @media (max-width: 480px) { .tc-grid { grid-template-columns: 1fr; } }

    .tc-field { display: flex; flex-direction: column; gap: 3px; }

    .tc-field label {
        font-size: 11px;
        font-weight: 500;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .tc-field input, .tc-field select {
        border: 1px solid #D1D5DB;
        border-radius: 7px;
        padding: 8px 10px;
        font-size: 13px;
        color: #111827;
        outline: none;
        background: #F9FAFB;
        transition: border 0.15s;
        font-family: inherit;
        width: 100%;
        box-sizing: border-box;
    }

    .tc-field input:focus, .tc-field select:focus { border-color: #1D4ED8; background: #fff; }

    .tc-check {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #374151;
        margin-top: 16px;
    }

    .tc-check input { width: 16px; height: 16px; accent-color: #1D4ED8; cursor: pointer; }

    @media (max-width: 640px) {
        .metrics-row { grid-template-columns: 1fr 1fr; }
    }
</style>

<div id="view-terceros">

    {{-- ── ENCABEZADO ── --}}
    <div class="sec-header">
        <div>
            <p class="sec-title">👥 Terceros</p>
            <p class="sec-subtitle">Directorio de clientes, proveedores y colaboradores</p>
        </div>
        <button class="btn-primary" onclick="openModalNuevoTercero()">＋ Nuevo Tercero</button>
    </div>

    {{-- ── MÉTRICAS ── --}}
    <div class="metrics-row">
        <div class="metric-card" style="--accent:#1D4ED8">
            <p class="metric-label">Total Terceros</p>
            <p class="metric-value">{{ $metricas['total'] }}</p>
            <p class="metric-sub">En el directorio</p>
        </div>
        <div class="metric-card" style="--accent:#7C3AED">
            <p class="metric-label">Personas Naturales</p>
            <p class="metric-value">{{ $metricas['personas'] }}</p>
            <p class="metric-sub">{{ $metricas['total'] ? round($metricas['personas'] / $metricas['total'] * 100) : 0 }}% del total</p>
        </div>
        <div class="metric-card" style="--accent:#1e3a8a">
            <p class="metric-label">Empresas</p>
            <p class="metric-value">{{ $metricas['empresas'] }}</p>
            <p class="metric-sub">{{ $metricas['total'] ? round($metricas['empresas'] / $metricas['total'] * 100) : 0 }}% del total</p>
        </div>
        <div class="metric-card" style="--accent:#DC2626">
            <p class="metric-label">Inactivos</p>
            <p class="metric-value">{{ $metricas['inactivos'] }}</p>
            <p class="metric-sub">No disponibles para venta</p>
        </div>
    </div>

    {{-- ── FILTROS ── --}}
    <div class="filter-bar">
        <div class="fi-group" style="min-width:220px;">
            <span class="fi-label">🔍</span>
            <input type="text" id="tc-buscar" class="fi-input" placeholder="Buscar por nombre, documento o celular…">
        </div>
        <div class="fi-group" style="flex:none;">
            <select class="fi-select" id="tc-tipo">
                <option value="">Todos los tipos</option>
                <option value="persona">Persona natural</option>
                <option value="empresa">Empresa</option>
            </select>
        </div>
        <div class="fi-group" style="flex:none;">
            <select class="fi-select" id="tc-estado">
                <option value="">Todos los estados</option>
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
            </select>
        </div>
        <button class="btn-outline" onclick="limpiarFiltrosTerceros()">✕ Limpiar</button>
    </div>

    {{-- ── TABLA ── --}}
    <div class="table-wrapper" id="tablaTerceros">
        @include('terceros.partials.tabla')
    </div>

</div>

{{-- ═══════════════════════════════════════════════
     MODAL TERCERO (crear / editar)
═══════════════════════════════════════════════ --}}
<div id="modalNuevoTercero" class="fixed inset-0 hidden items-center justify-center z-50 p-4 modal-backdrop-tc">
    <div class="modal-tc">
        <div class="modal-head-tc">
            <div>
                <p class="modal-head-title" id="tituloModalTercero">Nuevo Tercero</p>
                <p class="modal-head-sub">Completa la información del contacto</p>
            </div>
            <button onclick="closeModalNuevoTercero()"
                style="border:none;background:transparent;font-size:20px;cursor:pointer;color:#6B7280;padding:4px;border-radius:6px;line-height:1;">✕</button>
        </div>

        <form id="formTercero" class="modal-body-tc" onsubmit="return false;">
            <div class="tc-tabs">
                <label class="tc-tab activo" data-tab-tipo="persona">
                    <input type="radio" name="tipo" value="persona" class="hidden" checked onchange="toggleTipoTercero('persona')" style="display:none;">
                    Persona Natural
                </label>
                <label class="tc-tab" data-tab-tipo="empresa">
                    <input type="radio" name="tipo" value="empresa" class="hidden" onchange="toggleTipoTercero('empresa')" style="display:none;">
                    Empresa / NIT
                </label>
            </div>

            <div class="tc-grid">
                <div class="tc-field campo-persona">
                    <label>Nombre</label>
                    <input type="text" name="nombre">
                </div>
                <div class="tc-field campo-persona">
                    <label>Apellido</label>
                    <input type="text" name="apellido">
                </div>
                <div class="tc-field campo-persona" style="grid-column:1/-1;">
                    <label>Cédula de Ciudadanía</label>
                    <input type="text" name="cedula">
                </div>

                <div class="tc-field campo-empresa hidden" style="grid-column:1/-1;">
                    <label>Razón Social</label>
                    <input type="text" name="razon_social">
                </div>
                <div class="tc-field campo-empresa hidden" style="grid-column:1/-1;">
                    <label>NIT</label>
                    <input type="text" name="nit">
                </div>

                <div class="tc-field">
                    <label>Correo Electrónico</label>
                    <input type="email" name="email" placeholder="ejemplo@correo.com">
                </div>
                <div class="tc-field">
                    <label>Celular / Teléfono</label>
                    <input type="text" name="celular">
                </div>
                <div class="tc-field" style="grid-column:1/-1;">
                    <label>Dirección</label>
                    <input type="text" name="direccion">
                </div>
                <div class="tc-field">
                    <label>Ciudad</label>
                    <input type="text" name="ciudad" placeholder="Ej: Bogotá D.C.">
                </div>
                <div class="tc-field">
                    <label>Código CIIU</label>
                    <input type="text" name="codigo_ciiu" placeholder="Ej: 5611">
                </div>
                <div class="tc-field" style="grid-column:1/-1;">
                    <label>Régimen Tributario</label>
                    <select name="regimen_tributario">
                        <option value="">Sin definir</option>
                        @foreach (\App\Models\Tercero::REGIMENES_TRIBUTARIOS as $valor => $etiqueta)
                            <option value="{{ $valor }}">{{ $etiqueta }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <label class="tc-check"><input id="tc-activo" type="checkbox" checked> Tercero activo</label>
        </form>

        <div class="modal-foot-tc">
            <button type="button" onclick="closeModalNuevoTercero()" class="btn-outline">Cancelar</button>
            <button type="button" onclick="guardarTercero()" class="btn-primary">💾 Guardar Tercero</button>
        </div>
    </div>
</div>

<script>
    function tokenTC() { return document.querySelector('meta[name="csrf-token"]')?.content; }

    function cargarTablaTerceros() {
        var buscar = document.getElementById('tc-buscar').value.trim();
        var tipo = document.getElementById('tc-tipo').value;
        var estado = document.getElementById('tc-estado').value;
        var contenedor = document.getElementById('tablaTerceros');

        contenedor.innerHTML = '<div class="table-scroll"><div class="spinner-cell"><div class="spinner"></div>Cargando terceros…</div></div>';

        var params = new URLSearchParams({ search: buscar, tipo: tipo, estado: estado });

        fetch('/views/terceros?' + params.toString(), { headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'text/html' } })
            .then(function (r) { return r.text(); })
            .then(function (html) { contenedor.innerHTML = html; })
            .catch(function () {
                contenedor.innerHTML = '<div class="spinner-cell">⚠️ No fue posible cargar los terceros.</div>';
            });
    }

    window.filtrarTerceros = debounce(cargarTablaTerceros, 300);

    function limpiarFiltrosTerceros() {
        document.getElementById('tc-buscar').value = '';
        document.getElementById('tc-tipo').value = '';
        document.getElementById('tc-estado').value = '';
        cargarTablaTerceros();
    }

    document.getElementById('tc-buscar').addEventListener('input', filtrarTerceros);
    document.getElementById('tc-tipo').addEventListener('change', cargarTablaTerceros);
    document.getElementById('tc-estado').addEventListener('change', cargarTablaTerceros);

    /* Redefinimos toggleTipoTercero para esta vista: además de mostrar/ocultar
       los campos de persona/empresa, resalta la pestaña activa (clases propias). */
    window.toggleTipoTercero = function (tipo) {
        // Se usa style.display (no la clase .hidden) porque .tc-field ya fija
        // display:flex con la misma especificidad y, según el orden en que el
        // navegador cargó los estilos, podía ganarle a .hidden y dejar el campo visible.
        document.querySelectorAll('.campo-persona').forEach(function (el) { el.style.display = tipo === 'persona' ? '' : 'none'; });
        document.querySelectorAll('.campo-empresa').forEach(function (el) { el.style.display = tipo === 'empresa' ? '' : 'none'; });
        document.querySelectorAll('.tc-tab').forEach(function (tab) {
            tab.classList.toggle('activo', tab.dataset.tabTipo === tipo);
        });
    };

    window.cambiarEstadoTercero = function (id) {
        abrirConfirm('¿Deseas cambiar el estado de este tercero?', function () {
            fetch('/terceros/' + id + '/estado', {
                method: 'PUT',
                headers: { 'X-CSRF-TOKEN': tokenTC(), 'Content-Type': 'application/json', 'Accept': 'application/json' },
            })
                .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
                .then(function (res) {
                    if (!res.ok) throw new Error(res.d.message || 'No se pudo cambiar el estado.');
                    mostrarNotificacion(res.d.message, 'success');
                    if (typeof loadView === 'function') loadView('terceros');
                    else cargarTablaTerceros();
                })
                .catch(function (e) { mostrarNotificacion(e.message, 'error'); });
        });
    };
</script>
