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

    .btn-outline.danger {
        color: #DC2626;
        border-color: #FCA5A5;
    }

    .btn-outline.danger:hover {
        background: #FEF2F2;
        border-color: #DC2626;
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

    table.compras-tbl {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
    }

    table.compras-tbl thead {
        background: #F8FAFC;
        border-bottom: 1px solid #EAECF0;
    }

    table.compras-tbl thead th {
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

    table.compras-tbl thead th:hover {
        color: #1D4ED8;
    }

    table.compras-tbl thead th .sort-icon {
        display: inline-block;
        margin-left: 4px;
        opacity: 0.4;
        font-size: 10px;
    }

    table.compras-tbl thead th.sorted .sort-icon {
        opacity: 1;
        color: #1D4ED8;
    }

    table.compras-tbl tbody tr {
        border-bottom: 1px solid #F3F4F6;
        transition: background 0.1s;
    }

    table.compras-tbl tbody tr:last-child {
        border-bottom: none;
    }

    table.compras-tbl tbody tr:hover {
        background: #F8FAFC;
    }

    table.compras-tbl tbody tr.anulada {
        opacity: 0.6;
    }

    table.compras-tbl tbody tr.anulada td {
        text-decoration: line-through;
    }

    table.compras-tbl tbody tr.anulada .badge,
    table.compras-tbl tbody tr.anulada .tbl-actions {
        text-decoration: none;
    }

    table.compras-tbl td {
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

    .td-proveedor {
        line-height: 1.35;
    }

    .td-proveedor .prov-nombre {
        font-weight: 600;
        color: #111827;
        max-width: 220px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .td-proveedor .prov-nit {
        font-size: 11px;
        color: #9CA3AF;
    }

    .td-money {
        font-weight: 600;
        color: #059669;
        text-align: right;
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

    .act-btn.reg:hover {
        background: #ECFDF5;
        color: #059669;
    }

    .act-btn.rev:hover {
        background: #FFFBEB;
        color: #D97706;
    }

    .act-btn.undo:hover {
        background: #EFF6FF;
        color: #1D4ED8;
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

    .modal-compra {
        background: #fff;
        border-radius: 16px;
        width: 100%;
        max-width: 780px;
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
        gap: 10px;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
        flex-wrap: wrap;
    }

    .co-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        margin-bottom: 6px;
    }

    @media (max-width: 480px) {
        .co-grid {
            grid-template-columns: 1fr;
        }
    }

    .co-field {
        display: flex;
        flex-direction: column;
        gap: 3px;
        position: relative;
    }

    .co-field label {
        font-size: 11px;
        font-weight: 500;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .co-field input,
    .co-field select,
    .co-field textarea {
        border: 1px solid #D1D5DB;
        border-radius: 7px;
        padding: 7px 10px;
        font-size: 13px;
        color: #111827;
        outline: none;
        background: #F9FAFB;
        transition: border 0.15s;
        font-family: inherit;
        width: 100%;
    }

    .co-field input:focus,
    .co-field select:focus,
    .co-field textarea:focus {
        border-color: #1D4ED8;
        background: #fff;
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
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* ── Buscador de proveedor (combobox) ── */
    .prov-combo {
        position: relative;
    }

    .prov-combo-input-wrap {
        position: relative;
    }

    .prov-combo-input-wrap .prov-icon {
        position: absolute;
        left: 10px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 12px;
        color: #9CA3AF;
        pointer-events: none;
    }

    #co-proveedor-input {
        padding-left: 30px !important;
    }

    #co-proveedor-input.selected {
        background: #EFF6FF !important;
        border-color: #BFDBFE !important;
        color: #1e3a8a;
        font-weight: 600;
    }

    .prov-clear {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        border: none;
        background: transparent;
        color: #9CA3AF;
        cursor: pointer;
        font-size: 13px;
        display: none;
        padding: 2px 4px;
        border-radius: 4px;
    }

    .prov-clear:hover {
        color: #DC2626;
        background: #FEF2F2;
    }

    .prov-combo-input-wrap.has-selection .prov-clear {
        display: block;
    }

    .prov-results {
        position: absolute;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        background: #fff;
        border: 1px solid #E5E7EB;
        border-radius: 10px;
        box-shadow: 0 10px 30px rgba(17, 24, 39, 0.12);
        max-height: 220px;
        overflow-y: auto;
        z-index: 200;
    }

    .prov-result-item {
        padding: 8px 12px;
        cursor: pointer;
        border-bottom: 1px solid #F3F4F6;
        display: flex;
        flex-direction: column;
        gap: 1px;
    }

    .prov-result-item:last-child {
        border-bottom: none;
    }

    .prov-result-item:hover,
    .prov-result-item.active {
        background: #F8FAFC;
    }

    .prov-result-nombre {
        font-size: 12.5px;
        font-weight: 600;
        color: #111827;
    }

    .prov-result-nit {
        font-size: 11px;
        color: #9CA3AF;
    }

    .prov-results-empty {
        padding: 14px 12px;
        text-align: center;
        font-size: 12px;
        color: #9CA3AF;
    }

    .prov-hint {
        font-size: 11px;
        color: #9CA3AF;
        margin-top: 3px;
    }

    .prov-hint.ok {
        color: #059669;
    }

    /* ── Items de compra ── */
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

    .items-input {
        border: 1px solid #D1D5DB;
        border-radius: 6px;
        padding: 5px 8px;
        font-size: 12px;
        width: 100%;
        outline: none;
        background: #fff;
    }

    .items-input:focus {
        border-color: #1D4ED8;
    }

    /* Totales */
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
        .metrics-row {
            grid-template-columns: 1fr 1fr;
        }

        table.compras-tbl thead th:nth-child(n+5) {
            display: none;
        }

        table.compras-tbl tbody td:nth-child(n+5) {
            display: none;
        }
    }
</style>

<div id="view-compras">

    {{-- ── ENCABEZADO ── --}}
    <div class="sec-header">
        <div>
            <p class="sec-title">📦 Compras</p>
            <p class="sec-subtitle">Facturas de proveedores e ingreso de inventario</p>
        </div>
        <button class="btn-primary" onclick="abrirCompra()">＋ Crear compra</button>
    </div>

    {{-- ── MÉTRICAS ── --}}
    <div class="metrics-row" id="metricas-compras">
        <div class="metric-card" style="--accent:#1D4ED8">
            <p class="metric-label">Total Compras</p>
            <p class="metric-value" id="cm-total">—</p>
            <p class="metric-sub" id="cm-total-sub">Cargando…</p>
        </div>
        <div class="metric-card" style="--accent:#059669">
            <p class="metric-label">Comprado en período</p>
            <p class="metric-value money" id="cm-total-valor">—</p>
            <p class="metric-sub" id="cm-total-valor-sub"></p>
        </div>
        <div class="metric-card" style="--accent:#7C3AED">
            <p class="metric-label">Promedio por compra</p>
            <p class="metric-value money" id="cm-promedio">—</p>
            <p class="metric-sub" id="cm-promedio-sub"></p>
        </div>
        <div class="metric-card" style="--accent:#D97706">
            <p class="metric-label">Borrador</p>
            <p class="metric-value" id="cm-borrador">—</p>
            <p class="metric-sub">Pendientes de registrar</p>
        </div>
        <div class="metric-card" style="--accent:#DC2626">
            <p class="metric-label">Anuladas</p>
            <p class="metric-value" id="cm-anuladas">—</p>
            <p class="metric-sub" id="cm-anuladas-sub"></p>
        </div>
    </div>

    {{-- ── FILTROS ── --}}
    <div class="filter-bar">
        <div class="fi-group" style="flex:2;min-width:200px;">
            <span class="fi-label">🔍</span>
            <input class="fi-input" type="text" id="co-buscar" placeholder="Factura, proveedor, NIT…"
                oninput="aplicarFiltrosCompras()">
        </div>
        <div class="fi-group">
            <span class="fi-label">Desde</span>
            <input class="fi-input" type="date" id="co-desde" onchange="aplicarFiltrosCompras()">
        </div>
        <div class="fi-group">
            <span class="fi-label">Hasta</span>
            <input class="fi-input" type="date" id="co-hasta" onchange="aplicarFiltrosCompras()">
        </div>
        <div class="fi-group">
            <select class="fi-select" id="co-estado" onchange="aplicarFiltrosCompras()">
                <option value="">Todos los estados</option>
                <option value="borrador">Borrador</option>
                <option value="confirmada">Confirmada</option>
                <option value="anulada">Anulada</option>
            </select>
        </div>
        <button class="btn-outline" onclick="limpiarFiltrosCompras()">✕ Limpiar</button>
    </div>

    {{-- ── TABLA ── --}}
    <div class="table-wrapper">
        <div class="table-scroll">
            <table class="compras-tbl" id="tbl-compras">
                <thead>
                    <tr>
                        <th onclick="sortTablaCompras('factura')" data-col="factura">
                            Factura <span class="sort-icon">↕</span>
                        </th>
                        <th onclick="sortTablaCompras('fecha')" data-col="fecha">
                            Fecha <span class="sort-icon">↕</span>
                        </th>
                        <th onclick="sortTablaCompras('proveedor')" data-col="proveedor">
                            Proveedor <span class="sort-icon">↕</span>
                        </th>
                        <th>Ítems</th>
                        <th onclick="sortTablaCompras('total')" data-col="total" style="text-align:right;">
                            Total <span class="sort-icon">↕</span>
                        </th>
                        <th>Estado</th>
                        <th style="text-align:center;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="compras-lista">
                    <tr>
                        <td colspan="7">
                            <div class="spinner-cell">
                                <div class="spinner"></div>
                                Cargando compras…
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="pagination-bar">
            <span class="pag-info" id="pag-info-co">Mostrando 0 de 0 registros</span>
            <div class="pag-btns" id="pag-btns-co"></div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════
     MODAL NUEVA COMPRA
═══════════════════════════════════════════════ --}}
<div id="modal-compra" style="display:none;" class="modal-backdrop" onclick="cerrarCompraBackdrop(event)">
    <div class="modal-compra">

        <div class="modal-head">
            <div>
                <p class="modal-head-title" id="co-title">Nueva compra</p>
                <p class="modal-head-sub">Registre la factura del proveedor y su ingreso a inventario</p>
            </div>
            <button onclick="cerrarCompra()"
                style="
                border:none;background:transparent;font-size:20px;cursor:pointer;
                color:#6B7280;padding:4px;border-radius:6px;line-height:1;
            ">✕</button>
        </div>

        <div class="modal-body">

            <p class="fac-section-title">Encabezado</p>
            <div class="co-grid">
                <div class="co-field">
                    <label>Prefijo *</label>
                    <input type="text" id="co-prefijo" placeholder="Ej: FC" maxlength="10" value="FC"
                        oninput="this.value=this.value.toUpperCase()">
                </div>
                <div class="co-field">
                    <label>N° Factura proveedor *</label>
                    <input type="text" id="co-numero" placeholder="Número de la factura del proveedor">
                </div>
                <div class="co-field">
                    <label>Fecha *</label>
                    <input type="date" id="co-fecha">
                </div>

                {{-- Buscador de proveedor: escribe NIT o razón social --}}
                <div class="co-field prov-combo">
                    <label>Proveedor *</label>
                    <div class="prov-combo-input-wrap" id="prov-input-wrap">
                        <span class="prov-icon">🔍</span>
                        <input type="text" id="co-proveedor-input" autocomplete="off"
                            placeholder="Escriba NIT o razón social…" oninput="buscarProveedor(this.value)"
                            onfocus="buscarProveedor(this.value)" onblur="cerrarResultadosProveedorDiferido()">
                        <button type="button" class="prov-clear" onclick="limpiarProveedor()"
                            title="Cambiar proveedor">✕</button>
                    </div>
                    <input type="hidden" id="co-proveedor-id">
                    <div class="prov-results" id="prov-results" style="display:none;"></div>
                    <p class="prov-hint" id="prov-hint">Empiece a escribir para buscar un proveedor.</p>
                </div>

                <div class="co-field" style="grid-column:1/-1;">
                    <label>Observaciones</label>
                    <textarea id="co-observaciones" rows="2" placeholder="Notas adicionales…" style="resize:vertical;"></textarea>
                </div>
            </div>

            <p class="fac-section-title">
                Productos
                <button class="btn-primary" onclick="agregarLineaCompra()"
                    style="font-size:11px;padding:4px 10px;">+ Agregar producto</button>
            </p>

            <div class="items-tbl-wrap">
                <table class="items-tbl" id="co-items-tbl">
                    <thead>
                        <tr>
                            <th style="width:28%;">Producto</th>
                            <th style="width:22%;">Bodega</th>
                            <th>Cant.</th>
                            <th>Costo unit.</th>
                            <th>IVA %</th>
                            <th style="text-align:right;">Subtotal</th>
                            <th style="width:36px;"></th>
                        </tr>
                    </thead>
                    <tbody id="co-items-body">
                        <tr id="co-items-empty-row">
                            <td colspan="7" style="text-align:center;color:#9CA3AF;padding:16px;font-size:12px;">
                                Sin productos. Haga clic en "+ Agregar producto".
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="totales-box">
                <div class="totales-row">
                    <span>Subtotal</span>
                    <span id="co-tot-subtotal">$ 0</span>
                </div>
                <div class="totales-row">
                    <span>IVA</span>
                    <span id="co-tot-iva">$ 0</span>
                </div>
                <div class="totales-row total-final">
                    <span>TOTAL</span>
                    <span id="co-tot-total">$ 0</span>
                </div>
            </div>

        </div>

        <div class="modal-foot">
            <label style="display:flex;align-items:center;gap:7px;font-size:12px;color:#374151;font-weight:500;">
                <input type="checkbox" id="co-confirmar" checked
                    style="width:15px;height:15px;accent-color:#1D4ED8;">
                Confirmar e ingresar a inventario
            </label>
            <div style="display:flex;gap:8px;">
                <button class="btn-outline" onclick="cerrarCompra()">Cancelar</button>
                <button class="btn-primary" id="co-btn-save" onclick="guardarCompra()">💾 Guardar compra</button>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     MODAL VER COMPRA (solo lectura)
═══════════════════════════════════════════════ --}}
<div id="modal-ver-compra" style="display:none;" class="modal-backdrop" onclick="cerrarVerCompraBackdrop(event)">
    <div class="modal-compra">
        <div class="modal-head">
            <div>
                <p class="modal-head-title" id="vco-title">Compra</p>
                <p class="modal-head-sub" id="vco-sub"></p>
            </div>
            <button onclick="cerrarVerCompra()"
                style="
                border:none;background:transparent;font-size:20px;cursor:pointer;
                color:#6B7280;padding:4px;border-radius:6px;line-height:1;
            ">✕</button>
        </div>
        <div class="modal-body" id="vco-body"></div>
        <div class="modal-foot">
            <div id="vco-foot-actions"></div>
            <button class="btn-outline" onclick="cerrarVerCompra()">Cerrar</button>
        </div>
    </div>
</div>

<script>
    /* ════════════════════════════════════════════════
       ESTADO GLOBAL
    ════════════════════════════════════════════════ */
    var CO = {
        datos: [],
        filtradas: [],
        paginaActual: 1,
        porPagina: 15,
        sortCol: 'fecha',
        sortAsc: false,
        items: [],
        catalogos: {
            productos: [],
            bodegas: [],
            proveedores: []
        },
        proveedorSel: null,
        editandoId: null,
        provIndexActivo: -1,
        provResultadosActuales: [],
    };

    /* ════════════════════════════════════════════════
       INICIALIZACIÓN
    ════════════════════════════════════════════════ */
    cargarCompras();

    /* ════════════════════════════════════════════════
       CARGA DE COMPRAS (listado)
    ════════════════════════════════════════════════ */
    function cargarCompras() {
        mostrarSpinnerCompras();
        fetch('/compras', {
                headers: {
                    Accept: 'application/json'
                }
            })
            .then(r => r.json())
            .then(function(res) {
                CO.datos = normalizarCompras(Array.isArray(res.data) ? res.data : (Array.isArray(res) ? res : []));
                aplicarFiltrosCompras();
            })
            .catch(function() {
                CO.datos = datosDemoCompras();
                aplicarFiltrosCompras();
            });
    }

    function normalizarCompras(lista) {
        return lista.map(function(c) {
            return {
                id: c.id,
                factura: (c.prefijo || '') + '-' + (c.numero_factura || c.numero || ''),
                fecha: c.fecha,
                proveedor: c.proveedor?.razon_social || c.proveedor?.nombre || '—',
                proveedor_nit: nitDeProveedor(c.proveedor),
                cantidad_items: (c.items && c.items.length) || c.cantidad_items || 0,
                total: Number(c.total || 0),
                estado: (c.estado || 'confirmada').toLowerCase(),
                observaciones: c.observaciones || '',
                items: c.items || [],
                proveedor_obj: c.proveedor || null,
            };
        });
    }

    /* El nombre del campo de identificación varía según el modelo de "terceros"
       (nit, numero_documento, documento, identificacion, cedula...). Se revisan
       las variantes más comunes para no mostrar "—" cuando el dato sí existe. */
    function nitDeProveedor(p) {
        if (!p) return '';
        return p.nit || p.NIT || p.numero_documento || p.num_documento || p.documento ||
            p.identificacion || p.cedula || p.dni || '';
    }

    function datosDemoCompras() {
        var proveedores = ['Distribuidora El Ahorro S.A.S', 'Comercializadora Andina Ltda',
            'Suministros del Valle', 'Importadora JR', 'Alimentos La Cosecha'
        ];
        var estados = ['confirmada', 'confirmada', 'confirmada', 'borrador', 'anulada'];
        var hoy = new Date();
        var out = [];
        for (var i = 1; i <= 26; i++) {
            var fecha = new Date(hoy);
            fecha.setDate(hoy.getDate() - Math.floor(Math.random() * 45));
            out.push({
                id: i,
                factura: 'FC-' + String(4000 + i),
                fecha: fecha.toISOString().slice(0, 10),
                proveedor: proveedores[i % proveedores.length],
                proveedor_nit: '9' + (10000000 + i * 137),
                cantidad_items: Math.floor(Math.random() * 6) + 1,
                total: Math.round((Math.random() * 4000000 + 100000) / 1000) * 1000,
                estado: estados[i % estados.length],
                observaciones: '',
                items: [],
            });
        }
        return out.sort(function(a, b) {
            return new Date(b.fecha) - new Date(a.fecha);
        });
    }

    /* ════════════════════════════════════════════════
       FILTROS / ORDEN / PAGINACIÓN
    ════════════════════════════════════════════════ */
    function aplicarFiltrosCompras() {
        var buscar = (document.getElementById('co-buscar').value || '').toLowerCase().trim();
        var desde = document.getElementById('co-desde').value;
        var hasta = document.getElementById('co-hasta').value;
        var estado = document.getElementById('co-estado').value;

        CO.filtradas = CO.datos.filter(function(c) {
            if (buscar) {
                var texto = (c.factura + ' ' + c.proveedor + ' ' + c.proveedor_nit).toLowerCase();
                if (!texto.includes(buscar)) return false;
            }
            if (desde && c.fecha < desde) return false;
            if (hasta && c.fecha > hasta) return false;
            if (estado && c.estado !== estado) return false;
            return true;
        });

        CO.paginaActual = 1;
        sortTablaActualCompras();
        renderTablaCompras();
        actualizarMetricasCompras();
    }

    function limpiarFiltrosCompras() {
        document.getElementById('co-buscar').value = '';
        document.getElementById('co-desde').value = '';
        document.getElementById('co-hasta').value = '';
        document.getElementById('co-estado').value = '';
        aplicarFiltrosCompras();
    }

    function sortTablaCompras(col) {
        if (CO.sortCol === col) CO.sortAsc = !CO.sortAsc;
        else {
            CO.sortCol = col;
            CO.sortAsc = false;
        }
        sortTablaActualCompras();
        renderTablaCompras();
        document.querySelectorAll('table.compras-tbl thead th[data-col]').forEach(function(th) {
            th.classList.toggle('sorted', th.dataset.col === col);
            var icon = th.querySelector('.sort-icon');
            if (icon) icon.textContent = th.dataset.col === col ? (CO.sortAsc ? '↑' : '↓') : '↕';
        });
    }

    function sortTablaActualCompras() {
        var col = CO.sortCol,
            asc = CO.sortAsc;
        CO.filtradas.sort(function(a, b) {
            var va = a[col] ?? '',
                vb = b[col] ?? '';
            if (typeof va === 'number') return asc ? va - vb : vb - va;
            return asc ? String(va).localeCompare(String(vb)) : String(vb).localeCompare(String(va));
        });
    }

    function renderTablaCompras() {
        var tbody = document.getElementById('compras-lista');
        var total = CO.filtradas.length;
        var desde = (CO.paginaActual - 1) * CO.porPagina;
        var pagina = CO.filtradas.slice(desde, desde + CO.porPagina);

        document.getElementById('pag-info-co').textContent =
            'Mostrando ' + Math.min(pagina.length, total) + ' de ' + total + ' registros';

        if (pagina.length === 0) {
            tbody.innerHTML = '<tr><td colspan="7">' +
                '<div class="spinner-cell">📭 Aún no hay compras registradas</div></td></tr>';
            renderPaginacionCompras(total);
            return;
        }

        tbody.innerHTML = pagina.map(function(c) {
            var trCls = c.estado === 'anulada' ? 'anulada' : '';
            var badge = c.estado === 'confirmada' ?
                '<span class="badge badge-green"><span class="dot"></span>Confirmada</span>' :
                (c.estado === 'anulada' ?
                    '<span class="badge badge-red"><span class="dot"></span>Anulada</span>' :
                    '<span class="badge badge-gray"><span class="dot"></span>Borrador</span>');

            var btnVer = '<button class="act-btn view" data-tip="Ver compra" onclick="verCompra(' + c.id +
                ')">👁️</button>';
            var btnEditar = '';
            var btnEstado = '';
            if (c.estado === 'borrador') {
                btnEditar = '<button class="act-btn edit" data-tip="Editar compra" onclick="editarCompra(' + c.id + ')">&#9998;</button>';
                btnEstado = '<button class="act-btn reg" data-tip="Registrar e ingresar inventario" ' +
                    'onclick="registrarCompra(' + c.id + ')">✅</button>';
            } else if (c.estado === 'confirmada') {
                btnEstado = '<button class="act-btn undo" data-tip="Revertir a borrador para editar" ' +
                    'onclick="revertirRegistroCompra(' + c.id + ')">↩️</button>' +
                    '<button class="act-btn rev" data-tip="Anular compra" ' +
                    'onclick="anularCompra(' + c.id + ')">🚫</button>';
            } else if (c.estado === 'anulada') {
                btnEstado = '<button class="act-btn undo" data-tip="Revertir anulación" ' +
                    'onclick="revertirCompra(' + c.id + ')">↩️</button>';
            }

            var btnDel = '<button class="act-btn del" data-tip="Eliminar compra" ' +
                'onclick="eliminarCompra(' + c.id + ',\'' + esc(c.factura) + '\')">🗑️</button>';

            return '<tr class="' + trCls + '" data-id="' + c.id + '">' +
                '<td><span class="td-mono">' + esc(c.factura) + '</span></td>' +
                '<td>' + fmtFechaCO(c.fecha) + '</td>' +
                '<td class="td-proveedor">' +
                '<div class="prov-nombre" title="' + esc(c.proveedor) + '">' + esc(c.proveedor) + '</div>' +
                (c.proveedor_nit ? '<div class="prov-nit">NIT ' + esc(c.proveedor_nit) + '</div>' : '') +
                '</td>' +
                '<td style="color:#6B7280;">' + (c.cantidad_items || 0) + '</td>' +
                '<td class="td-money">' + fmtMoneyCO(c.total) + '</td>' +
                '<td>' + badge + '</td>' +
                '<td><div class="tbl-actions">' + btnVer + btnEditar + btnEstado + btnDel + '</div></td>' +
                '</tr>';
        }).join('');

        renderPaginacionCompras(total);
    }

    function renderPaginacionCompras(total) {
        var totalPags = Math.max(1, Math.ceil(total / CO.porPagina));
        var actual = CO.paginaActual;
        var btns = document.getElementById('pag-btns-co');
        var html = '';

        html += '<button class="pag-btn" onclick="irPaginaCompras(' + (actual - 1) + ')"' +
            (actual === 1 ? ' disabled' : '') + '>‹</button>';

        var desde = Math.max(1, actual - 2);
        var hasta = Math.min(totalPags, desde + 4);
        desde = Math.max(1, hasta - 4);

        if (desde > 1) html += '<button class="pag-btn" onclick="irPaginaCompras(1)">1</button>' +
            (desde > 2 ? '<span style="padding:0 4px;color:#9CA3AF;">…</span>' : '');

        for (var p = desde; p <= hasta; p++) {
            html += '<button class="pag-btn' + (p === actual ? ' active' : '') + '" onclick="irPaginaCompras(' + p +
                ')">' + p + '</button>';
        }

        if (hasta < totalPags) {
            html += (hasta < totalPags - 1 ? '<span style="padding:0 4px;color:#9CA3AF;">…</span>' : '') +
                '<button class="pag-btn" onclick="irPaginaCompras(' + totalPags + ')">' + totalPags + '</button>';
        }

        html += '<button class="pag-btn" onclick="irPaginaCompras(' + (actual + 1) + ')"' +
            (actual === totalPags ? ' disabled' : '') + '>›</button>';

        btns.innerHTML = html;
    }

    function irPaginaCompras(p) {
        var total = CO.filtradas.length;
        var totalPags = Math.max(1, Math.ceil(total / CO.porPagina));
        CO.paginaActual = Math.max(1, Math.min(p, totalPags));
        renderTablaCompras();
    }

    function mostrarSpinnerCompras() {
        document.getElementById('compras-lista').innerHTML =
            '<tr><td colspan="7"><div class="spinner-cell">' +
            '<div class="spinner"></div>Cargando compras…</div></td></tr>';
    }

    /* ════════════════════════════════════════════════
       MÉTRICAS
    ════════════════════════════════════════════════ */
    function actualizarMetricasCompras() {
        // Las tarjetas reflejan exactamente el período y filtros aplicados en la tabla.
        var compras = CO.filtradas;
        var total = compras.length;

        var anuladas = compras.filter(function(c) {
            return c.estado === 'anulada';
        }).length;

        var borrador = compras.filter(function(c) {
            return c.estado === 'borrador';
        }).length;

        var comprasValidas = compras.filter(function(c) {
            return c.estado !== 'anulada';
        });

        var sumaPeriodo = comprasValidas.reduce(function(s, c) {
            return s + c.total;
        }, 0);
        var promedioCompra = comprasValidas.length ? sumaPeriodo / comprasValidas.length : 0;

        document.getElementById('cm-total').textContent = total;
        document.getElementById('cm-total-sub').textContent = (total - anuladas) + ' vigentes';

        document.getElementById('cm-total-valor').textContent = fmtMoneyCO(sumaPeriodo);
        document.getElementById('cm-total-valor-sub').textContent = comprasValidas.length + ' compras vigentes';

        document.getElementById('cm-promedio').textContent = fmtMoneyCO(promedioCompra);
        document.getElementById('cm-promedio-sub').textContent = comprasValidas.length ? 'Según período filtrado' :
            'Sin compras vigentes';

        document.getElementById('cm-borrador').textContent = borrador;

        document.getElementById('cm-anuladas').textContent = anuladas;
        document.getElementById('cm-anuladas-sub').textContent =
            Math.round(anuladas / Math.max(1, total) * 100) + '% del total';
    }

    /* ════════════════════════════════════════════════
       VER COMPRA (detalle)
    ════════════════════════════════════════════════ */
    function verCompra(id) {
        var c = CO.datos.find(x => x.id === id);
        if (!c) return;

        document.getElementById('vco-title').textContent = c.factura;
        document.getElementById('vco-sub').innerHTML = 'Cargando detalle…';
        renderVerCompra(c);
        document.getElementById('modal-ver-compra').style.display = 'flex';

        fetch('/compras/' + id, {
                headers: {
                    Accept: 'application/json'
                }
            })
            .then(r => r.json())
            .then(function(det) {
                var full = det.data || det;
                c.items = full.detalles || full.items || c.items || [];
                c.proveedor_obj = full.proveedor || c.proveedor_obj;
                c.proveedor = full.proveedor?.razon_social || full.proveedor?.nombre || c.proveedor;
                c.proveedor_nit = nitDeProveedor(full.proveedor) || c.proveedor_nit;
                c.observaciones = full.observaciones ?? c.observaciones;
                c.total = Number(full.total ?? c.total);
                c.estado = (full.estado || c.estado || '').toLowerCase();
                renderVerCompra(c);
            })
            .catch(function() {
                /* Sin endpoint de detalle disponible: se muestra lo que ya se tenía en la lista */
                renderVerCompra(c);
            });
    }

    function renderVerCompra(c) {
        var badge = c.estado === 'confirmada' ?
            '<span class="badge badge-green"><span class="dot"></span>Confirmada</span>' :
            (c.estado === 'anulada' ?
                '<span class="badge badge-red"><span class="dot"></span>Anulada</span>' :
                '<span class="badge badge-gray"><span class="dot"></span>Borrador</span>');

        document.getElementById('vco-sub').innerHTML =
            fmtFechaCO(c.fecha) + ' · ' + esc(c.proveedor) + ' &nbsp;' + badge;

        var filasItems = (c.items || []).map(function(it) {
            var sub = (it.cantidad || 0) * (it.costo_unitario || it.costo || 0);
            return '<tr>' +
                '<td>' + esc(it.producto?.descripcion || it.producto?.nombre || it.producto_nombre || 'Producto') + '</td>' +
                '<td style="text-align:center;">' + (it.cantidad || 0) + '</td>' +
                '<td style="text-align:right;">' + fmtMoneyCO(it.costo_unitario || it.costo || 0) + '</td>' +
                '<td style="text-align:center;">' + (it.iva_porcentaje || it.iva || 0) + '%</td>' +
                '<td style="text-align:right;font-weight:600;">' + fmtMoneyCO(sub) + '</td>' +
                '</tr>';
        }).join('') || '<tr><td colspan="5" style="text-align:center;color:#9CA3AF;">' +
            (c.items ? 'Sin ítems registrados' : 'Cargando ítems…') + '</td></tr>';

        document.getElementById('vco-body').innerHTML =
            '<div class="co-grid" style="grid-template-columns:2fr 1fr;">' +
            fldCO('Proveedor', c.proveedor + (c.proveedor_nit ? ' · NIT ' + c.proveedor_nit : '')) +
            fldCO('Fecha', fmtFechaCO(c.fecha)) +
            '</div>' +
            (c.observaciones ?
                '<div class="co-field" style="margin-bottom:6px;">' +
                '<label>Observaciones</label>' +
                '<div style="padding:7px 10px;background:#F9FAFB;border:1px solid #E5E7EB;border-radius:7px;font-size:13px;color:#111827;">' +
                esc(c.observaciones) + '</div></div>' : '') +
            '<p class="fac-section-title">Ítems</p>' +
            '<div class="items-tbl-wrap"><table class="items-tbl">' +
            '<thead><tr><th>Producto</th><th>Cant.</th><th>Costo</th><th>IVA</th>' +
            '<th style="text-align:right;">Subtotal</th></tr></thead><tbody>' + filasItems + '</tbody></table></div>' +
            '<div class="totales-box"><div class="totales-row total-final"><span>TOTAL</span><span>' +
            fmtMoneyCO(c.total) + '</span></div></div>';

        /* Pie del modal: acción principal según el estado de la compra */
        var foot = document.getElementById('vco-foot-actions');
        if (c.estado === 'borrador') {
            foot.innerHTML = '<button class="btn-outline" onclick="editarCompra(' + c.id +
                ')">&#9998; Editar</button><button class="btn-primary" onclick="registrarCompra(' + c.id +
                ')">✅ Registrar compra</button>';
        } else if (c.estado === 'confirmada') {
            foot.innerHTML = '<button class="btn-outline" onclick="revertirRegistroCompra(' + c.id +
                ')">↩️ Revertir a borrador</button><button class="btn-outline danger" onclick="anularCompra(' + c.id +
                ')">🚫 Anular compra</button>';
        } else {
            foot.innerHTML = '<button class="btn-outline" onclick="revertirCompra(' + c.id +
                ')">↩️ Revertir anulación</button>' +
                '<button class="btn-outline danger" onclick="eliminarCompra(' + c.id + ',\'' + esc(c.factura) +
                '\')" style="margin-left:8px;">🗑️ Eliminar</button>';
        }
    }

    function fldCO(label, val) {
        return '<div class="co-field">' +
            '<label>' + label + '</label>' +
            '<div style="padding:7px 10px;background:#F9FAFB;border:1px solid #E5E7EB;border-radius:7px;font-size:13px;color:#111827;">' +
            esc(String(val)) + '</div></div>';
    }

    function cerrarVerCompra() {
        document.getElementById('modal-ver-compra').style.display = 'none';
    }

    function cerrarVerCompraBackdrop(e) {
        if (e.target === document.getElementById('modal-ver-compra')) cerrarVerCompra();
    }

    /* ════════════════════════════════════════════════
       MODAL NUEVA COMPRA
    ════════════════════════════════════════════════ */
    function abrirCompra() {
        CO.items = [];
        CO.proveedorSel = null;
        CO.editandoId = null;

        document.getElementById('co-title').textContent = 'Nueva compra';
        document.getElementById('co-prefijo').value = 'FC';
        document.getElementById('co-numero').value = '';
        document.getElementById('co-fecha').value = new Date().toISOString().slice(0, 10);
        document.getElementById('co-observaciones').value = '';
        document.getElementById('co-confirmar').checked = true;
        limpiarProveedor();

        renderItemsCompra();
        calcularTotalesCompra();
        document.getElementById('modal-compra').style.display = 'flex';

        if (!CO.catalogos.productos.length && !CO.catalogos.proveedores.length) {
            cargarCatalogosCompra().then(renderItemsCompra);
        }
    }

    function cerrarCompra() {
        document.getElementById('modal-compra').style.display = 'none';
    }

    function cerrarCompraBackdrop(e) {
        if (e.target === document.getElementById('modal-compra')) cerrarCompra();
    }

    function cargarCatalogosCompra() {
        return Promise.all([
            fetch('/terceros', {
                headers: {
                    Accept: 'application/json'
                }
            }).then(r => r.json()).catch(() => null),
            fetch('/productos', {
                headers: {
                    Accept: 'application/json'
                }
            }).then(r => r.json()).catch(() => null),
            fetch('/bodegas', {
                headers: {
                    Accept: 'application/json'
                }
            }).then(r => r.json()).catch(() => null),
        ]).then(function(x) {
            var terceros = x[0] ? (x[0].data || x[0]) : null;
            var productos = x[1] ? (x[1].data || x[1]) : null;
            var bodegas = x[2] ? (x[2].data || x[2]) : null;

            CO.catalogos.proveedores = (terceros && terceros.length) ? terceros : datosDemoProveedores();
            CO.catalogos.productos = (productos && productos.length) ? productos : datosDemoProductos();
            CO.catalogos.bodegas = (bodegas && bodegas.length) ? bodegas : datosDemoBodegas();
        }).catch(function() {
            CO.catalogos.proveedores = datosDemoProveedores();
            CO.catalogos.productos = datosDemoProductos();
            CO.catalogos.bodegas = datosDemoBodegas();
        });
    }

    function editarCompra(id) {
        fetch('/compras/' + id, {
                headers: { Accept: 'application/json' }
            })
            .then(function(r) {
                return r.json().then(function(data) { return { ok: r.ok, data: data }; });
            })
            .then(function(res) {
                if (!res.ok) throw new Error(res.data.message || 'No se pudo cargar la compra');
                var compra = res.data.data || res.data;
                if (compra.estado !== 'borrador') throw new Error('Solo se pueden editar compras en borrador');

                var catalogosListos = CO.catalogos.productos.length && CO.catalogos.bodegas.length && CO.catalogos.proveedores.length;
                return (catalogosListos ? Promise.resolve() : cargarCatalogosCompra()).then(function() {
                    CO.editandoId = compra.id;
                    CO.items = (compra.detalles || []).map(function(item) {
                        return {
                            producto_id: item.producto_id,
                            bodega_id: item.bodega_id,
                            cantidad: Number(item.cantidad),
                            costo_unitario: Number(item.costo_unitario),
                            iva_porcentaje: Number(item.iva_porcentaje || 0)
                        };
                    });
                    document.getElementById('co-title').textContent = 'Editar compra ' + compra.prefijo + '-' + compra.numero_factura;
                    document.getElementById('co-prefijo').value = compra.prefijo || '';
                    document.getElementById('co-numero').value = compra.numero_factura || '';
                    document.getElementById('co-fecha').value = String(compra.fecha || '').slice(0, 10);
                    document.getElementById('co-observaciones').value = compra.observaciones || '';
                    document.getElementById('co-confirmar').checked = false;
                    seleccionarProveedor(compra.proveedor_id);
                    document.getElementById('co-btn-save').textContent = 'Guardar cambios';
                    renderItemsCompra();
                    cerrarVerCompra();
                    document.getElementById('modal-compra').style.display = 'flex';
                });
            })
            .catch(function(e) { notifCO(e.message, 'error'); });
    }

    function datosDemoProveedores() {
        return [{
                id: 1,
                razon_social: 'Distribuidora El Ahorro S.A.S',
                nit: '900123456-1'
            },
            {
                id: 2,
                razon_social: 'Comercializadora Andina Ltda',
                nit: '901987654-2'
            },
            {
                id: 3,
                razon_social: 'Suministros del Valle',
                nit: '800456123-9'
            },
            {
                id: 4,
                razon_social: 'Importadora JR',
                nit: '901222333-4'
            },
            {
                id: 5,
                razon_social: 'Alimentos La Cosecha',
                nit: '890555777-0'
            },
        ];
    }

    function datosDemoProductos() {
        return [{
            id: 1,
            nombre: 'Arroz x 500g',
            iva_ventas: 0
        }, {
            id: 2,
            nombre: 'Aceite vegetal 1L',
            iva_ventas: 19
        }, {
            id: 3,
            nombre: 'Panela x 1kg',
            iva_ventas: 0
        }, {
            id: 4,
            nombre: 'Gaseosa 1.5L',
            iva_ventas: 19
        }];
    }

    function datosDemoBodegas() {
        return [{
            id: 1,
            nombre: 'Bodega Principal'
        }, {
            id: 2,
            nombre: 'Bodega Sucursal'
        }];
    }

    /* ── Buscador de proveedor (visual: filtra el catálogo cargado en memoria) ── */
    function buscarProveedor(q) {
        q = (q || '').toLowerCase().trim();
        var box = document.getElementById('prov-results');

        if (CO.proveedorSel && document.getElementById('co-proveedor-input').value !==
            (CO.proveedorSel.razon_social || CO.proveedorSel.nombre)) {
            // el usuario está editando el texto tras haber seleccionado: invalida selección
            limpiarProveedorSeleccionSoloEstado();
        }

        if (!q) {
            box.style.display = 'none';
            return;
        }

        var resultados = (CO.catalogos.proveedores || []).filter(function(p) {
            var nombre = (p.razon_social || p.nombre || '').toLowerCase();
            var nit = String(p.nit || '').toLowerCase();
            return nombre.includes(q) || nit.includes(q);
        }).slice(0, 6);

        CO.provResultadosActuales = resultados;
        CO.provIndexActivo = -1;

        if (!resultados.length) {
            box.innerHTML = '<div class="prov-results-empty">Sin coincidencias para "' + esc(q) + '"</div>';
            box.style.display = 'block';
            return;
        }

        box.innerHTML = resultados.map(function(p, idx) {
            return '<div class="prov-result-item" data-idx="' + idx + '" ' +
                'onmousedown="seleccionarProveedor(' + p.id + ')">' +
                '<span class="prov-result-nombre">' + esc(p.razon_social || p.nombre) + '</span>' +
                '<span class="prov-result-nit">NIT ' + esc(p.nit || '—') + '</span>' +
                '</div>';
        }).join('');
        box.style.display = 'block';
    }

    function seleccionarProveedor(id) {
        var p = (CO.catalogos.proveedores || []).find(x => x.id === id);
        if (!p) return;
        CO.proveedorSel = p;

        var input = document.getElementById('co-proveedor-input');
        input.value = p.razon_social || p.nombre;
        input.classList.add('selected');
        document.getElementById('co-proveedor-id').value = p.id;
        document.getElementById('prov-input-wrap').classList.add('has-selection');
        document.getElementById('prov-results').style.display = 'none';

        var hint = document.getElementById('prov-hint');
        hint.textContent = '✓ NIT ' + (p.nit || '—') + ' seleccionado';
        hint.classList.add('ok');
    }

    function limpiarProveedor() {
        CO.proveedorSel = null;
        var input = document.getElementById('co-proveedor-input');
        input.value = '';
        input.classList.remove('selected');
        document.getElementById('co-proveedor-id').value = '';
        document.getElementById('prov-input-wrap').classList.remove('has-selection');
        document.getElementById('prov-results').style.display = 'none';
        var hint = document.getElementById('prov-hint');
        hint.textContent = 'Empiece a escribir para buscar un proveedor.';
        hint.classList.remove('ok');
    }

    function limpiarProveedorSeleccionSoloEstado() {
        CO.proveedorSel = null;
        document.getElementById('co-proveedor-id').value = '';
        document.getElementById('co-proveedor-input').classList.remove('selected');
        document.getElementById('prov-input-wrap').classList.remove('has-selection');
        var hint = document.getElementById('prov-hint');
        hint.textContent = 'Empiece a escribir para buscar un proveedor.';
        hint.classList.remove('ok');
    }

    function cerrarResultadosProveedorDiferido() {
        setTimeout(function() {
            document.getElementById('prov-results').style.display = 'none';
        }, 150);
    }

    /* ── Ítems de la compra ── */
    function agregarLineaCompra() {
        CO.items.push({
            producto_id: '',
            bodega_id: '',
            cantidad: 1,
            costo_unitario: 0,
            iva_porcentaje: 0,
        });
        renderItemsCompra();
    }

    function renderItemsCompra() {
        var body = document.getElementById('co-items-body');

        if (CO.items.length === 0) {
            body.innerHTML = '<tr id="co-items-empty-row"><td colspan="7" ' +
                'style="text-align:center;color:#9CA3AF;padding:16px;font-size:12px;">' +
                'Sin productos. Haga clic en "+ Agregar producto".</td></tr>';
            calcularTotalesCompra();
            return;
        }

        body.innerHTML = CO.items.map(function(it, idx) {
            var sub = (it.cantidad || 0) * (it.costo_unitario || 0);

            var optsProd = '<option value="">— Producto —</option>' +
                CO.catalogos.productos.map(function(p) {
                    var sel = String(p.id) === String(it.producto_id) ? 'selected' : '';
                    return '<option value="' + p.id + '" data-iva="' + (p.iva_ventas || 0) + '" ' + sel + '>' +
                        esc(p.descripcion || p.nombre) + '</option>';
                }).join('');

            var optsBod = '<option value="">— Bodega —</option>' +
                CO.catalogos.bodegas.map(function(b) {
                    var sel = String(b.id) === String(it.bodega_id) ? 'selected' : '';
                    return '<option value="' + b.id + '" ' + sel + '>' + esc(b.descripcion || b.nombre) +
                        '</option>';
                }).join('');

            return '<tr>' +
                '<td><select class="items-input" onchange="itemCompraChange(' + idx +
                ',\'producto_id\',this.value,this)">' + optsProd + '</select></td>' +
                '<td><select class="items-input" onchange="itemCompraChange(' + idx +
                ',\'bodega_id\',this.value)">' + optsBod + '</select></td>' +
                '<td><input type="number" class="items-input" min="1" value="' + it.cantidad +
                '" onchange="itemCompraChange(' + idx + ',\'cantidad\',+this.value)"></td>' +
                '<td><input type="number" class="items-input" min="0" value="' + it.costo_unitario +
                '" onchange="itemCompraChange(' + idx + ',\'costo_unitario\',+this.value)"></td>' +
                '<td><input type="number" class="items-input" min="0" value="' + it.iva_porcentaje +
                '" onchange="itemCompraChange(' + idx + ',\'iva_porcentaje\',+this.value)"></td>' +
                '<td style="text-align:right;font-weight:600;font-size:12px;">' + fmtMoneyCO(sub) + '</td>' +
                '<td><button onclick="eliminarItemCompra(' + idx + ')" ' +
                'style="border:none;background:transparent;cursor:pointer;font-size:14px;color:#DC2626;">✕</button></td>' +
                '</tr>';
        }).join('');

        calcularTotalesCompra();
    }

    function itemCompraChange(idx, campo, val, selEl) {
        CO.items[idx][campo] = val;
        if (campo === 'producto_id' && selEl) {
            var opt = selEl.options[selEl.selectedIndex];
            var ivaDefault = Number(opt?.dataset?.iva || 0);
            if (!CO.items[idx].iva_porcentaje) CO.items[idx].iva_porcentaje = ivaDefault;
        }
        renderItemsCompra();
    }

    function eliminarItemCompra(idx) {
        CO.items.splice(idx, 1);
        renderItemsCompra();
    }

    function calcularTotalesCompra() {
        var subtotal = 0,
            iva = 0;
        CO.items.forEach(function(it) {
            var base = (it.cantidad || 0) * (it.costo_unitario || 0);
            subtotal += base;
            iva += base * (it.iva_porcentaje || 0) / 100;
        });
        document.getElementById('co-tot-subtotal').textContent = fmtMoneyCO(subtotal);
        document.getElementById('co-tot-iva').textContent = fmtMoneyCO(iva);
        document.getElementById('co-tot-total').textContent = fmtMoneyCO(subtotal + iva);
    }

    /* ── Guardar ── */
    function guardarCompra() {
        var prefijo = document.getElementById('co-prefijo').value.trim();
        var numero = document.getElementById('co-numero').value.trim();
        var fecha = document.getElementById('co-fecha').value;
        var proveedorId = document.getElementById('co-proveedor-id').value;

        if (!prefijo) {
            notifCO('⚠️ El prefijo es requerido', 'error');
            return;
        }
        if (!numero) {
            notifCO('⚠️ Ingrese el número de factura del proveedor', 'error');
            return;
        }
        if (!fecha) {
            notifCO('⚠️ Seleccione la fecha', 'error');
            return;
        }
        if (!proveedorId) {
            notifCO('⚠️ Busque y seleccione un proveedor de la lista', 'error');
            return;
        }
        if (CO.items.length === 0) {
            notifCO('⚠️ Agregue al menos un producto', 'error');
            return;
        }

        var body = {
            prefijo: prefijo,
            numero_factura: numero,
            fecha: fecha,
            proveedor_id: proveedorId,
            observaciones: document.getElementById('co-observaciones').value,
            items: CO.items,
            confirmar: document.getElementById('co-confirmar').checked,
        };

        var btn = document.getElementById('co-btn-save');
        btn.disabled = true;
        btn.textContent = '⏳ Guardando…';

        var esEdicion = Boolean(CO.editandoId);
        fetch(esEdicion ? '/compras/' + CO.editandoId : '/compras', {
                method: esEdicion ? 'PUT' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content,
                    Accept: 'application/json'
                },
                body: JSON.stringify(body)
            })
            .then(r => r.json().then(x => ({
                ok: r.ok,
                x
            })))
            .then(function(o) {
                if (!o.ok) throw new Error(Object.values(o.x.errors || {})[0]?.[0] || o.x.message);
                notifCO(esEdicion ? 'Compra actualizada correctamente' : 'Compra registrada correctamente', 'success');
                cerrarCompra();
                cargarCompras();
            })
            .catch(function(e) {
                notifCO(e.message || 'No se pudo guardar la compra', 'error');
            })
            .finally(function() {
                btn.disabled = false;
                btn.textContent = CO.editandoId ? 'Guardar cambios' : 'Guardar compra';
            });
    }

    function registrarCompra(id) {
        fetch('/compras/' + id + '/registrar', {method: 'POST', headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, Accept: 'application/json'}})
            .then(function(r) { return r.json().then(function(data) { return {ok: r.ok, data: data}; }); })
            .then(function(res) { if (!res.ok) throw new Error(res.data.message || 'No se pudo registrar'); cargarCompras(); })
            .catch(function(e) { notifCO(e.message, 'error'); });
    }
    function anularCompra(id) { cambiarEstadoCompra(id, 'anular'); }
    function revertirRegistroCompra(id) { cambiarEstadoCompra(id, 'revertir-registro'); }
    function revertirCompra(id) { cambiarEstadoCompra(id, 'revertir'); }
    function cambiarEstadoCompra(id, accion) {
        fetch('/compras/' + id + '/' + accion, {method: 'POST', headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, Accept: 'application/json'}})
            .then(function(r) { return r.json().then(function(data) { return {ok: r.ok, data: data}; }); })
            .then(function(res) { if (!res.ok) throw new Error(res.data.message || 'No se pudo actualizar'); cerrarVerCompra(); cargarCompras(); })
            .catch(function(e) { notifCO(e.message, 'error'); });
    }

    function eliminarCompra(id, codigo) {
        var confirmar = (typeof Swal !== 'undefined') ? Swal.fire({
            title: '¿Eliminar la compra ' + codigo + '?',
            text: 'Se eliminará permanentemente del sistema. Esta acción NO se puede deshacer.',
            icon: 'error',
            showCancelButton: true,
            confirmButtonColor: '#DC2626',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
        }) : Promise.resolve({ isConfirmed: window.confirm('¿Eliminar la compra ' + codigo + '? Esta acción no se puede deshacer.') });

        confirmar.then(function(r) {
            if (!r.isConfirmed) return;
            var token = document.querySelector('meta[name="csrf-token"]')?.content;
            fetch('/compras/' + id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': token,
                        Accept: 'application/json'
                    }
                })
                .then(function(res) {
                    return res.json().then(function(data) { return { ok: res.ok, data: data }; });
                })
                .then(function(o) {
                    if (!o.ok) throw new Error(o.data.message || 'No se pudo eliminar la compra');
                    notifCO('🗑️ Compra ' + codigo + ' eliminada', 'success');
                    cerrarVerCompra();
                    cargarCompras();
                })
                .catch(function() {
                    /* Sin endpoint disponible: simulación local (demo) */
                    notifCO('🗑️ Compra ' + codigo + ' eliminada (demo)', 'success');
                    CO.datos = CO.datos.filter(function(c) { return c.id !== id; });
                    cerrarVerCompra();
                    aplicarFiltrosCompras();
                });
        });
    }

    /* ════════════════════════════════════════════════
       UTILIDADES
    ════════════════════════════════════════════════ */
    function fmtMoneyCO(n) {
        return '$ ' + Number(n || 0).toLocaleString('es-CO', {
            maximumFractionDigits: 0
        });
    }

    function fmtFechaCO(s) {
        if (!s) return '—';
        var parts = String(s).slice(0, 10).split('-');
        return parts.length === 3 ? parts[2] + '/' + parts[1] + '/' + parts[0] : s;
    }

    function esc(s) {
        return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g,
            '&quot;');
    }

    function notifCO(msg, tipo) {
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
        setTimeout(() => el.remove(), 3500);
    }
</script>
