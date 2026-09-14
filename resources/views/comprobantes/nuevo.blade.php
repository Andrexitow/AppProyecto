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
        font-size: 16px;
    }

    .metric-sub {
        font-size: 11px;
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

    table.cp-tbl tbody tr.anulado .badge,
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

    .td-money {
        font-weight: 600;
        text-align: right;
        white-space: nowrap;
    }

    .td-money.debito {
        color: #1D4ED8;
    }

    .td-money.credito {
        color: #B45309;
    }

    .td-trunc {
        max-width: 190px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
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

    .badge-green { background: #ECFDF5; color: #065F46; }
    .badge-red { background: #FEF2F2; color: #991B1B; }
    .badge-yellow { background: #FFFBEB; color: #92400E; }
    .badge-blue { background: #EFF6FF; color: #1e3a8a; }
    .badge-gray { background: #F3F4F6; color: #374151; }
    .badge-purple { background: #EDE9FE; color: #4C1D95; }

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

    .act-btn:hover { background: #F3F4F6; color: #111827; transform: scale(1.05); }
    .act-btn.view:hover { background: #EFF6FF; color: #1D4ED8; }
    .act-btn.edit:hover { background: #ECFDF5; color: #059669; }
    .act-btn.rev:hover { background: #FFFBEB; color: #D97706; }
    .act-btn.del:hover { background: #FEF2F2; color: #DC2626; }

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

    .act-btn:hover::after { opacity: 1; }

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

    .pag-info { font-size: 12px; color: #6B7280; }
    .pag-btns { display: flex; gap: 4px; }

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

    .pag-btn:hover { background: #F3F4F6; border-color: #9CA3AF; }
    .pag-btn.active { background: #1D4ED8; color: #fff; border-color: #1D4ED8; }
    .pag-btn:disabled { opacity: 0.4; cursor: not-allowed; }

    /* ── Modal ── */
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
        max-width: 820px;
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

    .modal-head-title { font-size: 15px; font-weight: 600; color: #111827; }
    .modal-head-sub { font-size: 12px; color: #6B7280; margin-top: 2px; }

    .modal-body { flex: 1; overflow-y: auto; padding: 20px; }

    .modal-foot {
        padding: 14px 20px;
        border-top: 1px solid #EAECF0;
        display: flex;
        gap: 8px;
        justify-content: flex-end;
        flex-shrink: 0;
        flex-wrap: wrap;
    }

    .cp-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 10px;
        margin-bottom: 6px;
    }

    @media (max-width: 620px) {
        .cp-grid { grid-template-columns: 1fr 1fr; }
    }

    .cp-field { display: flex; flex-direction: column; gap: 3px; }

    .cp-field label {
        font-size: 11px;
        font-weight: 500;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .cp-field small {
        font-size: 10.5px;
        color: #9CA3AF;
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

    .cp-field input[readonly],
    .cp-field input:disabled {
        background: #F3F4F6;
        color: #6B7280;
    }

    .cp-field.required-empty select {
        border-color: #DC2626;
        background: #FEF2F2;
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
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .items-tbl-wrap { overflow-x: auto; }

    table.items-tbl {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
        min-width: 720px;
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
        white-space: nowrap;
    }

    table.items-tbl tbody td {
        padding: 6px 8px;
        border-top: 1px solid #F3F4F6;
        vertical-align: middle;
    }

    table.items-tbl tbody select,
    table.items-tbl tbody input {
        width: 100%;
        border: 1px solid #D1D5DB;
        border-radius: 6px;
        padding: 5px 7px;
        font-size: 12px;
        background: #fff;
        outline: none;
        box-sizing: border-box;
    }

    table.items-tbl tbody select.req-empty,
    table.items-tbl tbody input.req-empty {
        border-color: #DC2626;
        background: #FEF2F2;
    }

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
        font-size: 15px;
        font-weight: 700;
        color: #111827;
    }

    .totales-row.desc-ok { color: #059669; }
    .totales-row.desc-bad { color: #DC2626; }

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

    .alerta-info {
        background: #EFF6FF;
        border: 1px solid #BFDBFE;
        border-radius: 8px;
        padding: 10px 14px;
        font-size: 12px;
        color: #1e3a8a;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 8px;
        margin-bottom: 14px;
        flex-wrap: wrap;
    }

    .chk-row { width: 15px; height: 15px; cursor: pointer; accent-color: #1D4ED8; }

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

    .bulk-toolbar.visible { display: flex; }
    .bulk-text { font-size: 12px; color: #fff; font-weight: 500; }
    .bulk-actions { display: flex; gap: 6px; }

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

    .btn-bulk:hover { background: rgba(255, 255, 255, 0.25); }
    .btn-bulk.danger { background: rgba(220, 38, 38, 0.6); border-color: rgba(220, 38, 38, 0.7); }
    .btn-bulk.danger:hover { background: rgba(220, 38, 38, 0.8); }

    .spinner-cell {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
        color: #6B7280;
        font-size: 13px;
        gap: 10px;
    }

    @keyframes spin { to { transform: rotate(360deg); } }

    .spinner {
        width: 18px;
        height: 18px;
        border: 2px solid #E5E7EB;
        border-top-color: #1D4ED8;
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
    }

    .nx-toast {
        position: fixed;
        right: 20px;
        top: 20px;
        z-index: 10001;
        background: #fff;
        border: 1px solid #EAECF0;
        border-left: 4px solid #1D4ED8;
        border-radius: 9px;
        padding: 10px 16px;
        box-shadow: 0 8px 24px rgba(16,24,40,0.15);
        font-size: 13px;
        color: #344054;
        max-width: 340px;
    }

    @media (max-width: 640px) {
        .metrics-row { grid-template-columns: 1fr 1fr; }
        table.cp-tbl thead th:nth-child(n+7) { display: none; }
        table.cp-tbl tbody td:nth-child(n+7) { display: none; }
    }
</style>

<div id="view-comprobantes">

    {{-- ── ENCABEZADO ── --}}
    <div class="sec-header">
        <div>
            <p class="sec-title">🧾 Comprobantes Contables</p>
            <p class="sec-subtitle">Registro, consulta y gestión de asientos contables</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button class="btn-outline" onclick="exportarComprobantesCP()">⬇️ Exportar</button>
            <button class="btn-primary" onclick="abrirEditorCP(null)">＋ Nuevo Comprobante</button>
        </div>
    </div>

    {{-- ── MÉTRICAS ── --}}
    <div class="metrics-row">
        <div class="metric-card" style="--accent:#1D4ED8">
            <p class="metric-label">Total Comprobantes</p>
            <p class="metric-value" id="cp-m-total">—</p>
            <p class="metric-sub" id="cp-m-total-sub">Cargando…</p>
        </div>
        <div class="metric-card" style="--accent:#1D4ED8">
            <p class="metric-label">Débito del período</p>
            <p class="metric-value money" id="cp-m-debito">—</p>
            <p class="metric-sub" id="cp-m-debito-sub"></p>
        </div>
        <div class="metric-card" style="--accent:#D97706">
            <p class="metric-label">Crédito del período</p>
            <p class="metric-value money" id="cp-m-credito">—</p>
            <p class="metric-sub" id="cp-m-credito-sub"></p>
        </div>
        <div class="metric-card" style="--accent:#DC2626">
            <p class="metric-label">Anulados</p>
            <p class="metric-value" id="cp-m-anulados">—</p>
            <p class="metric-sub" id="cp-m-anulados-sub"></p>
        </div>
        <div class="metric-card" style="--accent:#7C3AED">
            <p class="metric-label">Borradores</p>
            <p class="metric-value" id="cp-m-borradores">—</p>
            <p class="metric-sub">Pendientes de registrar</p>
        </div>
    </div>

    {{-- ── FILTROS ── --}}
    <div class="filter-bar">
        <div class="fi-group" style="flex:2;min-width:200px;">
            <span class="fi-label">🔍</span>
            <input class="fi-input" type="text" id="cp-fi-buscar"
                placeholder="Tipo, prefijo, número, descripción, tercero, usuario…" oninput="aplicarFiltrosCP()">
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
            <select class="fi-select" id="cp-fi-estado" onchange="aplicarFiltrosCP()">
                <option value="">Todos los estados</option>
                <option value="BORRADOR">Borrador</option>
                <option value="REGISTRADO">Registrado</option>
                <option value="CONTABILIZADO">Contabilizado</option>
                <option value="ANULADO">Anulado</option>
            </select>
        </div>
        <button class="btn-outline" onclick="limpiarFiltrosCP()">✕ Limpiar</button>
    </div>

    {{-- ── BULK TOOLBAR ── --}}
    <div class="bulk-toolbar" id="cp-bulk-toolbar">
        <span class="bulk-text" id="cp-bulk-text">0 seleccionados</span>
        <div class="bulk-actions">
            <button class="btn-bulk" onclick="exportarSeleccionCP()">⬇️ Exportar selección</button>
            <button class="btn-bulk" onclick="eliminarSeleccionCP()">🗑️ Eliminar borradores</button>
            <button class="btn-bulk danger" onclick="anularSeleccionCP()">🚫 Anular registrados</button>
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
                        <th onclick="sortTablaCP('tipo')" data-col="tipo">Tipo <span class="sort-icon">↕</span></th>
                        <th onclick="sortTablaCP('numero')" data-col="numero">Documento <span class="sort-icon">↕</span></th>
                        <th onclick="sortTablaCP('fecha')" data-col="fecha">Fecha <span class="sort-icon">↕</span></th>
                        <th>Tercero</th>
                        <th>Descripción</th>
                        <th onclick="sortTablaCP('debito')" data-col="debito" style="text-align:right;">Débito <span class="sort-icon">↕</span></th>
                        <th onclick="sortTablaCP('credito')" data-col="credito" style="text-align:right;">Crédito <span class="sort-icon">↕</span></th>
                        <th>Estado</th>
                        <th>Usuario</th>
                        <th style="text-align:center;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-comprobantes">
                    <tr>
                        <td colspan="11">
                            <div class="spinner-cell"><div class="spinner"></div>Cargando comprobantes…</div>
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
     MODAL COMPROBANTE (crear / editar borrador)
═══════════════════════════════════════════════ --}}
<div id="modal-editor-cp" style="display:none;" class="modal-backdrop" onclick="cerrarEditorBackdropCP(event)">
    <div class="modal-comprobante" onclick="event.stopPropagation()">

        <div class="modal-head">
            <div>
                <p class="modal-head-title" id="cp-ed-title">Nuevo Comprobante</p>
                <p class="modal-head-sub" id="cp-ed-sub">Complete el encabezado y agregue los movimientos</p>
            </div>
            <button onclick="cerrarEditorCP()"
                style="border:none;background:transparent;font-size:20px;cursor:pointer;color:#6B7280;padding:4px;border-radius:6px;line-height:1;">✕</button>
        </div>

        <div class="modal-body" id="cp-ed-body">

            <p class="cp-section-title">Encabezado</p>
            <div class="cp-grid">
                <div class="cp-field">
                    <label>Tipo *</label>
                    <input type="text" id="cp-ed-tipo" list="cp-tipos-dl" placeholder="Ej: Ingreso, Egreso, Diario…" maxlength="100">
                    <datalist id="cp-tipos-dl">
                        <option value="Ingreso"><option value="Egreso"><option value="Diario">
                        <option value="Nota Débito"><option value="Nota Crédito"><option value="Ajuste">
                    </datalist>
                </div>
                <div class="cp-field">
                    <label>Prefijo *</label>
                    <input type="text" id="cp-ed-prefijo" placeholder="Ej: CC" maxlength="10"
                        oninput="this.value=this.value.toUpperCase()">
                    <small>Si el tipo ya existe se reutiliza su prefijo.</small>
                </div>
                <div class="cp-field">
                    <label>Número</label>
                    <input type="text" id="cp-ed-numero" placeholder="Se asigna al guardar" readonly>
                </div>
                <div class="cp-field">
                    <label>Fecha *</label>
                    <input type="date" id="cp-ed-fecha">
                </div>
                <div class="cp-field" style="grid-column:1/-1;">
                    <label>Descripción</label>
                    <textarea id="cp-ed-descripcion" rows="2" placeholder="Descripción del comprobante…" style="resize:vertical;"></textarea>
                </div>
            </div>

            <p class="cp-section-title">
                Movimientos contables
                <button class="btn-primary" onclick="agregarLineaCP()" style="font-size:11px;padding:4px 10px;">+ Añadir movimiento</button>
            </p>

            <div class="items-tbl-wrap">
                <table class="items-tbl">
                    <thead>
                        <tr>
                            <th style="width:22%;">Cuenta contable</th>
                            <th style="width:18%;">Detalle</th>
                            <th style="width:16%;">Tercero</th>
                            <th style="width:16%;">Centro de costo</th>
                            <th style="width:12%;text-align:right;">Débito</th>
                            <th style="width:12%;text-align:right;">Crédito</th>
                            <th style="width:32px;"></th>
                        </tr>
                    </thead>
                    <tbody id="cp-ed-lineas">
                        <tr id="cp-ed-lineas-empty">
                            <td colspan="7" style="text-align:center;color:#9CA3AF;padding:16px;font-size:12px;">
                                Sin movimientos. Haga clic en "＋ Añadir movimiento".
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="totales-box">
                <div class="totales-row"><span>Total débito</span><span id="cp-ed-tot-debito">$ 0</span></div>
                <div class="totales-row"><span>Total crédito</span><span id="cp-ed-tot-credito">$ 0</span></div>
                <div class="totales-row total-final" id="cp-ed-diferencia-row">
                    <span>Diferencia</span><span id="cp-ed-diferencia">$ 0</span>
                </div>
            </div>

        </div>

        <div class="modal-foot">
            <button class="btn-outline" onclick="cerrarEditorCP()">Cancelar</button>
            <button class="btn-outline" id="cp-btn-guardar" onclick="guardarBorradorCP()">💾 Guardar borrador</button>
            <button class="btn-primary" id="cp-btn-registrar" onclick="registrarComprobanteCP()">✅ Registrar comprobante</button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     MODAL VER COMPROBANTE (solo lectura)
═══════════════════════════════════════════════ --}}
<div id="modal-ver-cp" style="display:none;" class="modal-backdrop" onclick="cerrarVerBackdropCP(event)">
    <div class="modal-comprobante" onclick="event.stopPropagation()">
        <div class="modal-head">
            <div>
                <p class="modal-head-title" id="cp-vf-title">Comprobante</p>
                <p class="modal-head-sub" id="cp-vf-sub"></p>
            </div>
            <button onclick="cerrarVerCP()"
                style="border:none;background:transparent;font-size:20px;cursor:pointer;color:#6B7280;padding:4px;border-radius:6px;line-height:1;">✕</button>
        </div>
        <div class="modal-body" id="cp-vf-body">
            {{-- Se llena dinámicamente --}}
        </div>
        <div class="modal-foot">
            <button class="btn-outline" onclick="cerrarVerCP()">Cerrar</button>
        </div>
    </div>
</div>

<script>
    /* ════════════════════════════════════════════════
       ESTADO GLOBAL
    ════════════════════════════════════════════════ */
    var CP = {
        datos: [],
        filtradas: [],
        paginaActual: 1,
        porPagina: 15,
        sortCol: 'numero',
        sortAsc: false,
        seleccionadas: new Set(),
        cuentas: [],
        terceros: [],
        centros: [],
        editandoId: null,
        lineas: [],
        actual: null,
    };

    function fmtISOLocalCP(d) {
        return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }

    (function initCP() {
        var hoy = new Date();
        var primerDia = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        document.getElementById('cp-fi-desde').value = fmtISOLocalCP(primerDia);
        document.getElementById('cp-fi-hasta').value = fmtISOLocalCP(hoy);

        cargarComprobantesCP();
        cargarCatalogosCP();
    })();

    function hdrsCP() {
        return { 'X-CSRF-TOKEN': window.csrfToken || document.querySelector('meta[name="csrf-token"]')?.content, 'Accept': 'application/json' };
    }

    /* ════════════════════════════════════════════════
       CARGA DE DATOS
    ════════════════════════════════════════════════ */
    function cargarComprobantesCP() {
        mostrarSpinnerCP();
        fetch('/comprobantes', { headers: hdrsCP() })
            .then(function(r) { return r.json().then(function(d) { if (!r.ok) throw new Error(d.message || 'No se pudo cargar el listado.'); return d; }); })
            .then(function(res) {
                CP.datos = Array.isArray(res.data) ? res.data : [];
                aplicarFiltrosCP();
            })
            .catch(function(e) {
                CP.datos = [];
                aplicarFiltrosCP();
                notifCP(e.message || 'No fue posible cargar los comprobantes.', 'error');
            });
    }

    function cargarCatalogosCP() {
        fetch('/cuentas-contables/data', { headers: hdrsCP() }).then(function(r) { return r.json(); }).then(function(res) {
            var data = Array.isArray(res.data) ? res.data : [];
            CP.cuentas = data.filter(function(c) { return c.activa && c.movimientos; });
        }).catch(function() {});

        fetch('/terceros', { headers: hdrsCP() }).then(function(r) { return r.json(); }).then(function(res) {
            CP.terceros = Array.isArray(res.data) ? res.data : (Array.isArray(res) ? res : []);
        }).catch(function() {});

        fetch('/centros-costo/catalogo', { headers: hdrsCP() }).then(function(r) { return r.json(); }).then(function(res) {
            CP.centros = Array.isArray(res.data) ? res.data : [];
        }).catch(function() {});
    }

    /* ════════════════════════════════════════════════
       FILTROS / ORDEN / PAGINACIÓN
    ════════════════════════════════════════════════ */
    function aplicarFiltrosCP() {
        var buscar = (document.getElementById('cp-fi-buscar').value || '').toLowerCase().trim();
        var desde = document.getElementById('cp-fi-desde').value;
        var hasta = document.getElementById('cp-fi-hasta').value;
        var estado = document.getElementById('cp-fi-estado').value;

        CP.filtradas = CP.datos.filter(function(c) {
            if (buscar) {
                var hay = (String(c.tipo || '') + (c.numero || '') + (c.descripcion || '') +
                    (c.tercero || '') + (c.usuario || '')).toLowerCase().includes(buscar);
                if (!hay) return false;
            }
            if (desde && c.fecha < desde) return false;
            if (hasta && c.fecha > hasta) return false;
            if (estado && c.estado !== estado) return false;
            return true;
        });

        CP.paginaActual = 1;
        sortTablaActualCP();
        renderTablaCP();
        actualizarMetricasCP();
    }

    function limpiarFiltrosCP() {
        document.getElementById('cp-fi-buscar').value = '';
        document.getElementById('cp-fi-estado').value = '';
        var hoy = new Date(), primo = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        document.getElementById('cp-fi-desde').value = primo.toISOString().slice(0, 10);
        document.getElementById('cp-fi-hasta').value = hoy.toISOString().slice(0, 10);
        aplicarFiltrosCP();
    }

    function sortTablaCP(col) {
        if (CP.sortCol === col) CP.sortAsc = !CP.sortAsc; else { CP.sortCol = col; CP.sortAsc = false; }
        sortTablaActualCP();
        renderTablaCP();
        document.querySelectorAll('table.cp-tbl thead th[data-col]').forEach(function(th) {
            th.classList.toggle('sorted', th.dataset.col === col);
            var icon = th.querySelector('.sort-icon');
            if (icon) icon.textContent = th.dataset.col === col ? (CP.sortAsc ? '↑' : '↓') : '↕';
        });
    }

    function sortTablaActualCP() {
        var col = CP.sortCol, asc = CP.sortAsc;
        CP.filtradas.sort(function(a, b) {
            var va = a[col] ?? '', vb = b[col] ?? '';
            if (typeof va === 'number') return asc ? va - vb : vb - va;
            return asc ? String(va).localeCompare(String(vb)) : String(vb).localeCompare(String(va));
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

        document.getElementById('cp-pag-info').textContent = 'Mostrando ' + Math.min(pagina.length, total) + ' de ' + total + ' registros';

        if (pagina.length === 0) {
            tbody.innerHTML = '<tr><td colspan="11"><div class="spinner-cell">📭 No se encontraron comprobantes</div></td></tr>';
            renderPaginacionCP(total);
            return;
        }

        tbody.innerHTML = pagina.map(function(c) {
            var anulado = c.estado === 'ANULADO';
            var borrador = c.estado === 'BORRADOR';
            var activo = c.estado === 'REGISTRADO' || c.estado === 'CONTABILIZADO';
            var trCls = anulado ? 'anulado' : '';
            var chked = CP.seleccionadas.has(c.id) ? 'checked' : '';

            var btnVer = '<button class="act-btn view" data-tip="Ver detalle" onclick="verComprobanteCP(' + c.id + ')">👁️</button>';
            var btnEdit = borrador ? '<button class="act-btn edit" data-tip="Editar / Registrar" onclick="editarComprobanteCP(' + c.id + ')">✏️</button>' : '';
            var btnAnular = activo ? '<button class="act-btn rev" data-tip="Anular" onclick="anularComprobanteCP(' + c.id + ',\'' + esc(c.numero) + '\')">🚫</button>' : '';
            var btnRevertir = anulado ? '<button class="act-btn edit" data-tip="Revertir anulación" onclick="revertirComprobanteCP(' + c.id + ',\'' + esc(c.numero) + '\')">↩️</button>' : '';
            var btnDel = borrador ? '<button class="act-btn del" data-tip="Eliminar borrador" onclick="eliminarComprobanteCP(' + c.id + ',\'' + esc(c.numero) + '\')">🗑️</button>' : '';

            return '<tr class="' + trCls + '" data-id="' + c.id + '">' +
                '<td><input type="checkbox" class="chk-row chk-item-cp" ' + chked + ' onchange="toggleSeleccionCP(' + c.id + ',this)"></td>' +
                '<td>' + badgeTipoCP(c.tipo) + '</td>' +
                '<td><span class="td-mono">' + esc(c.numero) + '</span></td>' +
                '<td>' + fmtFechaCP(c.fecha) + '</td>' +
                '<td class="td-trunc" title="' + esc(c.tercero || '') + '">' + esc(c.tercero || '—') + '</td>' +
                '<td class="td-trunc" title="' + esc(c.descripcion || '') + '">' + esc(c.descripcion || '—') + '</td>' +
                '<td class="td-money debito">' + ((c.debito || 0) > 0 ? fmtMoneyCP(c.debito) : '—') + '</td>' +
                '<td class="td-money credito">' + ((c.credito || 0) > 0 ? fmtMoneyCP(c.credito) : '—') + '</td>' +
                '<td>' + badgeEstadoCP(c.estado) + '</td>' +
                '<td style="color:#6B7280;font-size:12px;">' + esc(c.usuario || '—') + '</td>' +
                '<td><div class="tbl-actions">' + btnVer + btnEdit + btnAnular + btnRevertir + btnDel + '</div></td>' +
                '</tr>';
        }).join('');

        renderPaginacionCP(total);
    }

    function badgeEstadoCP(estado) {
        var map = {
            BORRADOR: '<span class="badge badge-yellow"><span class="dot"></span>Borrador</span>',
            REGISTRADO: '<span class="badge badge-green"><span class="dot"></span>Registrado</span>',
            CONTABILIZADO: '<span class="badge badge-green"><span class="dot"></span>Contabilizado</span>',
            ANULADO: '<span class="badge badge-red"><span class="dot"></span>Anulado</span>',
        };
        return map[estado] || '<span class="badge badge-gray">' + esc(estado || '—') + '</span>';
    }

    function badgeTipoCP(tipo) {
        var conocidos = { 'Ingreso': 'badge-green', 'Egreso': 'badge-red', 'Diario': 'badge-blue', 'Nota Débito': 'badge-purple', 'Nota Crédito': 'badge-yellow', 'Ajuste': 'badge-gray' };
        var cls = conocidos[tipo] || hashColorCP(tipo || '');
        return '<span class="badge ' + cls + '">' + esc(tipo || '—') + '</span>';
    }

    function hashColorCP(str) {
        var paleta = ['badge-blue', 'badge-green', 'badge-purple', 'badge-yellow', 'badge-gray'];
        var h = 0;
        for (var i = 0; i < str.length; i++) h = (h * 31 + str.charCodeAt(i)) >>> 0;
        return paleta[h % paleta.length];
    }

    function renderPaginacionCP(total) {
        var totalPags = Math.max(1, Math.ceil(total / CP.porPagina));
        var actual = CP.paginaActual;
        var btns = document.getElementById('cp-pag-btns');
        var html = '';

        html += '<button class="pag-btn" onclick="irPaginaCP(' + (actual - 1) + ')"' + (actual === 1 ? ' disabled' : '') + '>‹</button>';

        var desde = Math.max(1, actual - 2);
        var hasta = Math.min(totalPags, desde + 4);
        desde = Math.max(1, hasta - 4);

        if (desde > 1) html += '<button class="pag-btn" onclick="irPaginaCP(1)">1</button>' + (desde > 2 ? '<span style="padding:0 4px;color:#9CA3AF;">…</span>' : '');
        for (var p = desde; p <= hasta; p++) html += '<button class="pag-btn' + (p === actual ? ' active' : '') + '" onclick="irPaginaCP(' + p + ')">' + p + '</button>';
        if (hasta < totalPags) html += (hasta < totalPags - 1 ? '<span style="padding:0 4px;color:#9CA3AF;">…</span>' : '') + '<button class="pag-btn" onclick="irPaginaCP(' + totalPags + ')">' + totalPags + '</button>';

        html += '<button class="pag-btn" onclick="irPaginaCP(' + (actual + 1) + ')"' + (actual === totalPags ? ' disabled' : '') + '>›</button>';
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
            '<tr><td colspan="11"><div class="spinner-cell"><div class="spinner"></div>Cargando comprobantes…</div></td></tr>';
    }

    /* ════════════════════════════════════════════════
       MÉTRICAS (sobre el período / filtros aplicados)
    ════════════════════════════════════════════════ */
    function actualizarMetricasCP() {
        var lista = CP.filtradas;
        var total = lista.length;
        var anulados = lista.filter(function(c) { return c.estado === 'ANULADO'; }).length;
        var borradores = lista.filter(function(c) { return c.estado === 'BORRADOR'; }).length;
        var activos = lista.filter(function(c) { return c.estado === 'REGISTRADO' || c.estado === 'CONTABILIZADO'; });

        var sumaDebito = activos.reduce(function(s, c) { return s + (c.debito || 0); }, 0);
        var sumaCredito = activos.reduce(function(s, c) { return s + (c.credito || 0); }, 0);

        document.getElementById('cp-m-total').textContent = total;
        document.getElementById('cp-m-total-sub').textContent = (total - anulados) + ' activos';
        document.getElementById('cp-m-debito').textContent = fmtMoneyCP(sumaDebito);
        document.getElementById('cp-m-debito-sub').textContent = activos.length + ' comprobantes';
        document.getElementById('cp-m-credito').textContent = fmtMoneyCP(sumaCredito);
        document.getElementById('cp-m-credito-sub').textContent = activos.length + ' comprobantes';
        document.getElementById('cp-m-anulados').textContent = anulados;
        document.getElementById('cp-m-anulados-sub').textContent = Math.round(anulados / Math.max(1, total) * 100) + '% del total';
        document.getElementById('cp-m-borradores').textContent = borradores;
    }

    /* ════════════════════════════════════════════════
       SELECCIÓN MÚLTIPLE
    ════════════════════════════════════════════════ */
    function toggleSeleccionCP(id, chk) {
        if (chk.checked) CP.seleccionadas.add(id); else CP.seleccionadas.delete(id);
        actualizarBulkCP();
    }

    function toggleTodasCP(chk) {
        CP.seleccionadas.clear();
        if (chk.checked) {
            var desde = (CP.paginaActual - 1) * CP.porPagina;
            CP.filtradas.slice(desde, desde + CP.porPagina).forEach(function(c) { CP.seleccionadas.add(c.id); });
        }
        document.querySelectorAll('.chk-item-cp').forEach(function(c) { c.checked = chk.checked; });
        actualizarBulkCP();
    }

    function actualizarBulkCP() {
        var n = CP.seleccionadas.size;
        document.getElementById('cp-bulk-toolbar').classList.toggle('visible', n > 0);
        document.getElementById('cp-bulk-text').textContent = n + ' comprobante' + (n !== 1 ? 's' : '') + ' seleccionado' + (n !== 1 ? 's' : '');
    }

    function seleccionActualCP() {
        return CP.datos.filter(function(c) { return CP.seleccionadas.has(c.id); });
    }

    function exportarSeleccionCP() {
        exportarCsvCP(seleccionActualCP(), 'comprobantes-seleccionados.csv');
    }

    function eliminarSeleccionCP() {
        var borradores = seleccionActualCP().filter(function(c) { return c.estado === 'BORRADOR'; });
        if (!borradores.length) { notifCP('La selección no tiene borradores para eliminar.', 'warning'); return; }
        confirmarAccionCP('¿Eliminar ' + borradores.length + ' borrador' + (borradores.length !== 1 ? 'es' : '') + '? Esta acción no se puede deshacer.', function() {
            Promise.all(borradores.map(function(c) {
                return fetch('/comprobantes/' + c.id, { method: 'DELETE', headers: hdrsCP() });
            })).then(function() {
                notifCP('🗑️ Borradores eliminados correctamente.', 'success');
            }).catch(function() {
                notifCP('No fue posible eliminar todos los borradores seleccionados.', 'error');
            }).finally(function() {
                CP.seleccionadas.clear();
                actualizarBulkCP();
                cargarComprobantesCP();
            });
        });
    }

    function anularSeleccionCP() {
        var activos = seleccionActualCP().filter(function(c) { return c.estado === 'REGISTRADO' || c.estado === 'CONTABILIZADO'; });
        if (!activos.length) { notifCP('La selección no tiene comprobantes registrados para anular.', 'warning'); return; }
        pedirMotivoAnulacionCP(activos.length).then(function(motivo) {
            if (motivo === null) return;
            Promise.all(activos.map(function(c) {
                return fetch('/comprobantes/' + c.id + '/anular', {
                    method: 'POST', headers: Object.assign({ 'Content-Type': 'application/json' }, hdrsCP()),
                    body: JSON.stringify({ motivo_anulacion: motivo })
                });
            })).then(function() {
                notifCP('🚫 Comprobantes anulados correctamente.', 'success');
            }).catch(function() {
                notifCP('No fue posible anular todos los comprobantes seleccionados.', 'error');
            }).finally(function() {
                CP.seleccionadas.clear();
                actualizarBulkCP();
                cargarComprobantesCP();
            });
        });
    }

    /* ════════════════════════════════════════════════
       VER DETALLE
    ════════════════════════════════════════════════ */
    function verComprobanteCP(id) {
        var body = document.getElementById('cp-vf-body');
        body.innerHTML = '<div class="spinner-cell"><div class="spinner"></div>Cargando detalle…</div>';
        document.getElementById('modal-ver-cp').style.display = 'flex';

        fetch('/comprobantes/' + id, { headers: hdrsCP() })
            .then(function(r) { return r.json().then(function(d) { if (!r.ok) throw new Error(d.message || 'No se pudo cargar el comprobante.'); return d; }); })
            .then(function(c) {
                CP.actual = c;
                document.getElementById('cp-vf-title').textContent = 'Comprobante ' + (c.numero || '');
                document.getElementById('cp-vf-sub').textContent = (c.tipo || '') + ' · ' + fmtFechaCP(c.fecha);
                renderVerCP(c);
            })
            .catch(function(e) {
                body.innerHTML = '<div class="spinner-cell">No fue posible cargar el detalle.</div>';
                notifCP(e.message || 'No fue posible cargar el detalle.', 'error');
            });
    }

    function renderVerCP(c) {
        var body = document.getElementById('cp-vf-body');
        var anulTag = c.estado === 'ANULADO'
            ? '<div class="alerta-anulada">🚫 Este comprobante está <strong>ANULADO</strong>' +
              (c.motivo ? '. Motivo: ' + esc(c.motivo) : '') +
              (c.anulado_por ? '<br>Por: ' + esc(c.anulado_por) + (c.anulado_at ? ' — ' + esc(c.anulado_at) : '') : '') + '</div>'
            : '';

        var relTag = '';
        if (c.reversion) {
            relTag = '<div class="alerta-info"><span>↩️ Tiene una reversión: <strong>' + esc(c.reversion.documento) + '</strong></span>' +
                '<button class="btn-outline" onclick="verComprobanteCP(' + c.reversion.id + ')">Ver reversión</button></div>';
        } else if (c.original) {
            relTag = '<div class="alerta-info"><span>↩️ Esta es la reversión de: <strong>' + esc(c.original.documento) + '</strong></span>' +
                '<button class="btn-outline" onclick="verComprobanteCP(' + c.original.id + ')">Ver original</button></div>';
        }

        var filas = (c.lineas || []).map(function(l) {
            return '<tr>' +
                '<td><span class="td-mono">' + esc(l.codigo || '') + '</span> ' + esc(l.cuenta || '') + '</td>' +
                '<td>' + esc(l.descripcion || '—') + '</td>' +
                '<td>' + esc(l.tercero || '—') + '</td>' +
                '<td>' + esc(l.centro_costo || '—') + '</td>' +
                '<td style="text-align:right;">' + ((l.debito || 0) > 0 ? fmtMoneyCP(l.debito) : '—') + '</td>' +
                '<td style="text-align:right;">' + ((l.credito || 0) > 0 ? fmtMoneyCP(l.credito) : '—') + '</td>' +
                '</tr>';
        }).join('') || '<tr><td colspan="6" style="text-align:center;color:#9CA3AF;padding:16px;">Sin movimientos</td></tr>';

        body.innerHTML = anulTag + relTag +
            '<div class="cp-grid" style="grid-template-columns:repeat(3,1fr);">' +
            fldCP('Tipo', c.tipo) + fldCP('Documento', c.numero || '') + fldCP('Estado', c.estado) +
            fldCP('Fecha', fmtFechaCP(c.fecha)) + fldCP('Tercero', c.tercero || '—') + fldCP('Usuario', c.usuario || '—') +
            (c.registrado_por ? fldCP('Registrado por', c.registrado_por) : '') +
            '</div>' +
            fldCP('Descripción', c.descripcion || '—', true) +
            '<p class="cp-section-title">Movimientos</p>' +
            '<div class="items-tbl-wrap"><table class="items-tbl"><thead><tr>' +
            '<th>Cuenta</th><th>Detalle</th><th>Tercero</th><th>Centro costo</th>' +
            '<th style="text-align:right;">Débito</th><th style="text-align:right;">Crédito</th></tr></thead>' +
            '<tbody>' + filas + '</tbody></table></div>' +
            '<div class="totales-box">' +
            '<div class="totales-row total-final"><span>TOTAL</span><span>' + fmtMoneyCP(c.total_debito) + ' / ' + fmtMoneyCP(c.total_credito) + '</span></div>' +
            '</div>';
    }

    function fldCP(label, val, full) {
        return '<div class="cp-field"' + (full ? ' style="grid-column:1/-1;"' : '') + '>' +
            '<label>' + label + '</label>' +
            '<div style="padding:7px 10px;background:#F9FAFB;border:1px solid #E5E7EB;border-radius:7px;font-size:13px;color:#111827;">' +
            esc(String(val ?? '—')) + '</div></div>';
    }

    function cerrarVerCP() { document.getElementById('modal-ver-cp').style.display = 'none'; }
    function cerrarVerBackdropCP(e) { if (e.target === document.getElementById('modal-ver-cp')) cerrarVerCP(); }

    /* ════════════════════════════════════════════════
       EDITOR (crear / editar borrador)
    ════════════════════════════════════════════════ */
    function abrirEditorCP(id) {
        CP.editandoId = id || null;
        CP.lineas = [];

        document.getElementById('cp-ed-tipo').value = '';
        document.getElementById('cp-ed-tipo').disabled = false;
        document.getElementById('cp-ed-prefijo').value = '';
        document.getElementById('cp-ed-prefijo').disabled = false;
        document.getElementById('cp-ed-numero').value = '';
        document.getElementById('cp-ed-fecha').value = fmtISOLocalCP(new Date());
        document.getElementById('cp-ed-descripcion').value = '';
        document.getElementById('cp-ed-title').textContent = 'Nuevo Comprobante';
        document.getElementById('cp-ed-sub').textContent = 'Complete el encabezado y agregue los movimientos';
        document.getElementById('cp-btn-registrar').style.display = '';

        renderLineasCP();
        document.getElementById('modal-editor-cp').style.display = 'flex';
    }

    function editarComprobanteCP(id) {
        fetch('/comprobantes/' + id + '/edit', { headers: hdrsCP() })
            .then(function(r) { return r.json().then(function(d) { if (!r.ok) throw new Error(d.message || 'No se pudo cargar el borrador.'); return d; }); })
            .then(function(c) {
                CP.editandoId = c.id;
                CP.lineas = (c.items || []).map(function(it) {
                    return {
                        cuenta_id: it.cuenta_id || '',
                        descripcion: it.descripcion ?? it.detalle ?? '',
                        tercero_id: it.tercero_id || '',
                        centro_costo_id: it.centro_costo_id || '',
                        debito: +it.debito || 0,
                        credito: +it.credito || 0,
                    };
                });

                document.getElementById('cp-ed-tipo').value = c.tipo || '';
                document.getElementById('cp-ed-tipo').disabled = true;
                document.getElementById('cp-ed-prefijo').value = c.prefijo || '';
                document.getElementById('cp-ed-prefijo').disabled = true;
                document.getElementById('cp-ed-numero').value = c.numero || '';
                document.getElementById('cp-ed-fecha').value = c.fecha || '';
                document.getElementById('cp-ed-descripcion').value = c.descripcion || '';
                document.getElementById('cp-ed-title').textContent = 'Editar ' + (c.numero || '');
                document.getElementById('cp-ed-sub').textContent = 'Complete los movimientos y registre cuando esté cuadrado';
                document.getElementById('cp-btn-registrar').style.display = '';

                renderLineasCP();
                document.getElementById('modal-editor-cp').style.display = 'flex';
            })
            .catch(function(e) { notifCP(e.message || 'No fue posible abrir el borrador.', 'error'); });
    }

    function cerrarEditorCP() { document.getElementById('modal-editor-cp').style.display = 'none'; }
    function cerrarEditorBackdropCP(e) { if (e.target === document.getElementById('modal-editor-cp')) cerrarEditorCP(); }

    /* ── Líneas de movimiento ── */
    function agregarLineaCP() {
        CP.lineas.push({ cuenta_id: '', descripcion: '', tercero_id: '', centro_costo_id: '', debito: 0, credito: 0 });
        renderLineasCP();
    }

    function eliminarLineaCP(idx) { CP.lineas.splice(idx, 1); renderLineasCP(); }

    function lineaCambiarCP(idx, campo, val) {
        CP.lineas[idx][campo] = val;
        if (campo === 'debito' && val > 0) CP.lineas[idx].credito = 0;
        if (campo === 'credito' && val > 0) CP.lineas[idx].debito = 0;
        renderLineasCP();
    }

    function renderLineasCP() {
        var tbody = document.getElementById('cp-ed-lineas');
        if (CP.lineas.length === 0) {
            tbody.innerHTML = '<tr id="cp-ed-lineas-empty"><td colspan="7" style="text-align:center;color:#9CA3AF;padding:16px;font-size:12px;">' +
                'Sin movimientos. Haga clic en "＋ Añadir movimiento".</td></tr>';
        } else {
            tbody.innerHTML = CP.lineas.map(function(l, idx) {
                var cuenta = CP.cuentas.find(function(c) { return String(c.id) === String(l.cuenta_id); });
                var reqTercero = cuenta && cuenta.requiere_tercero;
                var reqCentro = cuenta && cuenta.requiere_centro_costo;

                var optsCuenta = '<option value="">— Seleccione —</option>' + CP.cuentas.map(function(c) {
                    return '<option value="' + c.id + '" ' + (String(c.id) === String(l.cuenta_id) ? 'selected' : '') + '>' +
                        esc(c.codigo) + ' - ' + esc(c.nombre) + '</option>';
                }).join('');

                var optsTercero = '<option value="">— Ninguno —</option>' + CP.terceros.map(function(t) {
                    return '<option value="' + t.id + '" ' + (String(t.id) === String(l.tercero_id) ? 'selected' : '') + '>' +
                        esc(t.nombre_completo || t.nombre || t.razon_social || '') + '</option>';
                }).join('');

                var optsCentro = '<option value="">— Ninguno —</option>' + CP.centros.map(function(cc) {
                    return '<option value="' + cc.id + '" ' + (String(cc.id) === String(l.centro_costo_id) ? 'selected' : '') + '>' +
                        esc(cc.codigo) + ' - ' + esc(cc.nombre) + '</option>';
                }).join('');

                var tercCls = reqTercero && !l.tercero_id ? ' req-empty' : '';
                var centroCls = reqCentro && !l.centro_costo_id ? ' req-empty' : '';

                return '<tr>' +
                    '<td><select onchange="lineaCambiarCP(' + idx + ',\'cuenta_id\',this.value)">' + optsCuenta + '</select></td>' +
                    '<td><input type="text" value="' + esc(l.descripcion || '') + '" placeholder="Detalle" onchange="lineaCambiarCP(' + idx + ',\'descripcion\',this.value)"></td>' +
                    '<td><select class="' + tercCls.trim() + '" onchange="lineaCambiarCP(' + idx + ',\'tercero_id\',this.value)" title="' + (reqTercero ? 'Requerido por la cuenta' : '') + '">' + optsTercero + '</select></td>' +
                    '<td><select class="' + centroCls.trim() + '" onchange="lineaCambiarCP(' + idx + ',\'centro_costo_id\',this.value)" title="' + (reqCentro ? 'Requerido por la cuenta' : '') + '">' + optsCentro + '</select></td>' +
                    '<td><input type="number" min="0" step="0.01" style="text-align:right;" value="' + (l.debito || 0) + '" onchange="lineaCambiarCP(' + idx + ',\'debito\',+this.value)"></td>' +
                    '<td><input type="number" min="0" step="0.01" style="text-align:right;" value="' + (l.credito || 0) + '" onchange="lineaCambiarCP(' + idx + ',\'credito\',+this.value)"></td>' +
                    '<td><button onclick="eliminarLineaCP(' + idx + ')" style="border:none;background:transparent;cursor:pointer;font-size:14px;color:#DC2626;">✕</button></td>' +
                    '</tr>';
            }).join('');
        }
        calcularTotalesCP();
    }

    function calcularTotalesCP() {
        var d = CP.lineas.reduce(function(s, l) { return s + (+l.debito || 0); }, 0);
        var h = CP.lineas.reduce(function(s, l) { return s + (+l.credito || 0); }, 0);
        var dif = d - h;
        document.getElementById('cp-ed-tot-debito').textContent = fmtMoneyCP(d);
        document.getElementById('cp-ed-tot-credito').textContent = fmtMoneyCP(h);
        var row = document.getElementById('cp-ed-diferencia-row');
        var lbl = document.getElementById('cp-ed-diferencia');
        var cuadrado = Math.abs(dif) < 0.005 && d > 0;
        lbl.textContent = fmtMoneyCP(Math.abs(dif));
        row.classList.toggle('desc-ok', cuadrado);
        row.classList.toggle('desc-bad', !cuadrado);
        row.querySelector('span:first-child').textContent = cuadrado ? '✓ Comprobante cuadrado' : 'Diferencia (débito ≠ crédito)';
        document.getElementById('cp-btn-registrar').disabled = !(cuadrado && CP.lineas.length >= 2);
    }

    function lineasParaEnviarCP() {
        return CP.lineas.map(function(l) {
            return {
                cuenta_id: l.cuenta_id || null,
                descripcion: l.descripcion || null,
                tercero_id: l.tercero_id || null,
                centro_costo_id: l.centro_costo_id || null,
                debito: +l.debito || 0,
                credito: +l.credito || 0,
            };
        });
    }

    /* ── Guardar borrador ── */
    function guardarBorradorCP() {
        var tipo = document.getElementById('cp-ed-tipo').value.trim();
        var prefijo = document.getElementById('cp-ed-prefijo').value.trim();
        var fecha = document.getElementById('cp-ed-fecha').value;
        var descripcion = document.getElementById('cp-ed-descripcion').value.trim();

        if (!CP.editandoId && !tipo) { notifCP('⚠️ El tipo es requerido.', 'error'); return; }
        if (!CP.editandoId && !prefijo) { notifCP('⚠️ El prefijo es requerido.', 'error'); return; }
        if (!fecha) { notifCP('⚠️ Seleccione la fecha.', 'error'); return; }

        var btn = document.getElementById('cp-btn-guardar');
        btn.disabled = true;
        btn.textContent = '⏳ Guardando…';

        crearOReusarCabeceraCP(tipo, prefijo, fecha, descripcion)
            .then(function(id) {
                return fetch('/comprobantes/' + id, {
                    method: 'PUT',
                    headers: Object.assign({ 'Content-Type': 'application/json' }, hdrsCP()),
                    body: JSON.stringify({ fecha: fecha, descripcion: descripcion, items: lineasParaEnviarCP() }),
                }).then(function(r) { return r.json().then(function(d) { if (!r.ok) throw new Error(d.message || Object.values(d.errors || {}).flat().join(' ') || 'No se pudo guardar.'); return d; }); });
            })
            .then(function() {
                notifCP('💾 Borrador guardado correctamente.', 'success');
                cargarComprobantesCP();
                // Refrescar encabezado (número asignado) sin cerrar el modal
                return editarComprobanteCPSilencioso(CP.editandoId);
            })
            .catch(function(e) { notifCP(e.message || 'No se pudo guardar el borrador.', 'error'); })
            .finally(function() {
                btn.disabled = false;
                btn.textContent = '💾 Guardar borrador';
            });
    }

    function editarComprobanteCPSilencioso(id) {
        return fetch('/comprobantes/' + id + '/edit', { headers: hdrsCP() })
            .then(function(r) { return r.json(); })
            .then(function(c) {
                document.getElementById('cp-ed-numero').value = c.numero || '';
                document.getElementById('cp-ed-title').textContent = 'Editar ' + (c.numero || '');
            })
            .catch(function() {});
    }

    function crearOReusarCabeceraCP(tipo, prefijo, fecha, descripcion) {
        if (CP.editandoId) return Promise.resolve(CP.editandoId);
        return fetch('/comprobantes', {
            method: 'POST',
            headers: Object.assign({ 'Content-Type': 'application/json' }, hdrsCP()),
            body: JSON.stringify({ tipo: tipo, prefijo: prefijo, fecha: fecha, descripcion: descripcion }),
        }).then(function(r) { return r.json().then(function(d) { if (!r.ok) throw new Error(d.message || Object.values(d.errors || {}).flat().join(' ') || 'No se pudo crear el comprobante.'); return d; }); })
          .then(function(res) { CP.editandoId = res.id; return res.id; });
    }

    /* ── Registrar ── */
    function registrarComprobanteCP() {
        var tipo = document.getElementById('cp-ed-tipo').value.trim();
        var prefijo = document.getElementById('cp-ed-prefijo').value.trim();
        var fecha = document.getElementById('cp-ed-fecha').value;
        var descripcion = document.getElementById('cp-ed-descripcion').value.trim();
        var items = lineasParaEnviarCP();

        if (items.length < 2) { notifCP('⚠️ Agregue al menos dos movimientos.', 'error'); return; }
        for (var i = 0; i < items.length; i++) {
            if (!items[i].cuenta_id) { notifCP('⚠️ Todas las líneas deben tener una cuenta contable.', 'error'); return; }
            var cuenta = CP.cuentas.find(function(c) { return String(c.id) === String(items[i].cuenta_id); });
            if (cuenta && cuenta.requiere_tercero && !items[i].tercero_id) { notifCP('⚠️ La cuenta ' + cuenta.codigo + ' requiere tercero.', 'error'); return; }
            if (cuenta && cuenta.requiere_centro_costo && !items[i].centro_costo_id) { notifCP('⚠️ La cuenta ' + cuenta.codigo + ' requiere centro de costo.', 'error'); return; }
        }

        var btn = document.getElementById('cp-btn-registrar');
        btn.disabled = true;
        btn.textContent = '⏳ Registrando…';

        crearOReusarCabeceraCP(tipo, prefijo, fecha, descripcion)
            .then(function(id) {
                return fetch('/comprobantes/' + id + '/registrar', {
                    method: 'POST',
                    headers: Object.assign({ 'Content-Type': 'application/json' }, hdrsCP()),
                    body: JSON.stringify({ items: items }),
                }).then(function(r) { return r.json().then(function(d) { if (!r.ok) throw new Error(d.message || Object.values(d.errors || {}).flat().join(' ') || 'No se pudo registrar.'); return d; }); });
            })
            .then(function() {
                notifCP('✅ Comprobante registrado correctamente.', 'success');
                cerrarEditorCP();
                cargarComprobantesCP();
            })
            .catch(function(e) { notifCP(e.message || 'No se pudo registrar el comprobante.', 'error'); })
            .finally(function() {
                btn.disabled = false;
                btn.textContent = '✅ Registrar comprobante';
            });
    }

    /* ════════════════════════════════════════════════
       ANULAR / REVERTIR / ELIMINAR (individual)
    ════════════════════════════════════════════════ */
    function anularComprobanteCP(id, doc) {
        pedirMotivoAnulacionCP(1, doc).then(function(motivo) {
            if (motivo === null) return;
            fetch('/comprobantes/' + id + '/anular', {
                method: 'POST',
                headers: Object.assign({ 'Content-Type': 'application/json' }, hdrsCP()),
                body: JSON.stringify({ motivo_anulacion: motivo }),
            }).then(function(r) { return r.json().then(function(d) { if (!r.ok) throw new Error(d.message || 'No se pudo anular.'); return d; }); })
              .then(function() { notifCP('🚫 Comprobante ' + doc + ' anulado.', 'success'); cargarComprobantesCP(); })
              .catch(function(e) { notifCP(e.message || 'No se pudo anular el comprobante.', 'error'); });
        });
    }

    function pedirMotivoAnulacionCP(cantidad, doc) {
        var titulo = cantidad > 1 ? '¿Anular ' + cantidad + ' comprobantes?' : '¿Anular ' + (doc || 'el comprobante') + '?';
        if (typeof Swal !== 'undefined') {
            return Swal.fire({
                title: titulo,
                input: 'textarea',
                inputLabel: 'Motivo de la anulación',
                inputPlaceholder: 'Explique el motivo…',
                inputAttributes: { 'aria-label': 'Motivo de la anulación' },
                showCancelButton: true,
                confirmButtonText: 'Anular',
                cancelButtonText: 'Cancelar',
                confirmButtonColor: '#DC2626',
                inputValidator: function(value) { if (!value || !value.trim()) return 'El motivo es obligatorio'; }
            }).then(function(r) { return r.isConfirmed ? r.value.trim() : null; });
        }
        var motivo = window.prompt(titulo + '\nMotivo de la anulación:');
        return Promise.resolve(motivo && motivo.trim() ? motivo.trim() : null);
    }

    function revertirComprobanteCP(id, doc) {
        confirmarAccionCP('¿Revertir la anulación de ' + doc + '? El comprobante volverá a estar activo.', function() {
            fetch('/comprobantes/' + id + '/revertir', { method: 'POST', headers: hdrsCP() })
                .then(function(r) { return r.json().then(function(d) { return { ok: r.ok, data: d }; }); })
                .then(function(res) {
                    if (!res.ok) { notifCP(res.data.message || 'No se pudo revertir.', 'error'); return; }
                    notifCP('↩️ Comprobante ' + doc + ' restaurado.', 'success');
                    cargarComprobantesCP();
                })
                .catch(function() { notifCP('No se pudo revertir la anulación.', 'error'); });
        });
    }

    function eliminarComprobanteCP(id, doc) {
        confirmarAccionCP('¿Eliminar el borrador ' + doc + '? Esta acción no se puede deshacer.', function() {
            fetch('/comprobantes/' + id, { method: 'DELETE', headers: hdrsCP() })
                .then(function(r) { return r.json().then(function(d) { if (!r.ok) throw new Error(d.message || 'No se pudo eliminar.'); return d; }); })
                .then(function() { notifCP('🗑️ Borrador ' + doc + ' eliminado.', 'success'); cargarComprobantesCP(); })
                .catch(function(e) { notifCP(e.message || 'No se pudo eliminar el borrador.', 'error'); });
        });
    }

    function confirmarAccionCP(mensaje, accion) {
        var modal = document.getElementById('modalConfirm');
        var texto = document.getElementById('confirmMensaje');
        var boton = document.getElementById('btnConfirmarAccion');

        if (modal && texto && boton) {
            texto.textContent = mensaje;
            boton.onclick = function(ev) {
                ev.preventDefault();
                ev.stopImmediatePropagation();
                modal.classList.add('hidden');
                modal.classList.remove('flex', 'show');
                accion();
            };
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            return;
        }
        if (window.confirm(mensaje)) accion();
    }

    /* ════════════════════════════════════════════════
       EXPORTAR
    ════════════════════════════════════════════════ */
    function exportarComprobantesCP() { exportarCsvCP(CP.filtradas, 'comprobantes.csv'); }

    function exportarCsvCP(lista, nombre) {
        if (!lista.length) { notifCP('No hay comprobantes para exportar.', 'warning'); return; }
        var encabezados = ['Tipo', 'Documento', 'Fecha', 'Tercero', 'Descripción', 'Débito', 'Crédito', 'Estado', 'Usuario'];
        var filas = lista.map(function(c) {
            return [c.tipo || '', c.numero || '', c.fecha || '', c.tercero || '', c.descripcion || '',
                c.debito || 0, c.credito || 0, c.estado || '', c.usuario || ''
            ].map(function(v) { return '"' + String(v).replace(/"/g, '""') + '"'; }).join(',');
        });
        var csv = '﻿' + encabezados.join(',') + '\n' + filas.join('\n');
        var blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        var a = document.createElement('a');
        a.href = URL.createObjectURL(blob);
        a.download = nombre;
        a.click();
        URL.revokeObjectURL(a.href);
        notifCP('Exportación creada: ' + lista.length + ' registro' + (lista.length !== 1 ? 's' : ''), 'success');
    }

    /* ════════════════════════════════════════════════
       UTILIDADES
    ════════════════════════════════════════════════ */
    function fmtMoneyCP(n) { return '$ ' + (Number(n) || 0).toLocaleString('es-CO', { minimumFractionDigits: 0, maximumFractionDigits: 0 }); }

    function fmtFechaCP(s) {
        if (!s) return '—';
        var parts = String(s).slice(0, 10).split('-');
        return parts.length === 3 ? parts[2] + '/' + parts[1] + '/' + parts[0] : s;
    }

    function esc(s) { return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }

    function notifCP(msg, tipo) {
        if (typeof mostrarNotificacion === 'function' && document.getElementById('notificaciones')) {
            mostrarNotificacion(msg, tipo === 'error' ? 'error' : tipo === 'warning' ? 'warning' : 'success');
            return;
        }
        var colores = { success: '#059669', error: '#DC2626', info: '#1D4ED8', warning: '#D97706' };
        var el = document.createElement('div');
        el.className = 'nx-toast';
        el.style.borderLeftColor = colores[tipo] || colores.info;
        el.textContent = msg;
        document.body.appendChild(el);
        setTimeout(function() { el.remove(); }, 3500);
    }
</script>
