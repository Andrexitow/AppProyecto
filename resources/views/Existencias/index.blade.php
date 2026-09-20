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
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
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
    .metric-value.money { font-size: 16px; }
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

    .fi-input, .fi-select {
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

    .fi-select { flex: none; cursor: pointer; }
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
    .btn-outline:disabled { opacity: .5; cursor: not-allowed; }

    .table-wrapper { background: #fff; border: 1px solid #EAECF0; border-radius: 10px; overflow: hidden; }
    .table-scroll { overflow-x: auto; }

    table.ex-tbl { width: 100%; border-collapse: collapse; font-size: 12.5px; }
    table.ex-tbl thead { background: #F8FAFC; border-bottom: 1px solid #EAECF0; }

    table.ex-tbl thead th {
        padding: 10px 12px;
        text-align: left;
        font-size: 11px;
        font-weight: 600;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    table.ex-tbl tbody tr { border-bottom: 1px solid #F3F4F6; transition: background 0.1s; }
    table.ex-tbl tbody tr:last-child { border-bottom: none; }
    table.ex-tbl tbody tr:hover { background: #F8FAFC; }
    table.ex-tbl td { padding: 9px 12px; color: #374151; vertical-align: middle; }

    .td-mono { font-family: 'JetBrains Mono', 'Fira Mono', monospace; font-size: 12px; color: #1D4ED8; font-weight: 600; }
    .td-money { font-weight: 600; color: #111827; }

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
    .badge-yellow { background: #FFFBEB; color: #92400E; }
    .badge-gray { background: #F3F4F6; color: #374151; }
    .dot { width: 5px; height: 5px; border-radius: 50%; display: inline-block; background: currentColor; }

    .spinner-cell {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
        color: #6B7280;
        font-size: 13px;
        gap: 10px;
    }

    @keyframes spin-ex { to { transform: rotate(360deg); } }

    .spinner {
        width: 18px;
        height: 18px;
        border: 2px solid #E5E7EB;
        border-top-color: #1D4ED8;
        border-radius: 50%;
        animation: spin-ex 0.7s linear infinite;
    }

    @media (max-width: 640px) {
        .metrics-row { grid-template-columns: 1fr 1fr; }
    }
</style>

<div id="view-existencias">

    {{-- ── ENCABEZADO ── --}}
    <div class="sec-header">
        <div>
            <p class="sec-title">📦 Existencias por Bodega</p>
            <p class="sec-subtitle">Consulta el inventario disponible y genera reportes de impresión</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button class="btn-outline" id="ex-btn-carta" onclick="imprimirExistenciasCarta()" disabled>🖨️ Imprimir Carta</button>
            <select class="fi-select" id="ex-ancho-pos" style="flex:none;">
                <option value="42">POS 80mm</option>
                <option value="32">POS 58mm</option>
            </select>
            <select class="fi-select" id="ex-impresora-red" style="flex:none;min-width:160px;">
                <option value="">Impresora de red…</option>
            </select>
            <button class="btn-primary" id="ex-btn-red" onclick="enviarExistenciasRed()" disabled>📡 Enviar a impresora</button>
        </div>
    </div>

    {{-- ── MÉTRICAS ── --}}
    <div class="metrics-row" id="ex-metrics" style="display:none;">
        <div class="metric-card" style="--accent:#1D4ED8">
            <p class="metric-label">Total Productos</p>
            <p class="metric-value" id="ex-m-total">—</p>
            <p class="metric-sub">En esta bodega</p>
        </div>
        <div class="metric-card" style="--accent:#059669">
            <p class="metric-label">Disponibles</p>
            <p class="metric-value" id="ex-m-disp">—</p>
            <p class="metric-sub" id="ex-m-disp-sub"></p>
        </div>
        <div class="metric-card" style="--accent:#D97706">
            <p class="metric-label">Bajo Stock</p>
            <p class="metric-value" id="ex-m-bajo">—</p>
            <p class="metric-sub">Menos de 10 unidades</p>
        </div>
        <div class="metric-card" style="--accent:#DC2626">
            <p class="metric-label">Agotados</p>
            <p class="metric-value" id="ex-m-agotado">—</p>
            <p class="metric-sub">Sin existencias</p>
        </div>
        <div class="metric-card" style="--accent:#7C3AED">
            <p class="metric-label">Valor Inventario</p>
            <p class="metric-value money" id="ex-m-valor">—</p>
            <p class="metric-sub">Costo a precio de venta</p>
        </div>
    </div>

    {{-- ── FILTROS ── --}}
    <div class="filter-bar">
        <div class="fi-group" style="flex:1.4;min-width:200px;">
            <span class="fi-label">🏬</span>
            <select class="fi-select" id="ex-bodega" style="flex:1;" onchange="cargarExistencias()">
                <option value="">Seleccione bodega…</option>
                @foreach ($bodegas as $bodega)
                    <option value="{{ $bodega->id }}">{{ $bodega->descripcion }}</option>
                @endforeach
            </select>
        </div>
        <div class="fi-group" style="flex:2;min-width:200px;">
            <span class="fi-label">🔍</span>
            <input autocomplete="off" type="text" id="ex-buscar" class="fi-input" placeholder="Buscar producto por nombre o código…" oninput="renderExistencias()">
        </div>
        <div class="fi-group" style="flex:none;">
            <select class="fi-select" id="ex-estado" onchange="renderExistencias()">
                <option value="">Todos los estados</option>
                <option value="disponible">Disponible</option>
                <option value="bajo">Bajo stock</option>
                <option value="agotado">Agotado</option>
            </select>
        </div>
        <button class="btn-outline" onclick="cargarExistencias()">🔄 Actualizar</button>
    </div>

    {{-- ── TABLA ── --}}
    <div class="table-wrapper">
        <div class="table-scroll">
            <table class="ex-tbl">
                <thead>
                    <tr>
                        <th>Código</th>
                        <th>Producto</th>
                        <th style="text-align:center;">Cantidad</th>
                        <th style="text-align:right;">Vlr. Unitario</th>
                        <th style="text-align:right;">Vlr. Total</th>
                        <th style="text-align:center;">Estado</th>
                    </tr>
                </thead>
                <tbody id="ex-tbody">
                    <tr>
                        <td colspan="6">
                            <div class="spinner-cell">📭 Seleccione una bodega para visualizar los datos</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>

<script>
    var EX = { datos: [], bodegaNombre: '', filtradas: [] };

    function hdrsEX() {
        return { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content, 'Accept': 'application/json' };
    }

    window.cargarExistencias = function () {
        var bodegaId = document.getElementById('ex-bodega').value;
        var tbody = document.getElementById('ex-tbody');

        if (!bodegaId) {
            EX.datos = [];
            document.getElementById('ex-metrics').style.display = 'none';
            document.getElementById('ex-btn-carta').disabled = true;
            document.getElementById('ex-btn-red').disabled = true;
            tbody.innerHTML = '<tr><td colspan="6"><div class="spinner-cell">📭 Seleccione una bodega para visualizar los datos</div></td></tr>';
            return;
        }

        tbody.innerHTML = '<tr><td colspan="6"><div class="spinner-cell"><div class="spinner"></div>Cargando existencias…</div></td></tr>';

        fetch('/existencias/data?bodega_id=' + bodegaId, { headers: hdrsEX() })
            .then(function (r) { return r.json().then(function (d) { if (!r.ok) throw new Error(d.message || 'No fue posible cargar las existencias.'); return d; }); })
            .then(function (res) {
                EX.datos = res.data || [];
                EX.bodegaNombre = res.bodega ? res.bodega.descripcion : '';
                document.getElementById('ex-metrics').style.display = 'grid';
                document.getElementById('ex-btn-carta').disabled = EX.datos.length === 0;
                document.getElementById('ex-btn-red').disabled = EX.datos.length === 0;
                renderExistencias();
            })
            .catch(function (e) {
                tbody.innerHTML = '<tr><td colspan="6"><div class="spinner-cell">⚠️ ' + esc(e.message) + '</div></td></tr>';
                notifEX(e.message, 'error');
            });
    };

    function estadoDe(stock) {
        if (stock <= 0) return 'agotado';
        if (stock < 10) return 'bajo';
        return 'disponible';
    }

    function badgeEstadoEX(estado) {
        var map = {
            disponible: '<span class="badge badge-green"><span class="dot"></span>Disponible</span>',
            bajo: '<span class="badge badge-yellow"><span class="dot"></span>Bajo stock</span>',
            agotado: '<span class="badge badge-red"><span class="dot"></span>Agotado</span>',
        };
        return map[estado];
    }

    window.renderExistencias = function () {
        var buscar = (document.getElementById('ex-buscar').value || '').toLowerCase().trim();
        var estadoFiltro = document.getElementById('ex-estado').value;

        EX.filtradas = EX.datos.filter(function (p) {
            var estado = estadoDe(p.stock);
            if (estadoFiltro && estado !== estadoFiltro) return false;
            if (buscar && !((p.descripcion || '') + (p.codigo || '')).toLowerCase().includes(buscar)) return false;
            return true;
        });

        var tbody = document.getElementById('ex-tbody');

        if (EX.filtradas.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6"><div class="spinner-cell">📭 No se encontraron productos con estos filtros</div></td></tr>';
        } else {
            tbody.innerHTML = EX.filtradas.map(function (p) {
                var estado = estadoDe(p.stock);
                var valorTotal = p.stock * (p.precio || 0);
                return '<tr>' +
                    '<td><span class="td-mono">' + esc(p.codigo || '—') + '</span></td>' +
                    '<td style="font-weight:500;color:#111827;">' + esc(p.descripcion) + '</td>' +
                    '<td style="text-align:center;font-weight:600;">' + fmtNumEX(p.stock) + '</td>' +
                    '<td style="text-align:right;">' + fmtMoneyEX(p.precio) + '</td>' +
                    '<td class="td-money" style="text-align:right;">' + fmtMoneyEX(valorTotal) + '</td>' +
                    '<td style="text-align:center;">' + badgeEstadoEX(estado) + '</td>' +
                    '</tr>';
            }).join('');
        }

        actualizarMetricasEX();
    }

    function actualizarMetricasEX() {
        var lista = EX.datos;
        var total = lista.length;
        var disponibles = lista.filter(function (p) { return estadoDe(p.stock) === 'disponible'; }).length;
        var bajos = lista.filter(function (p) { return estadoDe(p.stock) === 'bajo'; }).length;
        var agotados = lista.filter(function (p) { return estadoDe(p.stock) === 'agotado'; }).length;
        var valorTotal = lista.reduce(function (s, p) { return s + p.stock * (p.precio || 0); }, 0);

        document.getElementById('ex-m-total').textContent = total;
        document.getElementById('ex-m-disp').textContent = disponibles;
        document.getElementById('ex-m-disp-sub').textContent = total ? Math.round(disponibles / total * 100) + '% del total' : '';
        document.getElementById('ex-m-bajo').textContent = bajos;
        document.getElementById('ex-m-agotado').textContent = agotados;
        document.getElementById('ex-m-valor').textContent = fmtMoneyEX(valorTotal);
    }

    /* ════════════════════════════════════════════════
       IMPRESIÓN — FORMATO CARTA
    ════════════════════════════════════════════════ */
    window.imprimirExistenciasCarta = function () {
        if (!EX.filtradas.length) { notifEX('No hay existencias para imprimir', 'warning'); return; }

        var fecha = new Date().toLocaleString('es-CO');
        var totalUnidades = EX.filtradas.reduce(function (s, p) { return s + Number(p.stock); }, 0);
        var totalValor = EX.filtradas.reduce(function (s, p) { return s + p.stock * (p.precio || 0); }, 0);

        var filas = EX.filtradas.map(function (p) {
            var estado = estadoDe(p.stock);
            var color = estado === 'agotado' ? '#DC2626' : (estado === 'bajo' ? '#D97706' : '#059669');
            var texto = estado === 'agotado' ? 'Agotado' : (estado === 'bajo' ? 'Bajo stock' : 'Disponible');
            return '<tr>' +
                '<td>' + esc(p.codigo || '—') + '</td>' +
                '<td>' + esc(p.descripcion) + '</td>' +
                '<td style="text-align:center;">' + fmtNumEX(p.stock) + '</td>' +
                '<td style="text-align:right;">' + fmtMoneyEX(p.precio) + '</td>' +
                '<td style="text-align:right;">' + fmtMoneyEX(p.stock * (p.precio || 0)) + '</td>' +
                '<td style="text-align:center;color:' + color + ';font-weight:600;">' + texto + '</td>' +
                '</tr>';
        }).join('');

        var html = '<!doctype html><html><head><meta charset="utf-8"><title>Reporte de Existencias</title><style>' +
            '@page{size:letter;margin:16mm;}' +
            'html{background:#fff;color-scheme:light;}' +
            'body{font-family:Arial,Helvetica,sans-serif;color:#111827;background:#fff;margin:0;}' +
            '.head{display:flex;justify-content:space-between;align-items:flex-end;border-bottom:3px solid #1D4ED8;padding-bottom:12px;margin-bottom:16px;}' +
            '.brand{font-size:20px;font-weight:800;color:#1D4ED8;}' +
            '.sub{font-size:11px;color:#6B7280;margin-top:2px;}' +
            '.meta{text-align:right;font-size:12px;color:#374151;}' +
            'table{width:100%;border-collapse:collapse;font-size:11.5px;}' +
            'th{background:#F8FAFC;text-align:left;padding:8px 10px;border-bottom:2px solid #E5E7EB;text-transform:uppercase;font-size:10px;color:#6B7280;letter-spacing:.4px;}' +
            'td{padding:7px 10px;border-bottom:1px solid #F3F4F6;}' +
            '.tot{margin-top:14px;display:flex;justify-content:flex-end;gap:28px;font-size:13px;}' +
            '.tot b{font-size:15px;}' +
            '.foot{margin-top:24px;text-align:center;font-size:10px;color:#9CA3AF;border-top:1px solid #E5E7EB;padding-top:10px;}' +
            '</style></head><body>' +
            '<div class="head"><div><div class="brand">📦 Nexora</div><div class="sub">Reporte de Existencias por Bodega</div></div>' +
            '<div class="meta"><b>Bodega:</b> ' + esc(EX.bodegaNombre) + '<br><b>Fecha:</b> ' + esc(fecha) + '</div></div>' +
            '<table><thead><tr><th>Código</th><th>Producto</th><th style="text-align:center;">Cant.</th>' +
            '<th style="text-align:right;">Vlr. Unit.</th><th style="text-align:right;">Vlr. Total</th><th style="text-align:center;">Estado</th></tr></thead>' +
            '<tbody>' + filas + '</tbody></table>' +
            '<div class="tot"><span>Total unidades: <b>' + totalUnidades + '</b></span><span>Valor inventario: <b>' + fmtMoneyEX(totalValor) + '</b></span></div>' +
            '<div class="foot">Generado desde Nexora — Sistema de Gestión POS · ' + esc(fecha) + '</div>' +
            '</body></html>';

        abrirEImprimir(html);
    };

    /* ════════════════════════════════════════════════
       IMPRESIÓN — FORMATO POS (térmica 80mm / 58mm)
    ════════════════════════════════════════════════ */
    /** Arma el texto plano del ticket de existencias (lo usan tanto la impresión por navegador como el envío por red). */
    function construirTextoExistenciasPOS(ancho) {
        var linea = repetir('-', ancho);
        var lineaDoble = repetir('=', ancho);
        var fecha = new Date().toLocaleString('es-CO');

        var txt = '';
        txt += lineaDoble + '\n';
        txt += centrar('NEXORA', ancho) + '\n';
        txt += centrar('Reporte de Existencias', ancho) + '\n';
        txt += lineaDoble + '\n';
        txt += 'Bodega : ' + EX.bodegaNombre + '\n';
        txt += 'Fecha  : ' + fecha + '\n';
        txt += linea + '\n';

        var colCant = 6;
        var colProd = ancho - colCant;
        txt += padDer('PRODUCTO', colProd) + padIzq('CANT', colCant) + '\n';
        txt += linea + '\n';

        var totalUnidades = 0;
        EX.filtradas.forEach(function (p) {
            var nombre = (p.descripcion || '').toUpperCase();
            totalUnidades += Number(p.stock);
            // Ajusta el nombre del producto a varias líneas si excede el ancho de columna.
            var partes = partirTexto(nombre, colProd);
            partes.forEach(function (parte, i) {
                if (i === 0) {
                    txt += padDer(parte, colProd) + padIzq(numTicketEX(p.stock), colCant) + '\n';
                } else {
                    txt += padDer(parte, colProd) + padIzq('', colCant) + '\n';
                }
            });
        });

        txt += linea + '\n';
        txt += padDer('TOTAL PRODUCTOS', colProd) + padIzq(String(EX.filtradas.length), colCant) + '\n';
        txt += padDer('TOTAL UNIDADES', colProd) + padIzq(String(totalUnidades), colCant) + '\n';
        txt += lineaDoble + '\n';
        txt += centrar('Sistema de Gestion POS', ancho) + '\n';
        txt += lineaDoble + '\n';

        return txt;
    }

    function abrirEImprimir(html) {
        var ventana = window.open('', '_blank', 'width=420,height=600');
        if (!ventana) { notifEX('El navegador bloqueó la ventana de impresión. Habilite las ventanas emergentes.', 'error'); return; }
        ventana.document.open();
        ventana.document.write(html);
        ventana.document.close();
        setTimeout(function () {
            ventana.focus();
            ventana.print();
        }, 300);
    }

    /* ════════════════════════════════════════════════
       UTILIDADES
    ════════════════════════════════════════════════ */
    function repetir(c, n) { return new Array(Math.max(0, n) + 1).join(c); }

    function padDer(txt, len) {
        txt = String(txt);
        return txt.length >= len ? txt.slice(0, len) : txt + repetir(' ', len - txt.length);
    }

    function padIzq(txt, len) {
        txt = String(txt);
        return txt.length >= len ? txt.slice(-len) : repetir(' ', len - txt.length) + txt;
    }

    function centrar(txt, ancho) {
        var esp = Math.max(0, Math.floor((ancho - txt.length) / 2));
        return repetir(' ', esp) + txt;
    }

    function partirTexto(txt, ancho) {
        var partes = [];
        while (txt.length > ancho) {
            partes.push(txt.slice(0, ancho));
            txt = txt.slice(ancho);
        }
        partes.push(txt);
        return partes;
    }

    function fmtMoneyEX(n) { return '$ ' + (Number(n) || 0).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 }); }

    function fmtNumEX(n) { return (Number(n) || 0).toLocaleString('es-CO', { maximumFractionDigits: 2 }); }

    function numTicketEX(n) {
        n = Number(n) || 0;
        return Number.isInteger(n) ? String(n) : n.toFixed(2);
    }

    function esc(s) { return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }

    function notifEX(msg, tipo) {
        if (typeof mostrarNotificacion === 'function') { mostrarNotificacion(msg, tipo === 'error' ? 'error' : tipo === 'warning' ? 'warning' : 'success'); return; }
        window.alert(msg);
    }

    /* ════════════════════════════════════════════════
       IMPRESIÓN POR RED — encola el reporte para que el
       agente local instalado en el negocio lo envíe por IP
       a la impresora térmica elegida (igual que las comandas).
    ════════════════════════════════════════════════ */
    (function cargarImpresorasRedEX() {
        fetch('/api/impresoras', { headers: hdrsEX() }).then(function (r) { return r.json(); }).then(function (impresoras) {
            var select = document.getElementById('ex-impresora-red');
            (impresoras || []).filter(function (i) { return i.activa; }).forEach(function (i) {
                select.insertAdjacentHTML('beforeend', '<option value="' + i.id + '">' + esc(i.nombre) + '</option>');
            });
        }).catch(function () {});
    })();

    window.enviarExistenciasRed = function () {
        if (!EX.filtradas.length) { notifEX('No hay existencias para enviar.', 'warning'); return; }
        var impresoraId = document.getElementById('ex-impresora-red').value;
        if (!impresoraId) { notifEX('Seleccione a qué impresora de red enviarlo.', 'error'); return; }

        var ancho = parseInt(document.getElementById('ex-ancho-pos').value, 10) || 42;
        var txt = construirTextoExistenciasPOS(ancho);

        var btn = document.getElementById('ex-btn-red');
        btn.disabled = true;

        fetch('/existencias/imprimir-red', {
            method: 'POST',
            headers: Object.assign({ 'Content-Type': 'application/json' }, hdrsEX()),
            body: JSON.stringify({ impresora_id: impresoraId, contenido: txt }),
        })
            .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, data: d }; }); })
            .then(function (r) {
                if (!r.ok) throw new Error(r.data.message || Object.values(r.data.errors || {}).flat().join(' ') || 'No se pudo enviar el reporte.');
                notifEX(r.data.message, 'success');
            })
            .catch(function (e) { notifEX(e.message, 'error'); })
            .finally(function () { btn.disabled = false; });
    };
</script>
