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
    .filter-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(170px, 1fr)); gap: 10px; align-items: end; }
    .pv-field { display: flex; flex-direction: column; gap: 3px; }
    .pv-field label { font-size: 11px; font-weight: 500; color: #9CA3AF; text-transform: uppercase; letter-spacing: .3px; }
    .pv-field input, .pv-field select { border: 1px solid #D1D5DB; border-radius: 7px; padding: 7px 9px; font-size: 12.5px; color: #111827; background: #F9FAFB; outline: none; width: 100%; box-sizing: border-box; }
    .pv-field input:focus, .pv-field select:focus { border-color: #1D4ED8; background: #fff; }

    .btn-primary { display: inline-flex; align-items: center; gap: 6px; background: #1D4ED8; color: #fff; border: none; border-radius: 7px; padding: 8px 16px; font-size: 12.5px; font-weight: 600; cursor: pointer; }
    .btn-primary:hover { background: #1e40af; }
    .btn-outline { display: inline-flex; align-items: center; gap: 5px; background: #fff; color: #374151; border: 1px solid #D1D5DB; border-radius: 7px; padding: 8px 14px; font-size: 12.5px; font-weight: 500; cursor: pointer; }
    .btn-outline:hover { background: #F3F4F6; }

    .table-wrapper { background: #fff; border: 1px solid #EAECF0; border-radius: 10px; overflow: hidden; }
    .table-scroll { overflow-x: auto; }
    table.pv-tbl { width: 100%; border-collapse: collapse; font-size: 12.5px; }
    table.pv-tbl thead { background: #F8FAFC; border-bottom: 1px solid #EAECF0; }
    table.pv-tbl thead th { padding: 10px 12px; text-align: left; font-size: 11px; font-weight: 600; color: #6B7280; text-transform: uppercase; letter-spacing: .5px; white-space: nowrap; }
    table.pv-tbl tbody tr { border-bottom: 1px solid #F3F4F6; }
    table.pv-tbl tbody tr:hover { background: #F8FAFC; }
    table.pv-tbl tfoot td { font-weight: 700; color: #111827; border-top: 2px solid #E5E7EB; }
    table.pv-tbl td { padding: 8px 12px; color: #374151; vertical-align: middle; }
    .td-money { font-weight: 600; color: #111827; text-align: right; white-space: nowrap; }
    .td-mono { font-family: 'JetBrains Mono','Fira Mono',monospace; font-size: 12px; color: #1D4ED8; font-weight: 600; }

    .pv-btn-facturas { background: #EFF6FF; color: #1D4ED8; border: 1px solid #BFDBFE; border-radius: 6px; padding: 3px 8px; font-size: 11.5px; font-weight: 600; cursor: pointer; }
    .pv-btn-facturas:hover { background: #DBEAFE; }
    .pv-det-fila td { color: #4B5563; font-size: 12px; background: #F8FAFC; }

    .spinner-cell { display: flex; align-items: center; justify-content: center; padding: 40px; color: #6B7280; font-size: 13px; gap: 10px; }
    @keyframes spin-pv { to { transform: rotate(360deg); } }
    .spinner { width: 18px; height: 18px; border: 2px solid #E5E7EB; border-top-color: #1D4ED8; border-radius: 50%; animation: spin-pv .7s linear infinite; }

    @media (max-width: 640px) { .metrics-row { grid-template-columns: 1fr 1fr; } }
</style>

<div id="view-propinas-vendedor">
    <div class="sec-header">
        <div>
            <p class="sec-title">💵 Propinas por Vendedor</p>
            <p class="sec-subtitle">Cuánto ha recaudado en propinas cada mesero/cajero que cobra cuentas</p>
        </div>
        <button class="btn-primary" onclick="imprimirPropinasVendedor()">🖨️ Imprimir</button>
    </div>

    <div class="filter-panel">
        <div class="filter-grid">
            <div class="pv-field"><label>Desde</label><input autocomplete="off" type="datetime-local" id="pv-desde"></div>
            <div class="pv-field"><label>Hasta</label><input autocomplete="off" type="datetime-local" id="pv-hasta"></div>
            <div class="pv-field"><label>Bodega</label><select id="pv-bodega"><option value="">Todas</option></select></div>
            <div class="pv-field"><label>Caja</label><select id="pv-caja"><option value="">Todas</option></select></div>
            <div class="pv-field"><label>Vendedor</label><select id="pv-usuario"><option value="">Todos</option></select></div>
            <div class="pv-field"><button class="btn-primary" style="width:100%;" onclick="consultarPropinasVendedor()">🔍 Consultar</button></div>
        </div>
    </div>

    <div class="metrics-row" id="pv-metrics" style="display:none;"></div>

    <div class="table-wrapper">
        <div class="table-scroll">
            <div id="pv-resultado">
                <div class="spinner-cell">💵 Ajuste los filtros y presione "Consultar"</div>
            </div>
        </div>
    </div>
</div>

<script>
    var PV = { datos: null };
    function escPv(s) { return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
    function fmtMoneyPv(n) { return '$ ' + (Number(n) || 0).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 }); }
    function hdrsPv() { return { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content, 'Accept': 'application/json' }; }
    function fmtFechaHoraPv(s) {
        if (!s) return '—';
        var d = new Date(String(s).replace(' ', 'T'));
        if (isNaN(d.getTime())) return String(s);
        return d.toLocaleString('es-CO', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' });
    }
    window.toggleFacturasPv = function (idx) {
        var filas = document.querySelectorAll('.pv-det-' + idx);
        if (!filas.length) return;
        var mostrar = filas[0].style.display === 'none';
        filas.forEach(function (fila) { fila.style.display = mostrar ? 'table-row' : 'none'; });
    };

    (function initPropinasVendedor() {
        var hoy = new Date();
        var primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        function isoDT(d) { return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0') + 'T' + String(d.getHours()).padStart(2, '0') + ':' + String(d.getMinutes()).padStart(2, '0'); }
        document.getElementById('pv-desde').value = primerDia.getFullYear() + '-' + String(primerDia.getMonth() + 1).padStart(2, '0') + '-01T00:00';
        document.getElementById('pv-hasta').value = isoDT(hoy);

        fetch('/venta-productos/filtros', { headers: hdrsPv() }).then(function (r) { return r.json(); }).then(function (res) {
            document.getElementById('pv-bodega').insertAdjacentHTML('beforeend', res.bodegas.map(function (b) { return '<option value="' + b.id + '">' + escPv(b.descripcion) + '</option>'; }).join(''));
            document.getElementById('pv-caja').insertAdjacentHTML('beforeend', res.cajas.map(function (c) { return '<option value="' + c.id + '">' + escPv(c.nombre) + '</option>'; }).join(''));
            document.getElementById('pv-usuario').insertAdjacentHTML('beforeend', res.usuarios.map(function (u) { return '<option value="' + u.id + '">' + escPv(u.name) + '</option>'; }).join(''));
        }).catch(function () {});
    })();

    window.consultarPropinasVendedor = function () {
        var desde = document.getElementById('pv-desde').value;
        var hasta = document.getElementById('pv-hasta').value;
        if (!desde || !hasta) { notifPv('Seleccione el rango de fecha y hora.', 'error'); return; }

        var params = new URLSearchParams({ desde: desde.replace('T', ' ') + ':00', hasta: hasta.replace('T', ' ') + ':00' });
        var bodegaId = document.getElementById('pv-bodega').value; if (bodegaId) params.set('bodega_id', bodegaId);
        var cajaId = document.getElementById('pv-caja').value; if (cajaId) params.set('caja_id', cajaId);
        var userId = document.getElementById('pv-usuario').value; if (userId) params.set('user_id', userId);

        document.getElementById('pv-resultado').innerHTML = '<div class="spinner-cell"><div class="spinner"></div>Generando informe…</div>';
        document.getElementById('pv-metrics').style.display = 'none';

        fetch('/propinas-vendedor/reporte?' + params.toString(), { headers: hdrsPv() })
            .then(function (r) { return r.json().then(function (d) { if (!r.ok) throw new Error(d.message || Object.values(d.errors || {}).flat().join(' ') || 'No fue posible generar el informe.'); return d; }); })
            .then(function (data) { PV.datos = data; renderPropinasVendedor(data); })
            .catch(function (e) { document.getElementById('pv-resultado').innerHTML = '<div class="spinner-cell">⚠️ ' + escPv(e.message) + '</div>'; notifPv(e.message, 'error'); });
    };

    function renderPropinasVendedor(data) {
        document.getElementById('pv-metrics').innerHTML = [
            { label: 'Vendedores', value: data.totales.vendedores, accent: '#1D4ED8' },
            { label: 'Facturas', value: data.totales.facturas, accent: '#7C3AED' },
            { label: 'Total ventas', value: fmtMoneyPv(data.totales.total_ventas), accent: '#D97706' },
            { label: 'Total propinas', value: fmtMoneyPv(data.totales.total_propinas), accent: '#059669' },
        ].map(function (t) { return '<div class="metric-card" style="--accent:' + t.accent + '"><p class="metric-label">' + escPv(t.label) + '</p><p class="metric-value">' + t.value + '</p></div>'; }).join('');
        document.getElementById('pv-metrics').style.display = 'grid';

        // Las filas de detalle usan las MISMAS 6 columnas que la tabla principal
        // (no una tabla anidada aparte) para que el navegador las alinee solo:
        // Vendedor→Factura, Facturas→Fecha, Con Propina→(vacío),
        // Total Ventas→Subtotal, Total Propinas→Propina, Propina Prom.→(vacío).
        var filas = data.filas.map(function (f, idx) {
            var detalleFacturas = (f.facturas_detalle || []).map(function (d) {
                return '<tr class="pv-det-fila pv-det-' + idx + '" style="display:none;">' +
                    '<td>↳ <span class="td-mono">' + escPv(d.numero_factura) + '</span></td>' +
                    '<td class="td-money">' + fmtFechaHoraPv(d.fecha) + '</td>' +
                    '<td></td>' +
                    '<td class="td-money">' + fmtMoneyPv(d.subtotal) + '</td>' +
                    '<td class="td-money">' + fmtMoneyPv(d.propina) + '</td>' +
                    '<td></td>' +
                    '</tr>';
            }).join('') || '<tr class="pv-det-fila pv-det-' + idx + '" style="display:none;"><td colspan="6" style="text-align:center;color:#9CA3AF;">Ninguna de sus facturas trajo propina</td></tr>';

            return '<tr>' +
                '<td>' + escPv(f.vendedor) + '</td>' +
                '<td class="td-money">' + f.facturas + '</td>' +
                '<td class="td-money"><button class="pv-btn-facturas" onclick="toggleFacturasPv(' + idx + ')">' + f.facturas_con_propina + ' 🔎</button></td>' +
                '<td class="td-money">' + fmtMoneyPv(f.total_ventas) + '</td>' +
                '<td class="td-money">' + fmtMoneyPv(f.total_propinas) + '</td>' +
                '<td class="td-money">' + fmtMoneyPv(f.propina_promedio) + '</td>' +
                '</tr>' + detalleFacturas;
        }).join('') || '<tr><td colspan="6"><div class="spinner-cell">📭 Sin ventas con estos filtros</div></td></tr>';

        document.getElementById('pv-resultado').innerHTML =
            '<table class="pv-tbl"><thead><tr><th>Vendedor</th><th style="text-align:right;">Facturas</th><th style="text-align:right;">Con Propina</th>' +
            '<th style="text-align:right;">Total Ventas</th><th style="text-align:right;">Total Propinas</th><th style="text-align:right;">Propina Prom.</th></tr></thead>' +
            '<tbody>' + filas + '</tbody>' +
            '<tfoot><tr><td colspan="3">TOTALES</td><td class="td-money">' + fmtMoneyPv(data.totales.total_ventas) + '</td><td class="td-money">' + fmtMoneyPv(data.totales.total_propinas) + '</td><td></td></tr></tfoot></table>';
    }

    window.imprimirPropinasVendedor = function () {
        var tabla = document.querySelector('#pv-resultado table');
        if (!tabla) { notifPv('Genere el informe antes de imprimir.', 'warning'); return; }

        var html = '<!doctype html><html><head><meta charset="utf-8"><title>Propinas por Vendedor</title><style>' +
            '@page{size:letter;margin:14mm;}html{background:#fff;color-scheme:light;}' +
            'body{font-family:Arial,Helvetica,sans-serif;color:#111827;background:#fff;margin:0;}' +
            '.head{display:flex;justify-content:space-between;align-items:flex-end;border-bottom:3px solid #1D4ED8;padding-bottom:12px;margin-bottom:16px;}' +
            '.brand{font-size:20px;font-weight:800;color:#1D4ED8;}.sub{font-size:11px;color:#6B7280;margin-top:2px;}.meta{text-align:right;font-size:12px;color:#374151;}' +
            'table{width:100%;border-collapse:collapse;font-size:11px;}th{background:#F8FAFC;text-align:left;padding:6px 8px;border-bottom:2px solid #E5E7EB;text-transform:uppercase;font-size:9px;color:#6B7280;}' +
            'td{padding:5px 8px;border-bottom:1px solid #F3F4F6;}' +
            '</style></head><body><div class="head"><div><div class="brand">💵 Nexora</div><div class="sub">Propinas por Vendedor</div></div>' +
            '<div class="meta"><b>Generado:</b> ' + new Date().toLocaleString('es-CO') + '</div></div>' +
            tabla.outerHTML + '</body></html>';

        var ventana = window.open('', '_blank', 'width=900,height=700');
        if (!ventana) { notifPv('El navegador bloqueó la ventana de impresión.', 'error'); return; }
        ventana.document.open(); ventana.document.write(html); ventana.document.close();
        setTimeout(function () { ventana.focus(); ventana.print(); }, 300);
    };

    function notifPv(msg, tipo) {
        if (typeof mostrarNotificacion === 'function') { mostrarNotificacion(msg, tipo === 'error' ? 'error' : tipo === 'warning' ? 'warning' : 'success'); return; }
        window.alert(msg);
    }
</script>
