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

    table.mp-tbl { width: 100%; border-collapse: collapse; font-size: 12.5px; }
    table.mp-tbl thead { background: #F8FAFC; border-bottom: 1px solid #EAECF0; }

    table.mp-tbl thead th {
        padding: 10px 12px;
        text-align: left;
        font-size: 11px;
        font-weight: 600;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    table.mp-tbl tbody tr { border-bottom: 1px solid #F3F4F6; transition: background 0.1s; }
    table.mp-tbl tbody tr:last-child { border-bottom: none; }
    table.mp-tbl tbody tr:hover { background: #F8FAFC; }
    table.mp-tbl tbody tr.inactivo { opacity: 0.6; }
    table.mp-tbl td { padding: 9px 12px; color: #374151; vertical-align: middle; }

    .td-mono { font-family: 'JetBrains Mono', 'Fira Mono', monospace; font-size: 12px; color: #1D4ED8; font-weight: 600; }

    .mp-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #EFF6FF;
        color: #1D4ED8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 15px;
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
    .act-btn.edit:hover { background: #ECFDF5; color: #059669; }
    .act-btn.state:hover { background: #FFFBEB; color: #D97706; }

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

    @keyframes spin-mp { to { transform: rotate(360deg); } }

    .spinner {
        width: 18px;
        height: 18px;
        border: 2px solid #E5E7EB;
        border-top-color: #1D4ED8;
        border-radius: 50%;
        animation: spin-mp 0.7s linear infinite;
    }

    /* ── Modal ── */
    .modal-backdrop-mp { background: rgba(17, 24, 39, 0.5); backdrop-filter: blur(4px); }

    .modal-mp {
        background: #fff;
        border-radius: 16px;
        width: 100%;
        max-width: 460px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }

    .modal-head-mp {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid #EAECF0;
    }

    .modal-head-title { font-size: 15px; font-weight: 600; color: #111827; }
    .modal-head-sub { font-size: 12px; color: #6B7280; margin-top: 2px; }
    .modal-body-mp { padding: 20px; display: flex; flex-direction: column; gap: 14px; }

    .modal-foot-mp {
        padding: 14px 20px;
        border-top: 1px solid #EAECF0;
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }

    .mp-field { display: flex; flex-direction: column; gap: 3px; }

    .mp-field label {
        font-size: 11px;
        font-weight: 500;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .mp-field input, .mp-field select {
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

    .mp-field input:focus, .mp-field select:focus { border-color: #1D4ED8; background: #fff; }

    .mp-check {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 13px;
        color: #374151;
    }

    .mp-check input { width: 16px; height: 16px; accent-color: #1D4ED8; cursor: pointer; }

    /* ── Buscador de cuenta contable ── */
    .mp-cuenta-wrap { position: relative; }

    .mp-cuenta-resultados {
        position: absolute;
        z-index: 20;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        display: none;
        max-height: 220px;
        overflow-y: auto;
        border: 1px solid #D1D5DB;
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 10px 25px rgba(16, 24, 40, .14);
    }

    .mp-cuenta-resultados.open { display: block; }

    .mp-cuenta-opcion {
        width: 100%;
        display: flex;
        align-items: baseline;
        gap: 8px;
        padding: 8px 12px;
        border: 0;
        border-bottom: 1px solid #F3F4F6;
        background: #fff;
        color: #374151;
        text-align: left;
        cursor: pointer;
        font-size: 12.5px;
    }

    .mp-cuenta-opcion:last-child { border-bottom: none; }
    .mp-cuenta-opcion:hover, .mp-cuenta-opcion.activa { background: #EFF6FF; }
    .mp-cuenta-opcion .td-mono { flex-shrink: 0; }

    .mp-cuenta-vacio { padding: 14px 12px; text-align: center; color: #9CA3AF; font-size: 12px; }

    .mp-cuenta-limite { padding: 6px 12px; text-align: center; color: #9CA3AF; font-size: 11px; border-top: 1px solid #F3F4F6; }

    @media (max-width: 640px) {
        .metrics-row { grid-template-columns: 1fr 1fr; }
    }
</style>

<div id="view-medios-pago">

    {{-- ── ENCABEZADO ── --}}
    <div class="sec-header">
        <div>
            <p class="sec-title">💳 Medios de Pago Contables</p>
            <p class="sec-subtitle">Cada medio acredita la cuenta contable que usted configure aquí</p>
        </div>
        <button class="btn-primary" onclick="abrirMetodoPago()">＋ Nuevo Medio</button>
    </div>

    {{-- ── MÉTRICAS ── --}}
    <div class="metrics-row">
        <div class="metric-card" style="--accent:#1D4ED8">
            <p class="metric-label">Total Medios</p>
            <p class="metric-value" id="mp-m-total">—</p>
            <p class="metric-sub">Configurados</p>
        </div>
        <div class="metric-card" style="--accent:#059669">
            <p class="metric-label">Activos</p>
            <p class="metric-value" id="mp-m-activos">—</p>
            <p class="metric-sub" id="mp-m-activos-sub"></p>
        </div>
        <div class="metric-card" style="--accent:#DC2626">
            <p class="metric-label">Inactivos</p>
            <p class="metric-value" id="mp-m-inactivos">—</p>
            <p class="metric-sub">No disponibles en caja</p>
        </div>
        <div class="metric-card" style="--accent:#D97706">
            <p class="metric-label">Sin Cuenta</p>
            <p class="metric-value" id="mp-m-sincuenta">—</p>
            <p class="metric-sub">Requieren configuración</p>
        </div>
    </div>

    {{-- ── FILTROS ── --}}
    <div class="filter-bar">
        <div class="fi-group" style="min-width:220px;">
            <span class="fi-label">🔍</span>
            <input type="text" id="mp-buscar" class="fi-input" placeholder="Buscar medio de pago…" oninput="renderMetodosPago()">
        </div>
        <div class="fi-group" style="flex:none;">
            <select class="fi-select" id="mp-estado" onchange="renderMetodosPago()">
                <option value="">Todos los estados</option>
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
            </select>
        </div>
        <button class="btn-outline" onclick="document.getElementById('mp-buscar').value='';document.getElementById('mp-estado').value='';renderMetodosPago();">✕ Limpiar</button>
    </div>

    {{-- ── TABLA ── --}}
    <div class="table-wrapper">
        <div class="table-scroll">
            <table class="mp-tbl">
                <thead>
                    <tr>
                        <th>Medio de Pago</th>
                        <th>Cuenta Contable Asociada</th>
                        <th style="text-align:center;">Estado</th>
                        <th style="text-align:right;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="mp-lista">
                    <tr><td colspan="4"><div class="spinner-cell"><div class="spinner"></div>Cargando medios de pago…</div></td></tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════
     MODAL MEDIO DE PAGO (crear / editar)
═══════════════════════════════════════════════ --}}
<div id="mp-modal" class="fixed inset-0 hidden items-center justify-center z-50 p-4 modal-backdrop-mp">
    <div class="modal-mp">
        <div class="modal-head-mp">
            <div>
                <p class="modal-head-title" id="mp-titulo">Nuevo Medio</p>
                <p class="modal-head-sub">Defina la cuenta que se acredita al usar este medio</p>
            </div>
            <button onclick="cerrarMetodoPago()"
                style="border:none;background:transparent;font-size:20px;cursor:pointer;color:#6B7280;padding:4px;border-radius:6px;line-height:1;">✕</button>
        </div>

        <div class="modal-body-mp">
            <input type="hidden" id="mp-id">
            <div class="mp-field">
                <label>Nombre del Medio</label>
                <input id="mp-nombre" placeholder="Ej: Efectivo, Tarjeta, Transferencia…">
            </div>
            <div class="mp-field mp-cuenta-wrap">
                <label>Cuenta Contable</label>
                <input type="text" id="mp-cuenta-buscar" autocomplete="off" placeholder="Buscar por código o nombre…">
                <input type="hidden" id="mp-cuenta">
                <div class="mp-cuenta-resultados" id="mp-cuenta-resultados"></div>
            </div>
            <label class="mp-check"><input id="mp-activo" type="checkbox" checked> Medio activo</label>
        </div>

        <div class="modal-foot-mp">
            <button class="btn-outline" onclick="cerrarMetodoPago()">Cancelar</button>
            <button class="btn-primary" id="mp-btn-guardar" onclick="guardarMetodoPago()">💾 Guardar</button>
        </div>
    </div>
</div>

<script>
    var MP = { datos: [], cuentas: [], filtradas: [] };

    function hdrsMP() {
        return { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content, 'Accept': 'application/json' };
    }

    function escMP(v) { return String(v ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }

    function cargarMetodosPago() {
        Promise.all([
            fetch('/metodos-pago-contables', { headers: hdrsMP() }).then(function (r) { return r.json(); }),
            fetch('/metodos-pago-contables/cuentas', { headers: hdrsMP() }).then(function (r) { return r.json(); }),
        ]).then(function (x) {
            MP.datos = x[0].data || [];
            MP.cuentas = x[1].data || [];
            renderMetodosPago();
        }).catch(function () {
            document.getElementById('mp-lista').innerHTML = '<tr><td colspan="4"><div class="spinner-cell">⚠️ No fue posible cargar la parametrización.</div></td></tr>';
            notifMP('No fue posible cargar la parametrización.', 'error');
        });
    }

    function renderMetodosPago() {
        var buscar = (document.getElementById('mp-buscar').value || '').toLowerCase().trim();
        var estadoFiltro = document.getElementById('mp-estado').value;

        MP.filtradas = MP.datos.filter(function (m) {
            if (buscar && !(m.metodo_pago || '').toLowerCase().includes(buscar)) return false;
            if (estadoFiltro !== '' && Number(!!m.estado) !== Number(estadoFiltro)) return false;
            return true;
        });

        var tbody = document.getElementById('mp-lista');

        if (MP.filtradas.length === 0) {
            tbody.innerHTML = '<tr><td colspan="4"><div class="spinner-cell">📭 No se encontraron medios de pago</div></td></tr>';
        } else {
            tbody.innerHTML = MP.filtradas.map(function (m) {
                var c = m.configuracion && m.configuracion.cuenta;
                var cuentaHtml = c
                    ? '<span class="td-mono">' + escMP(c.codigo) + '</span> ' + escMP(c.nombre)
                    : '<span class="badge badge-red">⚠️ Sin cuenta</span>';
                var badgeEstado = m.estado
                    ? '<span class="badge badge-green"><span class="dot"></span>Activo</span>'
                    : '<span class="badge badge-gray"><span class="dot"></span>Inactivo</span>';

                return '<tr class="' + (m.estado ? '' : 'inactivo') + '">' +
                    '<td><div style="display:flex;align-items:center;gap:10px;">' +
                    '<span class="mp-icon">' + iconoMedio(m.metodo_pago) + '</span>' +
                    '<span style="font-weight:500;color:#111827;text-transform:capitalize;">' + escMP(m.metodo_pago) + '</span>' +
                    '</div></td>' +
                    '<td>' + cuentaHtml + '</td>' +
                    '<td style="text-align:center;">' + badgeEstado + '</td>' +
                    '<td><div class="tbl-actions">' +
                    '<button title="Editar" class="act-btn edit" onclick="abrirMetodoPago(' + m.id + ')">✏️</button>' +
                    '<button title="' + (m.estado ? 'Desactivar' : 'Activar') + '" class="act-btn state" onclick="cambiarEstadoMetodoPago(' + m.id + ')">' + (m.estado ? '🚫' : '✅') + '</button>' +
                    '</div></td>' +
                    '</tr>';
            }).join('');
        }

        actualizarMetricasMP();
    }

    function iconoMedio(nombre) {
        var n = (nombre || '').toLowerCase();
        if (n.includes('efectivo')) return '💵';
        if (n.includes('tarjeta') || n.includes('credito') || n.includes('crédito') || n.includes('debito') || n.includes('débito')) return '💳';
        if (n.includes('transfer') || n.includes('consignaci')) return '🏦';
        if (n.includes('nequi') || n.includes('daviplata') || n.includes('digital') || n.includes('billetera')) return '📱';
        return '💰';
    }

    function actualizarMetricasMP() {
        var total = MP.datos.length;
        var activos = MP.datos.filter(function (m) { return m.estado; }).length;
        var sinCuenta = MP.datos.filter(function (m) { return !(m.configuracion && m.configuracion.cuenta); }).length;

        document.getElementById('mp-m-total').textContent = total;
        document.getElementById('mp-m-activos').textContent = activos;
        document.getElementById('mp-m-activos-sub').textContent = total ? Math.round(activos / total * 100) + '% del total' : '';
        document.getElementById('mp-m-inactivos').textContent = total - activos;
        document.getElementById('mp-m-sincuenta').textContent = sinCuenta;
    }

    function abrirMetodoPago(id) {
        var m = MP.datos.find(function (x) { return x.id === id; });

        document.getElementById('mp-id').value = m ? m.id : '';
        document.getElementById('mp-nombre').value = m ? m.metodo_pago : '';
        document.getElementById('mp-activo').checked = m ? !!m.estado : true;

        var cuentaActual = m && m.configuracion && m.configuracion.cuenta ? m.configuracion.cuenta : null;
        document.getElementById('mp-cuenta').value = cuentaActual ? cuentaActual.id : '';
        document.getElementById('mp-cuenta-buscar').value = cuentaActual ? (cuentaActual.codigo + ' - ' + cuentaActual.nombre) : '';
        cerrarResultadosCuenta();

        document.getElementById('mp-titulo').textContent = m ? 'Editar Medio' : 'Nuevo Medio';

        var modal = document.getElementById('mp-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function cerrarMetodoPago() {
        var modal = document.getElementById('mp-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    function guardarMetodoPago() {
        var id = document.getElementById('mp-id').value;
        var nombre = document.getElementById('mp-nombre').value.trim();
        var cuenta = document.getElementById('mp-cuenta').value;

        if (!nombre) { notifMP('⚠️ El nombre del medio es obligatorio.', 'error'); return; }
        if (!cuenta) { notifMP('⚠️ Seleccione la cuenta contable asociada.', 'error'); return; }

        var payload = {
            metodo_pago: nombre,
            cuenta_contable_id: cuenta,
            estado: document.getElementById('mp-activo').checked ? 1 : 0,
        };

        var btn = document.getElementById('mp-btn-guardar');
        btn.disabled = true;
        btn.textContent = '⏳ Guardando…';

        fetch(id ? '/metodos-pago-contables/' + id : '/metodos-pago-contables', {
            method: id ? 'PUT' : 'POST',
            headers: Object.assign({ 'Content-Type': 'application/json' }, hdrsMP()),
            body: JSON.stringify(payload),
        })
            .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
            .then(function (res) {
                if (!res.ok) throw new Error(res.d.message || Object.values(res.d.errors || {}).flat().join(' ') || 'No se pudo guardar.');
                notifMP(id ? '✅ Medio actualizado correctamente.' : '✅ Medio creado correctamente.', 'success');
                cerrarMetodoPago();
                cargarMetodosPago();
            })
            .catch(function (e) { notifMP(e.message, 'error'); })
            .finally(function () {
                btn.disabled = false;
                btn.textContent = '💾 Guardar';
            });
    }

    function cambiarEstadoMetodoPago(id) {
        var m = MP.datos.find(function (x) { return x.id === id; });
        if (!m || !m.configuracion || !m.configuracion.cuenta) {
            notifMP('Este medio no tiene cuenta asociada; edítelo para configurarla.', 'warning');
            return;
        }

        var payload = {
            metodo_pago: m.metodo_pago,
            cuenta_contable_id: m.configuracion.cuenta_contable_id,
            estado: m.estado ? 0 : 1,
        };

        fetch('/metodos-pago-contables/' + id, {
            method: 'PUT',
            headers: Object.assign({ 'Content-Type': 'application/json' }, hdrsMP()),
            body: JSON.stringify(payload),
        })
            .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
            .then(function (res) {
                if (!res.ok) throw new Error(res.d.message || 'No se pudo cambiar el estado.');
                notifMP(payload.estado ? '✅ Medio activado.' : '🚫 Medio desactivado.', 'success');
                cargarMetodosPago();
            })
            .catch(function (e) { notifMP(e.message, 'error'); });
    }

    function notifMP(msg, tipo) {
        if (typeof mostrarNotificacion === 'function') { mostrarNotificacion(msg, tipo === 'error' ? 'error' : tipo === 'warning' ? 'warning' : 'success'); return; }
        window.alert(msg);
    }

    /* ════════════════════════════════════════════════
       BUSCADOR DE CUENTA CONTABLE (autocompletar)
    ════════════════════════════════════════════════ */
    var LIMITE_RESULTADOS_CUENTA = 30;

    function renderResultadosCuenta() {
        var texto = document.getElementById('mp-cuenta-buscar').value.toLowerCase().trim();
        var contenedor = document.getElementById('mp-cuenta-resultados');

        var coincidencias = MP.cuentas.filter(function (c) {
            if (!texto) return true;
            return (c.codigo + ' ' + c.nombre).toLowerCase().includes(texto);
        });

        if (coincidencias.length === 0) {
            contenedor.innerHTML = '<div class="mp-cuenta-vacio">Sin coincidencias para "' + escMP(document.getElementById('mp-cuenta-buscar').value) + '"</div>';
        } else {
            var idSeleccionada = document.getElementById('mp-cuenta').value;
            contenedor.innerHTML = coincidencias.slice(0, LIMITE_RESULTADOS_CUENTA).map(function (c) {
                var activa = String(c.id) === String(idSeleccionada) ? ' activa' : '';
                return '<button type="button" class="mp-cuenta-opcion' + activa + '" onclick="seleccionarCuenta(' + c.id + ')">' +
                    '<span class="td-mono">' + escMP(c.codigo) + '</span><span>' + escMP(c.nombre) + '</span></button>';
            }).join('');
            if (coincidencias.length > LIMITE_RESULTADOS_CUENTA) {
                contenedor.innerHTML += '<div class="mp-cuenta-limite">Mostrando ' + LIMITE_RESULTADOS_CUENTA + ' de ' + coincidencias.length + ' · siga escribiendo para refinar</div>';
            }
        }

        contenedor.classList.add('open');
    }

    function seleccionarCuenta(id) {
        var c = MP.cuentas.find(function (x) { return Number(x.id) === Number(id); });
        if (!c) return;
        document.getElementById('mp-cuenta').value = c.id;
        document.getElementById('mp-cuenta-buscar').value = c.codigo + ' - ' + c.nombre;
        cerrarResultadosCuenta();
    }

    function cerrarResultadosCuenta() {
        document.getElementById('mp-cuenta-resultados').classList.remove('open');
    }

    document.getElementById('mp-cuenta-buscar').addEventListener('input', function () {
        document.getElementById('mp-cuenta').value = '';
        renderResultadosCuenta();
    });

    document.getElementById('mp-cuenta-buscar').addEventListener('focus', renderResultadosCuenta);

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.mp-cuenta-wrap')) cerrarResultadosCuenta();
    });

    document.getElementById('mp-cuenta-buscar').addEventListener('keydown', function (e) {
        if (e.key === 'Escape') cerrarResultadosCuenta();
    });

    cargarMetodosPago();
</script>
