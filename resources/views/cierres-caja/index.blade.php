<style>
    .sec-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 16px;
    }

    .sec-title {
        font-size: 17px;
        font-weight: 600;
        color: #111827;
        letter-spacing: -0.3px;
    }

    .sec-subtitle {
        font-size: 12px;
        color: #6B7280;
        margin-top: 2px;
    }

    /* ── Barra de filtros ── */
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

    .filter-bar .fi-group {
        display: flex;
        align-items: center;
        gap: 6px;
        flex: 1;
        min-width: 160px;
    }

    .fi-label {
        font-size: 12px;
        color: #6B7280;
        white-space: nowrap;
    }

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

    .fi-input:focus {
        border-color: #1D4ED8;
        background: #fff;
    }

    .fi-input::placeholder {
        color: #9CA3AF;
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
    }

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

    .btn-primary:hover {
        background: #1e40af;
    }

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

    .btn-outline:hover {
        background: #F3F4F6;
        border-color: #9CA3AF;
    }

    /* ── Tabla ── */
    .table-wrapper {
        background: #fff;
        border: 1px solid #EAECF0;
        border-radius: 10px;
        overflow: hidden;
    }

    .table-scroll {
        overflow-x: auto;
    }

    table.cierres-tbl {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
    }

    table.cierres-tbl thead {
        background: #F8FAFC;
        border-bottom: 1px solid #EAECF0;
    }

    table.cierres-tbl thead th {
        padding: 10px 12px;
        text-align: left;
        font-size: 11px;
        font-weight: 600;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
        cursor: pointer;
        user-select: none;
    }

    table.cierres-tbl thead th:hover {
        color: #1D4ED8;
    }

    table.cierres-tbl thead th .sort-icon {
        display: inline-block;
        margin-left: 4px;
        opacity: 0.4;
        font-size: 10px;
    }

    table.cierres-tbl thead th.sorted .sort-icon {
        opacity: 1;
        color: #1D4ED8;
    }

    table.cierres-tbl tbody tr {
        border-bottom: 1px solid #F3F4F6;
        transition: background 0.1s;
    }

    table.cierres-tbl tbody tr:last-child {
        border-bottom: none;
    }

    table.cierres-tbl tbody tr:hover {
        background: #F8FAFC;
    }

    table.cierres-tbl td {
        padding: 9px 12px;
        color: #374151;
        vertical-align: middle;
    }

    .td-mono {
        font-family: 'JetBrains Mono', 'Fira Mono', monospace;
        font-size: 12px;
        color: #111827;
        font-weight: 500;
    }

    .td-money {
        font-weight: 600;
        color: #111827;
        text-align: right;
    }

    .td-money.pos {
        color: #1D4ED8;
    }

    .td-money.neg {
        color: #DC2626;
    }

    .td-money.zero {
        color: #059669;
    }

    .td-cajero {
        line-height: 1.35;
    }

    .td-cajero .caja-nombre {
        font-weight: 600;
        color: #111827;
    }

    .td-cajero .cajero-nombre {
        font-size: 11px;
        color: #9CA3AF;
    }

    .td-rango {
        font-size: 11.5px;
        color: #6B7280;
        line-height: 1.35;
    }

    .td-rango .rango-cant {
        color: #111827;
        font-weight: 600;
    }

    /* ── Badges ── */
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

    .badge-green {
        background: #ECFDF5;
        color: #065F46;
    }

    .badge-red {
        background: #FEF2F2;
        color: #991B1B;
    }

    .badge-blue {
        background: #EFF6FF;
        color: #1e3a8a;
    }

    .badge-gray {
        background: #F3F4F6;
        color: #374151;
    }

    .dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        display: inline-block;
        background: currentColor;
    }

    /* ── Acciones ── */
    .tbl-actions {
        display: flex;
        align-items: center;
        gap: 4px;
    }

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

    .act-btn:hover {
        background: #F3F4F6;
        color: #111827;
        transform: scale(1.05);
    }

    .act-btn.view:hover {
        background: #EFF6FF;
        color: #1D4ED8;
    }

    .act-btn.print:hover {
        background: #F3F4F6;
        color: #111827;
    }

    .act-btn::after {
        content: attr(data-tip);
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

    .act-btn:hover::after {
        opacity: 1;
    }

    /* ── Paginación ── */
    .pagination-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 14px;
        border-top: 1px solid #F3F4F6;
        flex-wrap: wrap;
        gap: 8px;
    }

    .pag-info {
        font-size: 12px;
        color: #6B7280;
    }

    .pag-btns {
        display: flex;
        gap: 4px;
    }

    .pag-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: 6px;
        border: 1px solid #E5E7EB;
        background: #fff;
        font-size: 12px;
        color: #374151;
        cursor: pointer;
        transition: all 0.15s;
    }

    .pag-btn:hover {
        background: #F3F4F6;
        border-color: #9CA3AF;
    }

    .pag-btn.active {
        background: #1D4ED8;
        color: #fff;
        border-color: #1D4ED8;
    }

    .pag-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    /* ── Modal detalle ── */
    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(17, 24, 39, 0.5);
        backdrop-filter: blur(4px);
        z-index: 9000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    .modal-cierre {
        background: #fff;
        border-radius: 16px;
        width: 100%;
        max-width: 620px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }

    .modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid #EAECF0;
        flex-shrink: 0;
    }

    .modal-head-title {
        font-size: 15px;
        font-weight: 600;
        color: #111827;
    }

    .modal-head-sub {
        font-size: 12px;
        color: #6B7280;
        margin-top: 2px;
    }

    .modal-body {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
    }

    .modal-foot {
        padding: 14px 20px;
        border-top: 1px solid #EAECF0;
        display: flex;
        gap: 8px;
        justify-content: flex-end;
        flex-shrink: 0;
    }

    .cc-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 16px;
    }

    @media (max-width: 480px) {
        .cc-grid {
            grid-template-columns: 1fr;
        }
    }

    .cc-field label {
        font-size: 11px;
        font-weight: 500;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 3px;
    }

    .cc-field .cc-field-val {
        padding: 7px 10px;
        background: #F9FAFB;
        border: 1px solid #E5E7EB;
        border-radius: 7px;
        font-size: 13px;
        color: #111827;
    }

    .fac-section-title {
        font-size: 12px;
        font-weight: 600;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin: 16px 0 10px;
        padding-bottom: 6px;
        border-bottom: 1px solid #F3F4F6;
    }

    .totales-box {
        background: #F8FAFC;
        border: 1px solid #EAECF0;
        border-radius: 10px;
        padding: 12px 14px;
        margin-top: 4px;
    }

    .totales-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 4px 0;
        font-size: 13px;
        color: #374151;
    }

    .totales-row.total-final {
        border-top: 1px solid #EAECF0;
        margin-top: 6px;
        padding-top: 10px;
        font-size: 16px;
        font-weight: 700;
        color: #111827;
    }

    /* Spinner */
    .spinner-cell {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
        color: #6B7280;
        font-size: 13px;
        gap: 10px;
    }

    @keyframes spin {
        to {
            transform: rotate(360deg);
        }
    }

    .spinner {
        width: 18px;
        height: 18px;
        border: 2px solid #E5E7EB;
        border-top-color: #1D4ED8;
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
    }

    @media (max-width: 640px) {
        table.cierres-tbl thead th:nth-child(n+6) {
            display: none;
        }

        table.cierres-tbl tbody td:nth-child(n+6) {
            display: none;
        }
    }
</style>

<div id="view-cierres-caja">

    {{-- ── ENCABEZADO ── --}}
    <div class="sec-header">
        <div>
            <p class="sec-title">🧾 Cierres de Caja</p>
            <p class="sec-subtitle">Historial de arqueos realizados por cada cajero</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button class="btn-outline" onclick="exportarCierres()">
                ⬇️ Exportar
            </button>
        </div>
    </div>

    {{-- ── FILTROS ── --}}
    <div class="filter-bar">
        <div class="fi-group" style="flex:2;min-width:200px;">
            <span class="fi-label">🔍</span>
            <input class="fi-input" type="text" id="cc-buscar" placeholder="Caja o cajero…"
                oninput="aplicarFiltrosCC()">
        </div>
        <div class="fi-group">
            <span class="fi-label">Desde</span>
            <input class="fi-input" type="date" id="cc-desde" onchange="cargarCierresCaja()">
        </div>
        <div class="fi-group">
            <span class="fi-label">Hasta</span>
            <input class="fi-input" type="date" id="cc-hasta" onchange="cargarCierresCaja()">
        </div>
        <div class="fi-group">
            <select class="fi-select" id="cc-estado" onchange="aplicarFiltrosCC()">
                <option value="">Todos los resultados</option>
                <option value="cuadrado">Cuadrado</option>
                <option value="sobrante">Sobrante</option>
                <option value="faltante">Faltante</option>
            </select>
        </div>
        <button class="btn-outline" onclick="limpiarFiltrosCC()">✕ Limpiar</button>
    </div>

    {{-- ── TABLA ── --}}
    <div class="table-wrapper">
        <div class="table-scroll">
            <table class="cierres-tbl" id="tbl-cierres">
                <thead>
                    <tr>
                        <th onclick="sortTablaCC('fecha_inicio')" data-col="fecha_inicio">
                            Fecha de apertura <span class="sort-icon">↕</span>
                        </th>
                        <th onclick="sortTablaCC('caja')" data-col="caja">
                            Caja / Cajero <span class="sort-icon">↕</span>
                        </th>
                        <th>Facturas</th>
                        <th onclick="sortTablaCC('base_inicial')" data-col="base_inicial" style="text-align:right;">
                            Base <span class="sort-icon">↕</span>
                        </th>
                        <th onclick="sortTablaCC('efectivo_esperado')" data-col="efectivo_esperado"
                            style="text-align:right;">
                            Esperado <span class="sort-icon">↕</span>
                        </th>
                        <th onclick="sortTablaCC('total_fisico')" data-col="total_fisico" style="text-align:right;">
                            Físico <span class="sort-icon">↕</span>
                        </th>
                        <th onclick="sortTablaCC('diferencia')" data-col="diferencia" style="text-align:right;">
                            Diferencia <span class="sort-icon">↕</span>
                        </th>
                        <th>Estado</th>
                        <th style="text-align:center;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-cierres">
                    <tr>
                        <td colspan="9">
                            <div class="spinner-cell">
                                <div class="spinner"></div>
                                Cargando cierres…
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="pagination-bar">
            <span class="pag-info" id="pag-info-cc">Mostrando 0 de 0 registros</span>
            <div class="pag-btns" id="pag-btns-cc"></div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════
     MODAL VER DETALLE DE CIERRE (solo lectura)
═══════════════════════════════════════════════ --}}
<div id="modal-ver-cierre" style="display:none;" class="modal-backdrop" onclick="cerrarVerCierreBackdrop(event)">
    <div class="modal-cierre">
        <div class="modal-head">
            <div>
                <p class="modal-head-title" id="vc-title">Detalle de cierre</p>
                <p class="modal-head-sub" id="vc-sub"></p>
            </div>
            <button onclick="cerrarVerCierre()"
                style="
                border:none;background:transparent;font-size:20px;cursor:pointer;
                color:#6B7280;padding:4px;border-radius:6px;line-height:1;
            ">✕</button>
        </div>
        <div class="modal-body" id="vc-body">
            {{-- Se llena dinámicamente --}}
        </div>
        <div class="modal-foot">
            <button class="btn-outline" onclick="imprimirCierre()">🖨️ Imprimir</button>
            <button class="btn-outline" onclick="cerrarVerCierre()">Cerrar</button>
        </div>
    </div>
</div>

<script>
    /* ════════════════════════════════════════════════
       ESTADO GLOBAL
    ════════════════════════════════════════════════ */
    var CC = {
        datos: [], // cierres cargados desde el servidor (rango desde/hasta)
        filtrados: [], // tras aplicar búsqueda / estado
        paginaActual: 1,
        porPagina: 15,
        sortCol: 'fecha_inicio',
        sortAsc: false,
        cierreActual: null,
    };

    /* ════════════════════════════════════════════════
       INICIALIZACIÓN
    ════════════════════════════════════════════════ */
    (function initCC() {
        var hoy = new Date();
        var haceUnMes = new Date(hoy.getFullYear(), hoy.getMonth() - 1, hoy.getDate());
        document.getElementById('cc-desde').value = fmt(haceUnMes);
        document.getElementById('cc-hasta').value = fmt(hoy);

        cargarCierresCaja();

        function fmt(d) {
            return d.getFullYear() + '-' +
                String(d.getMonth() + 1).padStart(2, '0') + '-' +
                String(d.getDate()).padStart(2, '0');
        }
    })();

    /* ════════════════════════════════════════════════
       CARGA DE DATOS
    ════════════════════════════════════════════════ */
    function cargarCierresCaja() {
        mostrarSpinnerCC();

        var params = new URLSearchParams();
        var desde = document.getElementById('cc-desde').value;
        var hasta = document.getElementById('cc-hasta').value;
        if (desde) params.set('desde', desde);
        if (hasta) params.set('hasta', hasta);

        fetch('/cierres-caja/data?' + params, {
                headers: {
                    Accept: 'application/json'
                }
            })
            .then(function(r) {
                return r.json();
            })
            .then(function(res) {
                CC.datos = Array.isArray(res.data) ? res.data : (Array.isArray(res) ? res : []);
                aplicarFiltrosCC();
            })
            .catch(function() {
                /* Sin backend disponible: datos de previsualización */
                CC.datos = datosDemoCC();
                aplicarFiltrosCC();
            });
    }

    function datosDemoCC() {
        var hoy = new Date();
        var cajas = ['Caja Principal', 'Caja 2', 'Caja Terraza'];
        var cajeros = ['Laura Gómez', 'Andrés Torres', 'Camila Rojas', 'Diego Salazar'];
        var out = [];
        for (var i = 1; i <= 34; i++) {
            var fin = new Date(hoy);
            fin.setDate(hoy.getDate() - Math.floor(Math.random() * 30));
            var base = Math.round((Math.random() * 200000 + 50000) / 1000) * 1000;
            var esperado = base + Math.round((Math.random() * 900000 + 50000) / 1000) * 1000;
            var ruido = Math.round((Math.random() - 0.5) * 60000 / 1000) * 1000;
            var fisico = esperado + (Math.random() < 0.45 ? 0 : ruido);
            out.push({
                id: i,
                fecha_inicio: fin.toISOString(),
                fecha_fin: fin.toISOString(),
                caja: {
                    nombre: cajas[i % cajas.length]
                },
                usuario: {
                    name: cajeros[i % cajeros.length]
                },
                factura_inicial: 'FV' + String(1000 + i * 3),
                factura_final: 'FV' + String(1000 + i * 3 + 12),
                cantidad_facturas: 12 + (i % 8),
                base_inicial: base,
                efectivo_esperado: esperado,
                total_fisico: fisico,
                diferencia: fisico - esperado,
                observaciones: i % 6 === 0 ? 'Faltó registrar una devolución en efectivo.' : ''
            });
        }
        return out.sort(function(a, b) {
            return new Date(b.fecha_fin) - new Date(a.fecha_fin);
        });
    }

    /* ════════════════════════════════════════════════
       FILTROS
    ════════════════════════════════════════════════ */
    function aplicarFiltrosCC() {
        var buscar = (document.getElementById('cc-buscar').value || '').toLowerCase().trim();
        var estado = document.getElementById('cc-estado').value;

        CC.filtrados = CC.datos.filter(function(c) {
            if (buscar) {
                var texto = ((c.caja?.nombre || '') + ' ' + (c.usuario?.name || '')).toLowerCase();
                if (!texto.includes(buscar)) return false;
            }
            if (estado) {
                var diff = Number(c.diferencia || 0);
                var est = diff === 0 ? 'cuadrado' : (diff > 0 ? 'sobrante' : 'faltante');
                if (est !== estado) return false;
            }
            return true;
        });

        CC.paginaActual = 1;
        sortTablaActualCC();
        renderTablaCC();
    }

    function limpiarFiltrosCC() {
        document.getElementById('cc-buscar').value = '';
        document.getElementById('cc-estado').value = '';
        var hoy = new Date();
        var haceUnMes = new Date(hoy.getFullYear(), hoy.getMonth() - 1, hoy.getDate());
        document.getElementById('cc-desde').value = haceUnMes.toISOString().slice(0, 10);
        document.getElementById('cc-hasta').value = hoy.toISOString().slice(0, 10);
        cargarCierresCaja();
    }

    /* ════════════════════════════════════════════════
       ORDENAMIENTO
    ════════════════════════════════════════════════ */
    function sortTablaCC(col) {
        if (CC.sortCol === col) CC.sortAsc = !CC.sortAsc;
        else {
            CC.sortCol = col;
            CC.sortAsc = false;
        }
        sortTablaActualCC();
        renderTablaCC();
        document.querySelectorAll('table.cierres-tbl thead th[data-col]').forEach(function(th) {
            th.classList.toggle('sorted', th.dataset.col === col);
            var icon = th.querySelector('.sort-icon');
            if (icon) icon.textContent = th.dataset.col === col ? (CC.sortAsc ? '↑' : '↓') : '↕';
        });
    }

    function sortTablaActualCC() {
        var col = CC.sortCol,
            asc = CC.sortAsc;
        CC.filtrados.sort(function(a, b) {
            var va, vb;
            if (col === 'caja') {
                va = (a.caja?.nombre || '');
                vb = (b.caja?.nombre || '');
                return asc ? String(va).localeCompare(vb) : String(vb).localeCompare(va);
            }
            if (col === 'fecha_inicio') {
                va = new Date(a.fecha_inicio || 0).getTime();
                vb = new Date(b.fecha_inicio || 0).getTime();
                return asc ? va - vb : vb - va;
            }
            va = Number(a[col] || 0);
            vb = Number(b[col] || 0);
            return asc ? va - vb : vb - va;
        });
    }

    /* ════════════════════════════════════════════════
       RENDER TABLA
    ════════════════════════════════════════════════ */
    function renderTablaCC() {
        var tbody = document.getElementById('tbody-cierres');
        var total = CC.filtrados.length;
        var desde = (CC.paginaActual - 1) * CC.porPagina;
        var pagina = CC.filtrados.slice(desde, desde + CC.porPagina);

        document.getElementById('pag-info-cc').textContent =
            'Mostrando ' + Math.min(pagina.length, total) + ' de ' + total + ' registros';

        if (pagina.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9">' +
                '<div class="spinner-cell">📭 No se encontraron cierres para este período</div></td></tr>';
            renderPaginacionCC(total);
            return;
        }

        tbody.innerHTML = pagina.map(function(c) {
            var diff = Number(c.diferencia || 0);
            var moneyCls = diff === 0 ? 'zero' : (diff > 0 ? 'pos' : 'neg');
            var badge = diff === 0 ?
                '<span class="badge badge-green"><span class="dot"></span>Cuadrado</span>' :
                (diff > 0 ?
                    '<span class="badge badge-blue"><span class="dot"></span>Sobrante</span>' :
                    '<span class="badge badge-red"><span class="dot"></span>Faltante</span>');

            var btnVer = '<button class="act-btn view" data-tip="Ver detalle" ' +
                'onclick="verCierre(' + c.id + ')">👁️</button>';
            var btnPrint = '<button class="act-btn print" data-tip="Imprimir" ' +
                'onclick="imprimirCierreRapido(' + c.id + ')">🖨️</button>';

            return '<tr data-id="' + c.id + '">' +
                '<td>' + fmtFechaHoraCC(c.fecha_inicio) + '</td>' +
                '<td class="td-cajero">' +
                '<div class="caja-nombre">' + esc(c.caja?.nombre || '—') + '</div>' +
                '<div class="cajero-nombre">' + esc(c.usuario?.name || '—') + '</div>' +
                '</td>' +
                '<td class="td-rango">' +
                esc(c.factura_inicial || 'N/A') + ' – ' + esc(c.factura_final || 'N/A') + '<br>' +
                '<span class="rango-cant">' + (c.cantidad_facturas || 0) + '</span> facturas' +
                '</td>' +
                '<td class="td-money">' + fmtMoneyCC(c.base_inicial) + '</td>' +
                '<td class="td-money">' + fmtMoneyCC(c.efectivo_esperado) + '</td>' +
                '<td class="td-money">' + fmtMoneyCC(c.total_fisico) + '</td>' +
                '<td class="td-money ' + moneyCls + '">' + fmtMoneyCC(diff) + '</td>' +
                '<td>' + badge + '</td>' +
                '<td><div class="tbl-actions">' + btnVer + btnPrint + '</div></td>' +
                '</tr>';
        }).join('');

        renderPaginacionCC(total);
    }

    function renderPaginacionCC(total) {
        var totalPags = Math.max(1, Math.ceil(total / CC.porPagina));
        var actual = CC.paginaActual;
        var btns = document.getElementById('pag-btns-cc');
        var html = '';

        html += '<button class="pag-btn" onclick="irPaginaCC(' + (actual - 1) + ')"' +
            (actual === 1 ? ' disabled' : '') + '>‹</button>';

        var desde = Math.max(1, actual - 2);
        var hasta = Math.min(totalPags, desde + 4);
        desde = Math.max(1, hasta - 4);

        if (desde > 1) html += '<button class="pag-btn" onclick="irPaginaCC(1)">1</button>' +
            (desde > 2 ? '<span style="padding:0 4px;color:#9CA3AF;">…</span>' : '');

        for (var p = desde; p <= hasta; p++) {
            html += '<button class="pag-btn' + (p === actual ? ' active' : '') + '" onclick="irPaginaCC(' + p +
                ')">' + p + '</button>';
        }

        if (hasta < totalPags) {
            html += (hasta < totalPags - 1 ? '<span style="padding:0 4px;color:#9CA3AF;">…</span>' : '') +
                '<button class="pag-btn" onclick="irPaginaCC(' + totalPags + ')">' + totalPags + '</button>';
        }

        html += '<button class="pag-btn" onclick="irPaginaCC(' + (actual + 1) + ')"' +
            (actual === totalPags ? ' disabled' : '') + '>›</button>';

        btns.innerHTML = html;
    }

    function irPaginaCC(p) {
        var total = CC.filtrados.length;
        var totalPags = Math.max(1, Math.ceil(total / CC.porPagina));
        CC.paginaActual = Math.max(1, Math.min(p, totalPags));
        renderTablaCC();
    }

    function mostrarSpinnerCC() {
        document.getElementById('tbody-cierres').innerHTML =
            '<tr><td colspan="9"><div class="spinner-cell">' +
            '<div class="spinner"></div>Cargando cierres…</div></td></tr>';
    }

    /* ════════════════════════════════════════════════
       DETALLE DE UN CIERRE
    ════════════════════════════════════════════════ */
    function verCierre(id) {
        var c = CC.datos.find(function(x) {
            return x.id === id;
        });
        if (!c) {
            notifCC('Cierre no encontrado', 'error');
            return;
        }
        CC.cierreActual = c;

        var diff = Number(c.diferencia || 0);
        document.getElementById('vc-title').textContent = 'Cierre — ' + (c.caja?.nombre || 'Caja');
        document.getElementById('vc-sub').textContent =
            fmtFechaHoraCC(c.fecha_fin) + ' · ' + (c.usuario?.name || '—');

        var badge = diff === 0 ?
            '<span class="badge badge-green"><span class="dot"></span>Cuadrado</span>' :
            (diff > 0 ?
                '<span class="badge badge-blue"><span class="dot"></span>Sobrante</span>' :
                '<span class="badge badge-red"><span class="dot"></span>Faltante</span>');

        var body = document.getElementById('vc-body');
        body.innerHTML =
            '<div class="cc-grid">' +
            fldCC('Caja', c.caja?.nombre || '—') +
            fldCC('Cajero', c.usuario?.name || '—') +
            fldCC('Apertura', fmtFechaHoraCC(c.fecha_inicio)) +
            fldCC('Cierre', fmtFechaHoraCC(c.fecha_fin)) +
            fldCC('Rango de facturas', (c.factura_inicial || 'N/A') + ' – ' + (c.factura_final || 'N/A')) +
            fldCC('Cantidad de facturas', String(c.cantidad_facturas || 0)) +
            '</div>' +
            (c.observaciones ?
                '<p class="fac-section-title">Observaciones</p>' +
                '<div class="cc-field-val" style="margin-bottom:16px;">' + esc(c.observaciones) + '</div>' :
                '') +
            '<p class="fac-section-title">Arqueo</p>' +
            '<div class="totales-box">' +
            '<div class="totales-row"><span>Base inicial</span><span>' + fmtMoneyCC(c.base_inicial) +
            '</span></div>' +
            '<div class="totales-row"><span>Efectivo esperado</span><span>' + fmtMoneyCC(c.efectivo_esperado) +
            '</span></div>' +
            '<div class="totales-row"><span>Efectivo físico contado</span><span>' + fmtMoneyCC(c.total_fisico) +
            '</span></div>' +
            '<div class="totales-row total-final"><span>Diferencia ' + badge + '</span><span class="td-money ' +
            (diff === 0 ? 'zero' : (diff > 0 ? 'pos' : 'neg')) + '" style="font-size:16px;">' +
            fmtMoneyCC(diff) + '</span></div>' +
            '</div>' + resumenCC(c);

        document.getElementById('modal-ver-cierre').style.display = 'flex';
    }

    function fldCC(label, val) {
        return '<div class="cc-field">' +
            '<label>' + label + '</label>' +
            '<div class="cc-field-val">' + esc(String(val)) + '</div>' +
            '</div>';
    }

    function resumenCC(c) {
        var r = c.resumen;
        if (!r) return '<p class="fac-section-title">Resumen operativo</p><p style="font-size:12px;color:#9CA3AF;">Este cierre fue creado antes de guardar el resumen completo.</p>';
        var fila = function(label, valor) {
            return '<div class="totales-row"><span>' + label + '</span><span>' + fmtMoneyCC(valor) + '</span></div>';
        };
        var ventas = r.ventas || {}, propinas = r.propinas || {}, movimientos = r.movimientos || {};
        var nombres = {m100:'Moneda $100',m200:'Moneda $200',m500:'Moneda $500',m1000:'Moneda $1.000',b2000:'Billete $2.000',b5000:'Billete $5.000',b10000:'Billete $10.000',b20000:'Billete $20.000',b50000:'Billete $50.000',b100000:'Billete $100.000'};
        var conteo = Object.keys(c.denominaciones || {}).map(function(key) {
            var cantidad = Number(c.denominaciones[key] || 0);
            return cantidad ? '<div class="totales-row"><span>' + nombres[key] + ' (' + cantidad + ' x)</span><span>' + cantidad + ' unidades</span></div>' : '';
        }).join('') || '<div class="totales-row"><span>Sin denominaciones registradas</span></div>';
        return '<p class="fac-section-title">Ventas</p><div class="totales-box">' +
            fila('Venta bruta', ventas.bruta) + fila('Efectivo', ventas.efectivo) + fila('QR', ventas.qr) + fila('Tarjeta', ventas.tarjeta) + fila('Transferencia', ventas.transferencia) + '</div>' +
            '<p class="fac-section-title">Propinas</p><div class="totales-box">' + fila('Total propinas', propinas.total) + fila('Efectivo', propinas.efectivo) + fila('QR', propinas.qr) + fila('Tarjeta', propinas.tarjeta) + fila('Transferencia', propinas.transferencia) + '</div>' +
            '<p class="fac-section-title">Movimientos de caja</p><div class="totales-box">' + fila('Entradas', movimientos.entradas) + fila('Salidas', movimientos.salidas) + '</div>' +
            '<p class="fac-section-title">Conteo físico</p><div class="totales-box">' + conteo + '</div>';
    }

    function cerrarVerCierre() {
        document.getElementById('modal-ver-cierre').style.display = 'none';
    }

    function cerrarVerCierreBackdrop(e) {
        if (e.target === document.getElementById('modal-ver-cierre')) cerrarVerCierre();
    }

    function imprimirCierre() {
        notifCC('🖨️ Enviando cierre a impresora…', 'info');
    }

    function imprimirCierreRapido(id) {
        notifCC('🖨️ Enviando cierre a impresora…', 'info');
    }

    /* ════════════════════════════════════════════════
       EXPORTAR
    ════════════════════════════════════════════════ */
    function exportarCierres() {
        notifCC('⬇️ Preparando exportación de ' + CC.filtrados.length + ' registros…', 'info');
    }

    /* ════════════════════════════════════════════════
       UTILIDADES
    ════════════════════════════════════════════════ */
    function fmtMoneyCC(n) {
        var v = Number(n || 0);
        var signo = v < 0 ? '- ' : '';
        return signo + '$ ' + Math.abs(v).toLocaleString('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });
    }

    function fmtFechaHoraCC(fecha) {
        if (!fecha) return '—';
        var d = new Date(fecha);
        if (isNaN(d.getTime())) return '—';
        return d.toLocaleString('es-CO', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function esc(s) {
        return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g,
            '&quot;');
    }

    function notifCC(msg, tipo) {
        if (typeof mostrarNotificacion === 'function') {
            mostrarNotificacion(msg, tipo === 'error' ? 'error' : tipo === 'warning' ? 'warning' : 'success');
            return;
        }
        var cls = {
            success: '#059669',
            error: '#DC2626',
            info: '#1D4ED8',
            warning: '#D97706'
        };
        var el = document.createElement('div');
        el.textContent = msg;
        el.style.cssText = 'position:fixed;top:16px;right:16px;z-index:9999;background:#fff;' +
            'border:1px solid #EAECF0;border-left:4px solid ' + (cls[tipo] || cls.info) + ';' +
            'border-radius:8px;padding:10px 16px;font-size:13px;color:#111827;' +
            'box-shadow:0 4px 12px rgba(0,0,0,0.08);min-width:220px;max-width:360px;';
        document.body.appendChild(el);
        setTimeout(function() {
            el.remove();
        }, 3500);
    }
</script>
