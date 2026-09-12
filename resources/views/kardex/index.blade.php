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

    .kx-tabs { display: flex; gap: 4px; background: #F3F4F6; border-radius: 10px; padding: 4px; margin-bottom: 12px; overflow-x: auto; }
    .kx-tab { flex: 1; white-space: nowrap; text-align: center; padding: 8px 12px; border-radius: 8px; font-size: 12px; font-weight: 600; color: #6B7280; cursor: pointer; border: none; background: transparent; transition: all .15s; }
    .kx-tab.activo { background: #fff; color: #1D4ED8; box-shadow: 0 1px 2px rgba(16,24,40,.08); }
    .kx-tab:hover:not(.activo) { color: #374151; }

    .filter-bar { background: #fff; border: 1px solid #EAECF0; border-radius: 10px; padding: 12px 14px; display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-bottom: 12px; }
    .fi-group { display: flex; align-items: center; gap: 6px; flex: 1; min-width: 160px; position: relative; }
    .fi-label { font-size: 12px; color: #6B7280; white-space: nowrap; }
    .fi-input, .fi-select { flex: 1; border: 1px solid #D1D5DB; border-radius: 7px; padding: 6px 10px; font-size: 12px; color: #111827; background: #F9FAFB; outline: none; transition: border .15s; }
    .fi-input:focus, .fi-select:focus { border-color: #1D4ED8; background: #fff; }
    .fi-input::placeholder { color: #9CA3AF; }

    .btn-primary { display: inline-flex; align-items: center; gap: 6px; background: #1D4ED8; color: #fff; border: none; border-radius: 7px; padding: 7px 14px; font-size: 12px; font-weight: 600; cursor: pointer; transition: background .15s; white-space: nowrap; }
    .btn-primary:hover { background: #1e40af; }
    .btn-outline { display: inline-flex; align-items: center; gap: 5px; background: #fff; color: #374151; border: 1px solid #D1D5DB; border-radius: 7px; padding: 7px 12px; font-size: 12px; font-weight: 500; cursor: pointer; transition: all .15s; white-space: nowrap; }
    .btn-outline:hover { background: #F3F4F6; border-color: #9CA3AF; }

    .kx-resultados { position: absolute; z-index: 30; top: calc(100% + 4px); left: 0; right: 0; display: none; max-height: 220px; overflow-y: auto; border: 1px solid #D1D5DB; border-radius: 8px; background: #fff; box-shadow: 0 10px 25px rgba(16,24,40,.14); }
    .kx-resultados.open { display: block; }
    .kx-opcion { width: 100%; display: flex; align-items: baseline; gap: 8px; padding: 8px 12px; border: 0; border-bottom: 1px solid #F3F4F6; background: #fff; color: #374151; text-align: left; cursor: pointer; font-size: 12.5px; }
    .kx-opcion:last-child { border-bottom: none; }
    .kx-opcion:hover { background: #EFF6FF; }
    .kx-opcion-mono { font-family: 'JetBrains Mono','Fira Mono',monospace; font-size: 12px; color: #1D4ED8; font-weight: 600; flex-shrink: 0; }
    .kx-opcion-vacio { padding: 14px 12px; text-align: center; color: #9CA3AF; font-size: 12px; }

    .table-wrapper { background: #fff; border: 1px solid #EAECF0; border-radius: 10px; overflow: hidden; }
    .table-scroll { overflow-x: auto; }
    table.kx-tbl { width: 100%; border-collapse: collapse; font-size: 12.5px; }
    table.kx-tbl thead { background: #F8FAFC; border-bottom: 1px solid #EAECF0; }
    table.kx-tbl thead th { padding: 10px 12px; text-align: left; font-size: 11px; font-weight: 600; color: #6B7280; text-transform: uppercase; letter-spacing: .5px; white-space: nowrap; }
    table.kx-tbl tbody tr { border-bottom: 1px solid #F3F4F6; }
    table.kx-tbl tbody tr:hover { background: #F8FAFC; }
    table.kx-tbl tbody tr.kx-total td, table.kx-tbl tfoot td { font-weight: 700; color: #111827; border-top: 2px solid #E5E7EB; }
    table.kx-tbl td { padding: 8px 12px; color: #374151; vertical-align: middle; }
    .td-mono { font-family: 'JetBrains Mono','Fira Mono',monospace; font-size: 12px; color: #1D4ED8; font-weight: 600; }
    .td-money { font-weight: 600; color: #111827; text-align: right; white-space: nowrap; }

    .badge { display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; white-space: nowrap; }
    .badge-green { background: #ECFDF5; color: #065F46; }
    .badge-red { background: #FEF2F2; color: #991B1B; }

    .spinner-cell { display: flex; align-items: center; justify-content: center; padding: 40px; color: #6B7280; font-size: 13px; gap: 10px; }
    @keyframes spin-kx { to { transform: rotate(360deg); } }
    .spinner { width: 18px; height: 18px; border: 2px solid #E5E7EB; border-top-color: #1D4ED8; border-radius: 50%; animation: spin-kx .7s linear infinite; }

    @media (max-width: 640px) { .metrics-row { grid-template-columns: 1fr 1fr; } }
</style>

<div id="view-kardex">
    <div class="sec-header">
        <div>
            <p class="sec-title">📦 Kardex y Costos</p>
            <p class="sec-subtitle">Historial de movimientos, costo promedio ponderado y valorización de existencias</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button class="btn-outline" onclick="exportarKardexCsv()">⬇️ Exportar CSV</button>
            <button class="btn-primary" onclick="imprimirKardex()">🖨️ Imprimir</button>
        </div>
    </div>

    <div class="kx-tabs" id="kx-tabs">
        <button class="kx-tab activo" data-reporte="movimientos">Movimientos (Kardex)</button>
        <button class="kx-tab" data-reporte="valorizacion">Valorización de Inventario</button>
    </div>

    <div class="filter-bar">
        <div class="fi-group" id="kx-grupo-bodega">
            <span class="fi-label">Bodega</span>
            <select id="kx-bodega" class="fi-select"></select>
        </div>
        <div class="fi-group" id="kx-grupo-producto">
            <span class="fi-label">Producto</span>
            <input type="text" id="kx-producto-buscar" class="fi-input" autocomplete="off" placeholder="Buscar por código o nombre…">
            <input type="hidden" id="kx-producto-id">
            <div id="kx-producto-resultados" class="kx-resultados"></div>
        </div>
        <div class="fi-group" id="kx-grupo-desde">
            <span class="fi-label">Desde</span>
            <input type="date" id="kx-desde" class="fi-input">
        </div>
        <div class="fi-group" id="kx-grupo-hasta">
            <span class="fi-label">Hasta</span>
            <input type="date" id="kx-hasta" class="fi-input">
        </div>
        <button class="btn-primary" onclick="consultarKardex()">🔍 Consultar</button>
    </div>

    <div class="metrics-row" id="kx-metrics" style="display:none;"></div>

    <div class="table-wrapper">
        <div class="table-scroll">
            <div id="kx-resultado">
                <div class="spinner-cell">📦 Seleccione un producto y presione "Consultar"</div>
            </div>
        </div>
    </div>
</div>

<script>
    var KX = { reporte: 'movimientos', datos: null, bodegas: [], productosResultado: [] };

    function escKx(s) { return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
    function fmtMoneyKx(n) { return '$ ' + (Number(n) || 0).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 }); }
    function fmtCantKx(n) { return (Number(n) || 0).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 3 }); }
    function fmtFechaKx(s) { if (!s) return '—'; var p = String(s).slice(0, 10).split('-'); return p.length === 3 ? p[2] + '/' + p[1] + '/' + p[0] : s; }
    function hdrsKx() { return { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content, 'Accept': 'application/json' }; }

    (function initKardex() {
        var hoy = new Date();
        var primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        function iso(d) { return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0'); }
        document.getElementById('kx-desde').value = iso(primerDia);
        document.getElementById('kx-hasta').value = iso(hoy);

        document.querySelectorAll('.kx-tab').forEach(function (tab) {
            tab.addEventListener('click', function () { cambiarReporteKx(tab.dataset.reporte); });
        });

        fetch('/kardex/bodegas', { headers: hdrsKx() }).then(function (r) { return r.json(); }).then(function (res) {
            KX.bodegas = res.data || [];
            var sel = document.getElementById('kx-bodega');
            sel.innerHTML = '<option value="">Todas las bodegas</option>' + KX.bodegas.map(function (b) {
                return '<option value="' + b.id + '">' + escKx(b.descripcion) + '</option>';
            }).join('');
            if (KX.bodegas.length) sel.value = String(KX.bodegas[0].id);
        }).catch(function () {});

        document.getElementById('kx-resultado').innerHTML = '<div class="spinner-cell">📦 Seleccione un producto y presione "Consultar"</div>';
    })();

    function cambiarReporteKx(reporte) {
        KX.reporte = reporte;
        document.querySelectorAll('.kx-tab').forEach(function (t) { t.classList.toggle('activo', t.dataset.reporte === reporte); });

        var esMovimientos = reporte === 'movimientos';
        // Se usa style.display (no .hidden) porque .fi-group ya fija display:flex
        // con la misma especificidad y podía ganarle a la clase utilitaria.
        document.getElementById('kx-grupo-producto').style.display = esMovimientos ? '' : 'none';
        document.getElementById('kx-grupo-desde').style.display = esMovimientos ? '' : 'none';
        document.getElementById('kx-grupo-hasta').style.display = esMovimientos ? '' : 'none';

        document.getElementById('kx-metrics').style.display = 'none';
        document.getElementById('kx-metrics').innerHTML = '';
        document.getElementById('kx-resultado').innerHTML = esMovimientos
            ? '<div class="spinner-cell">📦 Seleccione un producto y presione "Consultar"</div>'
            : '<div class="spinner-cell">💰 Presione "Consultar" para valorizar el inventario</div>';
    }

    /* ════════════════════════════════════════════════
       BUSCADOR DE PRODUCTO
    ════════════════════════════════════════════════ */
    function buscarProductoKx() {
        var texto = document.getElementById('kx-producto-buscar').value.trim();
        var bodegaId = document.getElementById('kx-bodega').value;
        document.getElementById('kx-producto-id').value = '';
        var params = new URLSearchParams();
        if (texto) params.set('buscar', texto);
        if (bodegaId) params.set('bodega_id', bodegaId);

        fetch('/kardex/productos?' + params.toString(), { headers: hdrsKx() }).then(function (r) { return r.json(); }).then(function (data) {
            KX.productosResultado = data.data || [];
            var cont = document.getElementById('kx-producto-resultados');
            if (!KX.productosResultado.length) {
                cont.innerHTML = '<div class="kx-opcion-vacio">Sin coincidencias</div>';
            } else {
                cont.innerHTML = KX.productosResultado.map(function (p) {
                    return '<button type="button" class="kx-opcion" onclick="seleccionarProductoKx(' + p.id + ')">' +
                        '<span class="kx-opcion-mono">' + escKx(p.codigo) + '</span><span>' + escKx(p.descripcion) + '</span></button>';
                }).join('');
            }
            cont.classList.add('open');
        }).catch(function () {});
    }

    window.seleccionarProductoKx = function (id) {
        var p = KX.productosResultado.find(function (x) { return Number(x.id) === Number(id); });
        if (!p) return;
        document.getElementById('kx-producto-id').value = p.id;
        document.getElementById('kx-producto-buscar').value = p.codigo + ' - ' + p.descripcion;
        document.getElementById('kx-producto-resultados').classList.remove('open');
    };

    var buscarProductoKxDebounced = debounce(buscarProductoKx, 300);
    document.getElementById('kx-producto-buscar').addEventListener('input', buscarProductoKxDebounced);
    document.getElementById('kx-producto-buscar').addEventListener('focus', buscarProductoKx);
    document.getElementById('kx-bodega').addEventListener('change', function () {
        document.getElementById('kx-producto-buscar').value = '';
        document.getElementById('kx-producto-id').value = '';
    });

    document.addEventListener('click', function (e) {
        if (!e.target.closest('#kx-grupo-producto')) document.getElementById('kx-producto-resultados').classList.remove('open');
    });

    /* ════════════════════════════════════════════════
       CONSULTAR
    ════════════════════════════════════════════════ */
    function consultarKardex() {
        var resultado = document.getElementById('kx-resultado');
        var bodegaId = document.getElementById('kx-bodega').value;

        if (KX.reporte === 'movimientos') {
            var productoId = document.getElementById('kx-producto-id').value;
            var desde = document.getElementById('kx-desde').value;
            var hasta = document.getElementById('kx-hasta').value;
            if (!bodegaId) { notifKx('Seleccione una bodega.', 'error'); return; }
            if (!productoId) { notifKx('Busque y seleccione un producto.', 'error'); return; }
            if (!desde || !hasta) { notifKx('Seleccione el rango de fechas.', 'error'); return; }

            var params = new URLSearchParams({ producto_id: productoId, bodega_id: bodegaId, desde: desde, hasta: hasta });
            resultado.innerHTML = '<div class="spinner-cell"><div class="spinner"></div>Generando kardex…</div>';
            document.getElementById('kx-metrics').style.display = 'none';

            fetch('/kardex/movimientos?' + params.toString(), { headers: hdrsKx() })
                .then(function (r) { return r.json().then(function (d) { if (!r.ok) throw new Error(d.message || Object.values(d.errors || {}).flat().join(' ') || 'No fue posible generar el kardex.'); return d; }); })
                .then(function (data) { KX.datos = data; renderMovimientosKx(data); })
                .catch(function (e) { resultado.innerHTML = '<div class="spinner-cell">⚠️ ' + escKx(e.message) + '</div>'; notifKx(e.message, 'error'); });
        } else {
            var params2 = new URLSearchParams();
            if (bodegaId) params2.set('bodega_id', bodegaId);
            resultado.innerHTML = '<div class="spinner-cell"><div class="spinner"></div>Valorizando inventario…</div>';
            document.getElementById('kx-metrics').style.display = 'none';

            fetch('/kardex/valorizacion?' + params2.toString(), { headers: hdrsKx() })
                .then(function (r) { return r.json(); })
                .then(function (data) { KX.datos = data; renderValorizacionKx(data); })
                .catch(function (e) { resultado.innerHTML = '<div class="spinner-cell">⚠️ ' + escKx(e.message) + '</div>'; notifKx(e.message, 'error'); });
        }
    }

    function renderMetricasKx(tarjetas) {
        var cont = document.getElementById('kx-metrics');
        cont.innerHTML = tarjetas.map(function (t) {
            return '<div class="metric-card" style="--accent:' + t.accent + '"><p class="metric-label">' + escKx(t.label) + '</p>' +
                '<p class="metric-value">' + t.value + '</p>' + (t.sub ? '<p class="metric-sub">' + escKx(t.sub) + '</p>' : '') + '</div>';
        }).join('');
        cont.style.display = 'grid';
    }

    /* ════════════════════════════════════════════════
       RENDER — MOVIMIENTOS (KARDEX)
    ════════════════════════════════════════════════ */
    function renderMovimientosKx(data) {
        renderMetricasKx([
            { label: 'Producto', value: data.producto.codigo, sub: data.producto.descripcion, accent: '#1D4ED8' },
            { label: 'Saldo inicial', value: fmtCantKx(data.saldo_inicial.cantidad), sub: fmtMoneyKx(data.saldo_inicial.valor), accent: '#7C3AED' },
            { label: 'Costo promedio actual', value: fmtMoneyKx(data.saldo_final.costo_promedio), accent: '#D97706' },
            { label: 'Saldo final', value: fmtCantKx(data.saldo_final.cantidad), sub: fmtMoneyKx(data.saldo_final.valor), accent: '#059669' },
        ]);

        var filas = data.movimientos.map(function (m) {
            var tipoBadge = m.tipo === 'ENTRADA' ? '<span class="badge badge-green">▲ Entrada</span>' : '<span class="badge badge-red">▼ Salida</span>';
            return '<tr>' +
                '<td>' + fmtFechaKx(m.fecha) + '</td>' +
                '<td><span class="td-mono">' + escKx(m.documento_tipo || '—') + (m.prefijo ? (' ' + escKx(m.prefijo) + escKx(m.numero)) : '') + '</span></td>' +
                '<td>' + tipoBadge + '</td>' +
                '<td class="td-money">' + fmtCantKx(m.cantidad) + '</td>' +
                '<td class="td-money">' + fmtCantKx(m.stock_anterior) + ' → ' + fmtCantKx(m.stock_nuevo) + '</td>' +
                '<td class="td-money">' + fmtMoneyKx(m.costo_unitario) + '</td>' +
                '<td class="td-money">' + fmtMoneyKx(m.costo_promedio_nuevo) + '</td>' +
                '<td class="td-money">' + fmtMoneyKx(m.valor_movimiento) + '</td>' +
                '</tr>';
        }).join('') || '<tr><td colspan="8"><div class="spinner-cell">📭 Sin movimientos en el período</div></td></tr>';

        document.getElementById('kx-resultado').innerHTML =
            '<table class="kx-tbl"><thead><tr><th>Fecha</th><th>Documento</th><th>Tipo</th><th style="text-align:right;">Cantidad</th>' +
            '<th style="text-align:right;">Stock</th><th style="text-align:right;">Costo Unit.</th><th style="text-align:right;">Costo Prom.</th><th style="text-align:right;">Valor</th></tr></thead>' +
            '<tbody><tr><td colspan="8" style="font-style:italic;color:#6B7280;">Saldo inicial: ' + fmtCantKx(data.saldo_inicial.cantidad) + ' unid. — ' + fmtMoneyKx(data.saldo_inicial.valor) + '</td></tr>' + filas + '</tbody>' +
            '<tfoot><tr class="kx-total"><td colspan="4">SALDO FINAL</td><td class="td-money">' + fmtCantKx(data.saldo_final.cantidad) + '</td><td></td><td class="td-money">' + fmtMoneyKx(data.saldo_final.costo_promedio) + '</td><td class="td-money">' + fmtMoneyKx(data.saldo_final.valor) + '</td></tr></tfoot></table>';
    }

    /* ════════════════════════════════════════════════
       RENDER — VALORIZACIÓN DE INVENTARIO
    ════════════════════════════════════════════════ */
    function renderValorizacionKx(data) {
        renderMetricasKx([
            { label: 'Productos con existencia', value: data.filas.length, accent: '#1D4ED8' },
            { label: 'Total unidades', value: fmtCantKx(data.total_unidades), accent: '#7C3AED' },
            { label: 'Total valorizado', value: fmtMoneyKx(data.total_valorizado), accent: '#059669' },
        ]);

        var filas = data.filas.map(function (f) {
            return '<tr>' +
                '<td>' + escKx(f.bodega) + '</td>' +
                '<td><span class="td-mono">' + escKx(f.codigo) + '</span></td>' +
                '<td>' + escKx(f.descripcion) + '</td>' +
                '<td class="td-money">' + fmtCantKx(f.stock) + '</td>' +
                '<td class="td-money">' + fmtMoneyKx(f.costo_promedio) + '</td>' +
                '<td class="td-money">' + fmtMoneyKx(f.valor_total) + '</td>' +
                '</tr>';
        }).join('') || '<tr><td colspan="6"><div class="spinner-cell">📭 Sin existencias valorizadas</div></td></tr>';

        document.getElementById('kx-resultado').innerHTML =
            '<table class="kx-tbl"><thead><tr><th>Bodega</th><th>Código</th><th>Producto</th>' +
            '<th style="text-align:right;">Stock</th><th style="text-align:right;">Costo Prom.</th><th style="text-align:right;">Valor Total</th></tr></thead>' +
            '<tbody>' + filas + '</tbody>' +
            '<tfoot><tr class="kx-total"><td colspan="3">TOTALES</td><td class="td-money">' + fmtCantKx(data.total_unidades) + '</td><td></td><td class="td-money">' + fmtMoneyKx(data.total_valorizado) + '</td></tr></tfoot></table>';
    }

    /* ════════════════════════════════════════════════
       IMPRIMIR / EXPORTAR
    ════════════════════════════════════════════════ */
    function tituloKardex() { return KX.reporte === 'movimientos' ? 'Kardex de Movimientos' : 'Valorización de Inventario'; }

    window.imprimirKardex = function () {
        var tabla = document.querySelector('#kx-resultado table');
        if (!tabla) { notifKx('Genere el informe antes de imprimir.', 'warning'); return; }

        var html = '<!doctype html><html><head><meta charset="utf-8"><title>' + tituloKardex() + '</title><style>' +
            '@page{size:letter landscape;margin:14mm;}html{background:#fff;color-scheme:light;}' +
            'body{font-family:Arial,Helvetica,sans-serif;color:#111827;background:#fff;margin:0;}' +
            '.head{display:flex;justify-content:space-between;align-items:flex-end;border-bottom:3px solid #1D4ED8;padding-bottom:12px;margin-bottom:16px;}' +
            '.brand{font-size:20px;font-weight:800;color:#1D4ED8;}.sub{font-size:11px;color:#6B7280;margin-top:2px;}.meta{text-align:right;font-size:12px;color:#374151;}' +
            'table{width:100%;border-collapse:collapse;font-size:11px;}th{background:#F8FAFC;text-align:left;padding:6px 8px;border-bottom:2px solid #E5E7EB;text-transform:uppercase;font-size:9px;color:#6B7280;}' +
            'td{padding:5px 8px;border-bottom:1px solid #F3F4F6;}' +
            '.foot{margin-top:20px;text-align:center;font-size:10px;color:#9CA3AF;border-top:1px solid #E5E7EB;padding-top:10px;}' +
            '</style></head><body><div class="head"><div><div class="brand">📦 Nexora</div><div class="sub">' + escKx(tituloKardex()) + '</div></div>' +
            '<div class="meta"><b>Generado:</b> ' + new Date().toLocaleString('es-CO') + '</div></div>' +
            tabla.outerHTML +
            '<div class="foot">Generado desde Nexora — Sistema de Gestión POS</div></body></html>';

        var ventana = window.open('', '_blank', 'width=1000,height=700');
        if (!ventana) { notifKx('El navegador bloqueó la ventana de impresión. Habilite las ventanas emergentes.', 'error'); return; }
        ventana.document.open(); ventana.document.write(html); ventana.document.close();
        setTimeout(function () { ventana.focus(); ventana.print(); }, 300);
    };

    window.exportarKardexCsv = function () {
        var tabla = document.querySelector('#kx-resultado table');
        if (!tabla) { notifKx('Genere el informe antes de exportar.', 'warning'); return; }

        var filas = [];
        tabla.querySelectorAll('tr').forEach(function (tr) {
            var celdas = Array.from(tr.children).map(function (td) { return '"' + td.textContent.trim().replace(/"/g, '""') + '"'; });
            filas.push(celdas.join(','));
        });

        var csv = '﻿' + filas.join('\n');
        var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = tituloKardex().toLowerCase().replace(/ /g, '-') + '.csv';
        a.click();
        URL.revokeObjectURL(a.href);
        notifKx('Exportación creada.', 'success');
    };

    function notifKx(msg, tipo) {
        if (typeof mostrarNotificacion === 'function') { mostrarNotificacion(msg, tipo === 'error' ? 'error' : tipo === 'warning' ? 'warning' : 'success'); return; }
        window.alert(msg);
    }
</script>
