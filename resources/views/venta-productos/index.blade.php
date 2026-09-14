<style>
    .sec-header { display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; }
    .sec-title { font-size: 17px; font-weight: 600; color: #111827; letter-spacing: -0.3px; }
    .sec-subtitle { font-size: 12px; color: #6B7280; margin-top: 2px; }

    .metrics-row { display: grid; grid-template-columns: repeat(auto-fit, minmax(140px, 1fr)); gap: 10px; margin-bottom: 16px; }
    .metric-card { background: #fff; border: 1px solid #EAECF0; border-radius: 10px; padding: 12px 14px; position: relative; overflow: hidden; }
    .metric-card::before { content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px; background: var(--accent, #1D4ED8); }
    .metric-label { font-size: 11px; font-weight: 500; color: #9CA3AF; text-transform: uppercase; letter-spacing: 0.5px; }
    .metric-value { font-size: 18px; font-weight: 700; color: #111827; margin-top: 4px; }

    .filter-panel { background: #fff; border: 1px solid #EAECF0; border-radius: 10px; padding: 14px; margin-bottom: 12px; }
    .filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 10px; }
    .vp-field { display: flex; flex-direction: column; gap: 3px; position: relative; }
    .vp-field label { font-size: 11px; font-weight: 500; color: #9CA3AF; text-transform: uppercase; letter-spacing: .3px; }
    .vp-field input, .vp-field select { border: 1px solid #D1D5DB; border-radius: 7px; padding: 7px 9px; font-size: 12.5px; color: #111827; background: #F9FAFB; outline: none; width: 100%; box-sizing: border-box; }
    .vp-field input:focus, .vp-field select:focus { border-color: #1D4ED8; background: #fff; }

    .vp-resultados { position: absolute; z-index: 30; top: calc(100% + 4px); left: 0; right: 0; display: none; max-height: 200px; overflow-y: auto; border: 1px solid #D1D5DB; border-radius: 8px; background: #fff; box-shadow: 0 10px 25px rgba(16,24,40,.14); }
    .vp-resultados.open { display: block; }
    .vp-opcion { width: 100%; display: flex; align-items: baseline; gap: 8px; padding: 8px 12px; border: 0; border-bottom: 1px solid #F3F4F6; background: #fff; color: #374151; text-align: left; cursor: pointer; font-size: 12.5px; }
    .vp-opcion:hover { background: #EFF6FF; }
    .vp-opcion-mono { font-family: 'JetBrains Mono','Fira Mono',monospace; font-size: 12px; color: #1D4ED8; font-weight: 600; flex-shrink: 0; }
    .vp-opcion-vacio { padding: 14px 12px; text-align: center; color: #9CA3AF; font-size: 12px; }

    .filter-actions { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 10px; margin-top: 12px; padding-top: 12px; border-top: 1px solid #F3F4F6; }
    .vp-check { display: flex; align-items: center; gap: 7px; font-size: 12.5px; color: #374151; }
    .vp-check input { width: 15px; height: 15px; accent-color: #1D4ED8; cursor: pointer; }

    .btn-primary { display: inline-flex; align-items: center; gap: 6px; background: #1D4ED8; color: #fff; border: none; border-radius: 7px; padding: 8px 16px; font-size: 12.5px; font-weight: 600; cursor: pointer; }
    .btn-primary:hover { background: #1e40af; }
    .btn-outline { display: inline-flex; align-items: center; gap: 5px; background: #fff; color: #374151; border: 1px solid #D1D5DB; border-radius: 7px; padding: 8px 14px; font-size: 12.5px; font-weight: 500; cursor: pointer; }
    .btn-outline:hover { background: #F3F4F6; }

    .table-wrapper { background: #fff; border: 1px solid #EAECF0; border-radius: 10px; overflow: hidden; }
    .table-scroll { overflow-x: auto; }
    table.vp-tbl { width: 100%; border-collapse: collapse; font-size: 12.5px; }
    table.vp-tbl thead { background: #F8FAFC; border-bottom: 1px solid #EAECF0; }
    table.vp-tbl thead th { padding: 10px 12px; text-align: left; font-size: 11px; font-weight: 600; color: #6B7280; text-transform: uppercase; letter-spacing: .5px; white-space: nowrap; }
    table.vp-tbl tbody tr { border-bottom: 1px solid #F3F4F6; }
    table.vp-tbl tbody tr:hover { background: #F8FAFC; }
    table.vp-tbl tfoot td { font-weight: 700; color: #111827; border-top: 2px solid #E5E7EB; }
    table.vp-tbl td { padding: 8px 12px; color: #374151; vertical-align: middle; }
    .td-mono { font-family: 'JetBrains Mono','Fira Mono',monospace; font-size: 12px; color: #1D4ED8; font-weight: 600; }
    .td-money { font-weight: 600; color: #111827; text-align: right; white-space: nowrap; }

    .vp-btn-facturas { background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; border-radius: 6px; padding: 3px 8px; font-size: 11.5px; font-weight: 600; cursor: pointer; }
    .vp-btn-facturas:hover { background: #DBEAFE; }
    .vp-det-fila td { color: #4B5563; font-size: 12px; background: #F8FAFC; }

    .spinner-cell { display: flex; align-items: center; justify-content: center; padding: 40px; color: #6B7280; font-size: 13px; gap: 10px; }
    @keyframes spin-vp { to { transform: rotate(360deg); } }
    .spinner { width: 18px; height: 18px; border: 2px solid #E5E7EB; border-top-color: #1D4ED8; border-radius: 50%; animation: spin-vp .7s linear infinite; }

    @media (max-width: 640px) { .metrics-row { grid-template-columns: 1fr 1fr; } }
</style>

<div id="view-venta-productos">
    <div class="sec-header">
        <div>
            <p class="sec-title">🛒 Venta por Producto</p>
            <p class="sec-subtitle">Ranking de ventas con todos los cruces: bodega, vendedor, cliente, caja, prefijo, categoría, grupo y producto</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button class="btn-outline" onclick="exportarVentaProductosCsv()">⬇️ Exportar CSV</button>
            <button class="btn-primary" onclick="imprimirVentaProductos()">🖨️ Imprimir</button>
        </div>
    </div>

    <div class="filter-panel">
        <div class="filter-grid">
            <div class="vp-field"><label>Desde</label><input type="datetime-local" id="vp-desde"></div>
            <div class="vp-field"><label>Hasta</label><input type="datetime-local" id="vp-hasta"></div>
            <div class="vp-field"><label>Bodega</label><select id="vp-bodega"><option value="">Todas</option></select></div>
            <div class="vp-field"><label>Vendedor / Cajero</label><select id="vp-usuario"><option value="">Todos</option></select></div>
            <div class="vp-field"><label>Caja</label><select id="vp-caja"><option value="">Todas</option></select></div>
            <div class="vp-field"><label>Prefijo</label><select id="vp-prefijo"><option value="">Todos</option></select></div>
            <div class="vp-field"><label>Categoría</label><select id="vp-categoria"><option value="">Todas</option></select></div>
            <div class="vp-field"><label>Grupo de menú</label><select id="vp-grupo-menu"><option value="">Todos</option></select></div>
            <div class="vp-field" id="vp-grupo-cliente">
                <label>Cliente</label>
                <input type="text" id="vp-cliente-buscar" autocomplete="off" placeholder="Todos (buscar)…">
                <input type="hidden" id="vp-cliente-id">
                <div id="vp-cliente-resultados" class="vp-resultados"></div>
            </div>
            <div class="vp-field" id="vp-grupo-producto">
                <label>Producto</label>
                <input type="text" id="vp-producto-buscar" autocomplete="off" placeholder="Todos (buscar)…">
                <input type="hidden" id="vp-producto-id">
                <div id="vp-producto-resultados" class="vp-resultados"></div>
            </div>
        </div>

        <div class="filter-actions">
            <label class="vp-check"><input type="checkbox" id="vp-con-iva" checked> Mostrar valores con IVA incluido</label>
            <button class="btn-primary" onclick="consultarVentaProductos()">🔍 Consultar</button>
        </div>
    </div>

    <div class="metrics-row" id="vp-metrics" style="display:none;"></div>

    <div class="table-wrapper">
        <div class="table-scroll">
            <div id="vp-resultado">
                <div class="spinner-cell">🛒 Ajuste los filtros y presione "Consultar"</div>
            </div>
        </div>
    </div>
</div>

<script>
    var VP = { datos: null, clientesResultado: [], productosResultado: [] };

    function escVp(s) { return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
    function fmtMoneyVp(n) { return '$ ' + (Number(n) || 0).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 }); }
    function fmtCantVp(n) { return (Number(n) || 0).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 3 }); }
    function hdrsVp() { return { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content, 'Accept': 'application/json' }; }

    (function initVentaProductos() {
        var hoy = new Date();
        var primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        function isoDT(d) { return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0') + 'T' + String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0'); }
        document.getElementById('vp-desde').value = primerDia.getFullYear() + '-' + String(primerDia.getMonth() + 1).padStart(2, '0') + '-01T00:00';
        document.getElementById('vp-hasta').value = isoDT(hoy);

        fetch('/venta-productos/filtros', { headers: hdrsVp() }).then(function (r) { return r.json(); }).then(function (res) {
            document.getElementById('vp-bodega').insertAdjacentHTML('beforeend', res.bodegas.map(function (b) { return '<option value="' + b.id + '">' + escVp(b.descripcion) + '</option>'; }).join(''));
            document.getElementById('vp-usuario').insertAdjacentHTML('beforeend', res.usuarios.map(function (u) { return '<option value="' + u.id + '">' + escVp(u.name) + '</option>'; }).join(''));
            document.getElementById('vp-caja').insertAdjacentHTML('beforeend', res.cajas.map(function (c) { return '<option value="' + c.id + '">' + escVp(c.nombre) + '</option>'; }).join(''));
            document.getElementById('vp-grupo-menu').insertAdjacentHTML('beforeend', res.grupos_menu.map(function (g) { return '<option value="' + g.id + '">' + escVp(g.nombre) + '</option>'; }).join(''));
            document.getElementById('vp-categoria').insertAdjacentHTML('beforeend', res.categorias.map(function (c) { return '<option value="' + escVp(c) + '">' + escVp(c) + '</option>'; }).join(''));
        }).catch(function () {});

        fetch('/prefijos/opciones', { headers: hdrsVp() }).then(function (r) { return r.json(); }).then(function (res) {
            document.getElementById('vp-prefijo').insertAdjacentHTML('beforeend', (res.data || []).map(function (p) { return '<option value="' + escVp(p.codigo) + '">' + escVp(p.codigo) + ' - ' + escVp(p.nombre) + '</option>'; }).join(''));
        }).catch(function () {});
    })();

    /* ── Buscador de cliente ── */
    var buscarClienteVpDebounced = debounce(function () {
        var texto = document.getElementById('vp-cliente-buscar').value.trim();
        document.getElementById('vp-cliente-id').value = '';
        if (texto.length < 2) { document.getElementById('vp-cliente-resultados').classList.remove('open'); return; }
        fetch('/terceros/buscar?query=' + encodeURIComponent(texto), { headers: hdrsVp() }).then(function (r) { return r.json(); }).then(function (data) {
            VP.clientesResultado = Array.isArray(data) ? data : (data.data || []);
            var cont = document.getElementById('vp-cliente-resultados');
            if (!VP.clientesResultado.length) {
                cont.innerHTML = '<div class="vp-opcion-vacio">Sin coincidencias</div>';
            } else {
                cont.innerHTML = VP.clientesResultado.map(function (c) {
                    var nombre = c.nombre_completo || ((c.nombre || '') + ' ' + (c.apellido || '')).trim() || c.razon_social || 'Sin nombre';
                    return '<button type="button" class="vp-opcion" onclick="seleccionarClienteVp(' + c.id + ')"><span>' + escVp(nombre) + '</span></button>';
                }).join('');
            }
            cont.classList.add('open');
        }).catch(function () {});
    }, 300);
    document.getElementById('vp-cliente-buscar').addEventListener('input', buscarClienteVpDebounced);

    window.seleccionarClienteVp = function (id) {
        var c = VP.clientesResultado.find(function (x) { return Number(x.id) === Number(id); });
        if (!c) return;
        var nombre = c.nombre_completo || ((c.nombre || '') + ' ' + (c.apellido || '')).trim() || c.razon_social || 'Sin nombre';
        document.getElementById('vp-cliente-id').value = c.id;
        document.getElementById('vp-cliente-buscar').value = nombre;
        document.getElementById('vp-cliente-resultados').classList.remove('open');
    };

    /* ── Buscador de producto ── */
    var buscarProductoVpDebounced = debounce(function () {
        var texto = document.getElementById('vp-producto-buscar').value.trim();
        document.getElementById('vp-producto-id').value = '';
        fetch('/kardex/productos?buscar=' + encodeURIComponent(texto), { headers: hdrsVp() }).then(function (r) { return r.json(); }).then(function (data) {
            VP.productosResultado = data.data || [];
            var cont = document.getElementById('vp-producto-resultados');
            if (!VP.productosResultado.length) {
                cont.innerHTML = '<div class="vp-opcion-vacio">Sin coincidencias</div>';
            } else {
                cont.innerHTML = VP.productosResultado.map(function (p) {
                    return '<button type="button" class="vp-opcion" onclick="seleccionarProductoVp(' + p.id + ')"><span class="vp-opcion-mono">' + escVp(p.codigo) + '</span><span>' + escVp(p.descripcion) + '</span></button>';
                }).join('');
            }
            cont.classList.add('open');
        }).catch(function () {});
    }, 300);
    document.getElementById('vp-producto-buscar').addEventListener('input', buscarProductoVpDebounced);
    document.getElementById('vp-producto-buscar').addEventListener('focus', buscarProductoVpDebounced);

    window.seleccionarProductoVp = function (id) {
        var p = VP.productosResultado.find(function (x) { return Number(x.id) === Number(id); });
        if (!p) return;
        document.getElementById('vp-producto-id').value = p.id;
        document.getElementById('vp-producto-buscar').value = p.codigo + ' - ' + p.descripcion;
        document.getElementById('vp-producto-resultados').classList.remove('open');
    };

    document.addEventListener('click', function (e) {
        if (!e.target.closest('#vp-grupo-cliente')) document.getElementById('vp-cliente-resultados').classList.remove('open');
        if (!e.target.closest('#vp-grupo-producto')) document.getElementById('vp-producto-resultados').classList.remove('open');
    });

    /* ── Consultar ── */
    window.consultarVentaProductos = function () {
        var desde = document.getElementById('vp-desde').value;
        var hasta = document.getElementById('vp-hasta').value;
        if (!desde || !hasta) { notifVp('Seleccione el rango de fecha y hora.', 'error'); return; }

        var params = new URLSearchParams({
            desde: desde.replace('T', ' ') + ':00',
            hasta: hasta.replace('T', ' ') + ':00',
            con_iva: document.getElementById('vp-con-iva').checked ? 1 : 0,
        });
        var bodegaId = document.getElementById('vp-bodega').value; if (bodegaId) params.set('bodega_id', bodegaId);
        var userId = document.getElementById('vp-usuario').value; if (userId) params.set('user_id', userId);
        var cajaId = document.getElementById('vp-caja').value; if (cajaId) params.set('caja_id', cajaId);
        var prefijo = document.getElementById('vp-prefijo').value; if (prefijo) params.set('prefijo', prefijo);
        var categoria = document.getElementById('vp-categoria').value; if (categoria) params.set('categoria', categoria);
        var grupoMenuId = document.getElementById('vp-grupo-menu').value; if (grupoMenuId) params.set('grupo_menu_id', grupoMenuId);
        var clienteId = document.getElementById('vp-cliente-id').value; if (clienteId) params.set('cliente_id', clienteId);
        var productoId = document.getElementById('vp-producto-id').value; if (productoId) params.set('producto_id', productoId);

        document.getElementById('vp-resultado').innerHTML = '<div class="spinner-cell"><div class="spinner"></div>Generando informe…</div>';
        document.getElementById('vp-metrics').style.display = 'none';

        fetch('/venta-productos/reporte?' + params.toString(), { headers: hdrsVp() })
            .then(function (r) { return r.json().then(function (d) { if (!r.ok) throw new Error(d.message || Object.values(d.errors || {}).flat().join(' ') || 'No fue posible generar el informe.'); return d; }); })
            .then(function (data) { VP.datos = data; renderVentaProductos(data); })
            .catch(function (e) { document.getElementById('vp-resultado').innerHTML = '<div class="spinner-cell">⚠️ ' + escVp(e.message) + '</div>'; notifVp(e.message, 'error'); });
    };

    function renderVentaProductos(data) {
        renderMetricasVp([
            { label: 'Productos vendidos', value: data.totales.productos, accent: '#1D4ED8' },
            { label: 'Facturas', value: data.totales.facturas, accent: '#7C3AED' },
            { label: 'Unidades vendidas', value: fmtCantVp(data.totales.cantidad), accent: '#D97706' },
            { label: 'Valor total (' + (data.con_iva ? 'con IVA' : 'sin IVA') + ')', value: fmtMoneyVp(data.totales.valor), accent: '#059669' },
        ]);

        // Las filas de detalle usan las MISMAS 8 columnas que la tabla principal
        // (no una tabla anidada aparte) para que queden alineadas bajo su columna:
        // Código→Factura, Producto→Fecha, Cantidad→Cantidad, Valor Total→Valor.
        var filas = data.filas.map(function (f, idx) {
            var detalleFacturas = (f.facturas_detalle || []).map(function (d) {
                return '<tr class="vp-det-fila vp-det-' + idx + '" style="display:none;">' +
                    '<td>↳ <span class="td-mono">' + escVp(d.numero_factura) + '</span></td>' +
                    '<td class="td-money">' + fmtFechaHoraVp(d.fecha) + '</td>' +
                    '<td></td><td></td>' +
                    '<td class="td-money">' + fmtCantVp(d.cantidad) + '</td>' +
                    '<td></td>' +
                    '<td class="td-money">' + fmtMoneyVp(d.valor) + '</td>' +
                    '<td></td>' +
                    '</tr>';
            }).join('');

            return '<tr>' +
                '<td><span class="td-mono">' + escVp(f.codigo) + '</span></td>' +
                '<td>' + escVp(f.descripcion) + '</td>' +
                '<td>' + escVp(f.categoria || '—') + '</td>' +
                '<td>' + escVp(f.grupo_menu || '—') + '</td>' +
                '<td class="td-money">' + fmtCantVp(f.cantidad_vendida) + '</td>' +
                '<td class="td-money">' + fmtMoneyVp(f.valor_promedio_unitario) + '</td>' +
                '<td class="td-money">' + fmtMoneyVp(f.valor_total) + '</td>' +
                '<td class="td-money"><button class="vp-btn-facturas" onclick="toggleFacturasVp(' + idx + ')">' + f.facturas + ' 🔎</button></td>' +
                '</tr>' + detalleFacturas;
        }).join('') || '<tr><td colspan="8"><div class="spinner-cell">📭 Sin ventas con estos filtros</div></td></tr>';

        document.getElementById('vp-resultado').innerHTML =
            '<table class="vp-tbl"><thead><tr><th>Código</th><th>Producto</th><th>Categoría</th><th>Grupo Menú</th>' +
            '<th style="text-align:right;">Cantidad</th><th style="text-align:right;">Vlr. Prom. Unit.</th><th style="text-align:right;">Valor Total</th><th style="text-align:right;"># Facturas</th></tr></thead>' +
            '<tbody>' + filas + '</tbody>' +
            '<tfoot><tr><td colspan="4">TOTALES</td><td class="td-money">' + fmtCantVp(data.totales.cantidad) + '</td><td></td><td class="td-money">' + fmtMoneyVp(data.totales.valor) + '</td><td class="td-money">' + data.totales.facturas + '</td></tr></tfoot></table>';
    }

    window.toggleFacturasVp = function (idx) {
        var filas = document.querySelectorAll('.vp-det-' + idx);
        if (!filas.length) return;
        var mostrar = filas[0].style.display === 'none';
        filas.forEach(function (fila) { fila.style.display = mostrar ? 'table-row' : 'none'; });
    };

    function fmtFechaHoraVp(s) {
        if (!s) return '—';
        var d = new Date(String(s).replace(' ', 'T'));
        if (isNaN(d.getTime())) return String(s);
        return d.toLocaleString('es-CO', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    }

    function renderMetricasVp(tarjetas) {
        var cont = document.getElementById('vp-metrics');
        cont.innerHTML = tarjetas.map(function (t) {
            return '<div class="metric-card" style="--accent:' + t.accent + '"><p class="metric-label">' + escVp(t.label) + '</p><p class="metric-value">' + t.value + '</p></div>';
        }).join('');
        cont.style.display = 'grid';
    }

    /* ── Imprimir / exportar ── */
    window.imprimirVentaProductos = function () {
        var tabla = document.querySelector('#vp-resultado table');
        if (!tabla) { notifVp('Genere el informe antes de imprimir.', 'warning'); return; }

        var html = '<!doctype html><html><head><meta charset="utf-8"><title>Venta por Producto</title><style>' +
            '@page{size:letter landscape;margin:14mm;}html{background:#fff;color-scheme:light;}' +
            'body{font-family:Arial,Helvetica,sans-serif;color:#111827;background:#fff;margin:0;}' +
            '.head{display:flex;justify-content:space-between;align-items:flex-end;border-bottom:3px solid #1D4ED8;padding-bottom:12px;margin-bottom:16px;}' +
            '.brand{font-size:20px;font-weight:800;color:#1D4ED8;}.sub{font-size:11px;color:#6B7280;margin-top:2px;}.meta{text-align:right;font-size:12px;color:#374151;}' +
            'table{width:100%;border-collapse:collapse;font-size:11px;}th{background:#F8FAFC;text-align:left;padding:6px 8px;border-bottom:2px solid #E5E7EB;text-transform:uppercase;font-size:9px;color:#6B7280;}' +
            'td{padding:5px 8px;border-bottom:1px solid #F3F4F6;}' +
            '.foot{margin-top:20px;text-align:center;font-size:10px;color:#9CA3AF;border-top:1px solid #E5E7EB;padding-top:10px;}' +
            '</style></head><body><div class="head"><div><div class="brand">🛒 Nexora</div><div class="sub">Venta por Producto</div></div>' +
            '<div class="meta"><b>Generado:</b> ' + new Date().toLocaleString('es-CO') + '</div></div>' +
            tabla.outerHTML +
            '<div class="foot">Generado desde Nexora — Sistema de Gestión POS</div></body></html>';

        var ventana = window.open('', '_blank', 'width=1100,height=700');
        if (!ventana) { notifVp('El navegador bloqueó la ventana de impresión.', 'error'); return; }
        ventana.document.open(); ventana.document.write(html); ventana.document.close();
        setTimeout(function () { ventana.focus(); ventana.print(); }, 300);
    };

    window.exportarVentaProductosCsv = function () {
        var tabla = document.querySelector('#vp-resultado table');
        if (!tabla) { notifVp('Genere el informe antes de exportar.', 'warning'); return; }

        var filas = [];
        tabla.querySelectorAll('tr').forEach(function (tr) {
            var celdas = Array.from(tr.children).map(function (td) { return '"' + td.textContent.trim().replace(/"/g, '""') + '"'; });
            filas.push(celdas.join(','));
        });

        var csv = '﻿' + filas.join('\n');
        var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = 'venta-por-producto.csv';
        a.click();
        URL.revokeObjectURL(a.href);
        notifVp('Exportación creada.', 'success');
    };

    function notifVp(msg, tipo) {
        if (typeof mostrarNotificacion === 'function') { mostrarNotificacion(msg, tipo === 'error' ? 'error' : tipo === 'warning' ? 'warning' : 'success'); return; }
        window.alert(msg);
    }
</script>
