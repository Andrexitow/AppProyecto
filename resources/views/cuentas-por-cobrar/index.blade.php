<style>
    .sec-header { display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; }
    .sec-title { font-size: 17px; font-weight: 600; color: #111827; letter-spacing: -0.3px; }
    .sec-subtitle { font-size: 12px; color: #6B7280; margin-top: 2px; }

    .metrics-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px; margin-bottom: 16px; }
    .metric-card { background: #fff; border: 1px solid #EAECF0; border-radius: 10px; padding: 12px 14px; position: relative; overflow: hidden; }
    .metric-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--accent, #1D4ED8); }
    .metric-label { font-size: 11px; font-weight: 500; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.5px; }
    .metric-value { font-size: 18px; font-weight: 700; color: #111827; margin-top: 4px; letter-spacing: -0.5px; }
    .metric-sub { font-size: 11px; color: #6B7280; margin-top: 2px; }

    .filter-bar { background: #fff; border: 1px solid #EAECF0; border-radius: 10px; padding: 12px 14px; display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-bottom: 12px; }
    .fi-group { display: flex; align-items: center; gap: 6px; flex: 1; min-width: 160px; }
    .fi-label { font-size: 12px; color: #6B7280; white-space: nowrap; }
    .fi-input, .fi-select { flex: 1; border: 1px solid #D1D5DB; border-radius: 7px; padding: 6px 10px; font-size: 12px; color: #111827; background: #F9FAFB; outline: none; transition: border .15s; }
    .fi-input:focus, .fi-select:focus { border-color: #1D4ED8; background: #fff; }
    .fi-input::placeholder { color: #9CA3AF; }

    .btn-primary { display: inline-flex; align-items: center; gap: 6px; background: #1D4ED8; color: #fff; border: none; border-radius: 7px; padding: 7px 14px; font-size: 12px; font-weight: 600; cursor: pointer; transition: background .15s; white-space: nowrap; }
    .btn-primary:hover { background: #1e40af; }
    .btn-outline { display: inline-flex; align-items: center; gap: 5px; background: #fff; color: #374151; border: 1px solid #D1D5DB; border-radius: 7px; padding: 7px 12px; font-size: 12px; font-weight: 500; cursor: pointer; transition: all .15s; white-space: nowrap; }
    .btn-outline:hover { background: #F3F4F6; border-color: #9CA3AF; }
    .act-btn { background: #fff; border: 1px solid #D1D5DB; border-radius: 6px; padding: 5px 9px; font-size: 11px; font-weight: 600; color: #374151; cursor: pointer; }
    .act-btn:hover { background: #F3F4F6; }
    .act-btn.primary { background: #1D4ED8; color: #fff; border-color: #1D4ED8; }
    .act-btn.primary:hover { background: #1e40af; }

    .table-wrapper { background: #fff; border: 1px solid #EAECF0; border-radius: 10px; overflow: hidden; }
    .table-scroll { overflow-x: auto; }
    table.cxc-tbl { width: 100%; border-collapse: collapse; font-size: 12.5px; }
    table.cxc-tbl thead { background: #F8FAFC; border-bottom: 1px solid #EAECF0; }
    table.cxc-tbl thead th { padding: 10px 12px; text-align: left; font-size: 11px; font-weight: 600; color: #6B7280; text-transform: uppercase; letter-spacing: .5px; white-space: nowrap; }
    table.cxc-tbl tbody tr { border-bottom: 1px solid #F3F4F6; }
    table.cxc-tbl tbody tr:hover { background: #F8FAFC; }
    table.cxc-tbl td { padding: 8px 12px; color: #374151; vertical-align: middle; }
    .td-mono { font-family: 'JetBrains Mono','Fira Mono',monospace; font-size: 12px; color: #1D4ED8; font-weight: 600; }
    .td-money { font-weight: 600; color: #111827; text-align: right; white-space: nowrap; }

    .badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; white-space: nowrap; }
    .badge-green { background: #ECFDF5; color: #065F46; }
    .badge-yellow { background: #FFFBEB; color: #92400E; }
    .badge-red { background: #FEF2F2; color: #991B1B; }

    .spinner-cell { display: flex; align-items: center; justify-content: center; padding: 40px; color: #6B7280; font-size: 13px; gap: 10px; }
    @keyframes spin-cxc { to { transform: rotate(360deg); } }
    .spinner { width: 18px; height: 18px; border: 2px solid #E5E7EB; border-top-color: #1D4ED8; border-radius: 50%; animation: spin-cxc .7s linear infinite; }

    .pagination-bar { display: flex; align-items: center; justify-content: space-between; padding: 10px 14px; border-top: 1px solid #F3F4F6; font-size: 12px; color: #6B7280; }

    .modal-backdrop { position: fixed; inset: 0; background: rgba(17,24,39,.5); display: flex; align-items: center; justify-content: center; z-index: 200; padding: 16px; }
    .modal-box { background: #fff; border-radius: 12px; width: 100%; max-width: 460px; max-height: 90vh; overflow-y: auto; }
    .modal-head { display: flex; align-items: flex-start; justify-content: space-between; padding: 16px 18px; border-bottom: 1px solid #F3F4F6; }
    .modal-head-title { font-size: 14px; font-weight: 700; color: #111827; }
    .modal-head-sub { font-size: 11px; color: #6B7280; margin-top: 2px; }
    .modal-body { padding: 16px 18px; }
    .modal-foot { display: flex; justify-content: flex-end; gap: 8px; padding: 14px 18px; border-top: 1px solid #F3F4F6; }
    .co-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .co-field label { display: block; font-size: 11px; font-weight: 600; color: #6B7280; margin-bottom: 4px; }
    .co-field input, .co-field select { width: 100%; border: 1px solid #D1D5DB; border-radius: 7px; padding: 7px 10px; font-size: 12.5px; color: #111827; }

    @media (max-width: 640px) { .metrics-row { grid-template-columns: 1fr 1fr; } }
</style>

<div id="view-cuentas-por-cobrar">
    <div class="sec-header">
        <div>
            <p class="sec-title">💰 Cuentas por Cobrar</p>
            <p class="sec-subtitle">Ventas a crédito, abonos y saldos de clientes</p>
        </div>
    </div>

    <div class="metrics-row" id="cxc-metrics"></div>

    <div class="filter-bar">
        <div class="fi-group">
            <span class="fi-label">Buscar</span>
            <input type="text" id="cxc-buscar" class="fi-input" placeholder="Factura o cliente…">
        </div>
        <div class="fi-group" style="flex:0 0 200px;min-width:160px;">
            <span class="fi-label">Estado</span>
            <select id="cxc-estado" class="fi-select">
                <option value="">Todos</option>
                <option value="pendiente">Pendiente</option>
                <option value="parcialmente_pagada">Parcialmente pagada</option>
                <option value="pagada">Pagada</option>
            </select>
        </div>
        <button class="btn-primary" onclick="cargarCxc()">🔍 Filtrar</button>
    </div>

    <div class="table-wrapper">
        <div class="table-scroll">
            <table class="cxc-tbl">
                <thead>
                    <tr><th>Factura</th><th>Cliente</th><th>Fecha</th><th style="text-align:right;">Total</th><th style="text-align:right;">Abonado</th><th style="text-align:right;">Saldo</th><th>Estado</th><th>Acciones</th></tr>
                </thead>
                <tbody id="cxc-tbody"><tr><td colspan="8"><div class="spinner-cell"><div class="spinner"></div>Cargando…</div></td></tr></tbody>
            </table>
        </div>
        <div class="pagination-bar" id="cxc-paginacion" style="display:none;"></div>
    </div>
</div>

{{-- MODAL ABONO --}}
<div id="modal-abono-cxc" style="display:none;" class="modal-backdrop">
    <div class="modal-box">
        <div class="modal-head">
            <div><p class="modal-head-title">Registrar abono del cliente</p><p class="modal-head-sub" id="cxc-abono-ref"></p></div>
            <button onclick="cerrarAbonoCxc()" style="border:0;background:transparent;font-size:20px;cursor:pointer;">✕</button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="cxc-abono-factura-id">
            <div class="co-grid">
                <div class="co-field"><label>Fecha *</label><input id="cxc-abono-fecha" type="date"></div>
                <div class="co-field"><label>Saldo pendiente</label><input id="cxc-abono-saldo" readonly></div>
                <div class="co-field"><label>Medio de pago *</label><select id="cxc-abono-metodo"></select></div>
                <div class="co-field"><label>Valor *</label><input id="cxc-abono-valor" type="number" min="0.01" step="0.01"></div>
                <div class="co-field" style="grid-column:1/-1"><label>Referencia</label><input id="cxc-abono-referencia" placeholder="Transferencia, recibo, comprobante..."></div>
            </div>
        </div>
        <div class="modal-foot">
            <button class="btn-outline" onclick="cerrarAbonoCxc()">Cancelar</button>
            <button class="btn-primary" onclick="guardarAbonoCxc()">Registrar abono</button>
        </div>
    </div>
</div>

<script>
    var CXC = { pagina: 1, metodos: [], filas: [] };

    function escCxc(s) { return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
    function fmtMoneyCxc(n) { return '$ ' + (Number(n) || 0).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 }); }
    function fmtFechaCxc(s) { if (!s) return '—'; var p = String(s).slice(0, 10).split('-'); return p.length === 3 ? p[2] + '/' + p[1] + '/' + p[0] : s; }
    function hdrsCxc() { return { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content, 'Accept': 'application/json' }; }
    function nombreClienteCxc(c) { if (!c) return 'Sin cliente'; return c.razon_social || ((c.nombre || '') + ' ' + (c.apellido || '')).trim() || 'Sin nombre'; }

    (function initCxc() {
        fetch('/metodos-pago-contables/opciones', { headers: hdrsCxc() }).then(function (r) { return r.json(); }).then(function (res) {
            // "Crédito" no es un medio de pago válido para cobrar un abono
            // (es precisamente lo que se está saldando).
            CXC.metodos = (res.data || []).filter(function (m) { return m.metodo_pago !== 'credito'; });
        }).catch(function () {});
        cargarResumenCxc();
        cargarCxc();

        var buscarDebounced = debounce(function () { cargarCxc(); }, 350);
        document.getElementById('cxc-buscar').addEventListener('input', buscarDebounced);
    })();

    function cargarResumenCxc() {
        fetch('/cuentas-por-cobrar/resumen', { headers: hdrsCxc() }).then(function (r) { return r.json(); }).then(function (d) {
            document.getElementById('cxc-metrics').innerHTML = [
                { label: 'Cartera total', value: fmtMoneyCxc(d.total_cartera), accent: '#1D4ED8' },
                { label: 'Facturas pendientes', value: d.facturas_pendientes, accent: '#D97706' },
                { label: 'Clientes con deuda', value: d.clientes_con_deuda, accent: '#7C3AED' },
                { label: 'Vencida (+30 días)', value: fmtMoneyCxc(d.vencida_30_dias), accent: d.vencida_30_dias > 0 ? '#DC2626' : '#059669' },
            ].map(function (t) {
                return '<div class="metric-card" style="--accent:' + t.accent + '"><p class="metric-label">' + escCxc(t.label) + '</p><p class="metric-value">' + t.value + '</p></div>';
            }).join('');
        }).catch(function () {});
    }

    function badgeEstadoPagoCxc(estado) {
        if (estado === 'pagada') return '<span class="badge badge-green">✓ Pagada</span>';
        if (estado === 'parcialmente_pagada') return '<span class="badge badge-yellow">◐ Parcial</span>';
        return '<span class="badge badge-red">● Pendiente</span>';
    }

    function cargarCxc(pagina) {
        CXC.pagina = pagina || 1;
        var params = new URLSearchParams({ page: CXC.pagina });
        var buscar = document.getElementById('cxc-buscar').value.trim();
        var estado = document.getElementById('cxc-estado').value;
        if (buscar) params.set('buscar', buscar);
        if (estado) params.set('estado_pago', estado);

        document.getElementById('cxc-tbody').innerHTML = '<tr><td colspan="8"><div class="spinner-cell"><div class="spinner"></div>Cargando…</div></td></tr>';

        fetch('/cuentas-por-cobrar?' + params.toString(), { headers: hdrsCxc() }).then(function (r) { return r.json(); }).then(function (res) {
            CXC.filas = res.data || [];
            renderTablaCxc(res);
        }).catch(function () {
            document.getElementById('cxc-tbody').innerHTML = '<tr><td colspan="8"><div class="spinner-cell">⚠️ No fue posible cargar la cartera</div></td></tr>';
        });
    }

    function renderTablaCxc(res) {
        var tbody = document.getElementById('cxc-tbody');
        if (!CXC.filas.length) {
            tbody.innerHTML = '<tr><td colspan="8"><div class="spinner-cell">📭 No hay ventas a crédito con estos filtros</div></td></tr>';
        } else {
            tbody.innerHTML = CXC.filas.map(function (f) {
                var abonar = Number(f.saldo_pendiente) > 0 ? '<button class="act-btn primary" onclick="abrirAbonoCxc(' + f.id + ')">+ Abono</button>' : '';
                return '<tr>' +
                    '<td><span class="td-mono">' + escCxc(f.numero_factura) + '</span></td>' +
                    '<td>' + escCxc(nombreClienteCxc(f.cliente)) + '</td>' +
                    '<td>' + fmtFechaCxc(f.created_at) + '</td>' +
                    '<td class="td-money">' + fmtMoneyCxc(f.total) + '</td>' +
                    '<td class="td-money">' + fmtMoneyCxc(f.total_pagado) + '</td>' +
                    '<td class="td-money">' + fmtMoneyCxc(f.saldo_pendiente) + '</td>' +
                    '<td>' + badgeEstadoPagoCxc(f.estado_pago) + '</td>' +
                    '<td>' + abonar + '</td>' +
                    '</tr>';
            }).join('');
        }

        var pag = document.getElementById('cxc-paginacion');
        if (res.last_page > 1) {
            pag.style.display = 'flex';
            pag.innerHTML = '<span>Página ' + res.current_page + ' de ' + res.last_page + ' — ' + res.total + ' registros</span>' +
                '<span style="display:flex;gap:6px;">' +
                '<button class="btn-outline" ' + (res.current_page <= 1 ? 'disabled' : '') + ' onclick="cargarCxc(' + (res.current_page - 1) + ')">← Anterior</button>' +
                '<button class="btn-outline" ' + (res.current_page >= res.last_page ? 'disabled' : '') + ' onclick="cargarCxc(' + (res.current_page + 1) + ')">Siguiente →</button>' +
                '</span>';
        } else {
            pag.style.display = 'none';
        }
    }

    window.abrirAbonoCxc = function (id) {
        var f = CXC.filas.find(function (x) { return Number(x.id) === Number(id); });
        if (!f) return;
        document.getElementById('cxc-abono-factura-id').value = f.id;
        document.getElementById('cxc-abono-ref').textContent = f.numero_factura + ' · ' + nombreClienteCxc(f.cliente);
        document.getElementById('cxc-abono-fecha').value = new Date().toISOString().slice(0, 10);
        document.getElementById('cxc-abono-saldo').value = fmtMoneyCxc(f.saldo_pendiente);
        document.getElementById('cxc-abono-valor').value = Number(f.saldo_pendiente);
        document.getElementById('cxc-abono-referencia').value = '';
        document.getElementById('cxc-abono-metodo').innerHTML = CXC.metodos.map(function (m) { return '<option value="' + m.id + '">' + escCxc(m.metodo_pago) + '</option>'; }).join('');
        document.getElementById('modal-abono-cxc').style.display = 'flex';
    };

    window.cerrarAbonoCxc = function () { document.getElementById('modal-abono-cxc').style.display = 'none'; };

    window.guardarAbonoCxc = function () {
        var id = document.getElementById('cxc-abono-factura-id').value;
        var data = {
            fecha: document.getElementById('cxc-abono-fecha').value,
            valor: Number(document.getElementById('cxc-abono-valor').value),
            metodo_pago_contable_id: Number(document.getElementById('cxc-abono-metodo').value),
            referencia: document.getElementById('cxc-abono-referencia').value,
        };
        fetch('/cuentas-por-cobrar/' + id + '/abonos', { method: 'POST', headers: Object.assign({ 'Content-Type': 'application/json' }, hdrsCxc()), body: JSON.stringify(data) })
            .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
            .then(function (r) {
                if (!r.ok) throw new Error(r.data.message || Object.values(r.data.errors || {}).flat().join(' ') || 'No se pudo registrar el abono.');
                cerrarAbonoCxc();
                notifCxc('Abono registrado y contabilizado correctamente.', 'success');
                cargarResumenCxc();
                cargarCxc(CXC.pagina);
            })
            .catch(function (e) { notifCxc(e.message, 'error'); });
    };

    function notifCxc(msg, tipo) {
        if (typeof mostrarNotificacion === 'function') { mostrarNotificacion(msg, tipo === 'error' ? 'error' : 'success'); return; }
        window.alert(msg);
    }
</script>
