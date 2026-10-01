<style>
    .sec-header { display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; }
    .sec-title { font-size: 17px; font-weight: 600; color: #111827; letter-spacing: -0.3px; }
    .sec-subtitle { font-size: 12px; color: #6B7280; margin-top: 2px; }

    .if-tabs { display: flex; gap: 4px; background: #F3F4F6; border-radius: 10px; padding: 4px; margin-bottom: 14px; }
    .if-tab { flex: 1; text-align: center; padding: 8px 12px; border-radius: 8px; font-size: 12.5px; font-weight: 600; color: #6B7280; cursor: pointer; border: none; background: transparent; }
    .if-tab.activo { background: #fff; color: #1D4ED8; box-shadow: 0 1px 2px rgba(16,24,40,.08); }

    .metrics-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 10px; margin-bottom: 16px; }
    .metric-card { background: #fff; border: 1px solid #EAECF0; border-radius: 10px; padding: 12px 14px; position: relative; overflow: hidden; }
    .metric-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--accent, #1D4ED8); }
    .metric-label { font-size: 11px; font-weight: 500; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.5px; }
    .metric-value { font-size: 17px; font-weight: 700; color: #111827; margin-top: 4px; }

    .btn-primary { display: inline-flex; align-items: center; gap: 6px; background: #1D4ED8; color: #fff; border: none; border-radius: 7px; padding: 7px 14px; font-size: 12px; font-weight: 600; cursor: pointer; }
    .btn-primary:hover { background: #1e40af; }
    .btn-primary:disabled { opacity: .5; cursor: not-allowed; }
    .btn-outline { display: inline-flex; align-items: center; gap: 5px; background: #fff; color: #374151; border: 1px solid #D1D5DB; border-radius: 7px; padding: 7px 12px; font-size: 12px; font-weight: 500; cursor: pointer; }
    .btn-outline:hover { background: #F3F4F6; }

    .table-wrapper { background: #fff; border: 1px solid #EAECF0; border-radius: 10px; overflow: hidden; }
    .table-scroll { overflow-x: auto; }
    table.nm-tbl { width: 100%; border-collapse: collapse; font-size: 12.5px; }
    table.nm-tbl thead { background: #F8FAFC; }
    table.nm-tbl thead th { padding: 10px 12px; text-align: left; font-size: 11px; font-weight: 600; color: #6B7280; text-transform: uppercase; white-space: nowrap; }
    table.nm-tbl tbody tr { border-top: 1px solid #F3F4F6; }
    table.nm-tbl tbody tr:hover { background: #F8FAFC; }
    table.nm-tbl td { padding: 9px 12px; color: #374151; vertical-align: middle; }
    .td-mono { font-family: 'JetBrains Mono','Fira Mono',monospace; font-size: 12px; color: #1D4ED8; font-weight: 600; }
    .td-money { font-weight: 600; color: #111827; text-align: right; white-space: nowrap; }
    .badge { display: inline-flex; padding: 3px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; }
    .badge-green { background: #ECFDF5; color: #065F46; }
    .badge-red { background: #FEF2F2; color: #991B1B; }
    .act-btn { display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 6px; border: none; cursor: pointer; font-size: 12px; background: transparent; color: #6B7280; }
    .act-btn:hover { background: #F3F4F6; color: #111827; }
    .spinner-cell { display: flex; align-items: center; justify-content: center; padding: 30px; color: #6B7280; font-size: 13px; }

    .modal-backdrop-nm { background: rgba(17,24,39,.5); backdrop-filter: blur(4px); }
    .modal-nm { background: #fff; border-radius: 16px; width: 100%; max-width: 560px; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; }
    .modal-head-nm { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #EAECF0; }
    .modal-head-title { font-size: 15px; font-weight: 600; color: #111827; }
    .modal-body-nm { flex: 1; overflow-y: auto; padding: 20px; }
    .modal-foot-nm { padding: 14px 20px; border-top: 1px solid #EAECF0; display: flex; gap: 8px; justify-content: flex-end; }
    .nm-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .nm-field { display: flex; flex-direction: column; gap: 3px; }
    .nm-field label { font-size: 11px; font-weight: 500; color: #9CA3AF; text-transform: uppercase; }
    .nm-field input { border: 1px solid #D1D5DB; border-radius: 7px; padding: 8px 10px; font-size: 13px; background: #F9FAFB; outline: none; width: 100%; box-sizing: border-box; }
    .nm-field input:focus { border-color: #1D4ED8; background: #fff; }
</style>

<div id="view-nomina">
    <div class="sec-header">
        <div>
            <p class="sec-title">🧑‍💼 Nómina</p>
            <p class="sec-subtitle">Empleados, liquidación mensual y desprendibles de pago</p>
        </div>
        <button class="btn-outline" onclick="abrirModalParametros()">⚙️ Parámetros legales</button>
    </div>

    <div class="if-tabs">
        <button class="if-tab activo" data-tab="empleados" onclick="cambiarTabNomina('empleados')">Empleados</button>
        <button class="if-tab" data-tab="liquidaciones" onclick="cambiarTabNomina('liquidaciones')">Liquidaciones</button>
    </div>

    <div id="nm-panel-empleados">
        <div class="metrics-row" id="nm-emp-metrics"></div>
        <div style="display:flex;justify-content:flex-end;margin-bottom:10px;">
            <button class="btn-primary" onclick="abrirModalEmpleado()">＋ Nuevo Empleado</button>
        </div>
        <div class="table-wrapper"><div class="table-scroll"><div id="nm-tabla-empleados"><div class="spinner-cell">Cargando…</div></div></div></div>
    </div>

    <div id="nm-panel-liquidaciones" class="hidden">
        <div style="display:flex;justify-content:flex-end;margin-bottom:10px;">
            <button class="btn-primary" onclick="abrirModalLiquidar()">💵 Liquidar Nómina</button>
        </div>
        <div class="table-wrapper"><div class="table-scroll"><div id="nm-tabla-liquidaciones"><div class="spinner-cell">Cargando…</div></div></div></div>
    </div>
</div>

{{-- MODAL EMPLEADO --}}
<div id="modalEmpleado" class="fixed inset-0 hidden items-center justify-center z-50 p-4 modal-backdrop-nm">
    <div class="modal-nm">
        <div class="modal-head-nm"><p class="modal-head-title" id="nm-emp-titulo">Nuevo Empleado</p>
            <button onclick="cerrarModalEmpleado()" style="border:none;background:transparent;font-size:20px;cursor:pointer;color:#6B7280;">✕</button></div>
        <form id="formEmpleado" class="modal-body-nm" onsubmit="return false;">
            <div class="nm-grid">
                <div class="nm-field"><label>Nombre</label><input autocomplete="off" type="text" name="nombre"></div>
                <div class="nm-field"><label>Apellido</label><input autocomplete="off" type="text" name="apellido"></div>
                <div class="nm-field"><label>Cédula</label><input autocomplete="off" type="text" name="cedula"></div>
                <div class="nm-field"><label>Cargo</label><input autocomplete="off" type="text" name="cargo"></div>
                <div class="nm-field"><label>Fecha de ingreso</label><input autocomplete="off" type="date" name="fecha_ingreso"></div>
                <div class="nm-field"><label>Salario base</label><input autocomplete="off" type="number" name="salario_base" min="0" step="0.01"></div>
                <div class="nm-field"><label>Tarifa ARL (%)</label><input autocomplete="off" type="number" name="arl_tarifa" min="0" step="0.001" value="0.522"></div>
                <div class="nm-field"><label>Cuenta bancaria</label><input autocomplete="off" type="text" name="cuenta_bancaria"></div>
                <div class="nm-field"><label>Email</label><input autocomplete="off" type="email" name="email"></div>
                <div class="nm-field"><label>Celular</label><input autocomplete="off" type="text" name="celular"></div>
            </div>
        </form>
        <div class="modal-foot-nm">
            <button class="btn-outline" onclick="cerrarModalEmpleado()">Cancelar</button>
            <button class="btn-primary" id="btnGuardarEmpleado" onclick="guardarEmpleado()">💾 Guardar</button>
        </div>
    </div>
</div>

{{-- MODAL PARÁMETROS --}}
<div id="modalParametros" class="fixed inset-0 hidden items-center justify-center z-50 p-4 modal-backdrop-nm">
    <div class="modal-nm" style="max-width:640px;">
        <div class="modal-head-nm"><p class="modal-head-title">Parámetros Legales de Nómina</p>
            <button onclick="cerrarModalParametros()" style="border:none;background:transparent;font-size:20px;cursor:pointer;color:#6B7280;">✕</button></div>
        <div class="modal-body-nm">
            <p style="font-size:12px;color:#DC2626;background:#FEF2F2;padding:8px 10px;border-radius:8px;margin-bottom:14px;">⚠️ El SMMLV y el auxilio de transporte cambian cada año por decreto del Ministerio de Trabajo. Verifíquelos antes de liquidar.</p>
            <form id="formParametros" class="nm-grid" onsubmit="return false;">
                <div class="nm-field"><label>SMMLV vigente</label><input autocomplete="off" type="number" name="smmlv" min="0" step="1"></div>
                <div class="nm-field"><label>Auxilio de transporte</label><input autocomplete="off" type="number" name="auxilio_transporte" min="0" step="1"></div>
                <div class="nm-field"><label>Salud empleado (%)</label><input autocomplete="off" type="number" name="salud_empleado_pct" min="0" step="0.01"></div>
                <div class="nm-field"><label>Pensión empleado (%)</label><input autocomplete="off" type="number" name="pension_empleado_pct" min="0" step="0.01"></div>
                <div class="nm-field"><label>Salud patronal (%)</label><input autocomplete="off" type="number" name="salud_patronal_pct" min="0" step="0.01"></div>
                <div class="nm-field"><label>Pensión patronal (%)</label><input autocomplete="off" type="number" name="pension_patronal_pct" min="0" step="0.01"></div>
                <div class="nm-field"><label>Cesantías (%)</label><input autocomplete="off" type="number" name="cesantias_pct" min="0" step="0.01"></div>
                <div class="nm-field"><label>Intereses cesantías (% mensual)</label><input autocomplete="off" type="number" name="intereses_cesantias_pct" min="0" step="0.01"></div>
                <div class="nm-field"><label>Prima de servicios (%)</label><input autocomplete="off" type="number" name="prima_pct" min="0" step="0.01"></div>
                <div class="nm-field"><label>Vacaciones (%)</label><input autocomplete="off" type="number" name="vacaciones_pct" min="0" step="0.01"></div>
                <div class="nm-field"><label>SENA (%)</label><input autocomplete="off" type="number" name="sena_pct" min="0" step="0.01"></div>
                <div class="nm-field"><label>ICBF (%)</label><input autocomplete="off" type="number" name="icbf_pct" min="0" step="0.01"></div>
                <div class="nm-field"><label>Caja de Compensación (%)</label><input autocomplete="off" type="number" name="caja_compensacion_pct" min="0" step="0.01"></div>
            </form>
        </div>
        <div class="modal-foot-nm">
            <button class="btn-outline" onclick="cerrarModalParametros()">Cancelar</button>
            <button class="btn-primary" onclick="guardarParametros()">💾 Guardar</button>
        </div>
    </div>
</div>

{{-- MODAL LIQUIDAR --}}
<div id="modalLiquidar" class="fixed inset-0 hidden items-center justify-center z-50 p-4 modal-backdrop-nm">
    <div class="modal-nm" style="max-width:420px;">
        <div class="modal-head-nm"><p class="modal-head-title">Liquidar Nómina</p>
            <button onclick="cerrarModalLiquidar()" style="border:none;background:transparent;font-size:20px;cursor:pointer;color:#6B7280;">✕</button></div>
        <div class="modal-body-nm">
            <div class="nm-field" style="margin-bottom:12px;"><label>Período</label><input autocomplete="off" type="month" id="nm-liq-periodo"></div>
            <div class="nm-field"><label>Fecha de pago</label><input autocomplete="off" type="date" id="nm-liq-fecha"></div>
            <p style="font-size:11px;color:#9CA3AF;margin-top:10px;">Se liquidan TODOS los empleados activos de una sola vez en un único comprobante.</p>
        </div>
        <div class="modal-foot-nm">
            <button class="btn-outline" onclick="cerrarModalLiquidar()">Cancelar</button>
            <button class="btn-primary" id="btnLiquidar" onclick="ejecutarLiquidacion()">💵 Liquidar y Contabilizar</button>
        </div>
    </div>
</div>

<script>
    var NM = { empleados: [], liquidaciones: [] };
    function tokenNM() { return document.querySelector('meta[name="csrf-token"]')?.content; }
    function fmtMoneyNM(n) { return '$ ' + (Number(n) || 0).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 }); }
    function fmtFechaNM(s) { if (!s) return '—'; var p = String(s).slice(0, 10).split('-'); return p.length === 3 ? p[2] + '/' + p[1] + '/' + p[0] : s; }
    function notifNM(msg, tipo) { if (typeof mostrarNotificacion === 'function') { mostrarNotificacion(msg, tipo || 'success'); return; } window.alert(msg); }

    window.cambiarTabNomina = function (tab) {
        document.querySelectorAll('.if-tab').forEach(function (t) { t.classList.toggle('activo', t.dataset.tab === tab); });
        document.getElementById('nm-panel-empleados').classList.toggle('hidden', tab !== 'empleados');
        document.getElementById('nm-panel-liquidaciones').classList.toggle('hidden', tab !== 'liquidaciones');
        if (tab === 'liquidaciones') cargarLiquidaciones();
    };

    function cargarEmpleados() {
        fetch('/nomina/empleados', { headers: { Accept: 'application/json' } }).then(function (r) { return r.json(); }).then(function (res) {
            NM.empleados = res.data || [];
            var activos = NM.empleados.filter(function (e) { return e.estado === 'activo'; });
            document.getElementById('nm-emp-metrics').innerHTML = [
                { label: 'Empleados activos', value: activos.length, accent: '#059669' },
                { label: 'Inactivos', value: NM.empleados.length - activos.length, accent: '#DC2626' },
                { label: 'Nómina mensual estimada', value: fmtMoneyNM(activos.reduce(function (s, e) { return s + Number(e.salario_base); }, 0)), accent: '#1D4ED8' },
            ].map(function (t) { return '<div class="metric-card" style="--accent:' + t.accent + '"><p class="metric-label">' + t.label + '</p><p class="metric-value">' + t.value + '</p></div>'; }).join('');

            var filas = NM.empleados.map(function (e) {
                var badge = e.estado === 'activo' ? '<span class="badge badge-green">Activo</span>' : '<span class="badge badge-red">Inactivo</span>';
                return '<tr><td><span class="td-mono">' + e.codigo + '</span></td><td>' + e.nombre_completo + '</td><td>' + (e.cargo || '—') + '</td>' +
                    '<td class="td-money">' + fmtMoneyNM(e.salario_base) + '</td><td>' + badge + '</td>' +
                    '<td style="text-align:right;"><button class="act-btn" title="Editar" onclick="abrirModalEmpleado(' + e.id + ')">✏️</button>' +
                    '<button class="act-btn" title="' + (e.estado === 'activo' ? 'Retirar' : 'Reactivar') + '" onclick="toggleEstadoEmpleado(' + e.id + ')">' + (e.estado === 'activo' ? '🚫' : '↩️') + '</button></td></tr>';
            }).join('') || '<tr><td colspan="6"><div class="spinner-cell">Sin empleados registrados</div></td></tr>';

            document.getElementById('nm-tabla-empleados').innerHTML =
                '<table class="nm-tbl"><thead><tr><th>Código</th><th>Nombre</th><th>Cargo</th><th style="text-align:right;">Salario</th><th>Estado</th><th></th></tr></thead><tbody>' + filas + '</tbody></table>';
        });
    }

    window.abrirModalEmpleado = function (id) {
        window.empleadoEditandoId = id || null;
        var form = document.getElementById('formEmpleado');
        form.reset();
        document.getElementById('nm-emp-titulo').textContent = id ? 'Editar Empleado' : 'Nuevo Empleado';
        if (id) {
            var e = NM.empleados.find(function (x) { return x.id === id; });
            if (e) ['nombre', 'apellido', 'cedula', 'cargo', 'fecha_ingreso', 'salario_base', 'arl_tarifa', 'cuenta_bancaria', 'email', 'celular'].forEach(function (c) {
                if (form.elements[c]) form.elements[c].value = e[c] || '';
            });
        }
        document.getElementById('modalEmpleado').classList.remove('hidden');
        document.getElementById('modalEmpleado').classList.add('flex');
    };
    window.cerrarModalEmpleado = function () { document.getElementById('modalEmpleado').classList.add('hidden'); document.getElementById('modalEmpleado').classList.remove('flex'); };

    window.guardarEmpleado = function () {
        var form = document.getElementById('formEmpleado');
        var datos = Object.fromEntries(new FormData(form).entries());
        var id = window.empleadoEditandoId;
        var btn = document.getElementById('btnGuardarEmpleado');
        btn.disabled = true;

        fetch(id ? '/nomina/empleados/' + id : '/nomina/empleados', {
            method: id ? 'PUT' : 'POST',
            headers: { 'X-CSRF-TOKEN': tokenNM(), 'Content-Type': 'application/json', Accept: 'application/json' },
            body: JSON.stringify(datos),
        }).then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
            .then(function (res) {
                if (!res.ok) throw new Error(res.d.errors ? Object.values(res.d.errors).flat().join('<br>') : res.d.message);
                notifNM(res.d.message, 'success');
                cerrarModalEmpleado();
                cargarEmpleados();
            }).catch(function (e) { notifNM(e.message, 'error'); }).finally(function () { btn.disabled = false; });
    };

    window.toggleEstadoEmpleado = function (id) {
        fetch('/nomina/empleados/' + id + '/estado', { method: 'PUT', headers: { 'X-CSRF-TOKEN': tokenNM(), Accept: 'application/json' } })
            .then(function (r) { return r.json(); }).then(function (d) { notifNM(d.message, 'success'); cargarEmpleados(); });
    };

    window.abrirModalParametros = function () {
        fetch('/nomina/parametros', { headers: { Accept: 'application/json' } }).then(function (r) { return r.json(); }).then(function (p) {
            var form = document.getElementById('formParametros');
            Object.keys(p).forEach(function (k) { if (form.elements[k]) form.elements[k].value = p[k]; });
            document.getElementById('modalParametros').classList.remove('hidden');
            document.getElementById('modalParametros').classList.add('flex');
        });
    };
    window.cerrarModalParametros = function () { document.getElementById('modalParametros').classList.add('hidden'); document.getElementById('modalParametros').classList.remove('flex'); };

    window.guardarParametros = function () {
        var datos = Object.fromEntries(new FormData(document.getElementById('formParametros')).entries());
        fetch('/nomina/parametros', {
            method: 'PUT', headers: { 'X-CSRF-TOKEN': tokenNM(), 'Content-Type': 'application/json', Accept: 'application/json' }, body: JSON.stringify(datos),
        }).then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
            .then(function (res) { if (!res.ok) throw new Error(res.d.message); notifNM(res.d.message, 'success'); cerrarModalParametros(); })
            .catch(function (e) { notifNM(e.message, 'error'); });
    };

    function cargarLiquidaciones() {
        fetch('/nomina/liquidaciones', { headers: { Accept: 'application/json' } }).then(function (r) { return r.json(); }).then(function (res) {
            NM.liquidaciones = res.data || [];
            var filas = NM.liquidaciones.map(function (l) {
                var badge = l.estado === 'REGISTRADA' ? '<span class="badge badge-green">Vigente</span>' : '<span class="badge badge-red">Anulada</span>';
                var acciones = '<button class="act-btn" title="Ver desprendibles" onclick="verDesprendibles(' + l.id + ')">🖨️</button>';
                if (l.estado === 'REGISTRADA') acciones += '<button class="act-btn" title="Anular" onclick="anularLiquidacionNomina(' + l.id + ')">🚫</button>';
                return '<tr><td><b>' + l.periodo + '</b></td><td>' + fmtFechaNM(l.fecha_pago) + '</td><td>' + l.detalles_count + ' empleado(s)</td>' +
                    '<td class="td-money">' + fmtMoneyNM(l.total_devengado) + '</td><td class="td-money">' + fmtMoneyNM(l.total_neto) + '</td>' +
                    '<td>' + badge + '</td><td style="text-align:right;">' + acciones + '</td></tr>';
            }).join('') || '<tr><td colspan="7"><div class="spinner-cell">Sin liquidaciones registradas</div></td></tr>';

            document.getElementById('nm-tabla-liquidaciones').innerHTML =
                '<table class="nm-tbl"><thead><tr><th>Período</th><th>Fecha pago</th><th>Empleados</th><th style="text-align:right;">Devengado</th><th style="text-align:right;">Neto pagado</th><th>Estado</th><th></th></tr></thead><tbody>' + filas + '</tbody></table>';
        });
    }

    window.abrirModalLiquidar = function () {
        var hoy = new Date();
        document.getElementById('nm-liq-periodo').value = hoy.getFullYear() + '-' + String(hoy.getMonth() + 1).padStart(2, '0');
        document.getElementById('nm-liq-fecha').value = hoy.toISOString().slice(0, 10);
        document.getElementById('modalLiquidar').classList.remove('hidden');
        document.getElementById('modalLiquidar').classList.add('flex');
    };
    window.cerrarModalLiquidar = function () { document.getElementById('modalLiquidar').classList.add('hidden'); document.getElementById('modalLiquidar').classList.remove('flex'); };

    window.ejecutarLiquidacion = function () {
        var btn = document.getElementById('btnLiquidar');
        btn.disabled = true;
        fetch('/nomina/liquidaciones', {
            method: 'POST', headers: { 'X-CSRF-TOKEN': tokenNM(), 'Content-Type': 'application/json', Accept: 'application/json' },
            body: JSON.stringify({ periodo: document.getElementById('nm-liq-periodo').value, fecha_pago: document.getElementById('nm-liq-fecha').value }),
        }).then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
            .then(function (res) { if (!res.ok) throw new Error(res.d.errors ? Object.values(res.d.errors).flat().join('<br>') : res.d.message); notifNM(res.d.message, 'success'); cerrarModalLiquidar(); cargarLiquidaciones(); })
            .catch(function (e) { notifNM(e.message, 'error'); }).finally(function () { btn.disabled = false; });
    };

    window.anularLiquidacionNomina = function () {
        var id = arguments[0];
        var motivo = window.prompt('Motivo de anulación:');
        if (!motivo) return;
        fetch('/nomina/liquidaciones/' + id + '/anular', {
            method: 'POST', headers: { 'X-CSRF-TOKEN': tokenNM(), 'Content-Type': 'application/json', Accept: 'application/json' }, body: JSON.stringify({ motivo: motivo }),
        }).then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
            .then(function (res) { if (!res.ok) throw new Error(res.d.message); notifNM(res.d.message, 'success'); cargarLiquidaciones(); })
            .catch(function (e) { notifNM(e.message, 'error'); });
    };

    /* ── Desprendibles de pago (impresión vía ventana, igual que Informes) ── */
    window.verDesprendibles = function (id) {
        fetch('/nomina/liquidaciones/' + id, { headers: { Accept: 'application/json' } }).then(function (r) { return r.json(); }).then(function (liq) {
            var html = '<!doctype html><html><head><meta charset="utf-8"><title>Desprendibles de Nómina ' + liq.periodo + '</title><style>' +
                '@page{size:letter;margin:14mm;}body{font-family:Arial,Helvetica,sans-serif;color:#111827;margin:0;}' +
                '.desprendible{border:1px solid #E5E7EB;border-radius:8px;padding:16px;margin-bottom:18px;page-break-inside:avoid;}' +
                '.enc{display:flex;justify-content:space-between;border-bottom:2px solid #1D4ED8;padding-bottom:8px;margin-bottom:10px;}' +
                '.brand{font-weight:800;color:#1D4ED8;font-size:16px;}.meta{font-size:11px;color:#6B7280;text-align:right;}' +
                'table{width:100%;border-collapse:collapse;font-size:11px;}td{padding:3px 4px;}' +
                '.tit{font-weight:700;background:#F8FAFC;padding:4px 6px;margin-top:8px;}' +
                '.neto{font-weight:800;font-size:13px;background:#ECFDF5;padding:6px;text-align:right;margin-top:8px;}' +
                '</style></head><body>' +
                liq.detalles.map(function (d) {
                    var e = d.empleado;
                    return '<div class="desprendible"><div class="enc"><div><div class="brand">📈 NussoraPos</div><div style="font-size:11px;">Desprendible de Pago de Nómina</div></div>' +
                        '<div class="meta"><b>' + e.nombre_completo + '</b><br>CC ' + e.cedula + (e.cargo ? ' — ' + e.cargo : '') + '<br>Período: ' + liq.periodo + ' · Pago: ' + fmtFechaNM(liq.fecha_pago) + '</div></div>' +
                        '<div class="tit">DEVENGADOS</div><table>' +
                        '<tr><td>Salario</td><td style="text-align:right;">' + fmtMoneyNM(d.salario_devengado) + '</td></tr>' +
                        (d.auxilio_transporte > 0 ? '<tr><td>Auxilio de transporte</td><td style="text-align:right;">' + fmtMoneyNM(d.auxilio_transporte) + '</td></tr>' : '') +
                        '</table>' +
                        '<div class="tit">DEDUCCIONES</div><table>' +
                        '<tr><td>Salud (' + '4%' + ')</td><td style="text-align:right;">-' + fmtMoneyNM(d.salud_empleado) + '</td></tr>' +
                        '<tr><td>Pensión</td><td style="text-align:right;">-' + fmtMoneyNM(d.pension_empleado) + '</td></tr>' +
                        '</table>' +
                        '<div class="tit">PROVISIONES A CARGO DEL EMPLEADOR (informativo)</div><table>' +
                        '<tr><td>Cesantías</td><td style="text-align:right;">' + fmtMoneyNM(d.cesantias) + '</td></tr>' +
                        '<tr><td>Intereses cesantías</td><td style="text-align:right;">' + fmtMoneyNM(d.intereses_cesantias) + '</td></tr>' +
                        '<tr><td>Prima de servicios</td><td style="text-align:right;">' + fmtMoneyNM(d.prima) + '</td></tr>' +
                        '<tr><td>Vacaciones</td><td style="text-align:right;">' + fmtMoneyNM(d.vacaciones) + '</td></tr>' +
                        '</table>' +
                        '<div class="neto">NETO PAGADO: ' + fmtMoneyNM(d.neto_pagado) + '</div></div>';
                }).join('') +
                '</body></html>';

            var ventana = window.open('', '_blank', 'width=1000,height=700');
            if (!ventana) { notifNM('El navegador bloqueó la ventana de impresión.', 'error'); return; }
            ventana.document.open(); ventana.document.write(html); ventana.document.close();
            setTimeout(function () { ventana.focus(); ventana.print(); }, 300);
        });
    };

    cargarEmpleados();
</script>
