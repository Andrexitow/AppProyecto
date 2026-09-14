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

    .btn-primary:disabled {
        opacity: 0.5;
        cursor: not-allowed;
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

    table.cp-tbl {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
    }

    table.cp-tbl thead {
        background: #F8FAFC;
        border-bottom: 1px solid #EAECF0;
    }

    table.cp-tbl thead th {
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

    table.cp-tbl thead th:hover {
        color: #1D4ED8;
    }

    table.cp-tbl thead th .sort-icon {
        display: inline-block;
        margin-left: 4px;
        opacity: 0.4;
        font-size: 10px;
    }

    table.cp-tbl thead th.sorted .sort-icon {
        opacity: 1;
        color: #1D4ED8;
    }

    table.cp-tbl tbody tr {
        border-bottom: 1px solid #F3F4F6;
        transition: background 0.1s;
    }

    table.cp-tbl tbody tr:last-child {
        border-bottom: none;
    }

    table.cp-tbl tbody tr:hover {
        background: #F8FAFC;
    }

    table.cp-tbl tbody tr.anulado {
        opacity: 0.6;
    }

    table.cp-tbl tbody tr.anulado td {
        text-decoration: line-through;
    }

    table.cp-tbl tbody tr.anulado .badge {
        text-decoration: none;
    }

    table.cp-tbl tbody tr.anulado .tbl-actions {
        text-decoration: none;
    }

    table.cp-tbl td {
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
        white-space: nowrap;
    }

    .td-money.debito {
        color: #1D4ED8;
    }

    .td-money.credito {
        color: #DC2626;
    }

    .td-obs {
        max-width: 220px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        color: #6B7280;
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

    /* ── Modal Comprobante ── */
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

    .modal-comprobante {
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

    .cp-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 16px;
    }

    @media (max-width: 480px) {
        .cp-grid {
            grid-template-columns: 1fr;
        }
    }

    .cp-field {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .cp-field label {
        font-size: 11px;
        font-weight: 500;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .cp-field input,
    .cp-field select,
    .cp-field textarea {
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

    .cp-field input:focus,
    .cp-field select:focus,
    .cp-field textarea:focus {
        border-color: #1D4ED8;
        background: #fff;
    }

    .cp-field input[readonly] {
        background: #F3F4F6;
        color: #6B7280;
    }

    .cp-section-title {
        font-size: 12px;
        font-weight: 600;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin: 16px 0 10px;
        padding-bottom: 6px;
        border-bottom: 1px solid #F3F4F6;
    }

    /* Items del comprobante (movimientos) */
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

    .totales-row.desc-ok {
        color: #059669;
    }

    .totales-row.desc-bad {
        color: #DC2626;
    }

    /* Alerta anulado */
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

    /* Alerta descuadre */
    .alerta-descuadre {
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

        table.cp-tbl thead th:nth-child(n+7) {
            display: none;
        }

        table.cp-tbl tbody td:nth-child(n+7) {
            display: none;
        }
    }
</style>
<div id="view-comprobantes">

    {{-- ── ENCABEZADO ── --}}
    <div class="sec-header">
        <div>
            <p class="sec-title">🧾 Comprobantes Contables</p>
            <p class="sec-subtitle">Registro, consulta y gestión de comprobantes de contabilidad</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button class="btn-outline" onclick="exportarComprobantes()">
                ⬇️ Exportar
            </button>
            <button class="btn-primary" onclick="abrirModalComprobante(null)">
                ＋ Nuevo Comprobante
            </button>
        </div>
    </div>

    {{-- ── MÉTRICAS ── --}}
    <div class="metrics-row" id="metricas-comprobantes">
        <div class="metric-card" style="--accent:#1D4ED8">
            <p class="metric-label">Total Comprobantes</p>
            <p class="metric-value" id="cp-m-total">—</p>
            <p class="metric-sub" id="cp-m-total-sub">Cargando…</p>
        </div>
        <div class="metric-card" style="--accent:#1D4ED8">
            <p class="metric-label">Débito (mes)</p>
            <p class="metric-value money" id="cp-m-debito">—</p>
            <p class="metric-sub" id="cp-m-debito-sub"></p>
        </div>
        <div class="metric-card" style="--accent:#D97706">
            <p class="metric-label">Crédito (mes)</p>
            <p class="metric-value money" id="cp-m-credito">—</p>
            <p class="metric-sub" id="cp-m-credito-sub"></p>
        </div>
        <div class="metric-card" style="--accent:#DC2626">
            <p class="metric-label">Anulados</p>
            <p class="metric-value" id="cp-m-anulados">—</p>
            <p class="metric-sub" id="cp-m-anulados-sub"></p>
        </div>
        <div class="metric-card" style="--accent:#7C3AED">
            <p class="metric-label">Sin Cuadrar</p>
            <p class="metric-value" id="cp-m-descuadre">—</p>
            <p class="metric-sub">Débito ≠ Crédito</p>
        </div>
    </div>

    {{-- ── FILTROS ── --}}
    <div class="filter-bar">
        <div class="fi-group" style="flex:2;min-width:200px;">
            <span class="fi-label">🔍</span>
            <input class="fi-input" type="text" id="cp-fi-buscar"
                placeholder="Prefijo, número, observaciones, usuario…" oninput="aplicarFiltrosCP()">
        </div>
        <div class="fi-group">
            <span class="fi-label">Desde</span>
            <input class="fi-input" type="date" id="cp-fi-desde" onchange="aplicarFiltrosCP()">
        </div>
        <div class="fi-group">
            <span class="fi-label">Hasta</span>
            <input class="fi-input" type="date" id="cp-fi-hasta" onchange="aplicarFiltrosCP()">
        </div>
        <div class="fi-group">
            <select class="fi-select" id="cp-fi-tipo" onchange="aplicarFiltrosCP()">
                <option value="">Todos los tipos</option>
                <option value="Ingreso">Ingreso</option>
                <option value="Egreso">Egreso</option>
                <option value="Diario">Diario</option>
                <option value="Nota Débito">Nota Débito</option>
                <option value="Nota Crédito">Nota Crédito</option>
                <option value="Ajuste">Ajuste</option>
            </select>
        </div>
        <div class="fi-group">
            <select class="fi-select" id="cp-fi-estado" onchange="aplicarFiltrosCP()">
                <option value="">Todos los estados</option>
                <option value="activo">Activo</option>
                <option value="anulado">Anulado</option>
            </select>
        </div>
        <button class="btn-outline" onclick="limpiarFiltrosCP()">✕ Limpiar</button>
    </div>

    {{-- ── BULK TOOLBAR ── --}}
    <div class="bulk-toolbar" id="cp-bulk-toolbar">
        <span class="bulk-text" id="cp-bulk-text">0 comprobantes seleccionados</span>
        <div class="bulk-actions">
            <button class="btn-bulk" onclick="exportarSeleccionCP()">⬇️ Exportar selección</button>
            <button class="btn-bulk danger" onclick="anularSeleccionCP()">🚫 Anular selección</button>
        </div>
    </div>

    {{-- ── TABLA ── --}}
    <div class="table-wrapper">
        <div class="table-scroll">
            <table class="cp-tbl" id="tbl-comprobantes">
                <thead>
                    <tr>
                        <th style="width:36px;">
                            <input type="checkbox" class="chk-row" id="cp-chk-all" onchange="toggleTodasCP(this)">
                        </th>
                        <th onclick="sortTablaCP('tipo')" data-col="tipo">
                            Tipo <span class="sort-icon">↕</span>
                        </th>
                        <th onclick="sortTablaCP('numero')" data-col="numero" style="width:110px;">
                            N° <span class="sort-icon">↕</span>
                        </th>
                        <th onclick="sortTablaCP('fecha')" data-col="fecha">
                            Fecha <span class="sort-icon">↕</span>
                        </th>
                        <th>Observaciones</th>
                        <th onclick="sortTablaCP('debito')" data-col="debito" style="text-align:right;">
                            Débito <span class="sort-icon">↕</span>
                        </th>
                        <th onclick="sortTablaCP('credito')" data-col="credito" style="text-align:right;">
                            Crédito <span class="sort-icon">↕</span>
                        </th>
                        <th>Estado</th>
                        <th>Usuario</th>
                        <th style="text-align:center;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-comprobantes">
                    <tr>
                        <td colspan="10">
                            <div class="spinner-cell">
                                <div class="spinner"></div>
                                Cargando comprobantes…
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="pagination-bar">
            <span class="pag-info" id="cp-pag-info">Mostrando 0 de 0 registros</span>
            <div class="pag-btns" id="cp-pag-btns"></div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════
     MODAL COMPROBANTE (crear / editar)
═══════════════════════════════════════════════ --}}
<div id="modal-comprobante" style="display:none;" class="modal-backdrop"
    onclick="cerrarModalComprobanteBackdrop(event)">
    <div class="modal-comprobante">

        <div class="modal-head">
            <div>
                <p class="modal-head-title" id="cp-mf-title">Nuevo Comprobante</p>
                <p class="modal-head-sub" id="cp-mf-sub">Complete los campos requeridos</p>
            </div>
            <button onclick="cerrarModalComprobante()"
                style="
                border:none;background:transparent;font-size:20px;cursor:pointer;
                color:#6B7280;padding:4px;border-radius:6px;line-height:1;
            ">✕</button>
        </div>

        <div class="modal-body" id="cp-mf-body">

            {{-- Alertas condicionales --}}
            <div id="cp-mf-alerta-anulada" class="alerta-anulada" style="display:none;">
                🚫 Este comprobante se encuentra <strong>ANULADO</strong>. No se puede modificar.
            </div>
            <div id="cp-mf-alerta-descuadre" class="alerta-descuadre" style="display:none;">
                ⚠️ El comprobante no está cuadrado: el total débito debe ser igual al total crédito.
            </div>

            {{-- Encabezado comprobante --}}
            <p class="cp-section-title">Encabezado</p>
            <div class="cp-grid">
                <div class="cp-field">
                    <label>Tipo *</label>
                    <select id="cp-mf-tipo">
                        <option value="">— Seleccione tipo —</option>
                        <option value="Ingreso">Ingreso</option>
                        <option value="Egreso">Egreso</option>
                        <option value="Diario">Diario</option>
                        <option value="Nota Débito">Nota Débito</option>
                        <option value="Nota Crédito">Nota Crédito</option>
                        <option value="Ajuste">Ajuste</option>
                    </select>
                </div>
                <div class="cp-field">
                    <label>Prefijo *</label>
                    <input type="text" id="cp-mf-prefijo" placeholder="Ej: CC" maxlength="10"
                        oninput="this.value=this.value.toUpperCase()">
                </div>
                <div class="cp-field">
                    <label>Número</label>
                    <input type="text" id="cp-mf-numero" placeholder="Automático" readonly>
                </div>
                <div class="cp-field">
                    <label>Fecha *</label>
                    <input type="date" id="cp-mf-fecha">
                </div>
                <div class="cp-field" style="grid-column:1/-1;">
                    <label>Observaciones</label>
                    <textarea id="cp-mf-observaciones" rows="2" placeholder="Descripción del comprobante…"
                        style="resize:vertical;"></textarea>
                </div>
            </div>

            {{-- Detalle de movimientos --}}
            <p class="cp-section-title">
                Movimientos contables
                <button class="btn-primary" onclick="agregarItemFilaCP()"
                    style="float:right;font-size:11px;padding:4px 10px;">+ Añadir movimiento</button>
            </p>

            <div class="items-tbl-wrap">
                <table class="items-tbl" id="cp-items-tbl">
                    <thead>
                        <tr>
                            <th style="width:22%;">Cuenta contable</th>
                            <th>Tercero</th>
                            <th style="width:26%;">Detalle</th>
                            <th style="text-align:right;">Débito</th>
                            <th style="text-align:right;">Crédito</th>
                            <th style="width:36px;"></th>
                        </tr>
                    </thead>
                    <tbody id="cp-items-body">
                        <tr id="cp-items-empty-row">
                            <td colspan="6" style="text-align:center;color:#9CA3AF;padding:16px;font-size:12px;">
                                Sin movimientos. Haga clic en "＋ Añadir movimiento".
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            {{-- Totales --}}
            <div class="totales-box">
                <div class="totales-row">
                    <span>Total Débito</span>
                    <span id="cp-tot-debito">$ 0.00</span>
                </div>
                <div class="totales-row">
                    <span>Total Crédito</span>
                    <span id="cp-tot-credito">$ 0.00</span>
                </div>
                <div class="totales-row total-final" id="cp-tot-diferencia-row">
                    <span>Diferencia</span>
                    <span id="cp-tot-diferencia">$ 0.00</span>
                </div>
            </div>

        </div>

        <div class="modal-foot">
            <button class="btn-outline" onclick="cerrarModalComprobante()">Cancelar</button>
            <button class="btn-primary" id="cp-mf-btn-save" onclick="guardarComprobante()">
                💾 Guardar Comprobante
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     MODAL VER COMPROBANTE (solo lectura)
═══════════════════════════════════════════════ --}}
<div id="modal-ver-comprobante" style="display:none;" class="modal-backdrop"
    onclick="cerrarVerComprobanteBackdrop(event)">
    <div class="modal-comprobante">
        <div class="modal-head">
            <div>
                <p class="modal-head-title" id="cp-vf-title">Comprobante</p>
                <p class="modal-head-sub" id="cp-vf-sub"></p>
            </div>
            <button onclick="cerrarVerComprobante()"
                style="
                border:none;background:transparent;font-size:20px;cursor:pointer;
                color:#6B7280;padding:4px;border-radius:6px;line-height:1;
            ">✕</button>
        </div>
        <div class="modal-body" id="cp-vf-body">
            {{-- Se llena dinámicamente --}}
        </div>
        <div class="modal-foot">
            <button class="btn-outline" onclick="imprimirComprobante()">🖨️ Imprimir</button>
            <button class="btn-outline" onclick="cerrarVerComprobante()">Cerrar</button>
        </div>
    </div>
</div>

<script>
    var CP = {
        datos: [], // todos los comprobantes cargados
        filtradas: [], // tras aplicar filtros
        paginaActual: 1,
        porPagina: 15,
        sortCol: 'numero',
        sortAsc: false,
        seleccionadas: new Set(),
        modoModal: 'nuevo', // 'nuevo' | 'editar' | 'ver'
        comprobanteActual: null,
        items: [], // movimientos del modal activo
        cuentasCatalogo: [],
        tercerosCatalogo: [],
        editandoId: null,
    };

    /* ════════════════════════════════════════════════
       INICIALIZACIÓN
    ════════════════════════════════════════════════ */
    (function initCP() {
        var hoy = new Date();
        var primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        document.getElementById('cp-fi-desde').value = fmtCP(primerDia);
        document.getElementById('cp-fi-hasta').value = fmtCP(hoy);

        cargarComprobantes();
        cargarCatalogosCP();

        function fmtCP(d) {
            return d.getFullYear() + '-' +
                String(d.getMonth() + 1).padStart(2, '0') + '-' +
                String(d.getDate()).padStart(2, '0');
        }
    })();

    /* ════════════════════════════════════════════════
       CARGA DE DATOS (fetch API Laravel)
    ════════════════════════════════════════════════ */
    function cargarComprobantes() {
        mostrarSpinnerCP();
        var token = document.querySelector('meta[name="csrf-token"]')?.content;

        fetch('/comprobantes', {
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            })
            .then(function(r) {
                return r.json();
            })
            .then(function(res) {
                CP.datos = Array.isArray(res.data) ? res.data : (Array.isArray(res) ? res : []);
                aplicarFiltrosCP();
                actualizarMetricasCP();
            })
            .catch(function(e) {
                CP.datos = [];
                aplicarFiltrosCP();
                actualizarMetricasCP();
                notifCP('No fue posible cargar los comprobantes. Revise la conexión e inténtelo de nuevo.', 'error');
            });
    }

    function cargarCatalogosCP() {
        /* Carga plan de cuentas y terceros para los selects de movimientos */
        var token = document.querySelector('meta[name="csrf-token"]')?.content;
        var hdrs = {
            'X-CSRF-TOKEN': token,
            'Accept': 'application/json'
        };

        fetch('/cuentas-contables/data', {
            headers: hdrs
        }).then(r => r.json()).then(function(res) {
            CP.cuentasCatalogo = Array.isArray(res.data) ? res.data : (Array.isArray(res) ? res : []);
        }).catch(function() {});

        fetch('/terceros', {
            headers: hdrs
        }).then(r => r.json()).then(function(res) {
            CP.tercerosCatalogo = Array.isArray(res.data) ? res.data : (Array.isArray(res) ? res : []);
        }).catch(function() {});
    }

    /* ════════════════════════════════════════════════
       DATOS DEMO (para previsualización sin backend)
    ════════════════════════════════════════════════ */
    function datosDemoComprobantes() {
        var hoy = new Date();
        var tipos = ['Ingreso', 'Egreso', 'Diario', 'Nota Débito', 'Nota Crédito', 'Ajuste'];
        var prefijosPorTipo = {
            'Ingreso': 'CI',
            'Egreso': 'CE',
            'Diario': 'CD',
            'Nota Débito': 'ND',
            'Nota Crédito': 'NC',
            'Ajuste': 'AJ'
        };
        var observaciones = [
            'Pago proveedor mes de referencia', 'Consignación bancaria', 'Ajuste de inventario',
            'Registro de nómina', 'Causación de gastos varios', 'Traslado entre cuentas',
            'Compra de suministros', 'Recaudo cartera cliente', 'Depreciación de activos'
        ];
        var usuarios = ['admin', 'contador1', 'auxiliar2', 'supervisor'];
        var result = [];
        for (var i = 1; i <= 64; i++) {
            var fecha = new Date(hoy);
            fecha.setDate(hoy.getDate() - Math.floor(Math.random() * 60));
            var tipo = tipos[Math.floor(Math.random() * tipos.length)];
            var anulado = Math.random() < 0.08;
            var valor = Math.round((Math.random() * 3000000 + 50000) / 100) * 100;
            var cuadrado = Math.random() > 0.12;
            result.push({
                id: i,
                tipo: tipo,
                prefijo: prefijosPorTipo[tipo],
                numero: String(1000 + i).padStart(5, '0'),
                fecha: fecha.toISOString().slice(0, 10),
                observaciones: observaciones[i % observaciones.length],
                debito: valor,
                credito: cuadrado ? valor : Math.round(valor * 0.85),
                anulado: anulado,
                usuario: usuarios[i % usuarios.length],
                items: [{
                        cuenta: '110505 - Caja General',
                        tercero: 'Consumidor Final',
                        detalle: observaciones[i % observaciones.length],
                        debito: valor,
                        credito: 0
                    },
                    {
                        cuenta: '413501 - Ingresos Varios',
                        tercero: 'Consumidor Final',
                        detalle: observaciones[i % observaciones.length],
                        debito: 0,
                        credito: cuadrado ? valor : Math.round(valor * 0.85)
                    }
                ]
            });
        }
        return result.sort(function(a, b) {
            return b.numero.localeCompare(a.numero);
        });
    }

    /* ════════════════════════════════════════════════
       FILTROS
    ════════════════════════════════════════════════ */
    function aplicarFiltrosCP() {
        var buscar = (document.getElementById('cp-fi-buscar').value || '').toLowerCase().trim();
        var desde = document.getElementById('cp-fi-desde').value;
        var hasta = document.getElementById('cp-fi-hasta').value;
        var tipo = document.getElementById('cp-fi-tipo').value;
        var estado = document.getElementById('cp-fi-estado').value;

        CP.filtradas = CP.datos.filter(function(c) {
            if (buscar) {
                var hay = ((c.numero || '') + (c.observaciones || '') + (c.usuario || ''))
                    .toLowerCase().includes(buscar);
                if (!hay) return false;
            }
            if (desde && c.fecha < desde) return false;
            if (hasta && c.fecha > hasta) return false;
            if (tipo && c.tipo !== tipo) return false;
            if (estado === 'activo' && c.anulado) return false;
            if (estado === 'anulado' && !c.anulado) return false;
            return true;
        });

        CP.paginaActual = 1;
        sortTablaActualCP();
        renderTablaCP();
    }

    function limpiarFiltrosCP() {
        document.getElementById('cp-fi-buscar').value = '';
        document.getElementById('cp-fi-tipo').value = '';
        document.getElementById('cp-fi-estado').value = '';
        var hoy = new Date();
        var primo = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        document.getElementById('cp-fi-desde').value =
            primo.toISOString().slice(0, 10);
        document.getElementById('cp-fi-hasta').value =
            hoy.toISOString().slice(0, 10);
        aplicarFiltrosCP();
    }

    /* ════════════════════════════════════════════════
       ORDENAMIENTO
    ════════════════════════════════════════════════ */
    function sortTablaCP(col) {
        if (CP.sortCol === col) CP.sortAsc = !CP.sortAsc;
        else {
            CP.sortCol = col;
            CP.sortAsc = false;
        }
        sortTablaActualCP();
        renderTablaCP();
        document.querySelectorAll('table.cp-tbl thead th[data-col]').forEach(function(th) {
            th.classList.toggle('sorted', th.dataset.col === col);
            var icon = th.querySelector('.sort-icon');
            if (icon) icon.textContent = th.dataset.col === col ? (CP.sortAsc ? '↑' : '↓') : '↕';
        });
    }

    function sortTablaActualCP() {
        var col = CP.sortCol,
            asc = CP.sortAsc;
        CP.filtradas.sort(function(a, b) {
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
    function renderTablaCP() {
        var tbody = document.getElementById('tbody-comprobantes');
        var total = CP.filtradas.length;
        var desde = (CP.paginaActual - 1) * CP.porPagina;
        var pagina = CP.filtradas.slice(desde, desde + CP.porPagina);

        document.getElementById('cp-pag-info').textContent =
            'Mostrando ' + Math.min(pagina.length, total) +
            ' de ' + total + ' registros';

        if (pagina.length === 0) {
            tbody.innerHTML = '<tr><td colspan="10">' +
                '<div class="spinner-cell">📭 No se encontraron comprobantes</div></td></tr>';
            renderPaginacionCP(total);
            return;
        }

        tbody.innerHTML = pagina.map(function(c) {
            var anulado = c.anulado;
            var trCls = anulado ? 'anulado' : '';
            var descuadrado = Math.abs((c.debito || 0) - (c.credito || 0)) > 0.01;

            var badgeEstado = anulado ?
                '<span class="badge badge-red"><span class="dot"></span>Anulado</span>' :
                (descuadrado ?
                    '<span class="badge badge-yellow"><span class="dot"></span>Sin cuadrar</span>' :
                    '<span class="badge badge-green"><span class="dot"></span>Activo</span>');

            var badgeTipo = badgeTipoCP(c.tipo);

            var chked = CP.seleccionadas.has(c.id) ? 'checked' : '';

            /* Botones de acción */
            var btnVer = '<button class="act-btn view" data-tip="Ver comprobante" ' +
                'onclick="verComprobante(' + c.id + ')">👁️</button>';
            var btnEdit = anulado ? '' :
                '<button class="act-btn edit" data-tip="Editar" ' +
                'onclick="editarComprobante(' + c.id + ')">✏️</button>';
            btnEdit = (!c.manual || anulado) ? '' : btnEdit;
            var btnRev = (!c.manual || anulado) ? '' :
                '<button class="act-btn rev" data-tip="Anular" ' +
                'onclick="anularComprobante(' + c.id + ',\'' + esc(c.numero) + '\')">🚫</button>';

            var btnRestaurar = (anulado && c.manual) ?
                '<button class="act-btn edit" data-tip="Revertir anulación" ' +
                'onclick="revertirAnulacionCP(' + c.id + ',\'' + esc(c.numero) +
                '\')">↩️</button>' :
                '';
            var btnDel = (!c.manual || anulado) ? '' : '<button class="act-btn del" data-tip="Eliminar" ' +
                'onclick="eliminarComprobante(' + c.id + ',\'' + esc(c.numero) +
                '\')">🗑️</button>';

            return '<tr class="' + trCls + '" data-id="' + c.id + '">' +
                '<td><input type="checkbox" class="chk-row chk-item-cp" ' + chked +
                ' onchange="toggleSeleccionCP(' + c.id + ',this)"></td>' +
                '<td>' + badgeTipo + '</td>' +
                '<td><span class="td-num">' + esc(c.numero) + '</span></td>' +
                '<td>' + fmtFechaCP(c.fecha) + '</td>' +
                '<td class="td-obs" title="' + esc(c.observaciones || '') + '">' +
                esc(c.observaciones || '—') + '</td>' +
                '<td class="td-money debito">' + ((c.debito || 0) > 0 ? fmtMoneyCP(c.debito) : '—') + '</td>' +
                '<td class="td-money credito">' + ((c.credito || 0) > 0 ? fmtMoneyCP(c.credito) : '—') +
                '</td>' +
                '<td>' + badgeEstado + '</td>' +
                '<td style="color:#6B7280;font-size:12px;">' + esc(c.usuario || '—') + '</td>' +
                '<td><div class="tbl-actions">' + btnVer + btnEdit + btnRev + btnRestaurar + btnDel +
                '</div></td>' +
                '</tr>';
        }).join('');

        renderPaginacionCP(total);
    }

    function badgeTipoCP(tipo) {
        var map = {
            'Ingreso': 'badge-green',
            'Egreso': 'badge-red',
            'Diario': 'badge-blue',
            'Nota Débito': 'badge-purple',
            'Nota Crédito': 'badge-yellow',
            'Ajuste': 'badge-gray'
        };
        return '<span class="badge ' + (map[tipo] || 'badge-gray') + '">' + esc(tipo || '—') + '</span>';
    }

    function renderPaginacionCP(total) {
        var totalPags = Math.max(1, Math.ceil(total / CP.porPagina));
        var actual = CP.paginaActual;
        var btns = document.getElementById('cp-pag-btns');
        var html = '';

        html += '<button class="pag-btn" onclick="irPaginaCP(' + (actual - 1) + ')"' +
            (actual === 1 ? ' disabled' : '') + '>‹</button>';

        var desde = Math.max(1, actual - 2);
        var hasta = Math.min(totalPags, desde + 4);
        desde = Math.max(1, hasta - 4);

        if (desde > 1) html += '<button class="pag-btn" onclick="irPaginaCP(1)">1</button>' +
            (desde > 2 ? '<span style="padding:0 4px;color:#9CA3AF;">…</span>' : '');

        for (var p = desde; p <= hasta; p++) {
            html += '<button class="pag-btn' + (p === actual ? ' active' : '') + '" onclick="irPaginaCP(' + p + ')">' +
                p + '</button>';
        }

        if (hasta < totalPags) {
            html += (hasta < totalPags - 1 ? '<span style="padding:0 4px;color:#9CA3AF;">…</span>' : '') +
                '<button class="pag-btn" onclick="irPaginaCP(' + totalPags + ')">' + totalPags + '</button>';
        }

        html += '<button class="pag-btn" onclick="irPaginaCP(' + (actual + 1) + ')"' +
            (actual === totalPags ? ' disabled' : '') + '>›</button>';

        btns.innerHTML = html;
    }

    function irPaginaCP(p) {
        var total = CP.filtradas.length;
        var totalPags = Math.max(1, Math.ceil(total / CP.porPagina));
        CP.paginaActual = Math.max(1, Math.min(p, totalPags));
        renderTablaCP();
    }

    function mostrarSpinnerCP() {
        document.getElementById('tbody-comprobantes').innerHTML =
            '<tr><td colspan="10"><div class="spinner-cell">' +
            '<div class="spinner"></div>Cargando comprobantes…</div></td></tr>';
    }

    /* ════════════════════════════════════════════════
       MÉTRICAS
    ════════════════════════════════════════════════ */
    function actualizarMetricasCP() {
        var mesActual = new Date().toISOString().slice(0, 7);

        var total = CP.datos.length;
        var anulados = CP.datos.filter(function(c) {
            return c.anulado;
        }).length;
        var delMes = CP.datos.filter(function(c) {
            return c.fecha && c.fecha.startsWith(mesActual) && !c.anulado;
        });
        var descuadrados = CP.datos.filter(function(c) {
            return !c.anulado && Math.abs((c.debito || 0) - (c.credito || 0)) > 0.01;
        }).length;

        var sumaDebito = delMes.reduce(function(s, c) {
            return s + (c.debito || 0);
        }, 0);
        var sumaCredito = delMes.reduce(function(s, c) {
            return s + (c.credito || 0);
        }, 0);

        document.getElementById('cp-m-total').textContent = total;
        document.getElementById('cp-m-total-sub').textContent = (total - anulados) + ' activos';
        document.getElementById('cp-m-debito').textContent = fmtMoneyCP(sumaDebito);
        document.getElementById('cp-m-debito-sub').textContent = delMes.length + ' comprobantes';
        document.getElementById('cp-m-credito').textContent = fmtMoneyCP(sumaCredito);
        document.getElementById('cp-m-credito-sub').textContent = delMes.length + ' comprobantes';
        document.getElementById('cp-m-anulados').textContent = anulados;
        document.getElementById('cp-m-anulados-sub').textContent =
            Math.round(anulados / Math.max(1, total) * 100) + '% del total';
        document.getElementById('cp-m-descuadre').textContent = descuadrados;
    }

    /* ════════════════════════════════════════════════
       SELECCIÓN MÚLTIPLE
    ════════════════════════════════════════════════ */
    function toggleSeleccionCP(id, chk) {
        if (chk.checked) CP.seleccionadas.add(id);
        else CP.seleccionadas.delete(id);
        actualizarBulkCP();
    }

    function toggleTodasCP(chk) {
        CP.seleccionadas.clear();
        if (chk.checked) {
            var desde = (CP.paginaActual - 1) * CP.porPagina;
            CP.filtradas.slice(desde, desde + CP.porPagina).forEach(function(c) {
                CP.seleccionadas.add(c.id);
            });
        }
        document.querySelectorAll('.chk-item-cp').forEach(function(c) {
            c.checked = chk.checked;
        });
        actualizarBulkCP();
    }

    function actualizarBulkCP() {
        var n = CP.seleccionadas.size;
        var bar = document.getElementById('cp-bulk-toolbar');
        bar.classList.toggle('visible', n > 0);
        document.getElementById('cp-bulk-text').textContent =
            n + ' comprobante' + (n !== 1 ? 's' : '') + ' seleccionado' + (n !== 1 ? 's' : '');
    }

    function exportarSeleccionCP() {
        notifCP('⬇️ Exportando ' + CP.seleccionadas.size + ' comprobantes…', 'info');
    }

    function anularSeleccionCP() {
        var n = CP.seleccionadas.size;
        Swal.fire({
            title: '¿Anular ' + n + ' comprobante' + (n !== 1 ? 's' : '') + '?',
            text: 'Esta acción revierte los movimientos contables asociados.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#DC2626',
            confirmButtonText: 'Sí, anular',
            cancelButtonText: 'Cancelar',
        }).then(function(r) {
            if (r.isConfirmed) {
                var token = document.querySelector('meta[name="csrf-token"]')?.content;
                var seleccionados = CP.datos.filter(function(c) {
                    return CP.seleccionadas.has(c.id);
                });
                Promise.all(seleccionados.map(function(c) {
                    if (!c.manual || c.anulado) return Promise.resolve();
                    return fetch('/comprobantes/' + c.id + '/anular', {
                        method: 'POST',
                        headers: {'X-CSRF-TOKEN': token, 'Accept': 'application/json'}
                    }).then(function(res) {
                        if (!res.ok) throw new Error('No fue posible anular todos los comprobantes seleccionados.');
                    });
                })).then(function() {
                    notifCP('Comprobantes anulados correctamente.', 'success');
                    CP.seleccionadas.clear();
                    actualizarBulkCP();
                    cargarComprobantes();
                }).catch(function(error) {
                    notifCP(error.message, 'error');
                });
            }
        });
    }

    /* ════════════════════════════════════════════════
       ACCIONES INDIVIDUALES
    ════════════════════════════════════════════════ */
    function verComprobante(id) {
        var c = CP.datos.find(function(x) {
            return x.id === id;
        });
        if (!c) {
            notifCP('Comprobante no encontrado', 'error');
            return;
        }
        CP.comprobanteActual = c;

        document.getElementById('cp-vf-title').textContent =
            'Comprobante ' + (c.numero || '');

        document.getElementById('cp-vf-sub').textContent =
            'Fecha: ' + fmtFechaCP(c.fecha);

        var body = document.getElementById('cp-vf-body');
        body.innerHTML = '<div class="spinner-cell"><div class="spinner"></div>Cargando movimientos…</div>';
        document.getElementById('modal-ver-comprobante').style.display = 'flex';

        var token = document.querySelector('meta[name="csrf-token"]')?.content;

        fetch('/comprobantes/' + id, {
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            })
            .then(function(r) {
                return r.json();
            })
            .then(function(detalle) {

                c.documento_origen = detalle.documento_origen;
                c.fecha = detalle.fecha;
                c.estado = detalle.estado;
                c.comprobantes = detalle.comprobantes || [];


                document.getElementById('cp-vf-title').textContent =
                    'Comprobante ' + (c.numero || '');

                document.getElementById('cp-vf-sub').textContent =
                    'Fecha: ' + fmtFechaCP(c.fecha);

                renderVerComprobante(c);

            })
            .catch(function() {
                renderVerComprobante(c);
            });
    }

    /* ════════════════════════════════════════════════
       VISTA DE DETALLE (mismo estilo/clases que facturas:
       cp-grid + fldCP + items-tbl + totales-box)
    ════════════════════════════════════════════════ */
    function renderVerComprobante(c) {
        var body = document.getElementById('cp-vf-body');

        var totalDebito = 0;
        var totalCredito = 0;

        var html = '<div class="cp-grid" style="grid-template-columns:repeat(4,1fr);">' +
            fldCP('Factura', c.documento_origen || '—') +
            fldCP('Fecha', fmtFechaCP(c.fecha)) +
            fldCP('Estado', c.estado || '—') +
            fldCP('Usuario', c.usuario || '—') +
            '</div>';

        if (!c.comprobantes || c.comprobantes.length === 0) {
            html += '<div class="spinner-cell" style="padding:40px 0;">' +
                '📭 No existen movimientos para este comprobante.</div>';
            body.innerHTML = html;
            return;
        }

        c.comprobantes.forEach(function(comp) {
            html += '<p class="cp-section-title" style="display:flex;justify-content:space-between;align-items:center;">' +
                '<span>' + esc(comp.grupo) + '</span>' +
                '<span style="color:#9CA3AF;font-weight:500;text-transform:none;letter-spacing:0;">' +
                esc(comp.numero) + '</span></p>' +
                '<div class="items-tbl-wrap"><table class="items-tbl">' +
                '<thead><tr><th>Cuenta</th><th>Detalle</th>' +
                '<th style="text-align:right;">Débito</th><th style="text-align:right;">Crédito</th></tr></thead>' +
                '<tbody>';

            comp.movimientos.forEach(function(m) {
                totalDebito += Number(m.debito) || 0;
                totalCredito += Number(m.credito) || 0;

                html += '<tr>' +
                    '<td><span class="td-mono">' + esc(m.cuenta_codigo) + '</span><br>' +
                    '<span style="font-size:11.5px;color:#6B7280;">' + esc(m.cuenta_nombre) + '</span></td>' +
                    '<td>' + esc(m.detalle || '—') + '</td>' +
                    '<td style="text-align:right;">' + (Number(m.debito) > 0 ? fmtMoneyCP(m.debito) : '—') + '</td>' +
                    '<td style="text-align:right;">' + (Number(m.credito) > 0 ? fmtMoneyCP(m.credito) : '—') + '</td>' +
                    '</tr>';
            });

            html += '</tbody></table></div>';
        });

        html += '<div class="totales-box" style="margin-top:16px;">' +
            '<div class="totales-row"><span>Total Débito</span><span>' + fmtMoneyCP(totalDebito) + '</span></div>' +
            '<div class="totales-row"><span>Total Crédito</span><span>' + fmtMoneyCP(totalCredito) + '</span></div>' +
            '<div class="totales-row total-final"><span>Diferencia</span><span>' +
            fmtMoneyCP(Math.abs(totalDebito - totalCredito)) + '</span></div>' +
            '</div>';

        body.innerHTML = html;
    }

    function fldCP(label, val, full) {
        return '<div class="cp-field"' + (full ? ' style="grid-column:1/-1;"' : '') + '>' +
            '<label>' + label + '</label>' +
            '<div style="padding:7px 10px;background:#F9FAFB;border:1px solid #E5E7EB;' +
            'border-radius:7px;font-size:13px;color:#111827;">' + esc(String(val)) + '</div>' +
            '</div>';
    }

    function cerrarVerComprobante() {
        document.getElementById('modal-ver-comprobante').style.display = 'none';
    }

    function cerrarVerComprobanteBackdrop(e) {
        if (e.target === document.getElementById('modal-ver-comprobante')) cerrarVerComprobante();
    }

    function imprimirComprobante() {
        notifCP('🖨️ Enviando a impresora…', 'info');
        /* En producción: window.open('/comprobantes/'+CP.comprobanteActual.id+'/imprimir') */
    }

    /* ════════════════════════════════════════════════
       MODAL CREAR / EDITAR
    ════════════════════════════════════════════════ */
    function abrirModalComprobante(id) {
        CP.modoModal = id ? 'editar' : 'nuevo';
        CP.editandoId = id;
        CP.items = [];

        var hoy = new Date();
        document.getElementById('cp-mf-fecha').value = hoy.toISOString().slice(0, 10);
        document.getElementById('cp-mf-tipo').value = '';
        document.getElementById('cp-mf-prefijo').value = '';
        document.getElementById('cp-mf-numero').value = '(automático)';
        document.getElementById('cp-mf-observaciones').value = '';
        document.getElementById('cp-mf-alerta-anulada').style.display = 'none';
        document.getElementById('cp-mf-alerta-descuadre').style.display = 'none';

        if (id) {
            var c = CP.datos.find(function(x) {
                return x.id === id;
            });
            if (c) {
                document.getElementById('cp-mf-title').textContent = 'Editar Comprobante ' + c.numero;
                document.getElementById('cp-mf-sub').textContent = 'Modifique los campos necesarios';
                document.getElementById('cp-mf-tipo').value = c.tipo || '';
                document.getElementById('cp-mf-prefijo').value = c.prefijo;
                document.getElementById('cp-mf-numero').value = c.numero;
                document.getElementById('cp-mf-fecha').value = c.fecha;
                document.getElementById('cp-mf-observaciones').value = c.observaciones || '';
                if (c.anulado) {
                    document.getElementById('cp-mf-alerta-anulada').style.display = 'flex';
                    document.getElementById('cp-mf-btn-save').disabled = true;
                } else {
                    document.getElementById('cp-mf-btn-save').disabled = false;
                }

                CP.items = (c.items || []).map(function(it) {
                    return Object.assign({}, it);
                });
            }
        } else {
            document.getElementById('cp-mf-title').textContent = 'Nuevo Comprobante';
            document.getElementById('cp-mf-sub').textContent = 'Complete los campos requeridos';
            document.getElementById('cp-mf-btn-save').disabled = false;
        }

        renderItemsCP();
        document.getElementById('modal-comprobante').style.display = 'flex';
    }

    function editarComprobante(id) {
        var token = document.querySelector('meta[name="csrf-token"]')?.content;
        fetch('/comprobantes/' + id + '/edit', {
                headers: {'X-CSRF-TOKEN': token, 'Accept': 'application/json'}
            })
            .then(function(r) {
                return r.json().then(function(data) {
                    if (!r.ok) throw new Error(data.message || 'No se pudo cargar el comprobante.');
                    return data;
                });
            })
            .then(function(data) {
                CP.editandoId = id;
                CP.modoModal = 'editar';
                CP.items = data.items || [];
                document.getElementById('cp-mf-title').textContent = 'Editar Comprobante ' + data.numero;
                document.getElementById('cp-mf-sub').textContent = 'Modifique los movimientos antes de guardarlo';
                document.getElementById('cp-mf-tipo').value = data.tipo || '';
                document.getElementById('cp-mf-prefijo').value = data.prefijo || '';
                document.getElementById('cp-mf-numero').value = data.numero || '';
                document.getElementById('cp-mf-fecha').value = data.fecha || '';
                document.getElementById('cp-mf-observaciones').value = data.observaciones || '';
                document.getElementById('cp-mf-alerta-anulada').style.display = 'none';
                renderItemsCP();
                document.getElementById('modal-comprobante').style.display = 'flex';
            })
            .catch(function(error) {
                notifCP(error.message, 'error');
            });
    }

    function cerrarModalComprobante() {
        document.getElementById('modal-comprobante').style.display = 'none';
    }

    function cerrarModalComprobanteBackdrop(e) {
        if (e.target === document.getElementById('modal-comprobante')) cerrarModalComprobante();
    }

    /* ── Movimientos (ítems) ── */
    function agregarItemFilaCP() {
        CP.items.push({
            cuenta_id: '',
            tercero_id: '',
            detalle: '',
            debito: 0,
            credito: 0
        });
        renderItemsCP();
    }

    function renderItemsCP() {
        var body = document.getElementById('cp-items-body');
        if (CP.items.length === 0) {
            body.innerHTML = '<tr id="cp-items-empty-row"><td colspan="6" ' +
                'style="text-align:center;color:#9CA3AF;padding:16px;font-size:12px;">' +
                'Sin movimientos. Haga clic en "＋ Añadir movimiento".</td></tr>';
            calcularTotalesCP();
            return;
        }
        body.innerHTML = CP.items.map(function(it, idx) {
            var optsCuenta = '<option value="">— Cuenta —</option>' +
                CP.cuentasCatalogo.map(function(cta) {
                    var nombre = (cta.codigo ? cta.codigo + ' - ' : '') + (cta.nombre || cta.name || '');
                    var sel = String(cta.id) === String(it.cuenta_id) ? 'selected' : '';
                    return '<option value="' + cta.id + '" ' + sel + '>' + esc(nombre) + '</option>';
                }).join('');

            var optsTercero = '<option value="">— Tercero —</option>' +
                CP.tercerosCatalogo.map(function(t) {
                    var nombre = t.nombre || t.razon_social || t.name || '';
                    var sel = String(t.id) === String(it.tercero_id) ? 'selected' : '';
                    return '<option value="' + t.id + '" ' + sel + '>' + esc(nombre) + '</option>';
                }).join('');

            return '<tr>' +
                '<td><select style="width:100%;border:1px solid #D1D5DB;border-radius:6px;' +
                'padding:5px 8px;font-size:12px;" onchange="itemChangeCP(' + idx + ',\'cuenta_id\',this.value)">' +
                optsCuenta + '</select></td>' +
                '<td><select style="width:100%;border:1px solid #D1D5DB;border-radius:6px;' +
                'padding:5px 8px;font-size:12px;" onchange="itemChangeCP(' + idx +
                ',\'tercero_id\',this.value)">' +
                optsTercero + '</select></td>' +
                '<td><input type="text" value="' + esc(it.detalle || '') + '" placeholder="Detalle" ' +
                'style="width:100%;border:1px solid #D1D5DB;border-radius:6px;padding:5px 8px;font-size:12px;"' +
                ' onchange="itemChangeCP(' + idx + ',\'detalle\',this.value)"></td>' +
                '<td><input type="number" value="' + (it.debito || 0) + '" min="0" ' +
                'style="width:110px;border:1px solid #D1D5DB;border-radius:6px;padding:5px 8px;font-size:12px;text-align:right;"' +
                ' onchange="itemChangeCP(' + idx + ',\'debito\',+this.value)"></td>' +
                '<td><input type="number" value="' + (it.credito || 0) + '" min="0" ' +
                'style="width:110px;border:1px solid #D1D5DB;border-radius:6px;padding:5px 8px;font-size:12px;text-align:right;"' +
                ' onchange="itemChangeCP(' + idx + ',\'credito\',+this.value)"></td>' +
                '<td><button onclick="eliminarItemCP(' + idx + ')" ' +
                'style="border:none;background:transparent;cursor:pointer;font-size:14px;color:#DC2626;">✕</button></td>' +
                '</tr>';
        }).join('');
        calcularTotalesCP();
    }

    function itemChangeCP(idx, campo, val) {
        CP.items[idx][campo] = val;
        /* Un movimiento no debería tener valor en débito y crédito a la vez */
        if (campo === 'debito' && val > 0) CP.items[idx].credito = 0;
        if (campo === 'credito' && val > 0) CP.items[idx].debito = 0;
        renderItemsCP();
    }

    function eliminarItemCP(idx) {
        CP.items.splice(idx, 1);
        renderItemsCP();
    }

    function calcularTotalesCP() {
        var totDebito = 0,
            totCredito = 0;
        CP.items.forEach(function(it) {
            totDebito += (it.debito || 0);
            totCredito += (it.credito || 0);
        });
        var diferencia = totDebito - totCredito;

        document.getElementById('cp-tot-debito').textContent = fmtMoneyCP(totDebito);
        document.getElementById('cp-tot-credito').textContent = fmtMoneyCP(totCredito);
        document.getElementById('cp-tot-diferencia').textContent = fmtMoneyCP(Math.abs(diferencia));

        var row = document.getElementById('cp-tot-diferencia-row');
        var cuadrado = Math.abs(diferencia) < 0.01 && CP.items.length > 0;
        row.classList.toggle('desc-ok', cuadrado);
        row.classList.toggle('desc-bad', !cuadrado);
        return cuadrado;
    }

    /* ── Guardar ── */
    function guardarComprobante() {
        var tipo = document.getElementById('cp-mf-tipo').value;
        var prefijo = document.getElementById('cp-mf-prefijo').value.trim();
        var fecha = document.getElementById('cp-mf-fecha').value;

        if (!tipo) {
            notifCP('⚠️ Seleccione el tipo de comprobante', 'error');
            return;
        }
        if (!prefijo) {
            notifCP('⚠️ El prefijo es requerido', 'error');
            return;
        }
        if (!fecha) {
            notifCP('⚠️ Seleccione la fecha', 'error');
            return;
        }
        if (CP.items.length === 0) {
            notifCP('⚠️ Agregue al menos un movimiento', 'error');
            return;
        }
        var cuadrado = calcularTotalesCP();
        if (!cuadrado) {
            document.getElementById('cp-mf-alerta-descuadre').style.display = 'flex';
            notifCP('⚠️ El comprobante no está cuadrado (débito ≠ crédito)', 'error');
            return;
        }
        document.getElementById('cp-mf-alerta-descuadre').style.display = 'none';

        var token = document.querySelector('meta[name="csrf-token"]')?.content;
        var body = {
            tipo: tipo,
            prefijo: prefijo,
            fecha: fecha,
            observaciones: document.getElementById('cp-mf-observaciones').value,
            items: CP.items,
        };

        var url = CP.editandoId ? '/comprobantes/' + CP.editandoId : '/comprobantes';
        var method = CP.editandoId ? 'PUT' : 'POST';

        document.getElementById('cp-mf-btn-save').disabled = true;
        document.getElementById('cp-mf-btn-save').textContent = '⏳ Guardando…';

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
                return r.json().then(function(res) {
                    if (!r.ok) throw new Error(res.message || res.error || 'No fue posible guardar el comprobante.');
                    return res;
                });
            })
            .then(function(res) {
                if (res.error) throw new Error(res.error);
                notifCP(CP.editandoId ? '✅ Comprobante actualizado' : '✅ Comprobante creado correctamente',
                    'success');
                cerrarModalComprobante();
                cargarComprobantes();
            })
            .catch(function(e) {
                notifCP(e.message || 'No fue posible guardar el comprobante.', 'error');
            })
            .finally(function() {
                document.getElementById('cp-mf-btn-save').disabled = false;
                document.getElementById('cp-mf-btn-save').textContent = '💾 Guardar Comprobante';
            });
    }

    /* ── Revertir / Anular ── */
    function anularComprobante(id, codigo) {
        Swal.fire({
            title: '¿Anular el comprobante ' + codigo + '?',
            text: 'Se marcará como anulado. Esta acción revierte el movimiento contable.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#D97706',
            confirmButtonText: 'Sí, anular',
            cancelButtonText: 'Cancelar',
        }).then(function(r) {
            if (!r.isConfirmed) return;
            var token = document.querySelector('meta[name="csrf-token"]')?.content;
            fetch('/comprobantes/' + id + '/anular', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    }
                })
                .then(function(res) {
                    return res.json().then(function(data) {
                        if (!res.ok) throw new Error(data.message || 'No se pudo anular el comprobante.');
                        return data;
                    });
                })
                .then(function(data) {
                    notifCP('🔄 Comprobante ' + codigo + ' anulado correctamente', 'success');
                    cargarComprobantes();
                })
                .catch(function(error) {
                    notifCP(error.message || 'No se pudo anular el comprobante.', 'error');
                });
        });
    }

    function revertirAnulacionCP(id, codigo) {
        Swal.fire({
            title: '¿Revertir la anulación de ' + codigo + '?',
            text: 'El comprobante volverá a estar activo y se reaplicarán sus movimientos.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#059669',
            confirmButtonText: 'Sí, revertir',
            cancelButtonText: 'Cancelar',
        }).then(function(r) {
            if (!r.isConfirmed) return;
            var token = document.querySelector('meta[name="csrf-token"]')?.content;
            fetch('/comprobantes/' + id + '/revertir', {
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
                        notifCP(data.message, 'error');
                        return;
                    }
                    notifCP('↩️ Comprobante ' + codigo + ' restaurado', 'success');
                    cargarComprobantes();
                })
                .catch(function() {
                    notifCP('Error al revertir la anulación', 'error');
                });
        });
    }

    /* ── Eliminar ── */
    function eliminarComprobante(id, codigo) {
        Swal.fire({
            title: '¿Eliminar el comprobante ' + codigo + '?',
            text: 'Se eliminará permanentemente del sistema. Esta acción NO se puede deshacer.',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#DC2626',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
        }).then(function(r) {
            if (!r.isConfirmed) return;
            var token = document.querySelector('meta[name="csrf-token"]')?.content;
            fetch('/comprobantes/' + id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    }
                })
                .then(function(res) {
                    return res.json().then(function(data) {
                        if (!res.ok) throw new Error(data.message || 'No se pudo eliminar el comprobante.');
                        return data;
                    });
                })
                .then(function() {
                    notifCP('🗑️ Comprobante ' + codigo + ' eliminado', 'success');
                    cargarComprobantes();
                })
                .catch(function(error) {
                    notifCP(error.message || 'No se pudo eliminar el comprobante.', 'error');
                });
        });
    }

    /* ── Exportar ── */
    function exportarComprobantes() {
        notifCP('⬇️ Preparando exportación de ' + CP.filtradas.length + ' registros…', 'info');
        /* En producción: window.open('/comprobantes/exportar?...') */
    }

    /* ════════════════════════════════════════════════
       UTILIDADES
    ════════════════════════════════════════════════ */
    function fmtMoneyCP(n) {
        return '$ ' + (n || 0).toLocaleString('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });
    }

    function fmtFechaCP(s) {
        if (!s) return '—';
        var parts = s.split('-');
        return parts[2] + '/' + parts[1] + '/' + parts[0];
    }

    function esc(s) {
        return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g,
            '&quot;');
    }

    function notifCP(msg, tipo) {
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
            var cont = document.getElementById('notificaciones');
            if (cont) {
                cont.appendChild(el);
                setTimeout(function() {
                    el.remove();
                }, 3500);
            }
        }
    }
</script>