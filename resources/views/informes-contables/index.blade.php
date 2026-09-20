<style>
    .sec-header { display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; }
    .sec-title { font-size: 17px; font-weight: 600; color: #111827; letter-spacing: -0.3px; }
    .sec-subtitle { font-size: 12px; color: #6B7280; margin-top: 2px; }

    .metrics-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 10px; margin-bottom: 16px; }
    .metric-card { background: #fff; border: 1px solid #EAECF0; border-radius: 10px; padding: 12px 14px; position: relative; overflow: hidden; }
    .metric-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--accent, #1D4ED8); }
    .metric-label { font-size: 11px; font-weight: 500; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.5px; }
    .metric-value { font-size: 18px; font-weight: 700; color: #111827; margin-top: 4px; letter-spacing: -0.5px; }
    .metric-sub { font-size: 11px; color: #6B7280; margin-top: 2px; }

    /* ── Tabs de reporte ── */
    .if-tabs { display: flex; gap: 4px; background: #F3F4F6; border-radius: 10px; padding: 4px; margin-bottom: 12px; overflow-x: auto; }
    .if-tab { flex: 1; white-space: nowrap; text-align: center; padding: 8px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; color: #6B7280; cursor: pointer; border: none; background: transparent; transition: all .15s; }
    .if-tab.activo { background: #fff; color: #1D4ED8; box-shadow: 0 1px 2px rgba(16,24,40,.08); }
    .if-tab:hover:not(.activo) { color: #374151; }

    .filter-bar { background: #fff; border: 1px solid #EAECF0; border-radius: 10px; padding: 12px 14px; display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-bottom: 12px; }
    .fi-group { display: flex; align-items: center; gap: 6px; flex: 1; min-width: 160px; position: relative; }
    .fi-label { font-size: 12px; color: #6B7280; white-space: nowrap; }
    .fi-input { flex: 1; border: 1px solid #D1D5DB; border-radius: 7px; padding: 6px 10px; font-size: 12px; color: #111827; background: #F9FAFB; outline: none; transition: border .15s; }
    .fi-input:focus { border-color: #1D4ED8; background: #fff; }
    .fi-input::placeholder { color: #9CA3AF; }

    .btn-primary { display: inline-flex; align-items: center; gap: 6px; background: #1D4ED8; color: #fff; border: none; border-radius: 7px; padding: 7px 14px; font-size: 12px; font-weight: 600; cursor: pointer; transition: background .15s; white-space: nowrap; }
    .btn-primary:hover { background: #1e40af; }
    .btn-outline { display: inline-flex; align-items: center; gap: 5px; background: #fff; color: #374151; border: 1px solid #D1D5DB; border-radius: 7px; padding: 7px 12px; font-size: 12px; font-weight: 500; cursor: pointer; transition: all .15s; white-space: nowrap; }
    .btn-outline:hover { background: #F3F4F6; border-color: #9CA3AF; }

    .if-resultados-buscador { position: absolute; z-index: 30; top: calc(100% + 4px); left: 0; right: 0; display: none; max-height: 220px; overflow-y: auto; border: 1px solid #D1D5DB; border-radius: 8px; background: #fff; box-shadow: 0 10px 25px rgba(16,24,40,.14); }
    .if-resultados-buscador.open { display: block; }
    .if-opcion { width: 100%; display: flex; align-items: baseline; gap: 8px; padding: 8px 12px; border: 0; border-bottom: 1px solid #F3F4F6; background: #fff; color: #374151; text-align: left; cursor: pointer; font-size: 12.5px; }
    .if-opcion:last-child { border-bottom: none; }
    .if-opcion:hover { background: #EFF6FF; }
    .if-opcion-mono { font-family: 'JetBrains Mono','Fira Mono',monospace; font-size: 12px; color: #1D4ED8; font-weight: 600; flex-shrink: 0; }
    .if-opcion-vacio { padding: 14px 12px; text-align: center; color: #9CA3AF; font-size: 12px; }

    .table-wrapper { background: #fff; border: 1px solid #EAECF0; border-radius: 10px; overflow: hidden; }
    .table-scroll { overflow-x: auto; }
    table.if-tbl { width: 100%; border-collapse: collapse; font-size: 12.5px; }
    table.if-tbl thead { background: #F8FAFC; border-bottom: 1px solid #EAECF0; }
    table.if-tbl thead th { padding: 10px 12px; text-align: left; font-size: 11px; font-weight: 600; color: #6B7280; text-transform: uppercase; letter-spacing: .5px; white-space: nowrap; }
    table.if-tbl tbody tr { border-bottom: 1px solid #F3F4F6; }
    table.if-tbl tbody tr:hover { background: #F8FAFC; }
    table.if-tbl tbody tr.if-grupo td { background: #F8FAFC; font-weight: 700; color: #111827; text-transform: uppercase; font-size: 11px; letter-spacing: .4px; }
    table.if-tbl tbody tr.if-total td { font-weight: 700; color: #111827; border-top: 2px solid #E5E7EB; }
    table.if-tbl td { padding: 8px 12px; color: #374151; vertical-align: middle; }
    .td-mono { font-family: 'JetBrains Mono','Fira Mono',monospace; font-size: 12px; color: #1D4ED8; font-weight: 600; }
    .td-money { font-weight: 600; color: #111827; text-align: right; white-space: nowrap; }
    .td-money.debito { color: #1D4ED8; }
    .td-money.credito { color: #B45309; }
    .td-money.positivo { color: #059669; }
    .td-money.negativo { color: #DC2626; }

    .badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; white-space: nowrap; }
    .badge-green { background: #ECFDF5; color: #065F46; }
    .badge-red { background: #FEF2F2; color: #991B1B; }

    .spinner-cell { display: flex; align-items: center; justify-content: center; padding: 40px; color: #6B7280; font-size: 13px; gap: 10px; }
    @keyframes spin-if { to { transform: rotate(360deg); } }
    .spinner { width: 18px; height: 18px; border: 2px solid #E5E7EB; border-top-color: #1D4ED8; border-radius: 50%; animation: spin-if .7s linear infinite; }

    @media (max-width: 640px) { .metrics-row { grid-template-columns: 1fr 1fr; } }
</style>

<div id="view-informes">
    <div class="sec-header">
        <div>
            <p class="sec-title">📈 Informes Contables</p>
            <p class="sec-subtitle">Balance de prueba, libros oficiales y estados financieros</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button class="btn-outline" onclick="exportarInformeCsv()">⬇️ Exportar CSV</button>
            <button class="btn-primary" onclick="imprimirInforme()">🖨️ Imprimir</button>
        </div>
    </div>

    <div class="if-tabs" id="if-tabs">
        <button class="if-tab activo" data-reporte="balance-prueba">Balance de Prueba</button>
        <button class="if-tab" data-reporte="libro-diario">Libro Diario</button>
        <button class="if-tab" data-reporte="libro-mayor">Libro Mayor</button>
        <button class="if-tab" data-reporte="libro-auxiliar">Libro Auxiliar</button>
        <button class="if-tab" data-reporte="estado-resultados">Estado de Resultados</button>
        <button class="if-tab" data-reporte="balance-general">Balance General</button>
        <button class="if-tab" data-reporte="iva-periodo">IVA</button>
        <button class="if-tab" data-reporte="retenciones">Retenciones</button>
        <button class="if-tab" data-reporte="comparativo">Comparativo</button>
        <button class="if-tab" data-reporte="indicadores">Indicadores</button>
    </div>

    <div class="filter-bar">
        <div class="fi-group" id="if-grupo-desde">
            <span class="fi-label">Desde</span>
            <input autocomplete="off" type="date" id="if-desde" class="fi-input">
        </div>
        <div class="fi-group" id="if-grupo-hasta">
            <span class="fi-label">Hasta</span>
            <input autocomplete="off" type="date" id="if-hasta" class="fi-input">
        </div>
        <div class="fi-group hidden" id="if-grupo-cuenta">
            <span class="fi-label">Cuenta</span>
            <input type="text" id="if-cuenta-buscar" class="fi-input" autocomplete="off" placeholder="Buscar por código o nombre…">
            <input type="hidden" id="if-cuenta-id">
            <div id="if-cuenta-resultados" class="if-resultados-buscador"></div>
        </div>
        <div class="fi-group hidden" id="if-grupo-tercero">
            <span class="fi-label">Tercero</span>
            <input type="text" id="if-tercero-buscar" class="fi-input" autocomplete="off" placeholder="Todos (opcional)…">
            <input type="hidden" id="if-tercero-id">
            <div id="if-tercero-resultados" class="if-resultados-buscador"></div>
        </div>
        <button class="btn-primary" onclick="consultarInforme()">🔍 Consultar</button>
    </div>

    <div class="metrics-row" id="if-metrics" style="display:none;"></div>

    <div class="table-wrapper">
        <div class="table-scroll">
            <div id="if-resultado">
                <div class="spinner-cell">📊 Seleccione un informe y presione "Consultar"</div>
            </div>
        </div>
    </div>
</div>

<script>
    var IF = { reporte: 'balance-prueba', datos: null, cuentas: [], cuentasResultado: [], tercerosResultado: [] };

    function escIf(s) { return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
    function fmtMoneyIf(n) { return '$ ' + (Number(n) || 0).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 }); }
    function fmtFechaIf(s) { if (!s) return '—'; var p = String(s).slice(0, 10).split('-'); return p.length === 3 ? p[2] + '/' + p[1] + '/' + p[0] : s; }
    function hdrsIf() { return { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content, 'Accept': 'application/json' }; }

    (function initInformes() {
        var hoy = new Date();
        var primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        function iso(d) { return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0'); }
        document.getElementById('if-desde').value = iso(primerDia);
        document.getElementById('if-hasta').value = iso(hoy);

        document.querySelectorAll('.if-tab').forEach(function (tab) {
            tab.addEventListener('click', function () { cambiarReporteIf(tab.dataset.reporte); });
        });

        fetch('/informes-contables/cuentas', { headers: hdrsIf() }).then(function (r) { return r.json(); }).then(function (res) {
            IF.cuentas = res.data || [];
        }).catch(function () {});

        cambiarReporteIf('balance-prueba');
    })();

    function cambiarReporteIf(reporte) {
        IF.reporte = reporte;
        document.querySelectorAll('.if-tab').forEach(function (t) { t.classList.toggle('activo', t.dataset.reporte === reporte); });

        var necesitaCuenta = reporte === 'libro-mayor' || reporte === 'libro-auxiliar';
        var necesitaTercero = reporte === 'libro-auxiliar';
        var soloHasta = reporte === 'balance-general';

        // Se usa style.display (no la clase .hidden) porque .fi-group ya fija
        // display:flex con la misma especificidad y podía ganarle a .hidden.
        document.getElementById('if-grupo-cuenta').style.display = necesitaCuenta ? '' : 'none';
        document.getElementById('if-grupo-tercero').style.display = necesitaTercero ? '' : 'none';
        document.getElementById('if-grupo-desde').style.display = soloHasta ? 'none' : '';

        document.getElementById('if-metrics').style.display = 'none';
        document.getElementById('if-metrics').innerHTML = '';
        document.getElementById('if-resultado').innerHTML = '<div class="spinner-cell">📊 Presione "Consultar" para generar el informe</div>';
    }

    /* ════════════════════════════════════════════════
       BUSCADOR DE CUENTA (Mayor / Auxiliar)
    ════════════════════════════════════════════════ */
    function renderResultadosCuentaIf() {
        var contenedor = document.getElementById('if-cuenta-resultados');
        var texto = (document.getElementById('if-cuenta-buscar').value || '').toLowerCase().trim();
        var coincidencias = IF.cuentas.filter(function (c) { return !texto || (c.codigo + ' ' + c.nombre).toLowerCase().includes(texto); });

        if (!coincidencias.length) {
            contenedor.innerHTML = '<div class="if-opcion-vacio">Sin coincidencias</div>';
        } else {
            contenedor.innerHTML = coincidencias.slice(0, 30).map(function (c) {
                return '<button type="button" class="if-opcion" onclick="seleccionarCuentaIf(' + c.id + ')">' +
                    '<span class="if-opcion-mono">' + escIf(c.codigo) + '</span><span>' + escIf(c.nombre) + '</span></button>';
            }).join('');
        }
        contenedor.classList.add('open');
    }

    window.seleccionarCuentaIf = function (id) {
        var c = IF.cuentas.find(function (x) { return Number(x.id) === Number(id); });
        if (!c) return;
        document.getElementById('if-cuenta-id').value = c.id;
        document.getElementById('if-cuenta-buscar').value = c.codigo + ' - ' + c.nombre;
        document.getElementById('if-cuenta-resultados').classList.remove('open');
    };

    var ajTercerosIf = [];
    function buscarTerceroIf() {
        var texto = document.getElementById('if-tercero-buscar').value.trim();
        document.getElementById('if-tercero-id').value = '';
        if (texto.length < 2) { document.getElementById('if-tercero-resultados').classList.remove('open'); return; }
        fetch('/terceros/buscar?query=' + encodeURIComponent(texto), { headers: hdrsIf() }).then(function (r) { return r.json(); }).then(function (data) {
            ajTercerosIf = Array.isArray(data) ? data : (data.data || []);
            var contenedor = document.getElementById('if-tercero-resultados');
            if (!ajTercerosIf.length) {
                contenedor.innerHTML = '<div class="if-opcion-vacio">Sin coincidencias</div>';
            } else {
                contenedor.innerHTML = ajTercerosIf.map(function (t) {
                    var doc = t.cedula || t.nit || '—';
                    var nombre = t.nombre_completo || ((t.nombre || '') + ' ' + (t.apellido || '')).trim() || t.razon_social || 'Sin nombre';
                    return '<button type="button" class="if-opcion" onclick="seleccionarTerceroIf(' + t.id + ')">' +
                        '<span>' + escIf(nombre) + '</span><span class="if-opcion-mono">' + escIf(doc) + '</span></button>';
                }).join('');
            }
            contenedor.classList.add('open');
        }).catch(function () {});
    }

    window.seleccionarTerceroIf = function (id) {
        var t = ajTercerosIf.find(function (x) { return Number(x.id) === Number(id); });
        if (!t) return;
        var nombre = t.nombre_completo || ((t.nombre || '') + ' ' + (t.apellido || '')).trim() || t.razon_social || 'Sin nombre';
        document.getElementById('if-tercero-id').value = t.id;
        document.getElementById('if-tercero-buscar').value = nombre;
        document.getElementById('if-tercero-resultados').classList.remove('open');
    };

    var buscarTerceroIfDebounced = debounce(buscarTerceroIf, 300);

    document.getElementById('if-cuenta-buscar').addEventListener('input', function () { document.getElementById('if-cuenta-id').value = ''; renderResultadosCuentaIf(); });
    document.getElementById('if-cuenta-buscar').addEventListener('focus', renderResultadosCuentaIf);
    document.getElementById('if-tercero-buscar').addEventListener('input', buscarTerceroIfDebounced);

    document.addEventListener('click', function (e) {
        if (!e.target.closest('#if-grupo-cuenta')) document.getElementById('if-cuenta-resultados').classList.remove('open');
        if (!e.target.closest('#if-grupo-tercero')) document.getElementById('if-tercero-resultados').classList.remove('open');
    });

    /* ════════════════════════════════════════════════
       CONSULTAR
    ════════════════════════════════════════════════ */
    function consultarInforme() {
        var desde = document.getElementById('if-desde').value;
        var hasta = document.getElementById('if-hasta').value;
        var resultado = document.getElementById('if-resultado');

        if (!hasta) { notifIf('Seleccione la fecha final.', 'error'); return; }

        if (IF.reporte === 'comparativo') { consultarComparativo(desde, hasta); return; }

        var url, params = new URLSearchParams();
        if (IF.reporte !== 'balance-general') { if (!desde) { notifIf('Seleccione la fecha inicial.', 'error'); return; } params.set('desde', desde); }
        params.set('hasta', hasta);

        if (IF.reporte === 'libro-mayor' || IF.reporte === 'libro-auxiliar') {
            var cuentaId = document.getElementById('if-cuenta-id').value;
            if (!cuentaId) { notifIf('Busque y seleccione una cuenta contable.', 'error'); return; }
            params.set('cuenta_id', cuentaId);
            if (IF.reporte === 'libro-auxiliar') {
                var terceroId = document.getElementById('if-tercero-id').value;
                if (terceroId) params.set('tercero_id', terceroId);
            }
        }

        url = '/informes-contables/' + IF.reporte + '?' + params.toString();
        resultado.innerHTML = '<div class="spinner-cell"><div class="spinner"></div>Generando informe…</div>';
        document.getElementById('if-metrics').style.display = 'none';

        fetch(url, { headers: hdrsIf() })
            .then(function (r) { return r.json().then(function (d) { if (!r.ok) throw new Error(d.message || Object.values(d.errors || {}).flat().join(' ') || 'No fue posible generar el informe.'); return d; }); })
            .then(function (data) {
                IF.datos = data;
                RENDERERS[IF.reporte](data);
            })
            .catch(function (e) {
                resultado.innerHTML = '<div class="spinner-cell">⚠️ ' + escIf(e.message) + '</div>';
                notifIf(e.message, 'error');
            });
    }

    function renderMetricasIf(tarjetas) {
        var cont = document.getElementById('if-metrics');
        cont.innerHTML = tarjetas.map(function (t) {
            return '<div class="metric-card" style="--accent:' + t.accent + '"><p class="metric-label">' + escIf(t.label) + '</p>' +
                '<p class="metric-value">' + t.value + '</p>' + (t.sub ? '<p class="metric-sub">' + escIf(t.sub) + '</p>' : '') + '</div>';
        }).join('');
        cont.style.display = 'grid';
    }

    /* ════════════════════════════════════════════════
       RENDER — BALANCE DE PRUEBA
    ════════════════════════════════════════════════ */
    function renderBalancePrueba(data) {
        var t = data.totales;
        var cuadrado = Math.abs(t.saldo_final_debito - t.saldo_final_credito) < 0.5;
        renderMetricasIf([
            { label: 'Cuentas con movimiento', value: data.filas.length, accent: '#1D4ED8' },
            { label: 'Total débitos', value: fmtMoneyIf(t.movimiento_debito), accent: '#1D4ED8' },
            { label: 'Total créditos', value: fmtMoneyIf(t.movimiento_credito), accent: '#D97706' },
            { label: 'Cuadre', value: cuadrado ? '✓ Cuadrado' : '⚠️ Revisar', accent: cuadrado ? '#059669' : '#DC2626' },
        ]);

        var filas = data.filas.map(function (f) {
            return '<tr>' +
                '<td><span class="td-mono">' + escIf(f.codigo) + '</span></td>' +
                '<td>' + escIf(f.nombre) + '</td>' +
                '<td class="td-money debito">' + (f.saldo_inicial_debito > 0 ? fmtMoneyIf(f.saldo_inicial_debito) : '—') + '</td>' +
                '<td class="td-money credito">' + (f.saldo_inicial_credito > 0 ? fmtMoneyIf(f.saldo_inicial_credito) : '—') + '</td>' +
                '<td class="td-money debito">' + (f.movimiento_debito > 0 ? fmtMoneyIf(f.movimiento_debito) : '—') + '</td>' +
                '<td class="td-money credito">' + (f.movimiento_credito > 0 ? fmtMoneyIf(f.movimiento_credito) : '—') + '</td>' +
                '<td class="td-money debito">' + (f.saldo_final_debito > 0 ? fmtMoneyIf(f.saldo_final_debito) : '—') + '</td>' +
                '<td class="td-money credito">' + (f.saldo_final_credito > 0 ? fmtMoneyIf(f.saldo_final_credito) : '—') + '</td>' +
                '</tr>';
        }).join('') || '<tr><td colspan="8"><div class="spinner-cell">📭 Sin movimientos en el período</div></td></tr>';

        document.getElementById('if-resultado').innerHTML =
            '<table class="if-tbl"><thead><tr><th>Código</th><th>Cuenta</th>' +
            '<th colspan="2" style="text-align:center;">Saldo Inicial</th><th colspan="2" style="text-align:center;">Movimiento</th><th colspan="2" style="text-align:center;">Saldo Final</th></tr>' +
            '<tr><th></th><th></th><th style="text-align:right;">Débito</th><th style="text-align:right;">Crédito</th>' +
            '<th style="text-align:right;">Débito</th><th style="text-align:right;">Crédito</th><th style="text-align:right;">Débito</th><th style="text-align:right;">Crédito</th></tr></thead>' +
            '<tbody>' + filas + '</tbody>' +
            '<tfoot><tr class="if-total"><td colspan="2">TOTALES</td>' +
            '<td class="td-money debito">' + fmtMoneyIf(t.saldo_inicial_debito) + '</td><td class="td-money credito">' + fmtMoneyIf(t.saldo_inicial_credito) + '</td>' +
            '<td class="td-money debito">' + fmtMoneyIf(t.movimiento_debito) + '</td><td class="td-money credito">' + fmtMoneyIf(t.movimiento_credito) + '</td>' +
            '<td class="td-money debito">' + fmtMoneyIf(t.saldo_final_debito) + '</td><td class="td-money credito">' + fmtMoneyIf(t.saldo_final_credito) + '</td></tr></tfoot></table>';
    }

    /* ════════════════════════════════════════════════
       RENDER — LIBRO DIARIO
    ════════════════════════════════════════════════ */
    function renderLibroDiario(data) {
        renderMetricasIf([
            { label: 'Movimientos', value: data.movimientos.length, accent: '#1D4ED8' },
            { label: 'Total débitos', value: fmtMoneyIf(data.total_debito), accent: '#1D4ED8' },
            { label: 'Total créditos', value: fmtMoneyIf(data.total_credito), accent: '#D97706' },
        ]);

        var filas = data.movimientos.map(function (m) {
            return '<tr>' +
                '<td>' + fmtFechaIf(m.fecha) + '</td>' +
                '<td><span class="td-mono">' + escIf(m.tipo) + ' ' + escIf(m.numero) + '</span></td>' +
                '<td>' + escIf(m.cuenta_codigo) + ' - ' + escIf(m.cuenta_nombre) + '</td>' +
                '<td class="td-trunc" title="' + escIf(m.detalle || '') + '">' + escIf(m.detalle || m.tercero || '—') + '</td>' +
                '<td class="td-money debito">' + (m.debito > 0 ? fmtMoneyIf(m.debito) : '—') + '</td>' +
                '<td class="td-money credito">' + (m.credito > 0 ? fmtMoneyIf(m.credito) : '—') + '</td>' +
                '</tr>';
        }).join('') || '<tr><td colspan="6"><div class="spinner-cell">📭 Sin movimientos en el período</div></td></tr>';

        document.getElementById('if-resultado').innerHTML =
            '<table class="if-tbl"><thead><tr><th>Fecha</th><th>Comprobante</th><th>Cuenta</th><th>Detalle</th>' +
            '<th style="text-align:right;">Débito</th><th style="text-align:right;">Crédito</th></tr></thead>' +
            '<tbody>' + filas + '</tbody>' +
            '<tfoot><tr class="if-total"><td colspan="4">TOTALES</td>' +
            '<td class="td-money debito">' + fmtMoneyIf(data.total_debito) + '</td><td class="td-money credito">' + fmtMoneyIf(data.total_credito) + '</td></tr></tfoot></table>';
    }

    /* ════════════════════════════════════════════════
       RENDER — LIBRO MAYOR / AUXILIAR (comparten forma)
    ════════════════════════════════════════════════ */
    // Muestra el saldo con su convención contable (Db/Cr) en vez de un signo
    // negativo crudo: una cuenta de naturaleza crédito con saldo normal (ej.
    // Ventas) se ve "$ X Cr", no "-$ X", que confundiría a cualquier contador.
    function fmtSaldoIf(valor) {
        valor = Number(valor) || 0;
        if (Math.abs(valor) < 0.5) return '$ 0';
        return fmtMoneyIf(Math.abs(valor)) + ' <small style="font-weight:600;">' + (valor > 0 ? 'Db' : 'Cr') + '</small>';
    }

    function renderLibroCuenta(data, esAuxiliar) {
        renderMetricasIf([
            { label: 'Cuenta', value: data.cuenta.codigo, sub: data.cuenta.nombre, accent: '#1D4ED8' },
            { label: 'Saldo inicial', value: fmtSaldoIf(data.saldo_inicial), accent: '#7C3AED' },
            { label: 'Movimiento débito', value: fmtMoneyIf(data.total_debito), accent: '#1D4ED8' },
            { label: 'Movimiento crédito', value: fmtMoneyIf(data.total_credito), accent: '#D97706' },
            { label: 'Saldo final', value: fmtSaldoIf(data.saldo_final), accent: '#059669' },
        ]);

        var filas = data.movimientos.map(function (m) {
            var extra = esAuxiliar ? ('<td>' + escIf(m.tercero || '—') + '</td><td>' + escIf(m.centro_costo || '—') + '</td>') : ('<td>' + escIf(m.tercero || '—') + '</td>');
            return '<tr>' +
                '<td>' + fmtFechaIf(m.fecha) + '</td>' +
                '<td><span class="td-mono">' + escIf(m.tipo) + ' ' + escIf(m.numero) + '</span></td>' +
                '<td>' + escIf(m.detalle || '—') + '</td>' +
                extra +
                '<td class="td-money debito">' + (m.debito > 0 ? fmtMoneyIf(m.debito) : '—') + '</td>' +
                '<td class="td-money credito">' + (m.credito > 0 ? fmtMoneyIf(m.credito) : '—') + '</td>' +
                '<td class="td-money">' + fmtSaldoIf(m.saldo) + '</td>' +
                '</tr>';
        }).join('') || '<tr><td colspan="' + (esAuxiliar ? 8 : 7) + '"><div class="spinner-cell">📭 Sin movimientos en el período</div></td></tr>';

        var colTerceroExtra = esAuxiliar ? '<th>Tercero</th><th>Centro Costo</th>' : '<th>Tercero</th>';

        document.getElementById('if-resultado').innerHTML =
            '<table class="if-tbl"><thead><tr><th>Fecha</th><th>Comprobante</th><th>Detalle</th>' + colTerceroExtra +
            '<th style="text-align:right;">Débito</th><th style="text-align:right;">Crédito</th><th style="text-align:right;">Saldo</th></tr></thead>' +
            '<tbody><tr><td colspan="' + (esAuxiliar ? 8 : 7) + '" style="font-style:italic;color:#6B7280;">Saldo inicial: ' + fmtSaldoIf(data.saldo_inicial) + '</td></tr>' + filas + '</tbody>' +
            '<tfoot><tr class="if-total"><td colspan="' + (esAuxiliar ? 5 : 4) + '">TOTALES</td>' +
            '<td class="td-money debito">' + fmtMoneyIf(data.total_debito) + '</td><td class="td-money credito">' + fmtMoneyIf(data.total_credito) + '</td>' +
            '<td class="td-money">' + fmtSaldoIf(data.saldo_final) + '</td></tr></tfoot></table>';
    }

    /* ════════════════════════════════════════════════
       RENDER — ESTADO DE RESULTADOS
    ════════════════════════════════════════════════ */
    function filaGrupoIf(titulo) { return '<tr class="if-grupo"><td colspan="2">' + escIf(titulo) + '</td></tr>'; }
    function filaCuentaIf(c) {
        return '<tr><td><span class="td-mono">' + escIf(c.codigo) + '</span></td><td colspan="1">' + escIf(c.nombre) + '</td>' +
            '<td class="td-money ' + (c.valor >= 0 ? 'positivo' : 'negativo') + '">' + fmtMoneyIf(c.valor) + '</td></tr>';
    }

    function renderEstadoResultados(data) {
        renderMetricasIf([
            { label: 'Ingresos', value: fmtMoneyIf(data.ingresos.total), accent: '#059669' },
            { label: 'Costos', value: fmtMoneyIf(data.costos.total), accent: '#D97706' },
            { label: 'Gastos', value: fmtMoneyIf(data.gastos.total), accent: '#DC2626' },
            { label: 'Utilidad neta', value: fmtMoneyIf(data.utilidadNeta), accent: data.utilidadNeta >= 0 ? '#1D4ED8' : '#DC2626' },
        ]);

        var html = '<tr class="if-grupo"><td colspan="2">INGRESOS</td><td></td></tr>';
        html += data.ingresos.cuentas.map(filaCuentaIf).join('');
        html += '<tr class="if-total"><td colspan="2">Total Ingresos</td><td class="td-money positivo">' + fmtMoneyIf(data.ingresos.total) + '</td></tr>';

        html += '<tr class="if-grupo"><td colspan="2">COSTO DE VENTAS</td><td></td></tr>';
        html += data.costos.cuentas.map(filaCuentaIf).join('');
        html += '<tr class="if-total"><td colspan="2">Total Costos</td><td class="td-money negativo">' + fmtMoneyIf(data.costos.total) + '</td></tr>';

        html += '<tr class="if-total" style="background:#EFF6FF;"><td colspan="2">UTILIDAD BRUTA</td><td class="td-money">' + fmtMoneyIf(data.utilidadBruta) + '</td></tr>';

        html += '<tr class="if-grupo"><td colspan="2">GASTOS OPERACIONALES</td><td></td></tr>';
        html += data.gastos.cuentas.map(filaCuentaIf).join('');
        html += '<tr class="if-total"><td colspan="2">Total Gastos</td><td class="td-money negativo">' + fmtMoneyIf(data.gastos.total) + '</td></tr>';

        html += '<tr class="if-total" style="background:#ECFDF5;font-size:14px;"><td colspan="2">UTILIDAD (PÉRDIDA) NETA</td><td class="td-money ' + (data.utilidadNeta >= 0 ? 'positivo' : 'negativo') + '">' + fmtMoneyIf(data.utilidadNeta) + '</td></tr>';

        document.getElementById('if-resultado').innerHTML =
            '<table class="if-tbl"><thead><tr><th>Código</th><th>Cuenta</th><th style="text-align:right;">Valor</th></tr></thead><tbody>' + html + '</tbody></table>';
    }

    /* ════════════════════════════════════════════════
       COMPARATIVO — período actual vs. el inmediatamente anterior
       de la misma duración (no requiere backend nuevo: reutiliza
       estado-resultados dos veces con rangos de fecha distintos).
    ════════════════════════════════════════════════ */
    function consultarComparativo(desde, hasta) {
        if (!desde) { notifIf('Seleccione la fecha inicial.', 'error'); return; }

        var msDia = 24 * 60 * 60 * 1000;
        var dDesde = new Date(desde + 'T00:00:00');
        var dHasta = new Date(hasta + 'T00:00:00');
        var duracionDias = Math.round((dHasta - dDesde) / msDia) + 1;
        var hastaAnteriorDate = new Date(dDesde.getTime() - msDia);
        var desdeAnteriorDate = new Date(hastaAnteriorDate.getTime() - (duracionDias - 1) * msDia);
        function iso(d) { return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0'); }
        var desdeAnterior = iso(desdeAnteriorDate), hastaAnterior = iso(hastaAnteriorDate);

        document.getElementById('if-resultado').innerHTML = '<div class="spinner-cell"><div class="spinner"></div>Comparando períodos…</div>';
        document.getElementById('if-metrics').style.display = 'none';

        Promise.all([
            fetch('/informes-contables/estado-resultados?desde=' + desde + '&hasta=' + hasta, { headers: hdrsIf() }).then(function (r) { return r.json(); }),
            fetch('/informes-contables/estado-resultados?desde=' + desdeAnterior + '&hasta=' + hastaAnterior, { headers: hdrsIf() }).then(function (r) { return r.json(); }),
        ]).then(function (res) {
            renderComparativo(res[0], res[1], { desde: desde, hasta: hasta }, { desde: desdeAnterior, hasta: hastaAnterior });
        }).catch(function () {
            document.getElementById('if-resultado').innerHTML = '<div class="spinner-cell">⚠️ No fue posible generar el comparativo.</div>';
        });
    }

    function variacionIf(actual, anterior) {
        if (!anterior) return actual > 0 ? '+100%' : '—';
        var pct = ((actual - anterior) / Math.abs(anterior)) * 100;
        var signo = pct >= 0 ? '+' : '';
        return signo + pct.toFixed(1) + '%';
    }

    function renderComparativo(actual, anterior, rangoA, rangoB) {
        renderMetricasIf([
            { label: 'Ingresos (actual)', value: fmtMoneyIf(actual.ingresos.total), sub: variacionIf(actual.ingresos.total, anterior.ingresos.total), accent: '#059669' },
            { label: 'Utilidad neta (actual)', value: fmtMoneyIf(actual.utilidadNeta), sub: variacionIf(actual.utilidadNeta, anterior.utilidadNeta), accent: actual.utilidadNeta >= 0 ? '#1D4ED8' : '#DC2626' },
        ]);

        function filaComp(label, a, b) {
            var variacion = variacionIf(a, b);
            var claseVar = a >= b ? 'positivo' : 'negativo';
            return '<tr><td>' + escIf(label) + '</td><td class="td-money">' + fmtMoneyIf(a) + '</td><td class="td-money">' + fmtMoneyIf(b) + '</td>' +
                '<td class="td-money ' + claseVar + '">' + variacion + '</td></tr>';
        }

        var html = filaComp('Ingresos', actual.ingresos.total, anterior.ingresos.total) +
            filaComp('Costos', actual.costos.total, anterior.costos.total) +
            '<tr class="if-total">' + filaComp('Utilidad Bruta', actual.utilidadBruta, anterior.utilidadBruta).slice(4) +
            filaComp('Gastos Operacionales', actual.gastos.total, anterior.gastos.total) +
            '<tr class="if-total" style="background:#ECFDF5;font-size:14px;">' + filaComp('Utilidad Neta', actual.utilidadNeta, anterior.utilidadNeta).slice(4);

        document.getElementById('if-resultado').innerHTML =
            '<table class="if-tbl"><thead><tr><th>Concepto</th><th style="text-align:right;">' + fmtFechaIf(rangoA.desde) + ' a ' + fmtFechaIf(rangoA.hasta) + '</th>' +
            '<th style="text-align:right;">' + fmtFechaIf(rangoB.desde) + ' a ' + fmtFechaIf(rangoB.hasta) + '</th><th style="text-align:right;">Variación</th></tr></thead><tbody>' + html + '</tbody></table>';
    }

    /* ════════════════════════════════════════════════
       RENDER — BALANCE GENERAL
    ════════════════════════════════════════════════ */
    function renderBalanceGeneral(data) {
        renderMetricasIf([
            { label: 'Activo', value: fmtMoneyIf(data.activo.total), accent: '#1D4ED8' },
            { label: 'Pasivo', value: fmtMoneyIf(data.pasivo.total), accent: '#D97706' },
            { label: 'Patrimonio', value: fmtMoneyIf(data.patrimonio.total + data.resultadoEjercicio), accent: '#7C3AED' },
            { label: 'Cuadre', value: data.cuadrado ? '✓ Cuadrado' : '⚠️ Diferencia ' + fmtMoneyIf(data.diferencia), accent: data.cuadrado ? '#059669' : '#DC2626' },
        ]);

        var html = '<tr class="if-grupo"><td colspan="2">ACTIVO</td><td></td></tr>';
        html += data.activo.cuentas.map(filaCuentaIf).join('') || '<tr><td colspan="3" style="text-align:center;color:#9CA3AF;">Sin saldos</td></tr>';
        html += '<tr class="if-total" style="background:#EFF6FF;"><td colspan="2">TOTAL ACTIVO</td><td class="td-money">' + fmtMoneyIf(data.activo.total) + '</td></tr>';

        html += '<tr class="if-grupo"><td colspan="2">PASIVO</td><td></td></tr>';
        html += data.pasivo.cuentas.map(filaCuentaIf).join('') || '<tr><td colspan="3" style="text-align:center;color:#9CA3AF;">Sin saldos</td></tr>';
        html += '<tr class="if-total"><td colspan="2">Total Pasivo</td><td class="td-money">' + fmtMoneyIf(data.pasivo.total) + '</td></tr>';

        html += '<tr class="if-grupo"><td colspan="2">PATRIMONIO</td><td></td></tr>';
        html += data.patrimonio.cuentas.map(filaCuentaIf).join('') || '<tr><td colspan="3" style="text-align:center;color:#9CA3AF;">Sin saldos</td></tr>';
        html += '<tr><td></td><td>Utilidad del Ejercicio (año actual)</td><td class="td-money ' + (data.utilidadEjercicioActual >= 0 ? 'positivo' : 'negativo') + '">' + fmtMoneyIf(data.utilidadEjercicioActual) + '</td></tr>';
        html += '<tr><td></td><td>Utilidades Acumuladas (años anteriores)</td><td class="td-money ' + (data.utilidadesAcumuladas >= 0 ? 'positivo' : 'negativo') + '">' + fmtMoneyIf(data.utilidadesAcumuladas) + '</td></tr>';
        html += '<tr class="if-total"><td colspan="2">Total Patrimonio</td><td class="td-money">' + fmtMoneyIf(data.patrimonio.total + data.resultadoEjercicio) + '</td></tr>';

        html += '<tr class="if-total" style="background:' + (data.cuadrado ? '#ECFDF5' : '#FEF2F2') + ';font-size:14px;"><td colspan="2">TOTAL PASIVO + PATRIMONIO</td><td class="td-money">' + fmtMoneyIf(data.totalPasivoPatrimonio) + '</td></tr>';

        document.getElementById('if-resultado').innerHTML =
            '<table class="if-tbl"><thead><tr><th>Código</th><th>Cuenta</th><th style="text-align:right;">Valor</th></tr></thead><tbody>' + html + '</tbody></table>';
    }

    /* ════════════════════════════════════════════════
       RENDER — IVA DEL PERÍODO (Formulario 300)
    ════════════════════════════════════════════════ */
    function renderIvaPeriodo(data) {
        renderMetricasIf([
            { label: 'IVA Generado', value: fmtMoneyIf(data.ivaGenerado), accent: '#D97706' },
            { label: 'IVA Descontable', value: fmtMoneyIf(data.ivaDescontable), accent: '#1D4ED8' },
            { label: data.aPagar ? 'IVA a Pagar' : 'Saldo a Favor', value: fmtMoneyIf(data.valorAbsoluto), accent: data.aPagar ? '#DC2626' : '#059669' },
        ]);

        var html = '<tr><td>IVA Generado (ventas)</td><td class="td-money positivo">' + fmtMoneyIf(data.ivaGenerado) + '</td></tr>';
        html += '<tr><td>IVA Descontable (compras)</td><td class="td-money negativo">' + fmtMoneyIf(data.ivaDescontable) + '</td></tr>';
        html += '<tr class="if-total" style="background:' + (data.aPagar ? '#FEF2F2' : '#ECFDF5') + ';font-size:14px;">' +
            '<td>' + (data.aPagar ? 'IVA A PAGAR' : 'SALDO A FAVOR') + '</td>' +
            '<td class="td-money ' + (data.aPagar ? 'negativo' : 'positivo') + '">' + fmtMoneyIf(data.valorAbsoluto) + '</td></tr>';

        document.getElementById('if-resultado').innerHTML =
            '<table class="if-tbl"><thead><tr><th>Concepto</th><th style="text-align:right;">Valor</th></tr></thead><tbody>' + html + '</tbody></table>';
    }

    /* ════════════════════════════════════════════════
       RENDER — RETENCIONES PRACTICADAS (Formulario 350)
    ════════════════════════════════════════════════ */
    function renderRetenciones(data) {
        var tipos = data.tipos;
        IF.retencionesData = data; // usado por generarCertificadoRetencion()
        renderMetricasIf([
            { label: 'Retefuente', value: fmtMoneyIf(tipos.retefuente.total), accent: '#1D4ED8' },
            { label: 'Reteiva', value: fmtMoneyIf(tipos.reteiva.total), accent: '#D97706' },
            { label: 'Reteica', value: fmtMoneyIf(tipos.reteica.total), accent: '#7C3AED' },
            { label: 'Total retenido', value: fmtMoneyIf(data.totalGeneral), accent: '#059669' },
        ]);

        var html = '';
        ['retefuente', 'reteiva', 'reteica'].forEach(function (codigo) {
            var t = tipos[codigo];
            html += '<tr class="if-grupo"><td colspan="2">' + escIf(t.nombre.toUpperCase()) + '</td></tr>';
            if (!t.porTercero.length) {
                html += '<tr><td colspan="2" style="text-align:center;color:#9CA3AF;">Sin retenciones practicadas</td></tr>';
            } else {
                html += t.porTercero.map(function (f) {
                    return '<tr><td>' + escIf(f.tercero) + '</td><td class="td-money positivo">' + fmtMoneyIf(f.valor) + '</td></tr>';
                }).join('');
            }
            html += '<tr class="if-total"><td>Subtotal ' + escIf(t.nombre) + '</td><td class="td-money">' + fmtMoneyIf(t.total) + '</td></tr>';
        });
        html += '<tr class="if-total" style="background:#ECFDF5;font-size:14px;"><td>TOTAL RETENIDO</td><td class="td-money positivo">' + fmtMoneyIf(data.totalGeneral) + '</td></tr>';

        // Consolidado por tercero (para emitir el certificado anual): un mismo
        // proveedor puede aparecer en retefuente, reteiva y reteica a la vez —
        // aquí se agrupan sus 3 valores en una sola fila con un botón de imprimir.
        var porTerceroUnico = {};
        ['retefuente', 'reteiva', 'reteica'].forEach(function (codigo) {
            tipos[codigo].porTercero.forEach(function (f) {
                var key = f.tercero_id || f.tercero;
                if (!porTerceroUnico[key]) porTerceroUnico[key] = { id: f.tercero_id, nombre: f.tercero, cedula: f.cedula, nit: f.nit, retefuente: 0, reteiva: 0, reteica: 0 };
                porTerceroUnico[key][codigo] = f.valor;
            });
        });
        var filasCert = Object.values(porTerceroUnico);
        var htmlCert = filasCert.length ? filasCert.map(function (f) {
            var total = f.retefuente + f.reteiva + f.reteica;
            return '<tr><td>' + escIf(f.nombre) + '</td><td class="td-money">' + fmtMoneyIf(total) + '</td>' +
                '<td style="text-align:right;"><button class="btn-outline" style="padding:4px 10px;font-size:11px;" onclick=\'generarCertificadoRetencion(' + JSON.stringify(f).replace(/'/g, "&#39;") + ')\'>🖨️ Certificado</button></td></tr>';
        }).join('') : '<tr><td colspan="3" style="text-align:center;color:#9CA3AF;">Sin terceros con retenciones en el período</td></tr>';

        document.getElementById('if-resultado').innerHTML =
            '<table class="if-tbl"><thead><tr><th>Tercero</th><th style="text-align:right;">Valor</th></tr></thead><tbody>' + html + '</tbody></table>' +
            '<p style="font-size:12px;font-weight:700;color:#111827;margin:20px 0 8px;">📄 Certificados por tercero (consolidado del período)</p>' +
            '<table class="if-tbl"><thead><tr><th>Tercero</th><th style="text-align:right;">Total retenido</th><th></th></tr></thead><tbody>' + htmlCert + '</tbody></table>';
    }

    window.generarCertificadoRetencion = function (f) {
        var desde = document.getElementById('if-desde').value;
        var hasta = document.getElementById('if-hasta').value;
        var doc = f.cedula || f.nit || 'No registrado';
        var total = f.retefuente + f.reteiva + f.reteica;

        var filas = [
            ['Retención en la Fuente (Renta)', f.retefuente],
            ['Retención de IVA (Reteiva)', f.reteiva],
            ['Retención de ICA (Reteica)', f.reteica],
        ].filter(function (r) { return r[1] > 0; });

        var html = '<!doctype html><html><head><meta charset="utf-8"><title>Certificado de Retención — ' + escIf(f.nombre) + '</title><style>' +
            '@page{size:letter;margin:20mm;}body{font-family:Arial,Helvetica,sans-serif;color:#111827;}' +
            '.head{text-align:center;border-bottom:3px solid #1D4ED8;padding-bottom:12px;margin-bottom:24px;}' +
            '.brand{font-weight:800;color:#1D4ED8;font-size:18px;}.tit{font-size:14px;font-weight:700;margin-top:6px;text-transform:uppercase;}' +
            'table{width:100%;border-collapse:collapse;margin-top:16px;font-size:13px;}th,td{padding:8px;border:1px solid #E5E7EB;text-align:left;}' +
            'th{background:#F8FAFC;}.money{text-align:right;}.total-row{font-weight:800;background:#ECFDF5;}' +
            '.firma{margin-top:70px;display:flex;justify-content:space-between;}.linea{border-top:1px solid #111827;width:220px;text-align:center;padding-top:6px;font-size:11px;}' +
            '.legal{margin-top:24px;font-size:11px;color:#6B7280;}' +
            '</style></head><body>' +
            '<div class="head"><div class="brand">📈 Nexora</div><div class="tit">Certificado de Retenciones</div>' +
            '<div style="font-size:12px;color:#6B7280;">Del ' + fmtFechaIf(desde) + ' al ' + fmtFechaIf(hasta) + '</div></div>' +
            '<p>Se certifica que a <b>' + escIf(f.nombre) + '</b>, identificado con documento <b>' + escIf(doc) + '</b>, ' +
            'se le practicaron las siguientes retenciones durante el período indicado:</p>' +
            '<table><thead><tr><th>Concepto</th><th class="money">Valor Retenido</th></tr></thead><tbody>' +
            filas.map(function (r) { return '<tr><td>' + r[0] + '</td><td class="money">' + fmtMoneyIf(r[1]) + '</td></tr>'; }).join('') +
            '<tr class="total-row"><td>TOTAL RETENIDO</td><td class="money">' + fmtMoneyIf(total) + '</td></tr>' +
            '</tbody></table>' +
            '<p class="legal">Este certificado se expide para los efectos previstos en el Estatuto Tributario Nacional, con base en los registros contables del sistema.</p>' +
            '<div class="firma"><div class="linea">Representante Legal / Contador</div><div class="linea">Fecha de expedición: ' + fmtFechaIf(new Date().toISOString().slice(0, 10)) + '</div></div>' +
            '</body></html>';

        var ventana = window.open('', '_blank', 'width=900,height=700');
        if (!ventana) { notifIf('El navegador bloqueó la ventana de impresión.', 'error'); return; }
        ventana.document.open(); ventana.document.write(html); ventana.document.close();
        setTimeout(function () { ventana.focus(); ventana.print(); }, 300);
    };

    function renderIndicadores(data) {
        renderMetricasIf([
            { label: 'Margen Bruto', value: data.margenBruto === null ? '—' : data.margenBruto + '%', accent: '#059669' },
            { label: 'Margen Neto', value: data.margenNeto === null ? '—' : data.margenNeto + '%', accent: data.margenNeto >= 0 ? '#1D4ED8' : '#DC2626' },
            { label: 'Endeudamiento', value: data.endeudamiento === null ? '—' : data.endeudamiento + '%', accent: '#D97706' },
            { label: 'ROE (Rentab. Patrimonio)', value: data.roe === null ? '—' : data.roe + '%', accent: '#7C3AED' },
        ]);

        function fila(nombre, formula, valor, interpretacion) {
            return '<tr><td><b>' + nombre + '</b><div style="font-size:11px;color:#9CA3AF;">' + formula + '</div></td>' +
                '<td class="td-money">' + (valor === null ? 'N/A' : valor + '%') + '</td>' +
                '<td style="font-size:12px;color:#6B7280;">' + interpretacion + '</td></tr>';
        }

        var html = '<tr class="if-grupo"><td colspan="3">RENTABILIDAD</td></tr>' +
            fila('Margen Bruto', 'Utilidad Bruta / Ingresos', data.margenBruto, 'De cada $100 vendidos, cuánto queda tras el costo de venta.') +
            fila('Margen Neto', 'Utilidad Neta / Ingresos', data.margenNeto, 'De cada $100 vendidos, cuánto queda como utilidad final.') +
            fila('ROA', 'Utilidad Neta / Activo Total', data.roa, 'Qué tan rentable es el activo total del negocio.') +
            fila('ROE', 'Utilidad Neta / Patrimonio', data.roe, 'Rentabilidad para el dueño sobre lo invertido.') +
            '<tr class="if-grupo"><td colspan="3">ENDEUDAMIENTO</td></tr>' +
            fila('Endeudamiento', 'Pasivo Total / Activo Total', data.endeudamiento, 'Qué porcentaje del activo está financiado con deuda.');

        document.getElementById('if-resultado').innerHTML =
            '<table class="if-tbl"><thead><tr><th>Indicador</th><th style="text-align:right;">Valor</th><th>Qué significa</th></tr></thead><tbody>' + html + '</tbody></table>' +
            '<p style="font-size:11px;color:#9CA3AF;padding:12px;">Activo total: ' + fmtMoneyIf(data.activoTotal) + ' · Pasivo total: ' + fmtMoneyIf(data.pasivoTotal) +
            ' · Patrimonio: ' + fmtMoneyIf(data.patrimonioTotal) + ' · Ingresos del período: ' + fmtMoneyIf(data.ingresos) + '</p>';
    }

    var RENDERERS = {
        'balance-prueba': renderBalancePrueba,
        'libro-diario': renderLibroDiario,
        'libro-mayor': function (d) { renderLibroCuenta(d, false); },
        'libro-auxiliar': function (d) { renderLibroCuenta(d, true); },
        'estado-resultados': renderEstadoResultados,
        'balance-general': renderBalanceGeneral,
        'iva-periodo': renderIvaPeriodo,
        'retenciones': renderRetenciones,
        'indicadores': renderIndicadores,
    };

    /* ════════════════════════════════════════════════
       IMPRIMIR / EXPORTAR
    ════════════════════════════════════════════════ */
    function tituloInformeIf() {
        return {
            'balance-prueba': 'Balance de Prueba',
            'libro-diario': 'Libro Diario',
            'libro-mayor': 'Libro Mayor',
            'libro-auxiliar': 'Libro Auxiliar',
            'estado-resultados': 'Estado de Resultados',
            'balance-general': 'Balance General',
            'iva-periodo': 'IVA del Período',
            'retenciones': 'Retenciones Practicadas',
            'comparativo': 'Comparativo de Períodos',
            'indicadores': 'Indicadores Financieros',
        }[IF.reporte];
    }

    window.imprimirInforme = function () {
        var tabla = document.querySelector('#if-resultado table');
        if (!tabla) { notifIf('Genere el informe antes de imprimir.', 'warning'); return; }

        var desde = document.getElementById('if-desde').value;
        var hasta = document.getElementById('if-hasta').value;
        var periodo = IF.reporte === 'balance-general' ? ('Corte al ' + fmtFechaIf(hasta)) : ('Del ' + fmtFechaIf(desde) + ' al ' + fmtFechaIf(hasta));

        var html = '<!doctype html><html><head><meta charset="utf-8"><title>' + tituloInformeIf() + '</title><style>' +
            '@page{size:letter landscape;margin:14mm;}html{background:#fff;color-scheme:light;}' +
            'body{font-family:Arial,Helvetica,sans-serif;color:#111827;background:#fff;margin:0;}' +
            '.head{display:flex;justify-content:space-between;align-items:flex-end;border-bottom:3px solid #1D4ED8;padding-bottom:12px;margin-bottom:16px;}' +
            '.brand{font-size:20px;font-weight:800;color:#1D4ED8;}.sub{font-size:11px;color:#6B7280;margin-top:2px;}.meta{text-align:right;font-size:12px;color:#374151;}' +
            'table{width:100%;border-collapse:collapse;font-size:11px;}th{background:#F8FAFC;text-align:left;padding:6px 8px;border-bottom:2px solid #E5E7EB;text-transform:uppercase;font-size:9px;color:#6B7280;}' +
            'td{padding:5px 8px;border-bottom:1px solid #F3F4F6;}' +
            '.foot{margin-top:20px;text-align:center;font-size:10px;color:#9CA3AF;border-top:1px solid #E5E7EB;padding-top:10px;}' +
            '</style></head><body><div class="head"><div><div class="brand">📈 Nexora</div><div class="sub">' + escIf(tituloInformeIf()) + '</div></div>' +
            '<div class="meta"><b>Período:</b> ' + escIf(periodo) + '<br><b>Generado:</b> ' + new Date().toLocaleString('es-CO') + '</div></div>' +
            tabla.outerHTML +
            '<div class="foot">Generado desde Nexora — Sistema de Gestión POS</div></body></html>';

        var ventana = window.open('', '_blank', 'width=1000,height=700');
        if (!ventana) { notifIf('El navegador bloqueó la ventana de impresión. Habilite las ventanas emergentes.', 'error'); return; }
        ventana.document.open(); ventana.document.write(html); ventana.document.close();
        setTimeout(function () { ventana.focus(); ventana.print(); }, 300);
    };

    window.exportarInformeCsv = function () {
        var tabla = document.querySelector('#if-resultado table');
        if (!tabla) { notifIf('Genere el informe antes de exportar.', 'warning'); return; }

        var filas = [];
        tabla.querySelectorAll('tr').forEach(function (tr) {
            var celdas = Array.from(tr.children).map(function (td) { return '"' + td.textContent.trim().replace(/"/g, '""') + '"'; });
            filas.push(celdas.join(','));
        });

        var csv = '﻿' + filas.join('\n');
        var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = tituloInformeIf().toLowerCase().replace(/ /g, '-') + '.csv';
        a.click();
        URL.revokeObjectURL(a.href);
        notifIf('Exportación creada.', 'success');
    };

    function notifIf(msg, tipo) {
        if (typeof mostrarNotificacion === 'function') { mostrarNotificacion(msg, tipo === 'error' ? 'error' : tipo === 'warning' ? 'warning' : 'success'); return; }
        window.alert(msg);
    }
</script>
