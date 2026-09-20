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

    /* ── Métricas resumen ── */
    .metrics-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(130px, 1fr));
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
        top: 0;
        left: 0;
        right: 0;
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

    .metric-value {
        font-size: 20px;
        font-weight: 700;
        color: #111827;
        margin-top: 4px;
        letter-spacing: -0.5px;
    }

    .metric-value.money {
        font-size: 17px;
    }

    .metric-sub {
        font-size: 11px;
        color: #6B7280;
        margin-top: 2px;
    }

    /* ── Barra de filtros / búsqueda ── */
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

    table.facturas-tbl {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
    }

    table.facturas-tbl thead {
        background: #F8FAFC;
        border-bottom: 1px solid #EAECF0;
    }

    table.facturas-tbl thead th {
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

    table.facturas-tbl thead th:hover {
        color: #1D4ED8;
    }

    table.facturas-tbl thead th .sort-icon {
        display: inline-block;
        margin-left: 4px;
        opacity: 0.4;
        font-size: 10px;
    }

    table.facturas-tbl thead th.sorted .sort-icon {
        opacity: 1;
        color: #1D4ED8;
    }

    table.facturas-tbl tbody tr {
        border-bottom: 1px solid #F3F4F6;
        transition: background 0.1s;
    }

    table.facturas-tbl tbody tr:last-child {
        border-bottom: none;
    }

    table.facturas-tbl tbody tr:hover {
        background: #F8FAFC;
    }

    table.facturas-tbl tbody tr.anulada {
        opacity: 0.6;
    }

    table.facturas-tbl tbody tr.anulada td {
        text-decoration: line-through;
    }

    table.facturas-tbl tbody tr.anulada .badge {
        text-decoration: none;
    }

    table.facturas-tbl tbody tr.anulada .tbl-actions {
        text-decoration: none;
    }

    table.facturas-tbl td {
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

    .td-num {
        font-size: 13px;
        font-weight: 700;
        color: #111827;
        letter-spacing: -0.3px;
    }

    .td-money {
        font-weight: 600;
        color: #059669;
        text-align: right;
    }

    .td-money.negative {
        color: #DC2626;
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

    .badge-yellow {
        background: #FFFBEB;
        color: #92400E;
    }

    .badge-blue {
        background: #EFF6FF;
        color: #1e3a8a;
    }

    .badge-gray {
        background: #F3F4F6;
        color: #374151;
    }

    .badge-purple {
        background: #EDE9FE;
        color: #4C1D95;
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

    .act-btn.edit:hover {
        background: #ECFDF5;
        color: #059669;
    }

    .act-btn.rev:hover {
        background: #FFFBEB;
        color: #D97706;
    }

    .act-btn.del:hover {
        background: #FEF2F2;
        color: #DC2626;
    }

    .act-btn.elec:hover {
        background: #EDE9FE;
        color: #7C3AED;
    }

    /* Tooltip */
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

    /* ── Modal Factura ── */
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

    .modal-factura {
        background: #fff;
        border-radius: 16px;
        width: 100%;
        max-width: 720px;
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

    .fac-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 16px;
    }

    @media (max-width: 480px) {
        .fac-grid {
            grid-template-columns: 1fr;
        }
    }

    .fac-field {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .fac-field label {
        font-size: 11px;
        font-weight: 500;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .fac-field input,
    .fac-field select,
    .fac-field textarea {
        border: 1px solid #D1D5DB;
        border-radius: 7px;
        padding: 7px 10px;
        font-size: 13px;
        color: #111827;
        outline: none;
        background: #F9FAFB;
        transition: border 0.15s;
        font-family: inherit;
    }

    .fac-field input:focus,
    .fac-field select:focus,
    .fac-field textarea:focus {
        border-color: #1D4ED8;
        background: #fff;
    }

    .fac-field input[readonly] {
        background: #F3F4F6;
        color: #6B7280;
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

    /* Items de factura (detalle) */
    .items-tbl-wrap {
        overflow-x: auto;
    }

    table.items-tbl {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
    }

    table.items-tbl thead th {
        padding: 8px 10px;
        text-align: left;
        font-size: 11px;
        color: #6B7280;
        font-weight: 600;
        text-transform: uppercase;
        background: #F8FAFC;
        letter-spacing: 0.4px;
    }

    table.items-tbl tbody td {
        padding: 8px 10px;
        border-top: 1px solid #F3F4F6;
    }

    table.items-tbl tfoot td {
        padding: 8px 10px;
        border-top: 1px solid #EAECF0;
        font-size: 13px;
        font-weight: 600;
    }

    /* Totales modal */
    .totales-box {
        background: #F8FAFC;
        border: 1px solid #EAECF0;
        border-radius: 10px;
        padding: 12px 14px;
        margin-top: 16px;
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

    /* Alerta anulada */
    .alerta-anulada {
        background: #FEF2F2;
        border: 1px solid #FECACA;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 12px;
        color: #991B1B;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 14px;
    }

    /* Alerta electrónico pendiente */
    .alerta-elec {
        background: #FFFBEB;
        border: 1px solid #FDE68A;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 12px;
        color: #92400E;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 14px;
    }

    /* Checkbox selección */
    .chk-row {
        width: 15px;
        height: 15px;
        cursor: pointer;
        accent-color: #1D4ED8;
    }

    /* Toolbar bulk actions */
    .bulk-toolbar {
        display: none;
        background: #1D4ED8;
        border-radius: 8px;
        padding: 8px 14px;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        margin-bottom: 10px;
        flex-wrap: wrap;
    }

    .bulk-toolbar.visible {
        display: flex;
    }

    .bulk-text {
        font-size: 12px;
        color: #fff;
        font-weight: 500;
    }

    .bulk-actions {
        display: flex;
        gap: 6px;
    }

    .btn-bulk {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: rgba(255, 255, 255, 0.15);
        color: #fff;
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 6px;
        padding: 5px 10px;
        font-size: 11px;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.15s;
    }

    .btn-bulk:hover {
        background: rgba(255, 255, 255, 0.25);
    }

    .btn-bulk.danger {
        background: rgba(220, 38, 38, 0.6);
        border-color: rgba(220, 38, 38, 0.7);
    }

    .btn-bulk.danger:hover {
        background: rgba(220, 38, 38, 0.8);
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

    /* Responsive */
    @media (max-width: 640px) {
        .metrics-row {
            grid-template-columns: 1fr 1fr;
        }

        /* Ocultamos solo Total/Registrada/Usuario/Doc.Electrónico (7,8,10,11).
           Antes se ocultaba TODO desde la columna 7 en adelante, lo que
           también tapaba "Estado" (9) y "Acciones" (12) sin ninguna forma
           de llegar a ellas en el celular — exactamente donde estaban los
           botones para ver/anular/reimprimir una factura. */
        table.facturas-tbl thead th:nth-child(7),
        table.facturas-tbl thead th:nth-child(8),
        table.facturas-tbl thead th:nth-child(10),
        table.facturas-tbl thead th:nth-child(11) {
            display: none;
        }

        table.facturas-tbl tbody td:nth-child(7),
        table.facturas-tbl tbody td:nth-child(8),
        table.facturas-tbl tbody td:nth-child(10),
        table.facturas-tbl tbody td:nth-child(11) {
            display: none;
        }
    }
</style>
<div id="view-facturas">

    {{-- ── ENCABEZADO ── --}}
    <div class="sec-header">
        <div>
            <p class="sec-title">📄 Facturas de Venta</p>
            <p class="sec-subtitle">Registro, consulta y gestión de facturas emitidas</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button class="btn-outline" onclick="exportarFacturas()">
                ⬇️ Exportar
            </button>
            <span style="font-size:12px;color:#6B7280;padding:8px 0;">Las facturas se generan desde el POS.</span>
        </div>
    </div>

    {{-- ── MÉTRICAS ── --}}
    <div class="metrics-row" id="metricas-facturas">
        <div class="metric-card" style="--accent:#1D4ED8">
            <p class="metric-label">Total Facturas</p>
            <p class="metric-value" id="m-total">—</p>
            <p class="metric-sub" id="m-total-sub">Cargando…</p>
        </div>
        <div class="metric-card" style="--accent:#059669">
            <p class="metric-label">Facturado en período</p>
            <p class="metric-value money" id="m-hoy">—</p>
            <p class="metric-sub" id="m-hoy-sub"></p>
        </div>
        <div class="metric-card" style="--accent:#7C3AED">
            <p class="metric-label">Promedio por factura</p>
            <p class="metric-value money" id="m-mes">—</p>
            <p class="metric-sub" id="m-mes-sub"></p>
        </div>
        <div class="metric-card" style="--accent:#DC2626">
            <p class="metric-label">Anuladas</p>
            <p class="metric-value" id="m-anuladas">—</p>
            <p class="metric-sub" id="m-anuladas-sub"></p>
        </div>
        <div class="metric-card" style="--accent:#D97706">
            <p class="metric-label">Doc. Electrónico</p>
            <p class="metric-value" id="m-elec">—</p>
            <p class="metric-sub">Pendientes DIAN</p>
        </div>
    </div>

    {{-- ── FILTROS ── --}}
    <div class="filter-bar">
        <div class="fi-group" style="flex:2;min-width:200px;">
            <span class="fi-label">🔍</span>
            <input autocomplete="off" class="fi-input" type="text" id="fi-buscar" placeholder="Prefijo, número, cliente, usuario…"
                oninput="aplicarFiltros()">
        </div>
        <div class="fi-group">
            <span class="fi-label">Desde</span>
            <input autocomplete="off" class="fi-input" type="date" id="fi-desde" onchange="aplicarFiltros()">
        </div>
        <div class="fi-group">
            <span class="fi-label">Hasta</span>
            <input autocomplete="off" class="fi-input" type="date" id="fi-hasta" onchange="aplicarFiltros()">
        </div>
        <div class="fi-group">
            <select class="fi-select" id="fi-estado" onchange="aplicarFiltros()">
                <option value="">Todos los estados</option>
                <option value="activa">Activa</option>
                <option value="anulada">Anulada</option>
            </select>
        </div>
        <div class="fi-group">
            <select class="fi-select" id="fi-registrada" onchange="aplicarFiltros()">
                <option value="">Registrada / No</option>
                <option value="1">Registrada</option>
                <option value="0">Sin registrar</option>
            </select>
        </div>
        <button class="btn-outline" onclick="limpiarFiltros()">✕ Limpiar</button>
    </div>

    {{-- ── BULK TOOLBAR ── --}}
    <div class="bulk-toolbar" id="bulk-toolbar">
        <span class="bulk-text" id="bulk-text">0 facturas seleccionadas</span>
        <div class="bulk-actions">
            <button class="btn-bulk" onclick="exportarSeleccion()">⬇️ Exportar selección</button>
            @if ((auth()->user()->rol->nombre ?? '') === 'Administrador')
                <button class="btn-bulk danger" onclick="anularSeleccion()">🚫 Anular selección</button>
            @endif
        </div>
    </div>

    {{-- ── TABLA ── --}}
    <div class="table-wrapper">
        <div class="table-scroll">
            <table class="facturas-tbl" id="tbl-facturas">
                <thead>
                    <tr>
                        <th style="width:36px;">
                            <input type="checkbox" class="chk-row" id="chk-all" onchange="toggleTodas(this)">
                        </th>
                        <th onclick="sortTabla('prefijo')" data-col="prefijo">
                            Prefijo <span class="sort-icon">↕</span>
                        </th>
                        <th onclick="sortTabla('numero')" data-col="numero" style="width:80px;">
                            N° <span class="sort-icon">↕</span>
                        </th>
                        <th onclick="sortTabla('fecha')" data-col="fecha">
                            Fecha <span class="sort-icon">↕</span>
                        </th>
                        <th>Hora</th>
                        <th onclick="sortTabla('cliente')" data-col="cliente">
                            Cliente <span class="sort-icon">↕</span>
                        </th>
                        <th onclick="sortTabla('total')" data-col="total" style="text-align:right;">
                            Total <span class="sort-icon">↕</span>
                        </th>
                        <th>Registrada</th>
                        <th>Estado</th>
                        <th>Usuario</th>
                        <th>Doc. Electrónico</th>
                        <th style="text-align:center;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-facturas">
                    <tr>
                        <td colspan="12">
                            <div class="spinner-cell">
                                <div class="spinner"></div>
                                Cargando facturas…
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="pagination-bar">
            <span class="pag-info" id="pag-info">Mostrando 0 de 0 registros</span>
            <div class="pag-btns" id="pag-btns"></div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════
     MODAL FACTURA (crear / ver / editar)
═══════════════════════════════════════════════ --}}
<div id="modal-factura" style="display:none;" class="modal-backdrop" onclick="cerrarModalFacturaBackdrop(event)">
    <div class="modal-factura">

        <div class="modal-head">
            <div>
                <p class="modal-head-title" id="mf-title">Nueva Factura</p>
                <p class="modal-head-sub" id="mf-sub">Complete los campos requeridos</p>
            </div>
            <button onclick="cerrarModalFactura()"
                style="
                border:none;background:transparent;font-size:20px;cursor:pointer;
                color:#6B7280;padding:4px;border-radius:6px;line-height:1;
            ">✕</button>
        </div>

        <div class="modal-body" id="mf-body">

            {{-- Alertas condicionales --}}
            <div id="mf-alerta-anulada" class="alerta-anulada" style="display:none;">
                🚫 Esta factura se encuentra <strong>ANULADA</strong>. No se puede modificar.
            </div>
            <div id="mf-alerta-elec" class="alerta-elec" style="display:none;">
                ⚠️ El documento electrónico aún no ha sido generado ante la DIAN.
            </div>

            {{-- Encabezado factura --}}
            <p class="fac-section-title">Encabezado</p>
            <div class="fac-grid">
                <div class="fac-field">
                    <label>Prefijo *</label>
                    <input autocomplete="off" type="text" id="mf-prefijo" placeholder="Ej: FV" maxlength="10"
                        oninput="this.value=this.value.toUpperCase()">
                </div>
                <div class="fac-field">
                    <label>Número *</label>
                    <input autocomplete="off" type="text" id="mf-numero" placeholder="Automático" readonly>
                </div>
                <div class="fac-field">
                    <label>Fecha *</label>
                    <input autocomplete="off" type="date" id="mf-fecha">
                </div>
                <div class="fac-field">
                    <label>Hora</label>
                    <input autocomplete="off" type="time" id="mf-hora">
                </div>
                <div class="fac-field" style="grid-column:1/-1;">
                    <label>Cliente / Tercero *</label>
                    <select id="mf-cliente">
                        <option value="">— Seleccione cliente —</option>
                    </select>
                </div>
                <div class="fac-field">
                    <label>Caja</label>
                    <select id="mf-caja">
                        <option value="">— Sin caja —</option>
                    </select>
                </div>
                <div class="fac-field">
                    <label>Bodega</label>
                    <select id="mf-bodega">
                        <option value="">— Sin bodega —</option>
                    </select>
                </div>
                <div class="fac-field" style="grid-column:1/-1;">
                    <label>Observaciones</label>
                    <textarea id="mf-observaciones" rows="2" placeholder="Notas adicionales…" style="resize:vertical;"></textarea>
                </div>
            </div>

            {{-- Detalle de ítems --}}
            <p class="fac-section-title">
                Ítems de la factura
                <button class="btn-primary" onclick="agregarItemFila()"
                    style="float:right;font-size:11px;padding:4px 10px;">+ Añadir ítem</button>
            </p>

            <div class="items-tbl-wrap">
                <table class="items-tbl" id="items-tbl">
                    <thead>
                        <tr>
                            <th style="width:40%;">Producto</th>
                            <th>Cant.</th>
                            <th>P. Unitario</th>
                            <th>Desc. %</th>
                            <th>IVA %</th>
                            <th style="text-align:right;">Subtotal</th>
                            <th style="width:36px;"></th>
                        </tr>
                    </thead>
                    <tbody id="items-body">
                        <tr id="items-empty-row">
                            <td colspan="7" style="text-align:center;color:#9CA3AF;padding:16px;font-size:12px;">
                                Sin ítems. Haga clic en "＋ Añadir ítem".
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Totales --}}
            <div class="totales-box">
                <div class="totales-row">
                    <span>Subtotal bruto</span>
                    <span id="tot-subtotal">$ 0.00</span>
                </div>
                <div class="totales-row">
                    <span>Descuento global</span>
                    <span id="tot-descuento" style="color:#DC2626;">- $ 0.00</span>
                </div>
                <div class="totales-row">
                    <span>Base gravable</span>
                    <span id="tot-base">$ 0.00</span>
                </div>
                <div class="totales-row">
                    <span>IVA</span>
                    <span id="tot-iva">$ 0.00</span>
                </div>
                <div class="totales-row total-final">
                    <span>TOTAL</span>
                    <span id="tot-total">$ 0.00</span>
                </div>
                <div style="margin-top:10px;display:flex;align-items:center;gap:8px;flex-wrap:wrap;">
                    <label style="font-size:12px;color:#6B7280;">Desc. global %</label>
                    <input autocomplete="off" type="number" id="desc-global" min="0" max="100" step="0.01"
                        style="width:80px;border:1px solid #D1D5DB;border-radius:6px;
                                  padding:5px 8px;font-size:12px;outline:none;"
                        oninput="calcularTotales()" placeholder="0">
                    <label style="font-size:12px;color:#6B7280;margin-left:12px;">Registrada</label>
                    <input type="checkbox" id="mf-registrada" style="width:15px;height:15px;accent-color:#1D4ED8;">
                </div>
            </div>

        </div>

        <div class="modal-foot">
            <button class="btn-outline" onclick="cerrarModalFactura()">Cancelar</button>
            <button class="btn-primary" id="mf-btn-save" onclick="guardarFactura()">
                💾 Guardar Factura
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     MODAL VER FACTURA (solo lectura)
═══════════════════════════════════════════════ --}}
<div id="modal-ver-factura" style="display:none;" class="modal-backdrop" onclick="cerrarVerFacturaBackdrop(event)">
    <div class="modal-factura">
        <div class="modal-head">
            <div>
                <p class="modal-head-title" id="vf-title">Factura</p>
                <p class="modal-head-sub" id="vf-sub"></p>
            </div>
            <button onclick="cerrarVerFactura()"
                style="
                border:none;background:transparent;font-size:20px;cursor:pointer;
                color:#6B7280;padding:4px;border-radius:6px;line-height:1;
            ">✕</button>
        </div>
        <div class="modal-body" id="vf-body">
            {{-- Se llena dinámicamente --}}
        </div>
        <div class="modal-foot">
            <button class="btn-outline" onclick="imprimirFactura()">🖨️ Imprimir</button>
            <button class="btn-outline" onclick="cerrarVerFactura()">Cerrar</button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     MODAL NOTA CRÉDITO / DÉBITO
     Corrección parcial de una factura ya emitida (devolución de productos,
     corrección de precio) sin anular todo el documento — ver NotaFacturaService.
═══════════════════════════════════════════════ --}}
<div id="modal-nota" style="display:none;" class="modal-backdrop" onclick="if(event.target===this) cerrarModalNota()">
    <div class="modal-factura" style="max-width:560px;">
        <div class="modal-head">
            <div>
                <p class="modal-head-title">Nota crédito / débito</p>
                <p class="modal-head-sub" id="nota-sub"></p>
            </div>
            <button onclick="cerrarModalNota()" style="border:none;background:transparent;font-size:20px;cursor:pointer;color:#6B7280;padding:4px;border-radius:6px;line-height:1;">✕</button>
        </div>
        <div class="modal-body">
            <div class="field-grid" style="margin-bottom:14px;">
                <div class="field">
                    <label>Tipo</label>
                    <select id="nota-tipo" onchange="renderLineasNota()">
                        <option value="credito">Nota crédito (devolución/descuento)</option>
                        <option value="debito">Nota débito (cobro adicional)</option>
                    </select>
                </div>
                <div class="field" id="nota-campo-restaura">
                    <label>&nbsp;</label>
                    <label style="display:flex;align-items:center;gap:6px;font-weight:400;">
                        <input type="checkbox" id="nota-restaura-inventario" checked style="width:auto;">
                        Devolver producto a bodega
                    </label>
                </div>
            </div>

            <p class="field-hint" style="margin-bottom:8px;">Marca la cantidad a notar de cada línea (deja en 0 lo que no aplica):</p>
            <div id="nota-lineas" style="margin-bottom:14px;"></div>

            <div class="field full" style="margin-bottom:0;">
                <label>Motivo</label>
                <textarea id="nota-motivo" rows="2" placeholder="Ej: cliente devolvió 1 unidad por error de pedido"></textarea>
            </div>
        </div>
        <div class="modal-foot">
            <button class="btn-outline" onclick="cerrarModalNota()">Cancelar</button>
            <button class="btn-primary" id="nota-btn-guardar" onclick="guardarNota()">💾 Emitir nota</button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     SCRIPT
═══════════════════════════════════════════════ --}}
<script>
    var puedeGestionarFacturas = @json((auth()->user()->rol->nombre ?? '') === 'Administrador');

    /* ════════════════════════════════════════════════
   ESTADO GLOBAL
════════════════════════════════════════════════ */
    var FAC = {
        datos: [], // todas las facturas cargadas
        filtradas: [], // tras aplicar filtros
        paginaActual: 1,
        porPagina: 15,
        sortCol: 'numero',
        sortAsc: false,
        seleccionadas: new Set(),
        modoModal: 'nuevo', // 'nuevo' | 'editar' | 'ver'
        facturaActual: null,
        items: [], // ítems del modal activo
        productosCatalogo: [],
        editandoId: null,
    };

    /* ════════════════════════════════════════════════
       INICIALIZACIÓN
    ════════════════════════════════════════════════ */
    (function init() {
        var hoy = new Date();
        var primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        document.getElementById('fi-desde').value = fmt(primerDia);
        document.getElementById('fi-hasta').value = fmt(hoy);

        cargarFacturas();
        cargarCatalogos();

        function fmt(d) {
            return d.getFullYear() + '-' +
                String(d.getMonth() + 1).padStart(2, '0') + '-' +
                String(d.getDate()).padStart(2, '0');
        }
    })();

    /* ════════════════════════════════════════════════
       CARGA DE DATOS (fetch API Laravel)
    ════════════════════════════════════════════════ */
    function cargarFacturas() {
        mostrarSpinner();
        var token = document.querySelector('meta[name="csrf-token"]')?.content;

        fetch('/facturas', {
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            })
            .then(function(r) {
                return r.json().then(function(data) {
                    if (!r.ok) throw new Error(data.message || 'No se pudo cargar el listado de facturas');
                    return data;
                });
            })
            .then(function(res) {
                FAC.datos = Array.isArray(res.data) ? res.data : (Array.isArray(res) ? res : []);
                aplicarFiltros();
            })
            .catch(function(e) {
                FAC.datos = [];
                aplicarFiltros();
                notif(e.message || 'No se pudo cargar el listado de facturas', 'error');
            });
    }

    function cargarCatalogos() {
        /* Carga terceros, cajas, bodegas, productos para los selects */
        var token = document.querySelector('meta[name="csrf-token"]')?.content;
        var hdrs = {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        };

        fetch('/terceros', {
            headers: hdrs
        }).then(r => r.json()).then(function(res) {
            var sel = document.getElementById('mf-cliente');
            var data = Array.isArray(res.data) ? res.data : (Array.isArray(res) ? res : []);
            data.forEach(function(t) {
                var o = document.createElement('option');
                o.value = t.id;
                o.textContent = (t.nombre || t.razon_social || t.name || '') +
                    (t.nit ? ' – ' + t.nit : '');
                sel.appendChild(o);
            });
        }).catch(function() {});

        fetch('/cajas', {
            headers: hdrs
        }).then(r => r.json()).then(function(res) {
            var sel = document.getElementById('mf-caja');
            var data = Array.isArray(res.data) ? res.data : (Array.isArray(res) ? res : []);
            data.forEach(function(c) {
                var o = document.createElement('option');
                o.value = c.id;
                o.textContent = c.nombre || c.name || '';
                sel.appendChild(o);
            });
        }).catch(function() {});

        fetch('/bodegas', {
            headers: hdrs
        }).then(r => r.json()).then(function(res) {
            var sel = document.getElementById('mf-bodega');
            var data = Array.isArray(res.data) ? res.data : (Array.isArray(res) ? res : []);
            data.forEach(function(b) {
                var o = document.createElement('option');
                o.value = b.id;
                o.textContent = b.nombre || b.name || '';
                sel.appendChild(o);
            });
        }).catch(function() {});

        fetch('/productos', {
            headers: hdrs
        }).then(r => r.json()).then(function(res) {
            FAC.productosCatalogo = Array.isArray(res.data) ? res.data : (Array.isArray(res) ? res : []);
        }).catch(function() {});
    }

    /* ════════════════════════════════════════════════
       DATOS DEMO (para previsualización sin backend)
    ════════════════════════════════════════════════ */
    function datosDemoCargados() {
        var hoy = new Date();
        var clientes = ['Juan Pérez García', 'Empresa ABC S.A.S', 'María López', 'Tienda XYZ',
            'Carlos Ruiz & Cía', 'Distribuidora Norte', 'Ana Martínez', 'Consumidor Final'
        ];
        var usuarios = ['admin', 'cajero1', 'vendedor2', 'supervisor'];
        var prefijos = ['FV', 'FAC', 'FVTA'];
        var result = [];
        for (var i = 1; i <= 78; i++) {
            var fecha = new Date(hoy);
            fecha.setDate(hoy.getDate() - Math.floor(Math.random() * 60));
            var anulada = Math.random() < 0.08;
            result.push({
                id: i,
                prefijo: prefijos[Math.floor(Math.random() * prefijos.length)],
                numero: String(1000 + i).padStart(5, '0'),
                fecha: fecha.toISOString().slice(0, 10),
                hora: pad(Math.floor(Math.random() * 12) + 8) + ':' + pad(Math.floor(Math.random() * 60)),
                cliente: clientes[i % clientes.length],
                total: Math.round((Math.random() * 5000000 + 50000) / 100) * 100,
                registrada: Math.random() > 0.2,
                anulada: anulada,
                usuario: usuarios[i % usuarios.length],
                doc_electronico: !anulada && Math.random() > 0.3,
                items: [{
                    producto: 'Producto ' + i,
                    cantidad: Math.floor(Math.random() * 5) + 1,
                    precio: Math.round(Math.random() * 200000 + 10000),
                    descuento: 0,
                    iva: 19
                }]
            });
        }

        function pad(n) {
            return String(n).padStart(2, '0');
        }
        return result.sort(function(a, b) {
            return b.numero.localeCompare(a.numero);
        });
    }

    /* ════════════════════════════════════════════════
       FILTROS
    ════════════════════════════════════════════════ */
    function aplicarFiltros() {
        var buscar = (document.getElementById('fi-buscar').value || '').toLowerCase().trim();
        var desde = document.getElementById('fi-desde').value;
        var hasta = document.getElementById('fi-hasta').value;
        var estado = document.getElementById('fi-estado').value;
        var registrada = document.getElementById('fi-registrada').value;

        FAC.filtradas = FAC.datos.filter(function(f) {
            if (buscar) {
                var hay = (f.prefijo + f.numero + f.cliente + f.usuario).toLowerCase().includes(buscar);
                if (!hay) return false;
            }
            if (desde && f.fecha < desde) return false;
            if (hasta && f.fecha > hasta) return false;
            if (estado === 'activa' && f.anulada) return false;
            if (estado === 'anulada' && !f.anulada) return false;
            if (registrada === '1' && !f.registrada) return false;
            if (registrada === '0' && f.registrada) return false;
            return true;
        });

        FAC.paginaActual = 1;
        sortTablaActual();
        renderTabla();
        actualizarMetricas();
    }

    function limpiarFiltros() {
        document.getElementById('fi-buscar').value = '';
        document.getElementById('fi-estado').value = '';
        document.getElementById('fi-registrada').value = '';
        var hoy = new Date();
        var primo = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        document.getElementById('fi-desde').value =
            primo.toISOString().slice(0, 10);
        document.getElementById('fi-hasta').value =
            hoy.toISOString().slice(0, 10);
        aplicarFiltros();
    }

    /* ════════════════════════════════════════════════
       ORDENAMIENTO
    ════════════════════════════════════════════════ */
    function sortTabla(col) {
        if (FAC.sortCol === col) FAC.sortAsc = !FAC.sortAsc;
        else {
            FAC.sortCol = col;
            FAC.sortAsc = false;
        }
        sortTablaActual();
        renderTabla();
        document.querySelectorAll('table.facturas-tbl thead th[data-col]').forEach(function(th) {
            th.classList.toggle('sorted', th.dataset.col === col);
            var icon = th.querySelector('.sort-icon');
            if (icon) icon.textContent = th.dataset.col === col ? (FAC.sortAsc ? '↑' : '↓') : '↕';
        });
    }

    function sortTablaActual() {
        var col = FAC.sortCol,
            asc = FAC.sortAsc;
        FAC.filtradas.sort(function(a, b) {
            var va = a[col] ?? '',
                vb = b[col] ?? '';
            if (typeof va === 'number') return asc ? va - vb : vb - va;
            return asc ? String(va).localeCompare(String(vb)) :
                String(vb).localeCompare(String(va));
        });
    }

    /* ════════════════════════════════════════════════
       RENDER TABLA
    ════════════════════════════════════════════════ */
    function renderTabla() {
        var tbody = document.getElementById('tbody-facturas');
        var total = FAC.filtradas.length;
        var desde = (FAC.paginaActual - 1) * FAC.porPagina;
        var pagina = FAC.filtradas.slice(desde, desde + FAC.porPagina);

        document.getElementById('pag-info').textContent =
            'Mostrando ' + Math.min(pagina.length, total) +
            ' de ' + total + ' registros';

        if (pagina.length === 0) {
            tbody.innerHTML = '<tr><td colspan="12">' +
                '<div class="spinner-cell">📭 No se encontraron facturas</div></td></tr>';
            renderPaginacion(total);
            return;
        }

        tbody.innerHTML = pagina.map(function(f) {
            var anulada = f.anulada;
            var trCls = anulada ? 'anulada' : '';

            var badgeEstado = anulada ?
                '<span class="badge badge-red"><span class="dot"></span>Anulada</span>' :
                '<span class="badge badge-green"><span class="dot"></span>Activa</span>';

            var badgeReg = f.registrada ?
                '<span class="badge badge-blue">✓ Sí</span>' :
                '<span class="badge badge-gray">— No</span>';

            // estado_dian: 'no_aplica' mientras no haya proveedor de
            // facturación electrónica configurado (ver FacturacionElectronicaService) —
            // es el caso normal de hoy, no un error.
            var badgesEstadoDian = {
                no_aplica: '<span class="badge badge-gray">— No aplica</span>',
                pendiente: '<span class="badge badge-yellow">⏳ Pendiente</span>',
                enviada: '<span class="badge badge-blue">📤 Enviada</span>',
                aceptada: '<span class="badge badge-purple">⚡ Aceptada</span>',
                rechazada: '<span class="badge badge-red">❌ Rechazada</span>',
                error: '<span class="badge badge-red">⚠️ Error</span>',
            };
            var badgeElec = badgesEstadoDian[f.estado_dian] || badgesEstadoDian.no_aplica;

            var chked = FAC.seleccionadas.has(f.id) ? 'checked' : '';

            /* Botones de acción */
            var btnVer = '<button class="act-btn view" data-tip="Ver factura" ' +
                'onclick="verFactura(' + f.id + ')">👁️</button>';
            var esBorrador = !f.registrada && !anulada;
            var btnEdit = esBorrador ?
                '<button class="act-btn edit" data-tip="Editar" ' +
                'onclick="editarFactura(' + f.id + ')">✏️</button>' : '';
            var btnRev = !anulada && f.registrada ?
                '<button class="act-btn rev" data-tip="Anular" ' +
                'onclick="anularFactura(' + f.id + ',\'' + esc(f.prefijo) + f.numero + '\')">🚫</button>' : '';

            var btnRestaurar = anulada ?
                '<button class="act-btn edit" data-tip="Revertir anulación" ' +
                'onclick="revertirAnulacion(' + f.id + ',\'' + esc(f.prefijo) + f.numero + '\')">↩️</button>' :
                '';
            // Corrección parcial sin anular todo el documento (devolución de
            // productos, corrección de precio) — ver NotaFacturaController.
            var btnNota = !anulada && f.registrada ?
                '<button class="act-btn edit" data-tip="Nota crédito/débito" ' +
                'onclick="abrirModalNota(' + f.id + ')">📝</button>' : '';
            var btnDel = esBorrador ?
                '<button class="act-btn del" data-tip="Eliminar borrador" ' +
                'onclick="eliminarFactura(' + f.id + ',\'' + esc(f.prefijo) + f.numero + '\')">🗑️</button>' : '';
            // 'fallida' = agotó los reintentos automáticos de transmisión
            // DIAN — sin este botón quedaba invisible para siempre.
            var btnReintentarDian = f.estado_dian === 'fallida' ?
                '<button class="act-btn edit" data-tip="Reintentar transmisión DIAN" ' +
                'onclick="reintentarDian(' + f.id + ',\'' + esc(f.prefijo) + f.numero + '\')">📡</button>' : '';
            if (!puedeGestionarFacturas) {
                btnEdit = '';
                btnRev = '';
                btnRestaurar = '';
                btnNota = '';
                btnDel = '';
                btnReintentarDian = '';
            }
            return '<tr class="' + trCls + '" data-id="' + f.id + '">' +
                '<td><input type="checkbox" class="chk-row chk-item" ' + chked +
                ' onchange="toggleSeleccion(' + f.id + ',this)"></td>' +
                '<td><span class="td-mono">' + esc(f.prefijo) + '</span></td>' +
                '<td><span class="td-num">' + esc(f.numero) + '</span></td>' +
                '<td>' + fmtFecha(f.fecha) + '</td>' +
                '<td style="color:#6B7280;">' + esc(f.hora || '—') + '</td>' +
                '<td style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"' +
                ' title="' + esc(f.cliente) + '">' + esc(f.cliente) + '</td>' +
                '<td class="td-money">' + fmtMoney(f.total) + '</td>' +
                '<td>' + badgeReg + '</td>' +
                '<td>' + badgeEstado + '</td>' +
                '<td style="color:#6B7280;font-size:12px;">' + esc(f.usuario || '—') + '</td>' +
                '<td>' + badgeElec + '</td>' +
                '<td><div class="tbl-actions">' + btnVer + btnEdit + btnRev + btnRestaurar + btnNota + btnReintentarDian + btnDel + '</div></td>' +
                '</tr>';
        }).join('');

        renderPaginacion(total);
    }

    function renderPaginacion(total) {
        var totalPags = Math.max(1, Math.ceil(total / FAC.porPagina));
        var actual = FAC.paginaActual;
        var btns = document.getElementById('pag-btns');
        var html = '';

        html += '<button class="pag-btn" onclick="irPagina(' + (actual - 1) + ')"' +
            (actual === 1 ? ' disabled' : '') + '>‹</button>';

        var desde = Math.max(1, actual - 2);
        var hasta = Math.min(totalPags, desde + 4);
        desde = Math.max(1, hasta - 4);

        if (desde > 1) html += '<button class="pag-btn" onclick="irPagina(1)">1</button>' +
            (desde > 2 ? '<span style="padding:0 4px;color:#9CA3AF;">…</span>' : '');

        for (var p = desde; p <= hasta; p++) {
            html += '<button class="pag-btn' + (p === actual ? ' active' : '') + '" onclick="irPagina(' + p + ')">' +
                p + '</button>';
        }

        if (hasta < totalPags) {
            html += (hasta < totalPags - 1 ? '<span style="padding:0 4px;color:#9CA3AF;">…</span>' : '') +
                '<button class="pag-btn" onclick="irPagina(' + totalPags + ')">' + totalPags + '</button>';
        }

        html += '<button class="pag-btn" onclick="irPagina(' + (actual + 1) + ')"' +
            (actual === totalPags ? ' disabled' : '') + '>›</button>';

        btns.innerHTML = html;
    }

    function irPagina(p) {
        var total = FAC.filtradas.length;
        var totalPags = Math.max(1, Math.ceil(total / FAC.porPagina));
        FAC.paginaActual = Math.max(1, Math.min(p, totalPags));
        renderTabla();
    }

    function mostrarSpinner() {
        document.getElementById('tbody-facturas').innerHTML =
            '<tr><td colspan="12"><div class="spinner-cell">' +
            '<div class="spinner"></div>Cargando facturas…</div></td></tr>';
    }

    /* ════════════════════════════════════════════════
       MÉTRICAS
    ════════════════════════════════════════════════ */
    function actualizarMetricas() {
        // Las tarjetas deben reflejar exactamente el período y filtros aplicados en la tabla.
        var facturas = FAC.filtradas;
        var total = facturas.length;
        var anuladas = facturas.filter(function(f) {
            return f.anulada;
        }).length;
        var facturasActivas = facturas.filter(function(f) {
            return !f.anulada;
        });
        var pendElec = FAC.datos.filter(function(f) {
            return !f.doc_electronico && !f.anulada;
        }).length;

        var sumaPeriodo = facturasActivas.reduce(function(s, f) {
            return s + f.total;
        }, 0);
        var promedioFactura = facturasActivas.length ? sumaPeriodo / facturasActivas.length : 0;

        document.getElementById('m-total').textContent = total;
        document.getElementById('m-total-sub').textContent = (total - anuladas) + ' activas';
        document.getElementById('m-hoy').textContent = fmtMoney(sumaPeriodo);
        document.getElementById('m-hoy-sub').textContent = facturasActivas.length + ' facturas activas';
        document.getElementById('m-mes').textContent = fmtMoney(promedioFactura);
        document.getElementById('m-mes-sub').textContent = facturasActivas.length ? 'Según período filtrado' : 'Sin facturas activas';
        document.getElementById('m-anuladas').textContent = anuladas;
        document.getElementById('m-anuladas-sub').textContent = Math.round(anuladas / Math.max(1, total) * 100) +
            '% del total';
        document.getElementById('m-elec').textContent = pendElec;
    }

    /* ════════════════════════════════════════════════
       SELECCIÓN MÚLTIPLE
    ════════════════════════════════════════════════ */
    function toggleSeleccion(id, chk) {
        if (chk.checked) FAC.seleccionadas.add(id);
        else FAC.seleccionadas.delete(id);
        actualizarBulk();
    }

    function toggleTodas(chk) {
        FAC.seleccionadas.clear();
        if (chk.checked) {
            var desde = (FAC.paginaActual - 1) * FAC.porPagina;
            FAC.filtradas.slice(desde, desde + FAC.porPagina).forEach(function(f) {
                FAC.seleccionadas.add(f.id);
            });
        }
        document.querySelectorAll('.chk-item').forEach(function(c) {
            c.checked = chk.checked;
        });
        actualizarBulk();
    }

    function actualizarBulk() {
        var n = FAC.seleccionadas.size;
        var bar = document.getElementById('bulk-toolbar');
        bar.classList.toggle('visible', n > 0);
        document.getElementById('bulk-text').textContent =
            n + ' factura' + (n !== 1 ? 's' : '') + ' seleccionada' + (n !== 1 ? 's' : '');
    }

    function exportarSeleccion() {
        var seleccionadas = FAC.datos.filter(function(f) {
            return FAC.seleccionadas.has(f.id);
        });
        exportarCsvFacturas(seleccionadas, 'facturas-seleccionadas.csv');
    }

    function confirmarAccionFactura(mensaje, accion) {
        var modal = document.getElementById('modalConfirm');
        var texto = document.getElementById('confirmMensaje');
        var boton = document.getElementById('btnConfirmarAccion');

        if (modal && texto && boton) {
            texto.textContent = mensaje;
            boton.onclick = function(evento) {
                // Otros módulos escuchan este botón; Facturas ejecuta solo su propia acción.
                evento.preventDefault();
                evento.stopImmediatePropagation();
                modal.classList.add('hidden');
                modal.classList.remove('flex');
                accion();
            };
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            return;
        }

        if (window.confirm(mensaje)) accion();
    }

    function anularSeleccion() {
        var n = FAC.seleccionadas.size;
        confirmarAccionFactura('¿Anular ' + n + ' factura' + (n !== 1 ? 's' : '') + '? Esta acción no se puede deshacer.', function() {
                var token = document.querySelector('meta[name="csrf-token"]')?.content;
                var ids = Array.from(FAC.seleccionadas);
                Promise.all(ids.map(function(id) {
                    return fetch('/facturas/' + id + '/anular', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': token, Accept: 'application/json' }
                    }).then(function(res) {
                        return res.json().then(function(data) { return { ok: res.ok, data: data }; });
                    });
                })).then(function(resultados) {
                    var exitosas = resultados.filter(function(resultado) { return resultado.ok; }).length;
                    var fallidas = resultados.length - exitosas;
                    if (exitosas) notif('🚫 ' + exitosas + ' factura' + (exitosas !== 1 ? 's anuladas' : ' anulada'), 'success');
                    if (fallidas) notif(fallidas + ' factura' + (fallidas !== 1 ? 's no pudieron anularse' : ' no pudo anularse'), 'error');
                }).catch(function() {
                    notif('No fue posible procesar la anulación seleccionada', 'error');
                }).finally(function() {
                    FAC.seleccionadas.clear();
                    actualizarBulk();
                    cargarFacturas();
                });
        });
    }

    /* ════════════════════════════════════════════════
       ACCIONES INDIVIDUALES
    ════════════════════════════════════════════════ */
    function verFactura(id) {
        var f = FAC.datos.find(function(x) {
            return x.id === id;
        });
        if (!f) {
            notif('Factura no encontrada', 'error');
            return;
        }
        FAC.facturaActual = f;

        document.getElementById('vf-title').textContent = 'Factura ' + f.prefijo + f.numero;
        document.getElementById('vf-sub').textContent =
            'Fecha: ' + fmtFecha(f.fecha) + ' ' + (f.hora || '') + ' | ' + f.cliente;

        var body = document.getElementById('vf-body');
        body.innerHTML = '<div class="spinner-cell"><div class="spinner"></div>Cargando ítems…</div>';
        document.getElementById('modal-ver-factura').style.display = 'flex';

        var token = document.querySelector('meta[name="csrf-token"]')?.content;

        fetch('/facturas/' + id, {
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            })
            .then(function(r) {
                return r.json().then(function(data) {
                    if (!r.ok) throw new Error(data.message || 'No se pudo guardar la factura');
                    return data;
                });
            })
            .then(function(detalle) {
                f.items = detalle.items || [];
                f.propina = detalle.propina || 0; // NUEVO
                f.subtotal = detalle.subtotal || 0; // NUEVO (opcional, útil para desglose)
                renderVerFactura(f);
            })
            .catch(function(error) {
                body.innerHTML = '<div class="spinner-cell">No fue posible cargar el detalle.</div>';
                notif(error.message || 'No fue posible cargar el detalle de la factura', 'error');
            });
    }

    function renderVerFactura(f) {
        var body = document.getElementById('vf-body');
        var anulTag = f.anulada ?
            '<div class="alerta-anulada">🚫 Factura <strong>ANULADA</strong></div>' : '';
        var elecTag = !f.doc_electronico ?
            '<div class="alerta-elec">⚠️ Documento electrónico pendiente ante la DIAN</div>' : '';

        var detalleItems = (f.items || []).map(function(it) {
            var sub = it.cantidad * it.precio * (1 - (it.descuento || 0) / 100);
            return '<tr>' +
                '<td>' + esc(it.producto) + '</td>' +
                '<td style="text-align:center;">' + it.cantidad + '</td>' +
                '<td style="text-align:right;">' + fmtMoney(it.precio) + '</td>' +
                '<td style="text-align:center;">' + (it.descuento || 0) + '%</td>' +
                '<td style="text-align:center;">' + (it.iva || 0) + '%</td>' +
                '<td style="text-align:right;font-weight:600;">' + fmtMoney(sub) + '</td>' +
                '</tr>';
        }).join('') || '<tr><td colspan="6" style="text-align:center;color:#9CA3AF;">Sin ítems</td></tr>';

        body.innerHTML = anulTag + elecTag +
            '<div class="fac-grid" style="grid-template-columns:repeat(3,1fr);">' +
            fld('Prefijo', f.prefijo) +
            fld('Número', f.numero) +
            fld('Fecha', fmtFecha(f.fecha)) +
            fld('Hora', f.hora || '—') +
            fld('Usuario', f.usuario || '—') +
            fld('Registrada', f.registrada ? '✓ Sí' : '✗ No') +
            '</div>' +
            fld('Cliente', f.cliente, true) +
            '<p class="fac-section-title">Ítems</p>' +
            '<div class="items-tbl-wrap"><table class="items-tbl">' +
            '<thead><tr><th>Producto</th><th>Cant.</th><th>P.Unit.</th>' +
            '<th>Desc.</th><th>IVA</th><th style="text-align:right;">Subtotal</th></tr></thead>' +
            '<tbody>' + detalleItems + '</tbody></table></div>' +
            '<div class="totales-box" style="margin-top:14px;">' +
            '<div class="totales-box" style="margin-top:14px;">' +
            (f.propina > 0 ?
                '<div class="totales-row"><span>Propina</span><span>' + fmtMoney(f.propina) + '</span></div>' : '') +
            '<div class="totales-row total-final"><span>TOTAL</span><span>' + fmtMoney(f.total) + '</span></div>' +
            '</div>';
    }

    function fld(label, val, full) {
        return '<div class="fac-field"' + (full ? ' style="grid-column:1/-1;"' : '') + '>' +
            '<label>' + label + '</label>' +
            '<div style="padding:7px 10px;background:#F9FAFB;border:1px solid #E5E7EB;' +
            'border-radius:7px;font-size:13px;color:#111827;">' + esc(String(val)) + '</div>' +
            '</div>';
    }

    function cerrarVerFactura() {
        document.getElementById('modal-ver-factura').style.display = 'none';
    }

    function cerrarVerFacturaBackdrop(e) {
        if (e.target === document.getElementById('modal-ver-factura')) cerrarVerFactura();
    }

    function imprimirFactura() {
        if (!FAC.facturaActual) {
            notif('Seleccione una factura para imprimir', 'warning');
            return;
        }

        var token = document.querySelector('meta[name="csrf-token"]')?.content;
        fetch('/facturas/' + FAC.facturaActual.id + '/imprimir', {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': token, Accept: 'application/json' }
            })
            .then(function(res) {
                return res.json().then(function(data) {
                    return { ok: res.ok, data: data };
                });
            })
            .then(function(resultado) {
                if (!resultado.ok) throw new Error(resultado.data.message || 'No fue posible imprimir la factura');
                notif(resultado.data.message || 'Factura enviada a impresión', 'success');
            })
            .catch(function(error) {
                notif(error.message || 'No fue posible imprimir la factura', 'error');
            });
    }

    /* ════════════════════════════════════════════════
       MODAL CREAR / EDITAR
    ════════════════════════════════════════════════ */
    function abrirModalFactura(id) {
        FAC.modoModal = id ? 'editar' : 'nuevo';
        FAC.editandoId = id;
        FAC.items = [];

        var hoy = new Date();
        document.getElementById('mf-fecha').value = hoy.toISOString().slice(0, 10);
        document.getElementById('mf-hora').value =
            String(hoy.getHours()).padStart(2, '0') + ':' + String(hoy.getMinutes()).padStart(2, '0');
        document.getElementById('mf-prefijo').value = 'FV';
        document.getElementById('mf-numero').value = '(automático)';
        document.getElementById('mf-cliente').value = '';
        document.getElementById('mf-caja').value = '';
        document.getElementById('mf-bodega').value = '';
        document.getElementById('mf-observaciones').value = '';
        document.getElementById('mf-registrada').checked = false;
        document.getElementById('desc-global').value = '';
        document.getElementById('mf-alerta-anulada').style.display = 'none';
        document.getElementById('mf-alerta-elec').style.display = 'none';

        if (id) {
            var f = FAC.datos.find(function(x) {
                return x.id === id;
            });
            if (f) {
                document.getElementById('mf-title').textContent = 'Editar Factura ' + f.prefijo + f.numero;
                document.getElementById('mf-sub').textContent = 'Modifique los campos necesarios';
                document.getElementById('mf-prefijo').value = f.prefijo;
                document.getElementById('mf-numero').value = f.numero;
                document.getElementById('mf-fecha').value = f.fecha;
                document.getElementById('mf-hora').value = f.hora || '';
                document.getElementById('mf-registrada').checked = !!f.registrada;
                if (f.anulada) {
                    document.getElementById('mf-alerta-anulada').style.display = 'flex';
                    document.getElementById('mf-btn-save').disabled = true;
                } else {
                    document.getElementById('mf-btn-save').disabled = false;
                }
                if (!f.doc_electronico)
                    document.getElementById('mf-alerta-elec').style.display = 'flex';

                FAC.items = (f.items || []).map(function(it) {
                    return Object.assign({}, it);
                });
            }
        } else {
            document.getElementById('mf-title').textContent = 'Nueva Factura';
            document.getElementById('mf-sub').textContent = 'Complete los campos requeridos';
            document.getElementById('mf-btn-save').disabled = false;
        }

        renderItems();
        calcularTotales();
        document.getElementById('modal-factura').style.display = 'flex';
    }

    function editarFactura(id) {
        abrirModalFactura(id);
    }

    function cerrarModalFactura() {
        document.getElementById('modal-factura').style.display = 'none';
    }

    function cerrarModalFacturaBackdrop(e) {
        if (e.target === document.getElementById('modal-factura')) cerrarModalFactura();
    }

    /* ── Ítems ── */
    function agregarItemFila() {
        FAC.items.push({
            producto: '',
            cantidad: 1,
            precio: 0,
            descuento: 0,
            iva: 19
        });
        renderItems();
    }

    function renderItems() {
        var body = document.getElementById('items-body');
        if (FAC.items.length === 0) {
            body.innerHTML = '<tr id="items-empty-row"><td colspan="7" ' +
                'style="text-align:center;color:#9CA3AF;padding:16px;font-size:12px;">' +
                'Sin ítems. Haga clic en "＋ Añadir ítem".</td></tr>';
            return;
        }
        body.innerHTML = FAC.items.map(function(it, idx) {
            var sub = it.cantidad * it.precio * (1 - (it.descuento || 0) / 100);
            var opts = '<option value="">— Producto —</option>' +
                FAC.productosCatalogo.map(function(p) {
                    var sel = p.nombre === it.producto ? 'selected' : '';
                    return '<option value="' + esc(p.nombre) + '" ' + sel + '>' + esc(p.nombre) +
                        '</option>';
                }).join('');
            if (!FAC.productosCatalogo.length && it.producto)
                opts += '<option value="' + esc(it.producto) + '" selected>' + esc(it.producto) + '</option>';

            return '<tr>' +
                '<td><select style="width:100%;border:1px solid #D1D5DB;border-radius:6px;' +
                'padding:5px 8px;font-size:12px;" onchange="itemChange(' + idx + ',\'producto\',this.value)">' +
                opts + '</select></td>' +
                '<td><input autocomplete="off" type="number" value="' + it.cantidad + '" min="1" ' +
                'style="width:60px;border:1px solid #D1D5DB;border-radius:6px;padding:5px 8px;font-size:12px;"' +
                ' onchange="itemChange(' + idx + ',\'cantidad\',+this.value)"></td>' +
                '<td><input autocomplete="off" type="number" value="' + it.precio + '" min="0" ' +
                'style="width:100px;border:1px solid #D1D5DB;border-radius:6px;padding:5px 8px;font-size:12px;"' +
                ' onchange="itemChange(' + idx + ',\'precio\',+this.value)"></td>' +
                '<td><input autocomplete="off" type="number" value="' + (it.descuento || 0) + '" min="0" max="100" ' +
                'style="width:60px;border:1px solid #D1D5DB;border-radius:6px;padding:5px 8px;font-size:12px;"' +
                ' onchange="itemChange(' + idx + ',\'descuento\',+this.value)"></td>' +
                '<td><input autocomplete="off" type="number" value="' + (it.iva || 0) + '" min="0" ' +
                'style="width:60px;border:1px solid #D1D5DB;border-radius:6px;padding:5px 8px;font-size:12px;"' +
                ' onchange="itemChange(' + idx + ',\'iva\',+this.value)"></td>' +
                '<td style="text-align:right;font-weight:600;font-size:12px;">' + fmtMoney(sub) + '</td>' +
                '<td><button onclick="eliminarItem(' + idx + ')" ' +
                'style="border:none;background:transparent;cursor:pointer;font-size:14px;color:#DC2626;">✕</button></td>' +
                '</tr>';
        }).join('');
        calcularTotales();
    }

    function itemChange(idx, campo, val) {
        FAC.items[idx][campo] = val;
        renderItems();
    }

    function eliminarItem(idx) {
        FAC.items.splice(idx, 1);
        renderItems();
    }

    function calcularTotales() {
        var descG = parseFloat(document.getElementById('desc-global').value) || 0;
        var subtotal = 0,
            totalIva = 0;
        FAC.items.forEach(function(it) {
            var base = it.cantidad * it.precio * (1 - (it.descuento || 0) / 100);
            subtotal += base;
            totalIva += base * (it.iva || 0) / 100;
        });
        var descuento = subtotal * descG / 100;
        var base = subtotal - descuento;
        var total = base + totalIva;

        document.getElementById('tot-subtotal').textContent = fmtMoney(subtotal);
        document.getElementById('tot-descuento').textContent = '- ' + fmtMoney(descuento);
        document.getElementById('tot-base').textContent = fmtMoney(base);
        document.getElementById('tot-iva').textContent = fmtMoney(totalIva);
        document.getElementById('tot-total').textContent = fmtMoney(total);
    }

    /* ── Guardar ── */
    function guardarFactura() {
        var prefijo = document.getElementById('mf-prefijo').value.trim();
        var cliente = document.getElementById('mf-cliente').value;
        var fecha = document.getElementById('mf-fecha').value;

        if (!prefijo) {
            notif('⚠️ El prefijo es requerido', 'error');
            return;
        }
        if (!cliente) {
            notif('⚠️ Seleccione un cliente', 'error');
            return;
        }
        if (!fecha) {
            notif('⚠️ Seleccione la fecha', 'error');
            return;
        }
        if (FAC.items.length === 0) {
            notif('⚠️ Agregue al menos un ítem', 'error');
            return;
        }

        var token = document.querySelector('meta[name="csrf-token"]')?.content;
        var body = {
            prefijo: prefijo,
            tercero_id: cliente,
            caja_id: document.getElementById('mf-caja').value || null,
            bodega_id: document.getElementById('mf-bodega').value || null,
            fecha: fecha,
            hora: document.getElementById('mf-hora').value,
            observaciones: document.getElementById('mf-observaciones').value,
            registrada: document.getElementById('mf-registrada').checked ? 1 : 0,
            descuento_global: parseFloat(document.getElementById('desc-global').value) || 0,
            items: FAC.items,
        };

        var url = FAC.editandoId ? '/facturas/' + FAC.editandoId : '/facturas';
        var method = FAC.editandoId ? 'PUT' : 'POST';

        document.getElementById('mf-btn-save').disabled = true;
        document.getElementById('mf-btn-save').textContent = '⏳ Guardando…';

        fetch(url, {
                method: method,
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify(body),
            })
            .then(function(r) {
                return r.json();
            })
            .then(function(res) {
                if (res.error) throw new Error(res.error);
                notif(FAC.editandoId ? '✅ Factura actualizada' : '✅ Factura creada correctamente', 'success');
                cerrarModalFactura();
                cargarFacturas();
            })
            .catch(function(e) {
                notif(e.message || 'No se pudo guardar la factura', 'error');
            })
            .finally(function() {
                document.getElementById('mf-btn-save').disabled = false;
                document.getElementById('mf-btn-save').textContent = '💾 Guardar Factura';
            });
    }

    /* ── Nota crédito / débito ── */
    var NOTA = { facturaId: null, numero: '', items: [] };

    window.abrirModalNota = function (facturaId) {
        fetch('/facturas/' + facturaId)
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data || !data.id) throw new Error(data && data.message || 'No se pudo cargar la factura');
                NOTA.facturaId = data.id;
                NOTA.numero = data.numero;
                NOTA.items = data.items || [];

                document.getElementById('nota-sub').textContent = 'Factura ' + data.numero;
                document.getElementById('nota-tipo').value = 'credito';
                document.getElementById('nota-restaura-inventario').checked = true;
                document.getElementById('nota-motivo').value = '';
                renderLineasNota();

                document.getElementById('modal-nota').style.display = 'flex';
            })
            .catch(function (e) { notif(e.message || 'No se pudo abrir la factura', 'error'); });
    };

    window.cerrarModalNota = function () {
        document.getElementById('modal-nota').style.display = 'none';
    };

    window.renderLineasNota = function () {
        var tipo = document.getElementById('nota-tipo').value;
        var restauraWrap = document.getElementById('nota-campo-restaura');
        if (restauraWrap) restauraWrap.style.display = tipo === 'credito' ? '' : 'none';

        var box = document.getElementById('nota-lineas');
        box.innerHTML = NOTA.items.map(function (it, idx) {
            return '<div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #F3F4F6;">' +
                '<div style="flex:1;"><div style="font-weight:600;font-size:13px;">' + esc(it.producto) + '</div>' +
                '<div style="font-size:11px;color:#6B7280;">Vendido: ' + it.cantidad + ' × $' + Number(it.precio).toLocaleString('es-CO') + '</div></div>' +
                '<input autocomplete="off" type="number" min="0" max="' + it.cantidad + '" step="0.01" value="0" id="nota-cant-' + idx + '" ' +
                'style="width:90px;border:1px solid #D1D5DB;border-radius:7px;padding:6px 8px;font-size:12.5px;">' +
                '</div>';
        }).join('') || '<p class="field-hint">Esta factura no tiene líneas.</p>';
    };

    window.guardarNota = function () {
        var tipo = document.getElementById('nota-tipo').value;
        var motivo = document.getElementById('nota-motivo').value.trim();
        var restaurarInventario = document.getElementById('nota-restaura-inventario').checked;

        if (motivo.length < 5) {
            notif('Escribe el motivo de la nota (mínimo 5 caracteres)', 'warning');
            return;
        }

        var lineas = [];
        NOTA.items.forEach(function (it, idx) {
            var cantidad = Number(document.getElementById('nota-cant-' + idx).value) || 0;
            if (cantidad > 0) lineas.push({ factura_detalle_id: it.id, cantidad: cantidad });
        });

        if (lineas.length === 0) {
            notif('Indica la cantidad a notar de al menos una línea', 'warning');
            return;
        }

        var btn = document.getElementById('nota-btn-guardar');
        btn.disabled = true;
        btn.textContent = 'Procesando...';

        var token = document.querySelector('meta[name="csrf-token"]')?.content;
        fetch('/facturas/' + NOTA.facturaId + '/notas', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': token, 'Accept': 'application/json' },
                body: JSON.stringify({ tipo: tipo, motivo: motivo, restaura_inventario: restaurarInventario, lineas: lineas }),
            })
            .then(function (r) { return r.json().then(function (data) { return { ok: r.ok, data: data }; }); })
            .then(function (res) {
                if (!res.ok) {
                    var msg = res.data.message || (res.data.errors && Object.values(res.data.errors)[0][0]) || 'No se pudo emitir la nota';
                    throw new Error(msg);
                }
                notif(res.data.message, 'success');
                cerrarModalNota();
                cargarFacturas();
            })
            .catch(function (e) { notif(e.message, 'error'); })
            .finally(function () {
                btn.disabled = false;
                btn.textContent = '💾 Emitir nota';
            });
    };

    /* ── Revertir / Anular ── */
    function anularFactura(id, codigo) {
        confirmarAccionFactura('¿Anular la factura ' + codigo + '? Se marcará como anulada y se revertirá el inventario.', function() {
            var token = document.querySelector('meta[name="csrf-token"]')?.content;
            fetch('/facturas/' + id + '/anular', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    }
                })
                .then(function(res) {
                    return res.json().then(function(data) {
                        return { ok: res.ok, data: data };
                    });
                })
                .then(function(resultado) {
                    if (!resultado.ok) throw new Error(resultado.data.message || 'No se pudo anular la factura');
                    notif('🔄 Factura ' + codigo + ' anulada correctamente', 'success');
                    cargarFacturas();
                })
                .catch(function(error) {
                    notif(error.message || 'No se pudo anular la factura', 'error');
                });
        });
    }

    function revertirAnulacion(id, codigo) {
        confirmarAccionFactura('¿Revertir la anulación de ' + codigo + '? La factura volverá a estar activa.', function() {
            var token = document.querySelector('meta[name="csrf-token"]')?.content;
            fetch('/facturas/' + id + '/revertir', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    }
                })
                .then(function(res) {
                    return res.json().then(data => ({
                        ok: res.ok,
                        data
                    }));
                })
                .then(function({
                    ok,
                    data
                }) {
                    if (!ok) {
                        notif(data.message, 'error');
                        return;
                    }
                    notif('↩️ Factura ' + codigo + ' restaurada', 'success');
                    cargarFacturas();
                })
                .catch(function(e) {
                    notif(e.message || 'Error al revertir la anulación', 'error');
                });
        });
    }

    function reintentarDian(id, codigo) {
        confirmarAccionFactura('¿Reintentar la transmisión a la DIAN de ' + codigo + '?', function() {
            var token = document.querySelector('meta[name="csrf-token"]')?.content;
            fetch('/facturas/' + id + '/reintentar-dian', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                })
                .then(function(res) { return res.json().then(function(data) { return { ok: res.ok, data: data }; }); })
                .then(function(res) {
                    if (!res.ok) throw new Error(res.data.message || 'No se pudo reintentar');
                    notif(res.data.message, 'success');
                    cargarFacturas();
                })
                .catch(function(e) { notif(e.message, 'error'); });
        });
    }

    /* ── Eliminar ── */
    function eliminarFactura(id, codigo) {
        confirmarAccionFactura('¿Eliminar el borrador ' + codigo + '? Esta acción no se puede deshacer.', function() {
            var token = document.querySelector('meta[name="csrf-token"]')?.content;
            fetch('/facturas/' + id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    }
                })
                .then(function(res) {
                    return res.json().then(function(data) {
                        return { ok: res.ok, data: data };
                    });
                })
                .then(function(resultado) {
                    if (!resultado.ok) throw new Error(resultado.data.message || 'No se pudo eliminar el borrador');
                    notif('🗑️ Factura ' + codigo + ' eliminada', 'success');
                    cargarFacturas();
                })
                .catch(function(error) {
                    notif(error.message || 'No se pudo eliminar el borrador', 'error');
                });
        });
    }

    /* ── Exportar ── */
    function exportarFacturas() {
        exportarCsvFacturas(FAC.filtradas, 'facturas.csv');
    }

    function exportarCsvFacturas(facturas, nombreArchivo) {
        if (!facturas.length) {
            notif('No hay facturas para exportar', 'warning');
            return;
        }

        var encabezados = ['Factura', 'Fecha', 'Hora', 'Cliente', 'Subtotal', 'Impuestos', 'Propina', 'Total', 'Estado', 'Cajero'];
        var filas = facturas.map(function(f) {
            return [
                f.prefijo + '-' + f.numero,
                f.fecha || '',
                f.hora || '',
                f.cliente || '',
                f.subtotal || 0,
                f.impuestos || 0,
                f.propina || 0,
                f.total || 0,
                f.anulada ? 'Anulada' : 'Pagada',
                f.usuario || ''
            ].map(function(valor) {
                return '"' + String(valor).replace(/"/g, '""') + '"';
            }).join(',');
        });

        var csv = '\uFEFF' + encabezados.join(',') + '\n' + filas.join('\n');
        var archivo = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        var enlace = document.createElement('a');
        enlace.href = URL.createObjectURL(archivo);
        enlace.download = nombreArchivo;
        enlace.click();
        URL.revokeObjectURL(enlace.href);
        notif('Exportación creada: ' + facturas.length + ' factura' + (facturas.length !== 1 ? 's' : ''), 'success');
    }

    /* ════════════════════════════════════════════════
       UTILIDADES
    ════════════════════════════════════════════════ */
    function fmtMoney(n) {
        return '$ ' + (n || 0).toLocaleString('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });
    }

    function fmtFecha(s) {
        if (!s) return '—';
        var parts = s.split('-');
        return parts[2] + '/' + parts[1] + '/' + parts[0];
    }

    function esc(s) {
        return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g,
            '&quot;');
    }

    function notif(msg, tipo) {
        if (typeof mostrarNotificacion === 'function') {
            mostrarNotificacion(msg, tipo === 'error' ? 'error' : tipo === 'warning' ? 'warning' : 'success');
        } else {
            var cls = {
                success: '#059669',
                error: '#DC2626',
                info: '#1D4ED8',
                warning: '#D97706'
            };
            var el = document.createElement('div');
            el.textContent = msg;
            el.style.cssText = 'background:#fff;border:1px solid #EAECF0;border-left:4px solid ' +
                (cls[tipo] || cls.info) + ';border-radius:8px;padding:10px 16px;font-size:13px;' +
                'color:#111827;box-shadow:0 4px 12px rgba(0,0,0,0.08);pointer-events:auto;' +
                'min-width:220px;max-width:360px;';
            document.getElementById('notificaciones').appendChild(el);
            setTimeout(function() {
                el.remove();
            }, 3500);
        }
    }
</script>
