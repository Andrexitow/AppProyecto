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

    .btn-primary { display: inline-flex; align-items: center; gap: 6px; background: #1D4ED8; color: #fff; border: none; border-radius: 7px; padding: 7px 14px; font-size: 12px; font-weight: 600; cursor: pointer; transition: background .15s; white-space: nowrap; }
    .btn-primary:hover { background: #1e40af; }
    .btn-outline { display: inline-flex; align-items: center; gap: 5px; background: #fff; color: #374151; border: 1px solid #D1D5DB; border-radius: 7px; padding: 7px 12px; font-size: 12px; font-weight: 500; cursor: pointer; transition: all .15s; white-space: nowrap; }
    .btn-outline:hover { background: #F3F4F6; border-color: #9CA3AF; }
    .btn-danger-outline { display: inline-flex; align-items: center; gap: 5px; background: #fff; color: #B91C1C; border: 1px solid #FCA5A5; border-radius: 7px; padding: 7px 12px; font-size: 12px; font-weight: 600; cursor: pointer; }
    .btn-danger-outline:hover { background: #FEF2F2; }

    .cuentas-row { display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 12px; margin-bottom: 16px; }
    .cuenta-card { background: #fff; border: 2px solid #EAECF0; border-radius: 12px; padding: 14px 16px; cursor: pointer; transition: all .15s; }
    .cuenta-card:hover { border-color: #93C5FD; }
    .cuenta-card.activa { border-color: #1D4ED8; box-shadow: 0 0 0 3px rgba(29,78,216,.1); }
    .cuenta-card-tipo { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; padding: 2px 8px; border-radius: 20px; display: inline-block; }
    .cuenta-card-tipo.caja { background: #ECFDF5; color: #065F46; }
    .cuenta-card-tipo.banco { background: #EFF6FF; color: #1D4ED8; }
    .cuenta-card-nombre { font-size: 14px; font-weight: 700; color: #111827; margin-top: 8px; }
    .cuenta-card-cuenta { font-size: 11px; color: #9CA3AF; margin-top: 2px; }
    .cuenta-card-saldo { font-size: 20px; font-weight: 800; color: #111827; margin-top: 8px; letter-spacing: -0.5px; }
    .cuenta-card-nueva { display: flex; align-items: center; justify-content: center; border: 2px dashed #D1D5DB; border-radius: 12px; color: #6B7280; font-size: 12px; font-weight: 600; cursor: pointer; min-height: 96px; }
    .cuenta-card-nueva:hover { border-color: #9CA3AF; background: #F9FAFB; }

    .filter-bar { background: #fff; border: 1px solid #EAECF0; border-radius: 10px; padding: 12px 14px; display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-bottom: 12px; }
    .fi-group { display: flex; align-items: center; gap: 6px; flex: 1; min-width: 160px; }
    .fi-label { font-size: 12px; color: #6B7280; white-space: nowrap; }
    .fi-input, .fi-select { flex: 1; border: 1px solid #D1D5DB; border-radius: 7px; padding: 6px 10px; font-size: 12px; color: #111827; background: #F9FAFB; }
    .fi-input:focus, .fi-select:focus { border-color: #1D4ED8; background: #fff; }

    .table-wrapper { background: #fff; border: 1px solid #EAECF0; border-radius: 10px; overflow: hidden; }
    .table-scroll { overflow-x: auto; }
    table.tz-tbl { width: 100%; border-collapse: collapse; font-size: 12.5px; }
    table.tz-tbl thead { background: #F8FAFC; border-bottom: 1px solid #EAECF0; }
    table.tz-tbl thead th { padding: 10px 12px; text-align: left; font-size: 11px; font-weight: 600; color: #6B7280; text-transform: uppercase; letter-spacing: .5px; white-space: nowrap; }
    table.tz-tbl tbody tr { border-bottom: 1px solid #F3F4F6; }
    table.tz-tbl tbody tr:hover { background: #F8FAFC; }
    table.tz-tbl td { padding: 8px 12px; color: #374151; vertical-align: middle; }
    .td-money { font-weight: 600; color: #111827; text-align: right; white-space: nowrap; }
    .td-money.positivo { color: #059669; }
    .td-money.negativo { color: #DC2626; }
    .td-mono { font-family: 'JetBrains Mono','Fira Mono',monospace; font-size: 12px; color: #1D4ED8; font-weight: 600; }

    .badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; white-space: nowrap; }
    .badge-green { background: #ECFDF5; color: #065F46; }
    .badge-red { background: #FEF2F2; color: #991B1B; }
    .badge-blue { background: #EFF6FF; color: #1D4ED8; }

    .spinner-cell { display: flex; align-items: center; justify-content: center; padding: 40px; color: #6B7280; font-size: 13px; gap: 10px; }
    @keyframes spin-tz { to { transform: rotate(360deg); } }
    .spinner { width: 18px; height: 18px; border: 2px solid #E5E7EB; border-top-color: #1D4ED8; border-radius: 50%; animation: spin-tz .7s linear infinite; }

    .modal-backdrop { position: fixed; inset: 0; background: rgba(17,24,39,.5); display: flex; align-items: center; justify-content: center; z-index: 200; padding: 16px; }
    .modal-box { background: #fff; border-radius: 12px; width: 100%; max-width: 460px; max-height: 90vh; overflow-y: auto; }
    .modal-head { display: flex; align-items: flex-start; justify-content: space-between; padding: 16px 18px; border-bottom: 1px solid #F3F4F6; }
    .modal-head-title { font-size: 14px; font-weight: 700; color: #111827; }
    .modal-body { padding: 16px 18px; }
    .modal-foot { display: flex; justify-content: flex-end; gap: 8px; padding: 14px 18px; border-top: 1px solid #F3F4F6; }
    .co-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
    .co-field { position: relative; }
    .co-field.full { grid-column: 1/-1; }
    .co-field label { display: block; font-size: 11px; font-weight: 600; color: #6B7280; margin-bottom: 4px; }
    .co-field input, .co-field select { width: 100%; border: 1px solid #D1D5DB; border-radius: 7px; padding: 7px 10px; font-size: 12.5px; color: #111827; box-sizing: border-box; }
    .tz-resultados { position: absolute; z-index: 30; top: calc(100% + 4px); left: 0; right: 0; display: none; max-height: 200px; overflow-y: auto; border: 1px solid #D1D5DB; border-radius: 8px; background: #fff; box-shadow: 0 10px 25px rgba(16,24,40,.14); }
    .tz-resultados.open { display: block; }
    .tz-opcion { width: 100%; display: flex; align-items: baseline; gap: 8px; padding: 8px 12px; border: 0; border-bottom: 1px solid #F3F4F6; background: #fff; color: #374151; text-align: left; cursor: pointer; font-size: 12.5px; }
    .tz-opcion:hover { background: #EFF6FF; }
    .tz-opcion-mono { font-family: 'JetBrains Mono','Fira Mono',monospace; font-size: 12px; color: #1D4ED8; font-weight: 600; flex-shrink: 0; }

    @media (max-width: 640px) { .metrics-row { grid-template-columns: 1fr 1fr; } .co-grid { grid-template-columns: 1fr; } }
</style>

<div id="view-tesoreria">
    <div class="sec-header">
        <div>
            <p class="sec-title">🏦 Tesorería</p>
            <p class="sec-subtitle">Caja, bancos, ingresos, egresos y transferencias</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button class="btn-outline" onclick="abrirTransferenciaTz()">🔁 Transferir</button>
            <button class="btn-danger-outline" onclick="abrirEgresoTz()">➖ Egreso</button>
            <button class="btn-primary" onclick="abrirIngresoTz()">➕ Ingreso</button>
        </div>
    </div>

    <div class="metrics-row" id="tz-metrics"></div>

    <div class="cuentas-row" id="tz-cuentas"></div>

    <div class="filter-bar" id="tz-filtro-ledger" style="display:none;">
        <div class="fi-group">
            <span class="fi-label">Desde</span>
            <input autocomplete="off" type="date" id="tz-desde" class="fi-input">
        </div>
        <div class="fi-group">
            <span class="fi-label">Hasta</span>
            <input autocomplete="off" type="date" id="tz-hasta" class="fi-input">
        </div>
        <button class="btn-primary" onclick="cargarLedgerTz()">🔍 Consultar</button>
        <button class="btn-outline" onclick="abrirConciliacionTz()">🔄 Conciliar extracto</button>
    </div>

    <div class="table-wrapper" id="tz-tabla-wrapper" style="display:none;">
        <div class="table-scroll">
            <table class="tz-tbl">
                <thead><tr><th>Fecha</th><th>Comprobante</th><th>Detalle</th><th>Tercero</th><th style="text-align:right;">Débito</th><th style="text-align:right;">Crédito</th><th style="text-align:right;">Saldo</th></tr></thead>
                <tbody id="tz-tbody"></tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL NUEVA CUENTA --}}
<div id="modal-cuenta-tz" style="display:none;" class="modal-backdrop">
    <div class="modal-box">
        <div class="modal-head"><p class="modal-head-title">Nueva cuenta de tesorería</p><button onclick="cerrarModalTz('modal-cuenta-tz')" style="border:0;background:transparent;font-size:20px;cursor:pointer;">✕</button></div>
        <div class="modal-body">
            <div class="co-grid">
                <div class="co-field full"><label>Nombre *</label><input autocomplete="off" id="tzc-nombre" placeholder="Ej: Nequi Ventas, Caja Menor..."></div>
                <div class="co-field"><label>Tipo *</label><select id="tzc-tipo"><option value="CAJA">Caja</option><option value="BANCO">Banco</option></select></div>
                <div class="co-field"><label>N° cuenta</label><input autocomplete="off" id="tzc-numero" placeholder="Opcional"></div>
                <div class="co-field full"><label>Cuenta contable *</label><input id="tzc-cuenta-buscar" autocomplete="off" placeholder="Buscar por código o nombre…"><input type="hidden" id="tzc-cuenta-id"><div id="tzc-cuenta-resultados" class="tz-resultados"></div></div>
            </div>
        </div>
        <div class="modal-foot"><button class="btn-outline" onclick="cerrarModalTz('modal-cuenta-tz')">Cancelar</button><button class="btn-primary" onclick="guardarCuentaTz()">Crear cuenta</button></div>
    </div>
</div>

{{-- MODAL INGRESO / EGRESO --}}
<div id="modal-movimiento-tz" style="display:none;" class="modal-backdrop">
    <div class="modal-box">
        <div class="modal-head"><p class="modal-head-title" id="tzm-titulo">Registrar movimiento</p><button onclick="cerrarModalTz('modal-movimiento-tz')" style="border:0;background:transparent;font-size:20px;cursor:pointer;">✕</button></div>
        <div class="modal-body">
            <div class="co-grid">
                <div class="co-field"><label>Cuenta *</label><select id="tzm-cuenta"></select></div>
                <div class="co-field"><label>Fecha *</label><input autocomplete="off" id="tzm-fecha" type="date"></div>
                <div class="co-field full"><label id="tzm-label-contrapartida">Cuenta contrapartida *</label><input id="tzm-contrapartida-buscar" autocomplete="off" placeholder="Buscar por código o nombre…"><input type="hidden" id="tzm-contrapartida-id"><div id="tzm-contrapartida-resultados" class="tz-resultados"></div></div>
                <div class="co-field full"><label>Valor *</label><input autocomplete="off" id="tzm-valor" type="number" min="0.01" step="0.01"></div>
                <div class="co-field full"><label>Descripción *</label><input autocomplete="off" id="tzm-descripcion" placeholder="Concepto del movimiento…"></div>
            </div>
        </div>
        <div class="modal-foot"><button class="btn-outline" onclick="cerrarModalTz('modal-movimiento-tz')">Cancelar</button><button class="btn-primary" id="tzm-btn-guardar" onclick="guardarMovimientoTz()">Registrar</button></div>
    </div>
</div>

{{-- MODAL TRANSFERENCIA --}}
<div id="modal-transferencia-tz" style="display:none;" class="modal-backdrop">
    <div class="modal-box">
        <div class="modal-head"><p class="modal-head-title">Transferir entre cuentas</p><button onclick="cerrarModalTz('modal-transferencia-tz')" style="border:0;background:transparent;font-size:20px;cursor:pointer;">✕</button></div>
        <div class="modal-body">
            <div class="co-grid">
                <div class="co-field"><label>Desde *</label><select id="tzt-origen"></select></div>
                <div class="co-field"><label>Hacia *</label><select id="tzt-destino"></select></div>
                <div class="co-field"><label>Fecha *</label><input autocomplete="off" id="tzt-fecha" type="date"></div>
                <div class="co-field"><label>Valor *</label><input autocomplete="off" id="tzt-valor" type="number" min="0.01" step="0.01"></div>
                <div class="co-field full"><label>Descripción</label><input autocomplete="off" id="tzt-descripcion" placeholder="Opcional…"></div>
            </div>
        </div>
        <div class="modal-foot"><button class="btn-outline" onclick="cerrarModalTz('modal-transferencia-tz')">Cancelar</button><button class="btn-primary" onclick="guardarTransferenciaTz()">Transferir</button></div>
    </div>
</div>

{{-- MODAL CONCILIACIÓN BANCARIA --}}
<div id="modal-conciliacion-tz" style="display:none;" class="modal-backdrop">
    <div class="modal-box" style="max-width:920px;">
        <div class="modal-head">
            <div><p class="modal-head-title">Conciliación Bancaria</p><p style="font-size:11px;color:#6B7280;margin-top:2px;" id="cz-cuenta-nombre"></p></div>
            <button onclick="cerrarModalTz('modal-conciliacion-tz')" style="border:0;background:transparent;font-size:20px;cursor:pointer;">✕</button>
        </div>
        <div class="modal-body">
            <div class="co-grid" style="grid-template-columns:1fr 1fr 1fr auto;align-items:end;margin-bottom:12px;">
                <div class="co-field"><label>Desde</label><input autocomplete="off" id="cz-desde" type="date"></div>
                <div class="co-field"><label>Hasta</label><input autocomplete="off" id="cz-hasta" type="date"></div>
                <div class="co-field"><label>&nbsp;</label><button class="btn-primary" style="width:100%;" onclick="cargarConciliacionTz()">🔍 Consultar</button></div>
                <div></div>
            </div>

            <div class="metrics-row" id="cz-metrics" style="margin-bottom:14px;"></div>

            <p style="font-size:12px;font-weight:700;margin-bottom:6px;">➕ Añadir línea del extracto bancario</p>
            <div class="co-grid" style="grid-template-columns:1fr 2fr 1fr auto;margin-bottom:16px;">
                <input autocomplete="off" id="cz-nueva-fecha" type="date" placeholder="Fecha">
                <input autocomplete="off" id="cz-nueva-desc" placeholder="Descripción (ej: Consignación, Comisión...)">
                <input autocomplete="off" id="cz-nueva-valor" type="number" step="0.01" placeholder="Valor (+ / -)">
                <button class="btn-outline" onclick="agregarLineaExtractoTz()">＋ Añadir</button>
            </div>

            <div id="cz-sugerencias"></div>

            <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;">
                <div>
                    <p style="font-size:12px;font-weight:700;margin-bottom:6px;">📒 Según el sistema</p>
                    <div class="table-scroll" style="max-height:280px;overflow-y:auto;border:1px solid #EAECF0;border-radius:8px;">
                        <table class="tz-tbl" style="font-size:11.5px;"><thead><tr><th>Fecha</th><th>Comprobante</th><th style="text-align:right;">Valor</th><th></th></tr></thead><tbody id="cz-sistema-tbody"></tbody></table>
                    </div>
                </div>
                <div>
                    <p style="font-size:12px;font-weight:700;margin-bottom:6px;">🏦 Según el extracto</p>
                    <div class="table-scroll" style="max-height:280px;overflow-y:auto;border:1px solid #EAECF0;border-radius:8px;">
                        <table class="tz-tbl" style="font-size:11.5px;"><thead><tr><th>Fecha</th><th>Descripción</th><th style="text-align:right;">Valor</th><th></th></tr></thead><tbody id="cz-extracto-tbody"></tbody></table>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-foot"><button class="btn-outline" onclick="cerrarModalTz('modal-conciliacion-tz')">Cerrar</button></div>
    </div>
</div>

<script>
    var TZ = { cuentas: [], cuentaSeleccionada: null, cuentasContables: [] };

    function escTz(s) { return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
    function fmtMoneyTz(n) { return '$ ' + (Number(n) || 0).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 }); }
    function fmtFechaTz(s) { if (!s) return '—'; var p = String(s).slice(0, 10).split('-'); return p.length === 3 ? p[2] + '/' + p[1] + '/' + p[0] : s; }
    function hdrsTz() { return { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content, 'Accept': 'application/json' }; }
    function notifTz(msg, tipo) { if (typeof mostrarNotificacion === 'function') { mostrarNotificacion(msg, tipo === 'error' ? 'error' : 'success'); return; } window.alert(msg); }

    (function initTz() {
        var hoy = new Date().toISOString().slice(0, 10);
        document.getElementById('tz-desde').value = new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().slice(0, 10);
        document.getElementById('tz-hasta').value = hoy;

        fetch('/informes-contables/cuentas', { headers: hdrsTz() }).then(function (r) { return r.json(); }).then(function (res) { TZ.cuentasContables = res.data || []; }).catch(function () {});
        cargarCuentasTz();
    })();

    function cargarCuentasTz() {
        fetch('/tesoreria/cuentas', { headers: hdrsTz() }).then(function (r) { return r.json(); }).then(function (res) {
            TZ.cuentas = res.data || [];
            renderCuentasTz();
            renderMetricasTz();
        }).catch(function () {});
    }

    function renderMetricasTz() {
        var totalCaja = TZ.cuentas.filter(function (c) { return c.tipo === 'CAJA'; }).reduce(function (a, c) { return a + Number(c.saldo); }, 0);
        var totalBanco = TZ.cuentas.filter(function (c) { return c.tipo === 'BANCO'; }).reduce(function (a, c) { return a + Number(c.saldo); }, 0);
        document.getElementById('tz-metrics').innerHTML = [
            { label: 'Total en caja', value: fmtMoneyTz(totalCaja), accent: '#059669' },
            { label: 'Total en bancos', value: fmtMoneyTz(totalBanco), accent: '#1D4ED8' },
            { label: 'Total disponible', value: fmtMoneyTz(totalCaja + totalBanco), accent: '#7C3AED' },
            { label: 'Cuentas activas', value: TZ.cuentas.length, accent: '#D97706' },
        ].map(function (t) {
            return '<div class="metric-card" style="--accent:' + t.accent + '"><p class="metric-label">' + escTz(t.label) + '</p><p class="metric-value">' + t.value + '</p></div>';
        }).join('');
    }

    function renderCuentasTz() {
        var html = TZ.cuentas.map(function (c) {
            var activa = TZ.cuentaSeleccionada === c.id;
            return '<div class="cuenta-card' + (activa ? ' activa' : '') + '" onclick="seleccionarCuentaTz(' + c.id + ')">' +
                '<span class="cuenta-card-tipo ' + (c.tipo === 'CAJA' ? 'caja' : 'banco') + '">' + (c.tipo === 'CAJA' ? '💵 Caja' : '🏦 Banco') + '</span>' +
                '<p class="cuenta-card-nombre">' + escTz(c.nombre) + '</p>' +
                '<p class="cuenta-card-cuenta">' + escTz(c.cuenta_contable || '') + '</p>' +
                '<p class="cuenta-card-saldo">' + fmtMoneyTz(c.saldo) + '</p>' +
                '</div>';
        }).join('');
        html += '<div class="cuenta-card-nueva" onclick="abrirNuevaCuentaTz()">+ Nueva cuenta</div>';
        document.getElementById('tz-cuentas').innerHTML = html;
    }

    window.seleccionarCuentaTz = function (id) {
        TZ.cuentaSeleccionada = id;
        renderCuentasTz();
        document.getElementById('tz-filtro-ledger').style.display = 'flex';
        document.getElementById('tz-tabla-wrapper').style.display = 'block';
        cargarLedgerTz();
    };

    window.cargarLedgerTz = function () {
        if (!TZ.cuentaSeleccionada) return;
        var params = new URLSearchParams({ cuenta_tesoreria_id: TZ.cuentaSeleccionada, desde: document.getElementById('tz-desde').value, hasta: document.getElementById('tz-hasta').value });
        document.getElementById('tz-tbody').innerHTML = '<tr><td colspan="5"><div class="spinner-cell"><div class="spinner"></div>Cargando…</div></td></tr>';

        fetch('/tesoreria/movimientos?' + params.toString(), { headers: hdrsTz() }).then(function (r) { return r.json(); }).then(function (data) {
            var filas = data.movimientos.map(function (m) {
                return '<tr>' +
                    '<td>' + fmtFechaTz(m.fecha) + '</td>' +
                    '<td><span class="td-mono">' + escTz(m.tipo) + ' ' + escTz(m.numero) + '</span></td>' +
                    '<td>' + escTz(m.detalle || '—') + '</td>' +
                    '<td>' + escTz(m.tercero || '—') + '</td>' +
                    '<td class="td-money">' + (m.debito > 0 ? fmtMoneyTz(m.debito) : '—') + '</td>' +
                    '<td class="td-money">' + (m.credito > 0 ? fmtMoneyTz(m.credito) : '—') + '</td>' +
                    '<td class="td-money">' + fmtMoneyTz(m.saldo) + '</td>' +
                    '</tr>';
            }).join('') || '<tr><td colspan="7"><div class="spinner-cell">📭 Sin movimientos en el período</div></td></tr>';

            document.getElementById('tz-tbody').innerHTML =
                '<tr style="background:#F8FAFC;"><td colspan="6" style="font-style:italic;color:#6B7280;">Saldo inicial</td><td class="td-money">' + fmtMoneyTz(data.saldo_inicial) + '</td></tr>' + filas +
                '<tr style="border-top:2px solid #E5E7EB;"><td colspan="6" style="font-weight:700;">Saldo final</td><td class="td-money" style="font-weight:700;">' + fmtMoneyTz(data.saldo_final) + '</td></tr>';
        }).catch(function () {
            document.getElementById('tz-tbody').innerHTML = '<tr><td colspan="7"><div class="spinner-cell">⚠️ No fue posible cargar el movimiento</div></td></tr>';
        });
    };

    /* ═══════════ AUTOCOMPLETE CUENTA CONTABLE (reutilizado en 2 modales) ═══════════ */
    function bindBuscadorCuenta(inputId, hiddenId, resultadosId) {
        var input = document.getElementById(inputId);
        var render = function () {
            var texto = (input.value || '').toLowerCase().trim();
            var coincidencias = TZ.cuentasContables.filter(function (c) { return !texto || (c.codigo + ' ' + c.nombre).toLowerCase().includes(texto); });
            var cont = document.getElementById(resultadosId);
            cont.innerHTML = coincidencias.slice(0, 30).map(function (c) {
                return '<button type="button" class="tz-opcion" onclick="seleccionarCuentaBuscador(\'' + inputId + '\',\'' + hiddenId + '\',\'' + resultadosId + '\',' + c.id + ')">' +
                    '<span class="tz-opcion-mono">' + escTz(c.codigo) + '</span><span>' + escTz(c.nombre) + '</span></button>';
            }).join('') || '<div style="padding:12px;color:#9CA3AF;font-size:12px;">Sin coincidencias</div>';
            cont.classList.add('open');
        };
        input.addEventListener('input', function () { document.getElementById(hiddenId).value = ''; render(); });
        input.addEventListener('focus', render);
    }

    window.seleccionarCuentaBuscador = function (inputId, hiddenId, resultadosId, cuentaId) {
        var c = TZ.cuentasContables.find(function (x) { return Number(x.id) === Number(cuentaId); });
        if (!c) return;
        document.getElementById(hiddenId).value = c.id;
        document.getElementById(inputId).value = c.codigo + ' - ' + c.nombre;
        document.getElementById(resultadosId).classList.remove('open');
    };

    bindBuscadorCuenta('tzc-cuenta-buscar', 'tzc-cuenta-id', 'tzc-cuenta-resultados');
    bindBuscadorCuenta('tzm-contrapartida-buscar', 'tzm-contrapartida-id', 'tzm-contrapartida-resultados');

    document.addEventListener('click', function (e) {
        ['tzc-cuenta-resultados', 'tzm-contrapartida-resultados'].forEach(function (id) {
            var cont = document.getElementById(id);
            if (cont && !e.target.closest('.co-field')) cont.classList.remove('open');
        });
    });

    window.cerrarModalTz = function (id) { document.getElementById(id).style.display = 'none'; };

    /* ═══════════ NUEVA CUENTA ═══════════ */
    window.abrirNuevaCuentaTz = function () {
        document.getElementById('tzc-nombre').value = '';
        document.getElementById('tzc-tipo').value = 'CAJA';
        document.getElementById('tzc-numero').value = '';
        document.getElementById('tzc-cuenta-buscar').value = '';
        document.getElementById('tzc-cuenta-id').value = '';
        document.getElementById('modal-cuenta-tz').style.display = 'flex';
    };

    window.guardarCuentaTz = function () {
        var datos = {
            nombre: document.getElementById('tzc-nombre').value.trim(),
            tipo: document.getElementById('tzc-tipo').value,
            numero_cuenta: document.getElementById('tzc-numero').value.trim() || null,
            cuenta_contable_id: document.getElementById('tzc-cuenta-id').value,
        };
        if (!datos.nombre || !datos.cuenta_contable_id) { notifTz('Completa el nombre y la cuenta contable.', 'error'); return; }

        fetch('/tesoreria/cuentas', { method: 'POST', headers: Object.assign({ 'Content-Type': 'application/json' }, hdrsTz()), body: JSON.stringify(datos) })
            .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
            .then(function (r) {
                if (!r.ok) throw new Error(r.data.message || Object.values(r.data.errors || {}).flat().join(' ') || 'No se pudo crear la cuenta.');
                cerrarModalTz('modal-cuenta-tz');
                notifTz('Cuenta de tesorería creada.', 'success');
                cargarCuentasTz();
            }).catch(function (e) { notifTz(e.message, 'error'); });
    };

    /* ═══════════ INGRESO / EGRESO ═══════════ */
    var TZ_MODO = 'INGRESO';

    function poblarSelectCuentasTz(selectId, excluirId) {
        var sel = document.getElementById(selectId);
        sel.innerHTML = TZ.cuentas.filter(function (c) { return c.id !== excluirId; }).map(function (c) {
            return '<option value="' + c.id + '">' + escTz(c.nombre) + ' (' + fmtMoneyTz(c.saldo) + ')</option>';
        }).join('');
    }

    window.abrirIngresoTz = function () {
        TZ_MODO = 'INGRESO';
        document.getElementById('tzm-titulo').textContent = 'Registrar ingreso';
        document.getElementById('tzm-label-contrapartida').textContent = 'Cuenta de origen del ingreso *';
        document.getElementById('tzm-btn-guardar').className = 'btn-primary';
        abrirModalMovimientoTz();
    };

    window.abrirEgresoTz = function () {
        TZ_MODO = 'EGRESO';
        document.getElementById('tzm-titulo').textContent = 'Registrar egreso';
        document.getElementById('tzm-label-contrapartida').textContent = 'Cuenta de destino del gasto *';
        abrirModalMovimientoTz();
    };

    function abrirModalMovimientoTz() {
        poblarSelectCuentasTz('tzm-cuenta', null);
        document.getElementById('tzm-fecha').value = new Date().toISOString().slice(0, 10);
        document.getElementById('tzm-contrapartida-buscar').value = '';
        document.getElementById('tzm-contrapartida-id').value = '';
        document.getElementById('tzm-valor').value = '';
        document.getElementById('tzm-descripcion').value = '';
        document.getElementById('modal-movimiento-tz').style.display = 'flex';
    }

    window.guardarMovimientoTz = function () {
        var datos = {
            cuenta_tesoreria_id: Number(document.getElementById('tzm-cuenta').value),
            cuenta_contrapartida_id: document.getElementById('tzm-contrapartida-id').value,
            fecha: document.getElementById('tzm-fecha').value,
            valor: Number(document.getElementById('tzm-valor').value),
            descripcion: document.getElementById('tzm-descripcion').value.trim(),
        };
        if (!datos.cuenta_contrapartida_id || !datos.valor || !datos.descripcion) { notifTz('Completa la cuenta contrapartida, el valor y la descripción.', 'error'); return; }

        var url = TZ_MODO === 'INGRESO' ? '/tesoreria/ingresos' : '/tesoreria/egresos';
        fetch(url, { method: 'POST', headers: Object.assign({ 'Content-Type': 'application/json' }, hdrsTz()), body: JSON.stringify(datos) })
            .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
            .then(function (r) {
                if (!r.ok) throw new Error(r.data.message || Object.values(r.data.errors || {}).flat().join(' ') || 'No se pudo registrar el movimiento.');
                cerrarModalTz('modal-movimiento-tz');
                notifTz((TZ_MODO === 'INGRESO' ? 'Ingreso' : 'Egreso') + ' registrado y contabilizado correctamente.', 'success');
                cargarCuentasTz();
                if (TZ.cuentaSeleccionada === datos.cuenta_tesoreria_id) cargarLedgerTz();
            }).catch(function (e) { notifTz(e.message, 'error'); });
    };

    /* ═══════════ TRANSFERENCIA ═══════════ */
    window.abrirTransferenciaTz = function () {
        poblarSelectCuentasTz('tzt-origen', null);
        poblarSelectCuentasTz('tzt-destino', null);
        document.getElementById('tzt-fecha').value = new Date().toISOString().slice(0, 10);
        document.getElementById('tzt-valor').value = '';
        document.getElementById('tzt-descripcion').value = '';
        document.getElementById('modal-transferencia-tz').style.display = 'flex';
    };

    window.guardarTransferenciaTz = function () {
        var datos = {
            cuenta_origen_id: Number(document.getElementById('tzt-origen').value),
            cuenta_destino_id: Number(document.getElementById('tzt-destino').value),
            fecha: document.getElementById('tzt-fecha').value,
            valor: Number(document.getElementById('tzt-valor').value),
            descripcion: document.getElementById('tzt-descripcion').value.trim() || null,
        };
        if (datos.cuenta_origen_id === datos.cuenta_destino_id) { notifTz('Selecciona dos cuentas diferentes.', 'error'); return; }
        if (!datos.valor) { notifTz('Ingresa el valor a transferir.', 'error'); return; }

        fetch('/tesoreria/transferencias', { method: 'POST', headers: Object.assign({ 'Content-Type': 'application/json' }, hdrsTz()), body: JSON.stringify(datos) })
            .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
            .then(function (r) {
                if (!r.ok) throw new Error(r.data.message || Object.values(r.data.errors || {}).flat().join(' ') || 'No se pudo registrar la transferencia.');
                cerrarModalTz('modal-transferencia-tz');
                notifTz('Transferencia registrada y contabilizada correctamente.', 'success');
                cargarCuentasTz();
                if (TZ.cuentaSeleccionada) cargarLedgerTz();
            }).catch(function (e) { notifTz(e.message, 'error'); });
    };

    /* ── Conciliación bancaria ── */
    var CZ = { datos: null };

    window.abrirConciliacionTz = function () {
        if (!TZ.cuentaSeleccionada) { notifTz('Seleccione primero una cuenta de tesorería.', 'error'); return; }
        var cuenta = TZ.cuentas.find(function (c) { return c.id === TZ.cuentaSeleccionada; });
        document.getElementById('cz-cuenta-nombre').textContent = cuenta ? (cuenta.nombre + ' · ' + (cuenta.cuenta_contable || '')) : '';
        document.getElementById('cz-desde').value = document.getElementById('tz-desde').value || new Date(new Date().getFullYear(), new Date().getMonth(), 1).toISOString().slice(0, 10);
        document.getElementById('cz-hasta').value = document.getElementById('tz-hasta').value || new Date().toISOString().slice(0, 10);
        document.getElementById('cz-nueva-fecha').value = new Date().toISOString().slice(0, 10);
        document.getElementById('modal-conciliacion-tz').style.display = 'flex';
        cargarConciliacionTz();
    };

    window.cargarConciliacionTz = function () {
        var params = new URLSearchParams({ cuenta_tesoreria_id: TZ.cuentaSeleccionada, desde: document.getElementById('cz-desde').value, hasta: document.getElementById('cz-hasta').value });
        fetch('/conciliacion-bancaria/resumen?' + params.toString(), { headers: hdrsTz() }).then(function (r) { return r.json(); }).then(function (d) {
            CZ.datos = d;
            renderConciliacionTz();
        }).catch(function (e) { notifTz(e.message || 'No fue posible cargar la conciliación.', 'error'); });
    };

    function renderConciliacionTz() {
        var d = CZ.datos;
        document.getElementById('cz-metrics').innerHTML = [
            { label: 'Saldo según libros (al corte)', value: fmtMoneyTz(d.saldo_libros_al_corte), accent: '#1D4ED8' },
            { label: 'Movimientos del sistema (rango)', value: fmtMoneyTz(d.total_movimientos_libros), accent: '#374151' },
            { label: 'Líneas del extracto (rango)', value: fmtMoneyTz(d.total_lineas_extracto), accent: '#374151' },
            { label: 'Pendientes sin conciliar', value: fmtMoneyTz(d.total_pendientes_sistema) + ' / ' + fmtMoneyTz(d.total_pendientes_extracto), accent: d.pendientes_sistema.length || d.pendientes_extracto.length ? '#D97706' : '#059669' },
        ].map(function (t) { return '<div class="metric-card" style="--accent:' + t.accent + '"><p class="metric-label">' + escTz(t.label) + '</p><p class="metric-value" style="font-size:14px;">' + t.value + '</p></div>'; }).join('');

        if (d.sugerencias.length) {
            document.getElementById('cz-sugerencias').innerHTML = '<div style="background:#EFF6FF;border:1px solid #BFDBFE;border-radius:8px;padding:10px 12px;margin-bottom:14px;font-size:12px;">' +
                '💡 ' + d.sugerencias.length + ' coincidencia(s) sugerida(s) por mismo valor y fecha cercana — ' +
                '<button class="btn-outline" style="padding:3px 10px;font-size:11px;" onclick="aplicarSugerenciasTz()">Aplicar todas</button></div>';
        } else {
            document.getElementById('cz-sugerencias').innerHTML = '';
        }

        document.getElementById('cz-sistema-tbody').innerHTML = d.movimientos_sistema.map(function (m) {
            var accion = m.conciliado ? '<span class="badge badge-green">✓</span>' : '<button class="btn-outline" style="padding:2px 8px;font-size:10px;" onclick="marcarConciliadoManualTz(' + m.id + ')">Conciliar…</button>';
            return '<tr' + (m.conciliado ? ' style="opacity:.5;"' : '') + '><td>' + fmtFechaTz(m.fecha) + '</td><td>' + escTz(m.numero) + '</td>' +
                '<td class="td-money ' + (m.valor >= 0 ? 'positivo' : 'negativo') + '">' + fmtMoneyTz(m.valor) + '</td><td>' + accion + '</td></tr>';
        }).join('') || '<tr><td colspan="4" style="text-align:center;color:#9CA3AF;padding:14px;">Sin movimientos en el rango</td></tr>';

        document.getElementById('cz-extracto-tbody').innerHTML = d.lineas_extracto.map(function (l) {
            var accion = l.conciliado
                ? '<button class="btn-outline" style="padding:2px 8px;font-size:10px;" onclick="desconciliarLineaTz(' + l.id + ')">Desconciliar</button>'
                : '<button class="btn-outline" style="padding:2px 8px;font-size:10px;color:#DC2626;" onclick="eliminarLineaExtractoTz(' + l.id + ')">✕</button>';
            return '<tr' + (l.conciliado ? ' style="opacity:.5;"' : '') + '><td>' + fmtFechaTz(l.fecha) + '</td><td>' + escTz(l.descripcion) + '</td>' +
                '<td class="td-money ' + (l.valor >= 0 ? 'positivo' : 'negativo') + '">' + fmtMoneyTz(l.valor) + '</td><td>' + accion + '</td></tr>';
        }).join('') || '<tr><td colspan="4" style="text-align:center;color:#9CA3AF;padding:14px;">Sin líneas cargadas</td></tr>';
    }

    window.agregarLineaExtractoTz = function () {
        var valor = Number(document.getElementById('cz-nueva-valor').value);
        var descripcion = document.getElementById('cz-nueva-desc').value.trim();
        var fecha = document.getElementById('cz-nueva-fecha').value;
        if (!valor) { notifTz('Ingrese un valor distinto de cero (negativo si es un cargo).', 'error'); return; }
        if (!descripcion) { notifTz('Ingrese una descripción.', 'error'); return; }

        fetch('/conciliacion-bancaria/lineas', {
            method: 'POST', headers: Object.assign({ 'Content-Type': 'application/json' }, hdrsTz()),
            body: JSON.stringify({ cuenta_tesoreria_id: TZ.cuentaSeleccionada, fecha: fecha, descripcion: descripcion, valor: valor }),
        }).then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
            .then(function (r) {
                if (!r.ok) throw new Error(r.data.message || Object.values(r.data.errors || {}).flat().join(' ') || 'No se pudo añadir la línea.');
                document.getElementById('cz-nueva-desc').value = '';
                document.getElementById('cz-nueva-valor').value = '';
                cargarConciliacionTz();
            }).catch(function (e) { notifTz(e.message, 'error'); });
    };

    window.eliminarLineaExtractoTz = function (id) {
        fetch('/conciliacion-bancaria/lineas/' + id, { method: 'DELETE', headers: hdrsTz() })
            .then(function (r) { return r.json(); }).then(function () { cargarConciliacionTz(); });
    };

    window.marcarConciliadoManualTz = function (movimientoId) {
        var idLinea = window.prompt('ID de la línea del extracto con la que quiere conciliar este movimiento (véala en la tabla de la derecha):');
        if (!idLinea) return;
        conciliarParTz(Number(idLinea), movimientoId);
    };

    function conciliarParTz(lineaId, movimientoId) {
        fetch('/conciliacion-bancaria/lineas/' + lineaId + '/conciliar', {
            method: 'POST', headers: Object.assign({ 'Content-Type': 'application/json' }, hdrsTz()), body: JSON.stringify({ movimiento_contable_id: movimientoId }),
        }).then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
            .then(function (r) { if (!r.ok) throw new Error(r.data.message || 'No se pudo conciliar.'); cargarConciliacionTz(); })
            .catch(function (e) { notifTz(e.message, 'error'); });
    }

    window.desconciliarLineaTz = function (id) {
        fetch('/conciliacion-bancaria/lineas/' + id + '/desconciliar', { method: 'POST', headers: hdrsTz() })
            .then(function (r) { return r.json(); }).then(function () { cargarConciliacionTz(); });
    };

    window.aplicarSugerenciasTz = function () {
        var sugerencias = (CZ.datos && CZ.datos.sugerencias) || [];
        Promise.all(sugerencias.map(function (s) {
            return fetch('/conciliacion-bancaria/lineas/' + s.linea_extracto_id + '/conciliar', {
                method: 'POST', headers: Object.assign({ 'Content-Type': 'application/json' }, hdrsTz()), body: JSON.stringify({ movimiento_contable_id: s.movimiento_contable_id }),
            });
        })).then(function () { notifTz('Sugerencias aplicadas.', 'success'); cargarConciliacionTz(); });
    };
</script>
