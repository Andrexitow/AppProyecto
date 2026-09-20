<style>
    /* ═══════════════════════════════════════════════
       Estilos base (idénticos al módulo de Comprobantes)
    ═══════════════════════════════════════════════ */
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

    .btn-outline.active {
        background: #EFF6FF;
        border-color: #1D4ED8;
        color: #1D4ED8;
    }

    /* ── Tabla / Contenedor ── */
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

    table.cp-tbl tbody tr.inactiva {
        opacity: 0.55;
    }

    table.cp-tbl tbody tr.inactiva td {
        text-decoration: line-through;
    }

    table.cp-tbl tbody tr.inactiva .badge,
    table.cp-tbl tbody tr.inactiva .tbl-actions,
    table.cp-tbl tbody tr.inactiva .cta-nombre-wrap {
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

    .badge-teal {
        background: #ECFEFF;
        color: #0E7490;
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

    .act-btn.add:hover {
        background: #EEF2FF;
        color: #4338CA;
    }

    .act-btn.del:hover {
        background: #FEF2F2;
        color: #DC2626;
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
        max-width: 640px;
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

    .cp-field.checkbox-field {
        flex-direction: row;
        align-items: center;
        gap: 8px;
    }

    .cp-field.checkbox-field label {
        text-transform: none;
        font-size: 13px;
        color: #374151;
        letter-spacing: normal;
        font-weight: 500;
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

    /* ── Spinner ── */
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

    /* ═══════════════════════════════════════════════
       Específico: Árbol jerárquico de cuentas (PUC)
    ═══════════════════════════════════════════════ */
    .tree-container {
        padding: 6px 4px;
    }

    .tree-node {
        border-radius: 8px;
    }

    .tree-row {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 10px;
        border-radius: 8px;
        cursor: default;
        transition: background 0.12s;
    }

    .tree-row:hover {
        background: #F8FAFC;
    }

    .tree-row.inactiva {
        opacity: 0.55;
    }

    .tree-row.inactiva .tree-nombre {
        text-decoration: line-through;
    }

    .tree-toggle {
        width: 20px;
        height: 20px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        background: #F3F4F6;
        border-radius: 5px;
        cursor: pointer;
        font-size: 10px;
        color: #6B7280;
        flex-shrink: 0;
        transition: transform 0.15s, background 0.15s;
    }

    .tree-toggle:hover {
        background: #E5E7EB;
        color: #111827;
    }

    .tree-toggle.expanded {
        transform: rotate(90deg);
    }

    .tree-toggle.spacer {
        background: transparent;
        cursor: default;
        visibility: hidden;
    }

    .tree-icon {
        font-size: 13px;
        flex-shrink: 0;
    }

    .tree-codigo {
        font-family: 'JetBrains Mono', 'Fira Mono', monospace;
        font-size: 12px;
        font-weight: 600;
        color: #1D4ED8;
        min-width: 66px;
        flex-shrink: 0;
    }

    .tree-nombre {
        font-size: 13px;
        color: #111827;
        font-weight: 500;
        flex: 1;
        min-width: 0;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .tree-nombre.agrupadora {
        font-weight: 700;
    }

    .tree-meta {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-shrink: 0;
    }

    .tree-children {
        margin-left: 24px;
        border-left: 1px dashed #E5E7EB;
        padding-left: 4px;
    }

    .tree-empty {
        text-align: center;
        color: #9CA3AF;
        padding: 40px 16px;
        font-size: 13px;
    }

    .view-toggle {
        display: flex;
        gap: 4px;
        background: #F3F4F6;
        border-radius: 8px;
        padding: 3px;
    }

    .view-toggle button {
        border: none;
        background: transparent;
        padding: 6px 12px;
        font-size: 12px;
        font-weight: 600;
        color: #6B7280;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.15s;
    }

    .view-toggle button.active {
        background: #fff;
        color: #1D4ED8;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.06);
    }

    /* ── Responsive ── */
    @media (max-width: 640px) {
        .metrics-row {
            grid-template-columns: 1fr 1fr;
        }

        /* Ocultamos solo Padre y Reglas (6,7). Antes se ocultaba desde la
           columna 6 en adelante, lo que también tapaba "Estado" (8) y
           "Acciones" (9) sin ninguna forma de llegar a ellas en el celular. */
        table.cp-tbl thead th:nth-child(6),
        table.cp-tbl thead th:nth-child(7) {
            display: none;
        }

        table.cp-tbl tbody td:nth-child(6),
        table.cp-tbl tbody td:nth-child(7) {
            display: none;
        }

        .tree-children {
            margin-left: 14px;
        }
    }
</style>

<div id="view-cuentas-contables">

    {{-- ── ENCABEZADO ── --}}
    <div class="sec-header">
        <div>
            <p class="sec-title">📊 Plan Único de Cuentas</p>
            <p class="sec-subtitle">Administración del catálogo contable (PUC)</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button class="btn-outline" onclick="exportarCuentas()">
                ⬇️ Exportar
            </button>
            <button class="btn-primary" onclick="abrirModalCuenta(null)">
                ＋ Nueva Cuenta
            </button>
        </div>
    </div>

    {{-- ── MÉTRICAS ── --}}
    <div class="metrics-row" id="metricas-cuentas">
        <div class="metric-card" style="--accent:#1D4ED8">
            <p class="metric-label">Total Cuentas</p>
            <p class="metric-value" id="ct-m-total">—</p>
            <p class="metric-sub" id="ct-m-total-sub">Cargando…</p>
        </div>
        <div class="metric-card" style="--accent:#7C3AED">
            <p class="metric-label">Agrupadoras</p>
            <p class="metric-value" id="ct-m-agrupadoras">—</p>
            <p class="metric-sub">Cuentas de nivel superior</p>
        </div>
        <div class="metric-card" style="--accent:#059669">
            <p class="metric-label">Auxiliares (Detalle)</p>
            <p class="metric-value" id="ct-m-auxiliares">—</p>
            <p class="metric-sub">Permiten movimientos</p>
        </div>
        <div class="metric-card" style="--accent:#D97706">
            <p class="metric-label">Activas</p>
            <p class="metric-value" id="ct-m-activas">—</p>
            <p class="metric-sub" id="ct-m-activas-sub"></p>
        </div>
        <div class="metric-card" style="--accent:#DC2626">
            <p class="metric-label">Inactivas</p>
            <p class="metric-value" id="ct-m-inactivas">—</p>
            <p class="metric-sub" id="ct-m-inactivas-sub"></p>
        </div>
    </div>

    {{-- ── FILTROS ── --}}
    <div class="filter-bar">
        <div class="fi-group" style="flex:2;min-width:200px;">
            <span class="fi-label">🔍</span>
            <input autocomplete="off" class="fi-input" type="text" id="ct-fi-buscar" placeholder="Código o nombre de la cuenta…"
                oninput="aplicarFiltrosCT()">
        </div>
        <div class="fi-group">
            <select class="fi-select" id="ct-fi-clasificacion" onchange="aplicarFiltrosCT()">
                <option value="">Todas las clasificaciones</option>
                <option value="ACTIVO">Activo</option>
                <option value="PASIVO">Pasivo</option>
                <option value="PATRIMONIO">Patrimonio</option>
                <option value="INGRESO">Ingreso</option>
                <option value="COSTO">Costo</option>
                <option value="GASTO">Gasto</option>
            </select>
        </div>
        <div class="fi-group">
            <select class="fi-select" id="ct-fi-tipo" onchange="aplicarFiltrosCT()">
                <option value="">Todos los tipos</option>
                <option value="AGRUPADORA">Agrupadora</option>
                <option value="DETALLE">Detalle</option>
            </select>
        </div>
        <div class="fi-group">
            <select class="fi-select" id="ct-fi-estado" onchange="aplicarFiltrosCT()">
                <option value="">Todos los estados</option>
                <option value="activa">Activa</option>
                <option value="inactiva">Inactiva</option>
            </select>
        </div>
        <button class="btn-outline" onclick="limpiarFiltrosCT()">✕ Limpiar</button>

        <div class="view-toggle">
            <button id="ct-btn-arbol" class="active" onclick="cambiarVistaCT('arbol')">🌳 Árbol</button>
            <button id="ct-btn-tabla" onclick="cambiarVistaCT('tabla')">📋 Tabla</button>
        </div>
    </div>

    {{-- ── VISTA ÁRBOL ── --}}
    <div class="table-wrapper" id="ct-vista-arbol">
        <div style="display:flex;justify-content:flex-end;gap:6px;padding:10px 14px;border-bottom:1px solid #F3F4F6;">
            <button class="btn-outline" style="font-size:11px;padding:5px 10px;" onclick="expandirTodoCT()">⤢ Expandir
                todo</button>
            <button class="btn-outline" style="font-size:11px;padding:5px 10px;" onclick="colapsarTodoCT()">⤡ Colapsar
                todo</button>
        </div>
        <div class="tree-container" id="ct-tree-root">
            <div class="spinner-cell">
                <div class="spinner"></div>
                Cargando plan de cuentas…
            </div>
        </div>
    </div>

    {{-- ── VISTA TABLA (plana, con filtros/orden) ── --}}
    <div class="table-wrapper" id="ct-vista-tabla" style="display:none;">
        <div class="table-scroll">
            <table class="cp-tbl" id="tbl-cuentas">
                <thead>
                    <tr>
                        <th onclick="sortTablaCT('codigo')" data-col="codigo">
                            Código <span class="sort-icon">↕</span>
                        </th>
                        <th onclick="sortTablaCT('nombre')" data-col="nombre">
                            Nombre <span class="sort-icon">↕</span>
                        </th>
                        <th onclick="sortTablaCT('clasificacion')" data-col="clasificacion">
                            Clasificación <span class="sort-icon">↕</span>
                        </th>
                        <th>Naturaleza</th>
                        <th>Tipo</th>
                        <th>Padre</th>
                        <th style="text-align:center;">Reglas</th>
                        <th>Estado</th>
                        <th style="text-align:center;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-cuentas">
                    <tr>
                        <td colspan="9">
                            <div class="spinner-cell">
                                <div class="spinner"></div>
                                Cargando cuentas…
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="pagination-bar">
            <span class="pag-info" id="ct-pag-info">Mostrando 0 de 0 registros</span>
            <div class="pag-btns" id="ct-pag-btns"></div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════
     MODAL CUENTA (crear / editar)
═══════════════════════════════════════════════ --}}
<div id="modal-cuenta" style="display:none;" class="modal-backdrop" onclick="cerrarModalCuentaBackdrop(event)">
    <div class="modal-comprobante">

        <div class="modal-head">
            <div>
                <p class="modal-head-title" id="ct-mf-title">Nueva Cuenta</p>
                <p class="modal-head-sub" id="ct-mf-sub">Complete los campos requeridos</p>
            </div>
            <button onclick="cerrarModalCuenta()"
                style="border:none;background:transparent;font-size:20px;cursor:pointer;color:#6B7280;padding:4px;border-radius:6px;line-height:1;">✕</button>
        </div>

        <div class="modal-body" id="ct-mf-body">

            <div id="ct-mf-alerta-mov" class="alerta-descuadre" style="display:none;">
                ⚠️ Esta cuenta tiene movimientos registrados. Algunos campos no se pueden modificar.
            </div>

            <p class="cp-section-title">Datos de la cuenta</p>
            <div class="cp-grid">
                <div class="cp-field">
                    <label>Código *</label>
                    <input autocomplete="off" type="text" id="ct-mf-codigo" placeholder="Ej: 110505" maxlength="20">
                </div>
                <div class="cp-field" style="grid-column:span 1;">
                    <label>Nombre *</label>
                    <input autocomplete="off" type="text" id="ct-mf-nombre" placeholder="Ej: Caja General">
                </div>
                <div class="cp-field" style="grid-column:1/-1;">
                    <label>Cuenta Padre</label>
                    <select id="ct-mf-padre">
                        <option value="">— Sin cuenta padre (cuenta raíz) —</option>
                    </select>
                </div>
                <div class="cp-field">
                    <label>Clasificación *</label>
                    <select id="ct-mf-clasificacion">
                        <option value="">— Seleccione —</option>
                        <option value="ACTIVO">Activo</option>
                        <option value="PASIVO">Pasivo</option>
                        <option value="PATRIMONIO">Patrimonio</option>
                        <option value="INGRESO">Ingreso</option>
                        <option value="COSTO">Costo</option>
                        <option value="GASTO">Gasto</option>
                    </select>
                </div>
                <div class="cp-field">
                    <label>Naturaleza *</label>
                    <select id="ct-mf-naturaleza">
                        <option value="DEBITO">Débito</option>
                        <option value="CREDITO">Crédito</option>
                    </select>
                </div>
                <div class="cp-field">
                    <label>Tipo *</label>
                    <select id="ct-mf-tipo" onchange="onCambioTipoCT()">
                        <option value="AGRUPADORA">Agrupadora</option>
                        <option value="DETALLE">Detalle</option>
                    </select>
                </div>
                <div class="cp-field">
                    <label>Nivel</label>
                    <input autocomplete="off" type="text" id="ct-mf-nivel" placeholder="Automático" readonly>
                </div>
                <div class="cp-field checkbox-field">
                    <input type="checkbox" id="ct-mf-movimientos">
                    <label for="ct-mf-movimientos">Permite movimientos</label>
                </div>
                <div class="cp-field checkbox-field">
                    <input type="checkbox" id="ct-mf-requiere-tercero">
                    <label for="ct-mf-requiere-tercero">Requiere tercero</label>
                </div>
                <div class="cp-field checkbox-field">
                    <input type="checkbox" id="ct-mf-requiere-centro">
                    <label for="ct-mf-requiere-centro">Requiere centro de costo</label>
                </div>
                <div class="cp-field checkbox-field">
                    <input type="checkbox" id="ct-mf-estado" checked>
                    <label for="ct-mf-estado">Cuenta activa</label>
                </div>
            </div>

        </div>

        <div class="modal-foot">
            <button class="btn-outline" onclick="cerrarModalCuenta()">Cancelar</button>
            <button class="btn-primary" id="ct-mf-btn-save" onclick="guardarCuenta()">
                💾 Guardar Cuenta
            </button>
        </div>
    </div>
</div>

<script>
    var CT = {
        datos: [], // todas las cuentas (planas, con nivel y padre_id)
        filtradas: [], // tras aplicar filtros (vista tabla)
        arbol: [], // datos organizados jerárquicamente (vista árbol)
        expandidos: new Set(), // ids de nodos expandidos
        paginaActual: 1,
        porPagina: 20,
        sortCol: 'codigo',
        sortAsc: true,
        vistaActual: 'arbol', // 'arbol' | 'tabla'
        editandoId: null,
    };

    /* ════════════════════════════════════════════════
       INICIALIZACIÓN
    ════════════════════════════════════════════════ */
    (function initCT() {
        cargarCuentas();
    })();

    /* ════════════════════════════════════════════════
       CARGA DE DATOS (fetch API Laravel)
    ════════════════════════════════════════════════ */
    function cargarCuentas() {
        var token = document.querySelector('meta[name="csrf-token"]')?.content;

        fetch('/cuentas-contables/data', { // ← antes decía '/cuentas-contables'
                headers: {
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            })
            .then(function(r) {
                return r.json();
            })
            .then(function(res) {
                CT.datos = Array.isArray(res.data) ? res.data : (Array.isArray(res) ? res : []);
                despuesDeCargarCT();
            })
            .catch(function() {
                CT.datos = datosDemoCuentas();
                despuesDeCargarCT();
            });
    }

    function despuesDeCargarCT() {
        construirArbolCT();
        actualizarMetricasCT();
        aplicarFiltrosCT();
        renderVistaActualCT();
        poblarSelectPadreCT();
    }

    /* ════════════════════════════════════════════════
       DATOS DEMO (para previsualización sin backend)
    ════════════════════════════════════════════════ */
    function datosDemoCuentas() {
        function c(id, codigo, nombre, clasificacion, naturaleza, tipo, padre, movimientos, activa) {
            return {
                id: id,
                codigo: codigo,
                nombre: nombre,
                clasificacion: clasificacion,
                naturaleza: naturaleza,
                tipo: tipo,
                cuenta_padre_id: padre,
                nivel: codigo.length <= 1 ? 1 : Math.ceil(codigo.length / 2),
                movimientos: !!movimientos,
                activa: activa !== false
            };
        }
        return [
            c(1, '1', 'ACTIVO', 'ACTIVO', 'DEBITO', 'AGRUPADORA', null, false),
            c(2, '11', 'DISPONIBLE', 'ACTIVO', 'DEBITO', 'AGRUPADORA', 1, false),
            c(3, '1105', 'CAJA', 'ACTIVO', 'DEBITO', 'AGRUPADORA', 2, false),
            c(4, '110505', 'Caja General', 'ACTIVO', 'DEBITO', 'DETALLE', 3, true),
            c(5, '110510', 'Caja Menor', 'ACTIVO', 'DEBITO', 'DETALLE', 3, true),
            c(6, '1110', 'BANCOS', 'ACTIVO', 'DEBITO', 'AGRUPADORA', 2, false),
            c(7, '111005', 'Bancolombia', 'ACTIVO', 'DEBITO', 'DETALLE', 6, true),
            c(8, '111010', 'Banco Bogotá', 'ACTIVO', 'DEBITO', 'DETALLE', 6, true),
            c(9, '13', 'DEUDORES', 'ACTIVO', 'DEBITO', 'AGRUPADORA', 1, false),
            c(10, '1305', 'CLIENTES', 'ACTIVO', 'DEBITO', 'AGRUPADORA', 9, false),
            c(11, '130505', 'Clientes Nacionales', 'ACTIVO', 'DEBITO', 'DETALLE', 10, true),
            c(12, '2', 'PASIVO', 'PASIVO', 'CREDITO', 'AGRUPADORA', null, false),
            c(13, '22', 'PROVEEDORES', 'PASIVO', 'CREDITO', 'AGRUPADORA', 12, false),
            c(14, '220505', 'Proveedores Nacionales', 'PASIVO', 'CREDITO', 'DETALLE', 13, true),
            c(15, '3', 'PATRIMONIO', 'PATRIMONIO', 'CREDITO', 'AGRUPADORA', null, false),
            c(16, '31', 'CAPITAL SOCIAL', 'PATRIMONIO', 'CREDITO', 'AGRUPADORA', 15, false),
            c(17, '310505', 'Capital Suscrito', 'PATRIMONIO', 'CREDITO', 'DETALLE', 16, true),
            c(18, '4', 'INGRESOS', 'INGRESO', 'CREDITO', 'AGRUPADORA', null, false),
            c(19, '41', 'OPERACIONALES', 'INGRESO', 'CREDITO', 'AGRUPADORA', 18, false),
            c(20, '413505', 'Ventas', 'INGRESO', 'CREDITO', 'DETALLE', 19, true),
            c(21, '413510', 'Servicios', 'INGRESO', 'CREDITO', 'DETALLE', 19, true),
            c(22, '5', 'GASTOS', 'GASTO', 'DEBITO', 'AGRUPADORA', null, false),
            c(23, '51', 'OPERACIONALES DE ADMINISTRACIÓN', 'GASTO', 'DEBITO', 'AGRUPADORA', 22, false),
            c(24, '510506', 'Sueldos y Salarios', 'GASTO', 'DEBITO', 'DETALLE', 23, true),
            c(25, '510515', 'Servicios Públicos', 'GASTO', 'DEBITO', 'DETALLE', 23, false),
            c(26, '6', 'COSTO DE VENTAS', 'COSTO', 'DEBITO', 'AGRUPADORA', null, false),
            c(27, '613505', 'Costo de Mercancía Vendida', 'COSTO', 'DEBITO', 'DETALLE', 26, true),
        ];
    }

    /* ════════════════════════════════════════════════
       ÁRBOL: construcción de la jerarquía
    ════════════════════════════════════════════════ */
    function construirArbolCT() {
        var byId = {};
        CT.datos.forEach(function(c) {
            byId[c.id] = Object.assign({}, c, {
                hijos: []
            });
        });

        var raiz = [];
        CT.datos.forEach(function(c) {
            var nodo = byId[c.id];
            if (c.cuenta_padre_id && byId[c.cuenta_padre_id]) {
                byId[c.cuenta_padre_id].hijos.push(nodo);
            } else {
                raiz.push(nodo);
            }
        });

        function ordenar(lista) {
            lista.sort(function(a, b) {
                return String(a.codigo).localeCompare(String(b.codigo));
            });
            lista.forEach(function(n) {
                ordenar(n.hijos);
            });
        }
        ordenar(raiz);

        CT.arbol = raiz;

        /* Expandir el primer nivel por defecto */
        if (CT.expandidos.size === 0) {
            raiz.forEach(function(n) {
                CT.expandidos.add(n.id);
            });
        }
    }

    function renderArbolCT() {
        var root = document.getElementById('ct-tree-root');
        var buscar = (document.getElementById('ct-fi-buscar').value || '').toLowerCase().trim();
        var clasFiltro = document.getElementById('ct-fi-clasificacion').value;
        var tipoFiltro = document.getElementById('ct-fi-tipo').value;
        var estadoFiltro = document.getElementById('ct-fi-estado').value;

        function coincide(nodo) {
            var hay = true;
            if (buscar) hay = (nodo.codigo + ' ' + nodo.nombre).toLowerCase().includes(buscar);
            if (hay && clasFiltro) hay = nodo.clasificacion === clasFiltro;
            if (hay && tipoFiltro) hay = nodo.tipo === tipoFiltro;
            if (hay && estadoFiltro === 'activa') hay = nodo.activa;
            if (hay && estadoFiltro === 'inactiva') hay = !nodo.activa;
            return hay;
        }

        /* Si hay filtros activos, filtra recursivamente conservando ramas con hijos que coincidan */
        var hayFiltro = !!(buscar || clasFiltro || tipoFiltro || estadoFiltro);

        function filtrarRama(nodo) {
            var hijosFiltrados = nodo.hijos.map(filtrarRama).filter(Boolean);
            if (coincide(nodo) || hijosFiltrados.length > 0) {
                return Object.assign({}, nodo, {
                    hijos: hijosFiltrados
                });
            }
            return null;
        }

        var lista = hayFiltro ? CT.arbol.map(filtrarRama).filter(Boolean) : CT.arbol;

        if (lista.length === 0) {
            root.innerHTML = '<div class="tree-empty">📭 No se encontraron cuentas</div>';
            return;
        }

        /* Si hay filtro, auto-expandir todo lo que quedó */
        if (hayFiltro) {
            (function expandAll(nodos) {
                nodos.forEach(function(n) {
                    if (n.hijos.length) {
                        CT.expandidos.add(n.id);
                        expandAll(n.hijos);
                    }
                });
            })(lista);
        }

        root.innerHTML = lista.map(renderNodoCT).join('');
    }

    function renderNodoCT(nodo) {
        var tieneHijos = nodo.hijos && nodo.hijos.length > 0;
        var expandido = CT.expandidos.has(nodo.id);
        var esAgrupadora = nodo.tipo === 'AGRUPADORA';

        var toggleHtml = tieneHijos ?
            '<button class="tree-toggle' + (expandido ? ' expanded' : '') + '" onclick="toggleNodoCT(' + nodo.id +
            ')">▶</button>' :
            '<button class="tree-toggle spacer">▶</button>';

        var icono = esAgrupadora ? '📁' : '📄';

        var badgeClas = badgeClasificacionCT(nodo.clasificacion);
        var badgeEstado = nodo.activa ?
            '' :
            '<span class="badge badge-red"><span class="dot"></span>Inactiva</span>';
        var badgeMov = (!esAgrupadora && nodo.movimientos) ?
            '<span class="badge badge-teal">Mov.</span>' : '';
        var badgeTercero = nodo.requiere_tercero ? '<span class="badge badge-yellow">Tercero</span>' : '';
        var badgeCentro = nodo.requiere_centro_costo ? '<span class="badge badge-purple">C. costo</span>' : '';

        var acciones = '<div class="tbl-actions">' +
            (esAgrupadora ?
                '<button class="act-btn add" data-tip="Añadir subcuenta" onclick="abrirModalCuenta(null,' + nodo.id +
                ')">➕</button>' :
                '') +
            '<button class="act-btn edit" data-tip="Editar" onclick="editarCuenta(' + nodo.id + ')">✏️</button>' +
            '<button class="act-btn del" data-tip="Eliminar" onclick="eliminarCuenta(' + nodo.id + ',\'' + esc(nodo
                .codigo) + '\')">🗑️</button>' +
            '</div>';

        var html = '<div class="tree-node" data-id="' + nodo.id + '">' +
            '<div class="tree-row' + (nodo.activa ? '' : ' inactiva') + '">' +
            toggleHtml +
            '<span class="tree-icon">' + icono + '</span>' +
            '<span class="tree-codigo">' + esc(nodo.codigo) + '</span>' +
            '<span class="tree-nombre' + (esAgrupadora ? ' agrupadora' : '') + '">' + esc(nodo.nombre) + '</span>' +
            '<span class="tree-meta">' + badgeClas + badgeMov + badgeTercero + badgeCentro + badgeEstado + acciones + '</span>' +
            '</div>';

        if (tieneHijos) {
            html += '<div class="tree-children" style="' + (expandido ? '' : 'display:none;') + '">' +
                nodo.hijos.map(renderNodoCT).join('') +
                '</div>';
        }

        html += '</div>';
        return html;
    }

    function toggleNodoCT(id) {
        if (CT.expandidos.has(id)) CT.expandidos.delete(id);
        else CT.expandidos.add(id);
        renderArbolCT();
    }

    function expandirTodoCT() {
        (function walk(nodos) {
            nodos.forEach(function(n) {
                if (n.hijos.length) {
                    CT.expandidos.add(n.id);
                    walk(n.hijos);
                }
            });
        })(CT.arbol);
        renderArbolCT();
    }

    function colapsarTodoCT() {
        CT.expandidos.clear();
        renderArbolCT();
    }

    function badgeClasificacionCT(clas) {
        var map = {
            'ACTIVO': 'badge-blue',
            'PASIVO': 'badge-red',
            'PATRIMONIO': 'badge-purple',
            'INGRESO': 'badge-green',
            'COSTO': 'badge-yellow',
            'GASTO': 'badge-gray'
        };
        return '<span class="badge ' + (map[clas] || 'badge-gray') + '">' + esc(clas || '—') + '</span>';
    }

    /* ════════════════════════════════════════════════
       VISTA TABLA (plana)
    ════════════════════════════════════════════════ */
    function cambiarVistaCT(vista) {
        CT.vistaActual = vista;
        document.getElementById('ct-btn-arbol').classList.toggle('active', vista === 'arbol');
        document.getElementById('ct-btn-tabla').classList.toggle('active', vista === 'tabla');
        document.getElementById('ct-vista-arbol').style.display = vista === 'arbol' ? 'block' : 'none';
        document.getElementById('ct-vista-tabla').style.display = vista === 'tabla' ? 'block' : 'none';
        renderVistaActualCT();
    }

    function renderVistaActualCT() {
        if (CT.vistaActual === 'arbol') renderArbolCT();
        else renderTablaCT();
    }

    function aplicarFiltrosCT() {
        var buscar = (document.getElementById('ct-fi-buscar').value || '').toLowerCase().trim();
        var clasificacion = document.getElementById('ct-fi-clasificacion').value;
        var tipo = document.getElementById('ct-fi-tipo').value;
        var estado = document.getElementById('ct-fi-estado').value;

        CT.filtradas = CT.datos.filter(function(c) {
            if (buscar && !((c.codigo + ' ' + c.nombre).toLowerCase().includes(buscar))) return false;
            if (clasificacion && c.clasificacion !== clasificacion) return false;
            if (tipo && c.tipo !== tipo) return false;
            if (estado === 'activa' && !c.activa) return false;
            if (estado === 'inactiva' && c.activa) return false;
            return true;
        });

        CT.paginaActual = 1;
        sortTablaActualCT();
        renderVistaActualCT();
    }

    function limpiarFiltrosCT() {
        document.getElementById('ct-fi-buscar').value = '';
        document.getElementById('ct-fi-clasificacion').value = '';
        document.getElementById('ct-fi-tipo').value = '';
        document.getElementById('ct-fi-estado').value = '';
        aplicarFiltrosCT();
    }

    function sortTablaCT(col) {
        if (CT.sortCol === col) CT.sortAsc = !CT.sortAsc;
        else {
            CT.sortCol = col;
            CT.sortAsc = true;
        }
        sortTablaActualCT();
        renderTablaCT();
        document.querySelectorAll('table.cp-tbl thead th[data-col]').forEach(function(th) {
            th.classList.toggle('sorted', th.dataset.col === col);
            var icon = th.querySelector('.sort-icon');
            if (icon) icon.textContent = th.dataset.col === col ? (CT.sortAsc ? '↑' : '↓') : '↕';
        });
    }

    function sortTablaActualCT() {
        var col = CT.sortCol,
            asc = CT.sortAsc;
        CT.filtradas.sort(function(a, b) {
            var va = a[col] ?? '',
                vb = b[col] ?? '';
            return asc ? String(va).localeCompare(String(vb)) : String(vb).localeCompare(String(va));
        });
    }

    function renderTablaCT() {
        var tbody = document.getElementById('tbody-cuentas');
        var total = CT.filtradas.length;
        var desde = (CT.paginaActual - 1) * CT.porPagina;
        var pagina = CT.filtradas.slice(desde, desde + CT.porPagina);

        document.getElementById('ct-pag-info').textContent =
            'Mostrando ' + Math.min(pagina.length, total) + ' de ' + total + ' registros';

        if (pagina.length === 0) {
            tbody.innerHTML =
                '<tr><td colspan="9"><div class="spinner-cell">📭 No se encontraron cuentas</div></td></tr>';
            renderPaginacionCT(total);
            return;
        }

        tbody.innerHTML = pagina.map(function(c) {
            var padre = CT.datos.find(function(x) {
                return x.id === c.cuenta_padre_id;
            });
            var trCls = c.activa ? '' : 'inactiva';

            var badgeClas = badgeClasificacionCT(c.clasificacion);
            var badgeTipo = c.tipo === 'AGRUPADORA' ?
                '<span class="badge badge-purple">📁 Agrupadora</span>' :
                '<span class="badge badge-gray">📄 Detalle</span>';
            var badgeEstado = c.activa ?
                '<span class="badge badge-green"><span class="dot"></span>Activa</span>' :
                '<span class="badge badge-red"><span class="dot"></span>Inactiva</span>';
            var badgeMov = c.movimientos ?
                '<span class="badge badge-teal">Sí</span>' :
                '<span class="badge badge-gray">No</span>';
            var reglas = badgeMov +
                (c.requiere_tercero ? ' <span class="badge badge-yellow">Tercero</span>' : '') +
                (c.requiere_centro_costo ? ' <span class="badge badge-purple">C. costo</span>' : '');

            var acciones = '<div class="tbl-actions">' +
                '<button class="act-btn edit" data-tip="Editar" onclick="editarCuenta(' + c.id +
                ')">✏️</button>' +
                '<button class="act-btn del" data-tip="Eliminar" onclick="eliminarCuenta(' + c.id + ',\'' + esc(
                    c.codigo) + '\')">🗑️</button>' +
                '</div>';

            return '<tr class="' + trCls + '" data-id="' + c.id + '">' +
                '<td><span class="td-mono">' + esc(c.codigo) + '</span></td>' +
                '<td>' + esc(c.nombre) + '</td>' +
                '<td>' + badgeClas + '</td>' +
                '<td style="color:#6B7280;font-size:12px;">' + esc(c.naturaleza) + '</td>' +
                '<td>' + badgeTipo + '</td>' +
                '<td style="color:#6B7280;font-size:12px;">' + (padre ? esc(padre.codigo) : '—') + '</td>' +
                '<td style="text-align:center;">' + reglas + '</td>' +
                '<td>' + badgeEstado + '</td>' +
                '<td>' + acciones + '</td>' +
                '</tr>';
        }).join('');

        renderPaginacionCT(total);
    }

    function renderPaginacionCT(total) {
        var totalPags = Math.max(1, Math.ceil(total / CT.porPagina));
        var actual = CT.paginaActual;
        var btns = document.getElementById('ct-pag-btns');
        var html = '';

        html += '<button class="pag-btn" onclick="irPaginaCT(' + (actual - 1) + ')"' + (actual === 1 ? ' disabled' :
            '') + '>‹</button>';

        var desde = Math.max(1, actual - 2);
        var hasta = Math.min(totalPags, desde + 4);
        desde = Math.max(1, hasta - 4);

        if (desde > 1) html += '<button class="pag-btn" onclick="irPaginaCT(1)">1</button>' +
            (desde > 2 ? '<span style="padding:0 4px;color:#9CA3AF;">…</span>' : '');

        for (var p = desde; p <= hasta; p++) {
            html += '<button class="pag-btn' + (p === actual ? ' active' : '') + '" onclick="irPaginaCT(' + p + ')">' +
                p + '</button>';
        }

        if (hasta < totalPags) {
            html += (hasta < totalPags - 1 ? '<span style="padding:0 4px;color:#9CA3AF;">…</span>' : '') +
                '<button class="pag-btn" onclick="irPaginaCT(' + totalPags + ')">' + totalPags + '</button>';
        }

        html += '<button class="pag-btn" onclick="irPaginaCT(' + (actual + 1) + ')"' + (actual === totalPags ?
            ' disabled' : '') + '>›</button>';

        btns.innerHTML = html;
    }

    function irPaginaCT(p) {
        var total = CT.filtradas.length;
        var totalPags = Math.max(1, Math.ceil(total / CT.porPagina));
        CT.paginaActual = Math.max(1, Math.min(p, totalPags));
        renderTablaCT();
    }

    /* ════════════════════════════════════════════════
       MÉTRICAS
    ════════════════════════════════════════════════ */
    function actualizarMetricasCT() {
        var total = CT.datos.length;
        var agrupadoras = CT.datos.filter(function(c) {
            return c.tipo === 'AGRUPADORA';
        }).length;
        var auxiliares = CT.datos.filter(function(c) {
            return c.tipo === 'DETALLE';
        }).length;
        var activas = CT.datos.filter(function(c) {
            return c.activa;
        }).length;
        var inactivas = total - activas;

        document.getElementById('ct-m-total').textContent = total;
        document.getElementById('ct-m-total-sub').textContent = 'En el catálogo';
        document.getElementById('ct-m-agrupadoras').textContent = agrupadoras;
        document.getElementById('ct-m-auxiliares').textContent = auxiliares;
        document.getElementById('ct-m-activas').textContent = activas;
        document.getElementById('ct-m-activas-sub').textContent = Math.round(activas / Math.max(1, total) * 100) +
            '% del total';
        document.getElementById('ct-m-inactivas').textContent = inactivas;
        document.getElementById('ct-m-inactivas-sub').textContent = Math.round(inactivas / Math.max(1, total) * 100) +
            '% del total';
    }

    /* ════════════════════════════════════════════════
       MODAL CREAR / EDITAR
    ════════════════════════════════════════════════ */
    function poblarSelectPadreCT() {
        var sel = document.getElementById('ct-mf-padre');
        var agrupadoras = CT.datos.filter(function(c) {
                return c.tipo === 'AGRUPADORA';
            })
            .sort(function(a, b) {
                return String(a.codigo).localeCompare(String(b.codigo));
            });

        sel.innerHTML = '<option value="">— Sin cuenta padre (cuenta raíz) —</option>' +
            agrupadoras.map(function(c) {
                return '<option value="' + c.id + '">' + esc(c.codigo) + ' - ' + esc(c.nombre) + '</option>';
            }).join('');
    }

    function abrirModalCuenta(id, padreSugeridoId) {
        CT.editandoId = id;

        document.getElementById('ct-mf-codigo').value = '';
        document.getElementById('ct-mf-nombre').value = '';
        document.getElementById('ct-mf-padre').value = padreSugeridoId || '';
        document.getElementById('ct-mf-clasificacion').value = '';
        document.getElementById('ct-mf-naturaleza').value = 'DEBITO';
        document.getElementById('ct-mf-tipo').value = 'DETALLE';
        document.getElementById('ct-mf-nivel').value = '(automático)';
        document.getElementById('ct-mf-movimientos').checked = true;
        document.getElementById('ct-mf-requiere-tercero').checked = false;
        document.getElementById('ct-mf-requiere-centro').checked = false;
        document.getElementById('ct-mf-estado').checked = true;
        document.getElementById('ct-mf-alerta-mov').style.display = 'none';
        document.getElementById('ct-mf-codigo').removeAttribute('readonly');

        if (padreSugeridoId) {
            var padre = CT.datos.find(function(x) {
                return x.id === padreSugeridoId;
            });
            if (padre) document.getElementById('ct-mf-clasificacion').value = padre.clasificacion;
        }

        if (id) {
            var c = CT.datos.find(function(x) {
                return x.id === id;
            });
            if (c) {
                document.getElementById('ct-mf-title').textContent = 'Editar Cuenta ' + c.codigo;
                document.getElementById('ct-mf-sub').textContent = 'Modifique los campos necesarios';
                document.getElementById('ct-mf-codigo').value = c.codigo;
                document.getElementById('ct-mf-nombre').value = c.nombre;
                document.getElementById('ct-mf-padre').value = c.cuenta_padre_id || '';
                document.getElementById('ct-mf-clasificacion').value = c.clasificacion;
                document.getElementById('ct-mf-naturaleza').value = c.naturaleza;
                document.getElementById('ct-mf-tipo').value = c.tipo;
                document.getElementById('ct-mf-nivel').value = c.nivel;
                document.getElementById('ct-mf-movimientos').checked = !!c.movimientos;
                document.getElementById('ct-mf-requiere-tercero').checked = !!c.requiere_tercero;
                document.getElementById('ct-mf-requiere-centro').checked = !!c.requiere_centro_costo;
                document.getElementById('ct-mf-estado').checked = !!c.activa;

                if (c.movimientos) {
                    document.getElementById('ct-mf-alerta-mov').style.display = 'flex';
                    document.getElementById('ct-mf-codigo').setAttribute('readonly', true);
                }
            }
        } else {
            document.getElementById('ct-mf-title').textContent = 'Nueva Cuenta';
            document.getElementById('ct-mf-sub').textContent = 'Complete los campos requeridos';
        }

        onCambioTipoCT();
        document.getElementById('modal-cuenta').style.display = 'flex';
    }

    function onCambioTipoCT() {
        var esAgrupadora = document.getElementById('ct-mf-tipo').value === 'AGRUPADORA';
        var chkMov = document.getElementById('ct-mf-movimientos');
        if (esAgrupadora) {
            chkMov.checked = false;
            chkMov.disabled = true;
        } else {
            chkMov.disabled = false;
        }
    }

    function editarCuenta(id) {
        abrirModalCuenta(id);
    }

    function cerrarModalCuenta() {
        document.getElementById('modal-cuenta').style.display = 'none';
    }

    function cerrarModalCuentaBackdrop(e) {
        if (e.target === document.getElementById('modal-cuenta')) cerrarModalCuenta();
    }

    /* ── Guardar ── */
    function guardarCuenta() {
        var codigo = document.getElementById('ct-mf-codigo').value.trim();
        var nombre = document.getElementById('ct-mf-nombre').value.trim();
        var clasificacion = document.getElementById('ct-mf-clasificacion').value;
        var padreId = document.getElementById('ct-mf-padre').value;

        if (!codigo) {
            notifCT('⚠️ El código de la cuenta es requerido', 'error');
            return;
        }
        if (!nombre) {
            notifCT('⚠️ El nombre de la cuenta es requerido', 'error');
            return;
        }
        if (!clasificacion) {
            notifCT('⚠️ Seleccione la clasificación', 'error');
            return;
        }

        /* Validar código único */
        var duplicado = CT.datos.find(function(c) {
            return c.codigo === codigo && c.id !== CT.editandoId;
        });
        if (duplicado) {
            notifCT('⚠️ Ya existe una cuenta con el código ' + codigo, 'error');
            return;
        }

        var token = document.querySelector('meta[name="csrf-token"]')?.content;
        var body = {
            codigo: codigo,
            nombre: nombre,
            cuenta_padre_id: padreId || null,
            clasificacion: clasificacion,
            naturaleza: document.getElementById('ct-mf-naturaleza').value,
            tipo: document.getElementById('ct-mf-tipo').value,
            movimientos: document.getElementById('ct-mf-movimientos').checked,
            requiere_tercero: document.getElementById('ct-mf-requiere-tercero').checked,
            requiere_centro_costo: document.getElementById('ct-mf-requiere-centro').checked,
            activa: document.getElementById('ct-mf-estado').checked,
        };

        var url = CT.editandoId ? '/cuentas-contables/' + CT.editandoId : '/cuentas-contables';
        var method = CT.editandoId ? 'PUT' : 'POST';

        document.getElementById('ct-mf-btn-save').disabled = true;
        document.getElementById('ct-mf-btn-save').textContent = '⏳ Guardando…';

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
                notifCT(CT.editandoId ? '✅ Cuenta actualizada' : '✅ Cuenta creada correctamente', 'success');
                cerrarModalCuenta();
                cargarCuentas();
            })
            .catch(function() {
                /* Demo: simular éxito localmente */
                if (CT.editandoId) {
                    var idx = CT.datos.findIndex(function(c) {
                        return c.id === CT.editandoId;
                    });
                    if (idx > -1) CT.datos[idx] = Object.assign({}, CT.datos[idx], body, {
                        cuenta_padre_id: body.cuenta_padre_id ? Number(body.cuenta_padre_id) : null
                    });
                } else {
                    var nuevoId = Math.max(0, ...CT.datos.map(function(c) {
                        return c.id;
                    })) + 1;
                    CT.datos.push(Object.assign({
                        id: nuevoId,
                        nivel: 1
                    }, body, {
                        cuenta_padre_id: body.cuenta_padre_id ? Number(body.cuenta_padre_id) : null
                    }));
                }
                notifCT(CT.editandoId ? '✅ Cuenta actualizada (demo)' : '✅ Cuenta creada (demo)', 'success');
                cerrarModalCuenta();
                despuesDeCargarCT();
            })
            .finally(function() {
                document.getElementById('ct-mf-btn-save').disabled = false;
                document.getElementById('ct-mf-btn-save').textContent = '💾 Guardar Cuenta';
            });
    }

    /* ── Eliminar ── */
    function eliminarCuenta(id, codigo) {
        var tieneHijos = CT.datos.some(function(c) {
            return c.cuenta_padre_id === id;
        });
        if (tieneHijos) {
            notifCT('⚠️ No se puede eliminar: la cuenta ' + codigo + ' tiene subcuentas asociadas', 'error');
            return;
        }

        Swal.fire({
            title: '¿Eliminar la cuenta ' + codigo + '?',
            text: 'Se eliminará permanentemente del plan de cuentas. Esta acción NO se puede deshacer.',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#DC2626',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
        }).then(function(r) {
            if (!r.isConfirmed) return;
            var token = document.querySelector('meta[name="csrf-token"]')?.content;
            fetch('/cuentas-contables/' + id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        'Accept': 'application/json'
                    }
                })
                .then(function(res) {
                    return res.json();
                })
                .then(function() {
                    notifCT('🗑️ Cuenta ' + codigo + ' eliminada', 'success');
                    cargarCuentas();
                })
                .catch(function() {
                    notifCT('🗑️ Cuenta ' + codigo + ' eliminada (demo)', 'success');
                    CT.datos = CT.datos.filter(function(c) {
                        return c.id !== id;
                    });
                    despuesDeCargarCT();
                });
        });
    }

    /* ── Exportar ── */
    function exportarCuentas() {
        notifCT('⬇️ Preparando exportación de ' + CT.datos.length + ' cuentas…', 'info');
        /* En producción: window.open('/cuentas-contables/exportar?...') */
    }

    /* ════════════════════════════════════════════════
       UTILIDADES
    ════════════════════════════════════════════════ */
    function esc(s) {
        return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g,
            '&quot;');
    }

    function notifCT(msg, tipo) {
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
                'color:#111827;box-shadow:0 4px 12px rgba(0,0,0,0.08);pointer-events:auto;min-width:220px;max-width:360px;';
            var cont = document.getElementById('notificaciones');
            if (cont) {
                cont.appendChild(el);
                setTimeout(function() {
                    el.remove();
                }, 3500);
            } else {
                console.log('[' + tipo + '] ' + msg);
            }
        }
    }
</script>
