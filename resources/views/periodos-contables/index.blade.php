<style>
    .sec-header { display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; }
    .sec-title { font-size: 17px; font-weight: 600; color: #111827; letter-spacing: -0.3px; }
    .sec-subtitle { font-size: 12px; color: #6B7280; margin-top: 2px; }

    .metrics-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(150px, 1fr)); gap: 10px; margin-bottom: 16px; }
    .metric-card { background: #fff; border: 1px solid #EAECF0; border-radius: 10px; padding: 12px 14px; position: relative; overflow: hidden; }
    .metric-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--accent, #1D4ED8); }
    .metric-label { font-size: 11px; font-weight: 500; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.5px; }
    .metric-value { font-size: 18px; font-weight: 700; color: #111827; margin-top: 4px; letter-spacing: -0.5px; }

    .btn-primary { display: inline-flex; align-items: center; gap: 6px; background: #1D4ED8; color: #fff; border: none; border-radius: 7px; padding: 7px 14px; font-size: 12px; font-weight: 600; cursor: pointer; }
    .btn-primary:hover { background: #1e40af; }
    .btn-outline { display: inline-flex; align-items: center; gap: 5px; background: #fff; color: #374151; border: 1px solid #D1D5DB; border-radius: 7px; padding: 7px 12px; font-size: 12px; font-weight: 500; cursor: pointer; }
    .btn-outline:hover { background: #F3F4F6; }

    .table-wrapper { background: #fff; border: 1px solid #EAECF0; border-radius: 10px; overflow: hidden; }
    .table-scroll { overflow-x: auto; }
    table.pc-tbl { width: 100%; border-collapse: collapse; font-size: 12.5px; }
    table.pc-tbl thead { background: #F8FAFC; border-bottom: 1px solid #EAECF0; }
    table.pc-tbl thead th { padding: 10px 12px; text-align: left; font-size: 11px; font-weight: 600; color: #6B7280; text-transform: uppercase; letter-spacing: .5px; white-space: nowrap; }
    table.pc-tbl tbody tr { border-bottom: 1px solid #F3F4F6; }
    table.pc-tbl tbody tr:hover { background: #F8FAFC; }
    table.pc-tbl td { padding: 8px 12px; color: #374151; vertical-align: middle; }

    .badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; white-space: nowrap; }
    .badge-red { background: #FEF2F2; color: #991B1B; }
    .badge-green { background: #ECFDF5; color: #065F46; }

    .spinner-cell { display: flex; align-items: center; justify-content: center; padding: 40px; color: #6B7280; font-size: 13px; gap: 10px; }
    @keyframes spin-pc { to { transform: rotate(360deg); } }
    .spinner { width: 18px; height: 18px; border: 2px solid #E5E7EB; border-top-color: #1D4ED8; border-radius: 50%; animation: spin-pc .7s linear infinite; }

    .modal-backdrop { position: fixed; inset: 0; background: rgba(17,24,39,.5); display: flex; align-items: center; justify-content: center; z-index: 200; padding: 16px; }
    .modal-box { background: #fff; border-radius: 12px; width: 100%; max-width: 420px; }
    .modal-head { display: flex; align-items: flex-start; justify-content: space-between; padding: 16px 18px; border-bottom: 1px solid #F3F4F6; }
    .modal-head-title { font-size: 14px; font-weight: 700; color: #111827; }
    .modal-body { padding: 16px 18px; }
    .modal-foot { display: flex; justify-content: flex-end; gap: 8px; padding: 14px 18px; border-top: 1px solid #F3F4F6; }
    .co-field { margin-bottom: 12px; }
    .co-field label { display: block; font-size: 11px; font-weight: 600; color: #6B7280; margin-bottom: 4px; }
    .co-field input { width: 100%; border: 1px solid #D1D5DB; border-radius: 7px; padding: 7px 10px; font-size: 12.5px; color: #111827; box-sizing: border-box; }
    .aviso { background: #FFFBEB; border: 1px solid #FDE68A; color: #92400E; border-radius: 8px; padding: 10px 12px; font-size: 12px; margin-bottom: 14px; }
</style>

<div id="view-periodos-contables">
    <div class="sec-header">
        <div>
            <p class="sec-title">🔒 Períodos Contables</p>
            <p class="sec-subtitle">Cierre y bloqueo de períodos ya reportados, para que nadie modifique el pasado por accidente</p>
        </div>
        <button class="btn-primary" onclick="abrirCierrePc()">🔒 Cerrar un período</button>
    </div>

    <div class="metrics-row" id="pc-metrics"></div>

    <div class="table-wrapper">
        <div class="table-scroll">
            <table class="pc-tbl">
                <thead><tr><th>Período</th><th>Desde</th><th>Hasta</th><th>Estado</th><th>Cerrado por</th><th>Acciones</th></tr></thead>
                <tbody id="pc-tbody"><tr><td colspan="6"><div class="spinner-cell"><div class="spinner"></div>Cargando…</div></td></tr></tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL CERRAR PERÍODO --}}
<div id="modal-cierre-pc" style="display:none;" class="modal-backdrop">
    <div class="modal-box">
        <div class="modal-head"><p class="modal-head-title">Cerrar un período contable</p><button onclick="cerrarModalPc()" style="border:0;background:transparent;font-size:20px;cursor:pointer;">✕</button></div>
        <div class="modal-body">
            <div class="aviso">⚠️ Una vez cerrado, nadie podrá crear, editar ni anular comprobantes con fecha dentro de este rango — en ningún módulo (ventas, compras, ajustes, tesorería, etc.) — hasta que un Administrador lo reabra.</div>
            <div class="co-field"><label>Nombre *</label><input id="pcc-nombre" placeholder="Ej: Septiembre 2026"></div>
            <div class="co-field"><label>Desde *</label><input id="pcc-desde" type="date"></div>
            <div class="co-field"><label>Hasta *</label><input id="pcc-hasta" type="date"></div>
        </div>
        <div class="modal-foot"><button class="btn-outline" onclick="cerrarModalPc()">Cancelar</button><button class="btn-primary" onclick="guardarCierrePc()">Cerrar período</button></div>
    </div>
</div>

<script>
    var PC = { periodos: [] };

    function escPc(s) { return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
    function fmtFechaPc(s) { if (!s) return '—'; var p = String(s).slice(0, 10).split('-'); return p.length === 3 ? p[2] + '/' + p[1] + '/' + p[0] : s; }
    function hdrsPc() { return { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content, 'Accept': 'application/json' }; }
    function notifPc(msg, tipo) { if (typeof mostrarNotificacion === 'function') { mostrarNotificacion(msg, tipo === 'error' ? 'error' : 'success'); return; } window.alert(msg); }

    (function initPc() {
        var hoy = new Date();
        document.getElementById('pcc-desde').value = new Date(hoy.getFullYear(), hoy.getMonth(), 1).toISOString().slice(0, 10);
        var ultimoDia = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);
        document.getElementById('pcc-hasta').value = ultimoDia.toISOString().slice(0, 10);
        document.getElementById('pcc-nombre').value = hoy.toLocaleDateString('es-CO', { month: 'long', year: 'numeric' });
        cargarPeriodosPc();
    })();

    function cargarPeriodosPc() {
        fetch('/periodos-contables', { headers: hdrsPc() }).then(function (r) { return r.json(); }).then(function (res) {
            PC.periodos = res.data || [];
            renderPeriodosPc();
        }).catch(function () {
            document.getElementById('pc-tbody').innerHTML = '<tr><td colspan="6"><div class="spinner-cell">⚠️ No fue posible cargar los períodos</div></td></tr>';
        });
    }

    function renderPeriodosPc() {
        var cerrados = PC.periodos.filter(function (p) { return p.estado === 'CERRADO'; }).length;
        document.getElementById('pc-metrics').innerHTML = [
            { label: 'Períodos registrados', value: PC.periodos.length, accent: '#1D4ED8' },
            { label: 'Cerrados actualmente', value: cerrados, accent: '#DC2626' },
        ].map(function (t) {
            return '<div class="metric-card" style="--accent:' + t.accent + '"><p class="metric-label">' + escPc(t.label) + '</p><p class="metric-value">' + t.value + '</p></div>';
        }).join('');

        var tbody = document.getElementById('pc-tbody');
        if (!PC.periodos.length) {
            tbody.innerHTML = '<tr><td colspan="6"><div class="spinner-cell">📭 Todavía no se ha cerrado ningún período</div></td></tr>';
            return;
        }

        tbody.innerHTML = PC.periodos.map(function (p) {
            var estado = p.estado === 'CERRADO' ? '<span class="badge badge-red">🔒 Cerrado</span>' : '<span class="badge badge-green">🔓 Abierto</span>';
            // Ojo: la relación cerradoPor() se sirve en JSON bajo la misma
            // llave "cerrado_por" que la columna (fk), reemplazándola por el
            // objeto {id,name} una vez viene con with(); por eso se lee así.
            var cerradoPor = (p.cerrado_por && p.cerrado_por.name) || '—';
            var accion = p.estado === 'CERRADO' ? '<button class="btn-outline" onclick="reabrirPc(' + p.id + ')">🔓 Reabrir</button>' : '<span style="color:#9CA3AF;font-size:11px;">—</span>';
            return '<tr>' +
                '<td>' + escPc(p.nombre) + '</td>' +
                '<td>' + fmtFechaPc(p.fecha_inicio) + '</td>' +
                '<td>' + fmtFechaPc(p.fecha_fin) + '</td>' +
                '<td>' + estado + '</td>' +
                '<td>' + escPc(cerradoPor) + '</td>' +
                '<td>' + accion + '</td>' +
                '</tr>';
        }).join('');
    }

    window.abrirCierrePc = function () { document.getElementById('modal-cierre-pc').style.display = 'flex'; };
    window.cerrarModalPc = function () { document.getElementById('modal-cierre-pc').style.display = 'none'; };

    window.guardarCierrePc = function () {
        var datos = {
            nombre: document.getElementById('pcc-nombre').value.trim(),
            fecha_inicio: document.getElementById('pcc-desde').value,
            fecha_fin: document.getElementById('pcc-hasta').value,
        };
        if (!datos.nombre || !datos.fecha_inicio || !datos.fecha_fin) { notifPc('Completa nombre, desde y hasta.', 'error'); return; }
        if (!window.confirm('¿Cerrar "' + datos.nombre + '"? Nadie podrá registrar ni modificar comprobantes en ese rango hasta que un Administrador lo reabra.')) return;

        fetch('/periodos-contables', { method: 'POST', headers: Object.assign({ 'Content-Type': 'application/json' }, hdrsPc()), body: JSON.stringify(datos) })
            .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
            .then(function (r) {
                if (!r.ok) throw new Error(r.data.message || Object.values(r.data.errors || {}).flat().join(' ') || 'No se pudo cerrar el período.');
                cerrarModalPc();
                notifPc('Período cerrado correctamente.', 'success');
                cargarPeriodosPc();
            }).catch(function (e) { notifPc(e.message, 'error'); });
    };

    window.reabrirPc = function (id) {
        var p = PC.periodos.find(function (x) { return Number(x.id) === Number(id); });
        if (!p) return;
        if (!window.confirm('¿Reabrir "' + p.nombre + '"? Volverá a permitirse crear y modificar comprobantes en ese rango.')) return;

        fetch('/periodos-contables/' + id + '/reabrir', { method: 'POST', headers: hdrsPc() })
            .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
            .then(function (r) {
                if (!r.ok) throw new Error(r.data.message || 'No se pudo reabrir el período.');
                notifPc('Período reabierto.', 'success');
                cargarPeriodosPc();
            }).catch(function (e) { notifPc(e.message, 'error'); });
    };
</script>
