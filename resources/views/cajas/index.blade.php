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

    table.cajas-tbl {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
    }

    table.cajas-tbl thead {
        background: #F8FAFC;
        border-bottom: 1px solid #EAECF0;
    }

    table.cajas-tbl thead th {
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

    table.cajas-tbl thead th:hover {
        color: #1D4ED8;
    }

    table.cajas-tbl thead th .sort-icon {
        display: inline-block;
        margin-left: 4px;
        opacity: 0.4;
        font-size: 10px;
    }

    table.cajas-tbl thead th.sorted .sort-icon {
        opacity: 1;
        color: #1D4ED8;
    }

    table.cajas-tbl tbody tr {
        border-bottom: 1px solid #F3F4F6;
        transition: background 0.1s;
    }

    table.cajas-tbl tbody tr:last-child {
        border-bottom: none;
    }

    table.cajas-tbl tbody tr:hover {
        background: #F8FAFC;
    }

    table.cajas-tbl tbody tr.inactiva {
        opacity: 0.6;
    }

    table.cajas-tbl td {
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

    .act-btn.edit:hover {
        background: #ECFDF5;
        color: #059669;
    }

    .act-btn.toggle:hover {
        background: #FFFBEB;
        color: #D97706;
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

    /* ── Modal Caja ── */
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

    .modal-caja {
        background: #fff;
        border-radius: 16px;
        width: 100%;
        max-width: 560px;
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
    .fac-field select {
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
    .fac-field select:focus {
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
    }

    .estado-toggle-box {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
        background: #F8FAFC;
        border: 1px solid #EAECF0;
        border-radius: 10px;
        padding: 12px 14px;
        margin-top: 4px;
    }

    .estado-toggle-box p.tt {
        font-size: 12.5px;
        font-weight: 600;
        color: #111827;
    }

    .estado-toggle-box p.st {
        font-size: 11px;
        color: #6B7280;
        margin-top: 1px;
    }

    .cj-switch {
        position: relative;
        width: 40px;
        height: 22px;
        flex-shrink: 0;
    }

    .cj-switch input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .cj-slider {
        position: absolute;
        inset: 0;
        cursor: pointer;
        background: #E5E7EB;
        border-radius: 999px;
        transition: .2s;
    }

    .cj-slider::before {
        content: "";
        position: absolute;
        width: 16px;
        height: 16px;
        left: 3px;
        top: 3px;
        background: #fff;
        border-radius: 999px;
        transition: .2s;
        box-shadow: 0 1px 2px rgba(0, 0, 0, .2);
    }

    .cj-switch input:checked+.cj-slider {
        background: #059669;
    }

    .cj-switch input:checked+.cj-slider::before {
        transform: translateX(18px);
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

        table.cajas-tbl thead th:nth-child(n+6) {
            display: none;
        }

        table.cajas-tbl tbody td:nth-child(n+6) {
            display: none;
        }
    }
</style>

<div id="view-cajas">

    {{-- ── ENCABEZADO ── --}}
    <div class="sec-header">
        <div>
            <p class="sec-title">🖥️ Cajas / Puntos de Venta</p>
            <p class="sec-subtitle">Configuración operativa de terminales POS</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button class="btn-outline" onclick="exportarCajas()">
                ⬇️ Exportar
            </button>
            <button class="btn-primary" onclick="abrirModalCaja()">
                ＋ Nueva Caja
            </button>
        </div>
    </div>

    {{-- ── MÉTRICAS ── --}}
    <div class="metrics-row" id="metricas-cajas">
        <div class="metric-card" style="--accent:#1D4ED8">
            <p class="metric-label">Total Cajas</p>
            <p class="metric-value" id="m-total">—</p>
            <p class="metric-sub" id="m-total-sub">Cargando…</p>
        </div>
        <div class="metric-card" style="--accent:#059669">
            <p class="metric-label">Activas</p>
            <p class="metric-value" id="m-activas">—</p>
            <p class="metric-sub" id="m-activas-sub">Disponibles para facturar</p>
        </div>
        <div class="metric-card" style="--accent:#DC2626">
            <p class="metric-label">Inactivas</p>
            <p class="metric-value" id="m-inactivas">—</p>
            <p class="metric-sub">Fuera de servicio</p>
        </div>
        <div class="metric-card" style="--accent:#D97706">
            <p class="metric-label">Sin cajero asignado</p>
            <p class="metric-value" id="m-sin-cajero">—</p>
            <p class="metric-sub">Requieren asignación</p>
        </div>
    </div>

    {{-- ── FILTROS ── --}}
    <div class="filter-bar">
        <div class="fi-group" style="flex:2;min-width:200px;">
            <span class="fi-label">🔍</span>
            <input class="fi-input" type="text" id="fi-buscar" placeholder="Nombre, prefijo, bodega, cajero…"
                oninput="aplicarFiltrosCajas()">
        </div>
        <div class="fi-group">
            <select class="fi-select" id="fi-estado" onchange="aplicarFiltrosCajas()">
                <option value="">Todos los estados</option>
                <option value="activa">Activa</option>
                <option value="inactiva">Inactiva</option>
            </select>
        </div>
        <div class="fi-group">
            <select class="fi-select" id="fi-bodega" onchange="aplicarFiltrosCajas()">
                <option value="">Todas las bodegas</option>
                @foreach ($bodegas as $bodega)
                    <option value="{{ $bodega->id }}">{{ $bodega->descripcion }}</option>
                @endforeach
            </select>
        </div>
        <button class="btn-outline" onclick="limpiarFiltrosCajas()">✕ Limpiar</button>
    </div>

    {{-- ── BULK TOOLBAR ── --}}
    <div class="bulk-toolbar" id="bulk-toolbar-cajas">
        <span class="bulk-text" id="bulk-text-cajas">0 cajas seleccionadas</span>
        <div class="bulk-actions">
            <button class="btn-bulk" onclick="activarSeleccionCajas()">✅ Activar</button>
            <button class="btn-bulk" onclick="desactivarSeleccionCajas()">🚫 Desactivar</button>
            <button class="btn-bulk danger" onclick="eliminarSeleccionCajas()">🗑️ Eliminar</button>
        </div>
    </div>

    {{-- ── TABLA ── --}}
    <div class="table-wrapper">
        <div class="table-scroll">
            <table class="cajas-tbl" id="tbl-cajas">
                <thead>
                    <tr>
                        <th style="width:36px;">
                            <input type="checkbox" class="chk-row" id="chk-all-cajas" onchange="toggleTodasCajas(this)">
                        </th>
                        <th onclick="sortTablaCajas('nombre')" data-col="nombre">
                            Nombre <span class="sort-icon">↕</span>
                        </th>
                        <th onclick="sortTablaCajas('prefijo')" data-col="prefijo">
                            Prefijo <span class="sort-icon">↕</span>
                        </th>
                        <th>Próximo Doc.</th>
                        <th onclick="sortTablaCajas('bodega')" data-col="bodega">
                            Bodega <span class="sort-icon">↕</span>
                        </th>
                        <th onclick="sortTablaCajas('cajero')" data-col="cajero">
                            Cajero <span class="sort-icon">↕</span>
                        </th>
                        <th>Impresora</th>
                        <th onclick="sortTablaCajas('estado')" data-col="estado">
                            Estado <span class="sort-icon">↕</span>
                        </th>
                        <th style="text-align:center;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-cajas">
                    <tr>
                        <td colspan="9">
                            <div class="spinner-cell">
                                <div class="spinner"></div>
                                Cargando cajas…
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="pagination-bar">
            <span class="pag-info" id="pag-info-cajas">Mostrando 0 de 0 registros</span>
            <div class="pag-btns" id="pag-btns-cajas"></div>
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════
     MODAL CREAR / EDITAR CAJA
═══════════════════════════════════════════════ --}}
<div id="modal-caja" style="display:none;" class="modal-backdrop" onclick="cerrarModalCajaBackdrop(event)">
    <div class="modal-caja">

        <div class="modal-head">
            <div>
                <p class="modal-head-title" id="mc-title">Nueva Caja</p>
                <p class="modal-head-sub" id="mc-sub">Configuración de punto de venta</p>
            </div>
            <button onclick="cerrarModalCaja()"
                style="border:none;background:transparent;font-size:20px;cursor:pointer;
                color:#6B7280;padding:4px;border-radius:6px;line-height:1;">✕</button>
        </div>

        <div class="modal-body" id="mc-body">

            <p class="fac-section-title" style="margin-top:0;">Datos generales</p>
            <div class="fac-grid">
                <div class="fac-field" style="grid-column:1/-1;">
                    <label>Nombre de la caja *</label>
                    <input type="text" id="mc-nombre" placeholder="Ej: Caja Principal">
                </div>
                <div class="fac-field">
                    <label>Prefijo *</label>
                    <input type="text" id="mc-prefijo" placeholder="Ej: FAC" maxlength="10"
                        oninput="this.value=this.value.toUpperCase()">
                </div>
                <div class="fac-field">
                    <label>Próximo número *</label>
                    <input type="number" id="mc-proximo-numero" min="1" placeholder="Ej: 1">
                </div>
            </div>

            <p class="fac-section-title">Asignaciones</p>
            <div class="fac-grid">
                <div class="fac-field" style="grid-column:1/-1;">
                    <label>Bodega de origen</label>
                    <select id="mc-bodega">
                        <option value="">Sin bodega asignada</option>
                        @foreach ($bodegas as $bodega)
                            <option value="{{ $bodega->id }}">{{ $bodega->descripcion }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="fac-field" style="grid-column:1/-1;">
                    <label>Impresora POS asignada</label>
                    <select id="mc-impresora">
                        <option value="">Sin tiquetera vinculada</option>
                        @foreach ($impresoras as $impresora)
                            <option value="{{ $impresora->id }}">{{ $impresora->nombre }} ({{ $impresora->ip }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="fac-field" style="grid-column:1/-1;">
                    <label>Cajero asignado</label>
                    <select id="mc-user">
                        <option value="">Sin cajero asignado</option>
                        @foreach ($usuarios as $usuario)
                            <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="estado-toggle-box">
                <div>
                    <p class="tt">Caja activa</p>
                    <p class="st">Disponible para facturar</p>
                </div>
                <label class="cj-switch">
                    <input type="checkbox" id="mc-activa" checked>
                    <span class="cj-slider"></span>
                </label>
            </div>

        </div>

        <div class="modal-foot">
            <button class="btn-outline" onclick="cerrarModalCaja()">Cancelar</button>
            <button class="btn-primary" id="mc-btn-save" onclick="guardarCaja()">
                💾 <span id="mc-btn-save-text">Crear Caja</span>
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     SCRIPT
═══════════════════════════════════════════════ --}}
<script>
    var puedeGestionarCajas = @json((auth()->user()->rol->nombre ?? '') === 'Administrador');

    /* ════════════════════════════════════════════════
       ESTADO GLOBAL
    ════════════════════════════════════════════════ */
    var CAJ = {
        datos: [],
        filtradas: [],
        paginaActual: 1,
        porPagina: 15,
        sortCol: 'nombre',
        sortAsc: true,
        seleccionadas: new Set(),
        editandoId: null,
    };

    /* ════════════════════════════════════════════════
       INICIALIZACIÓN
    ════════════════════════════════════════════════ */
    (function init() {
        cargarCajas();
    })();

    /* ════════════════════════════════════════════════
       CARGA DE DATOS (fetch API Laravel)
    ════════════════════════════════════════════════ */
    function cargarCajas() {
        mostrarSpinnerCajas();
        var token = document.querySelector('meta[name="csrf-token"]')?.content;

        fetch('/cajas/data', {
                headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
            })
            .then(function(r) {
                return r.json().then(function(data) {
                    if (!r.ok) throw new Error(data.message || 'No se pudo cargar el listado de cajas');
                    return data;
                });
            })
            .then(function(res) {
                var raw = Array.isArray(res.data) ? res.data : (Array.isArray(res) ? res : []);
                CAJ.datos = raw.map(normalizarCaja);
                aplicarFiltrosCajas();
            })
            .catch(function(e) {
                CAJ.datos = [];
                aplicarFiltrosCajas();
                notifCajas(e.message || 'No se pudo cargar el listado de cajas', 'error');
            });
    }

    function normalizarCaja(c) {
        return {
            id: c.id,
            nombre: c.nombre || '',
            prefijo: c.prefijo || '',
            proximo_numero: c.proximo_numero || 1,
            bodega_id: c.bodega_id || (c.bodega ? c.bodega.id : null),
            bodega: (c.bodega && c.bodega.descripcion) || 'Sin Bodega',
            impresora_id: c.impresora_id || (c.impresora ? c.impresora.id : null),
            impresora: (c.impresora && c.impresora.nombre) || 'No vinculada',
            user_id: c.user_id || (c.cajero ? c.cajero.id : null),
            cajero: (c.cajero && c.cajero.name) || 'No asignado',
            activa: !!c.activa,
        };
    }

    /* ════════════════════════════════════════════════
       FILTROS
    ════════════════════════════════════════════════ */
    function aplicarFiltrosCajas() {
        var buscar = (document.getElementById('fi-buscar').value || '').toLowerCase().trim();
        var estado = document.getElementById('fi-estado').value;
        var bodegaId = document.getElementById('fi-bodega').value;

        CAJ.filtradas = CAJ.datos.filter(function(c) {
            if (buscar) {
                var hay = (c.nombre + c.prefijo + c.bodega + c.cajero).toLowerCase().includes(buscar);
                if (!hay) return false;
            }
            if (estado === 'activa' && !c.activa) return false;
            if (estado === 'inactiva' && c.activa) return false;
            if (bodegaId && String(c.bodega_id) !== String(bodegaId)) return false;
            return true;
        });

        CAJ.paginaActual = 1;
        sortTablaCajasActual();
        renderTablaCajas();
        actualizarMetricasCajas();
    }

    function limpiarFiltrosCajas() {
        document.getElementById('fi-buscar').value = '';
        document.getElementById('fi-estado').value = '';
        document.getElementById('fi-bodega').value = '';
        aplicarFiltrosCajas();
    }

    /* ════════════════════════════════════════════════
       ORDENAMIENTO
    ════════════════════════════════════════════════ */
    function sortTablaCajas(col) {
        if (CAJ.sortCol === col) CAJ.sortAsc = !CAJ.sortAsc;
        else { CAJ.sortCol = col; CAJ.sortAsc = true; }
        sortTablaCajasActual();
        renderTablaCajas();
        document.querySelectorAll('table.cajas-tbl thead th[data-col]').forEach(function(th) {
            th.classList.toggle('sorted', th.dataset.col === col);
            var icon = th.querySelector('.sort-icon');
            if (icon) icon.textContent = th.dataset.col === col ? (CAJ.sortAsc ? '↑' : '↓') : '↕';
        });
    }

    function sortTablaCajasActual() {
        var col = CAJ.sortCol, asc = CAJ.sortAsc;
        CAJ.filtradas.sort(function(a, b) {
            var va = col === 'estado' ? (a.activa ? 1 : 0) : (a[col] ?? '');
            var vb = col === 'estado' ? (b.activa ? 1 : 0) : (b[col] ?? '');
            if (typeof va === 'number') return asc ? va - vb : vb - va;
            return asc ? String(va).localeCompare(String(vb)) : String(vb).localeCompare(String(va));
        });
    }

    /* ════════════════════════════════════════════════
       RENDER TABLA
    ════════════════════════════════════════════════ */
    function renderTablaCajas() {
        var tbody = document.getElementById('tbody-cajas');
        var total = CAJ.filtradas.length;
        var desde = (CAJ.paginaActual - 1) * CAJ.porPagina;
        var pagina = CAJ.filtradas.slice(desde, desde + CAJ.porPagina);

        document.getElementById('pag-info-cajas').textContent =
            'Mostrando ' + Math.min(pagina.length, total) + ' de ' + total + ' registros';

        if (pagina.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9">' +
                '<div class="spinner-cell">📭 No se encontraron cajas</div></td></tr>';
            renderPaginacionCajas(total);
            return;
        }

        tbody.innerHTML = pagina.map(function(c) {
            var trCls = c.activa ? '' : 'inactiva';
            var badgeEstado = c.activa ?
                '<span class="badge badge-green"><span class="dot"></span>Activa</span>' :
                '<span class="badge badge-red"><span class="dot"></span>Inactiva</span>';

            var chked = CAJ.seleccionadas.has(c.id) ? 'checked' : '';
            var proximoDoc = esc(c.prefijo) + '-' + String(c.proximo_numero).padStart(5, '0');

            var btnEdit = '<button class="act-btn edit" data-tip="Editar" ' +
                'onclick="editarCaja(' + c.id + ')">✏️</button>';
            var btnToggle = '<button class="act-btn toggle" data-tip="' +
                (c.activa ? 'Desactivar' : 'Activar') + '" ' +
                'onclick="toggleEstadoCaja(' + c.id + ')">' + (c.activa ? '🚫' : '✅') + '</button>';
            var btnDel = '<button class="act-btn del" data-tip="Eliminar" ' +
                'onclick="eliminarCaja(' + c.id + ',\'' + esc(c.nombre) + '\')">🗑️</button>';
            if (!puedeGestionarCajas) { btnEdit = ''; btnToggle = ''; btnDel = ''; }

            return '<tr class="' + trCls + '" data-id="' + c.id + '">' +
                '<td><input type="checkbox" class="chk-row chk-item-caja" ' + chked +
                ' onchange="toggleSeleccionCaja(' + c.id + ',this)"></td>' +
                '<td><span class="td-num">' + esc(c.nombre) + '</span></td>' +
                '<td><span class="td-mono">' + esc(c.prefijo) + '</span></td>' +
                '<td style="color:#6B7280;">' + proximoDoc + '</td>' +
                '<td>' + esc(c.bodega) + '</td>' +
                '<td>' + esc(c.cajero) + '</td>' +
                '<td style="color:#6B7280;">' + esc(c.impresora) + '</td>' +
                '<td>' + badgeEstado + '</td>' +
                '<td><div class="tbl-actions" style="justify-content:center;">' + btnEdit + btnToggle + btnDel + '</div></td>' +
                '</tr>';
        }).join('');

        renderPaginacionCajas(total);
    }

    function renderPaginacionCajas(total) {
        var totalPags = Math.max(1, Math.ceil(total / CAJ.porPagina));
        var actual = CAJ.paginaActual;
        var btns = document.getElementById('pag-btns-cajas');
        var html = '';

        html += '<button class="pag-btn" onclick="irPaginaCajas(' + (actual - 1) + ')"' +
            (actual === 1 ? ' disabled' : '') + '>‹</button>';

        var desde = Math.max(1, actual - 2);
        var hasta = Math.min(totalPags, desde + 4);
        desde = Math.max(1, hasta - 4);

        if (desde > 1) html += '<button class="pag-btn" onclick="irPaginaCajas(1)">1</button>' +
            (desde > 2 ? '<span style="padding:0 4px;color:#9CA3AF;">…</span>' : '');

        for (var p = desde; p <= hasta; p++) {
            html += '<button class="pag-btn' + (p === actual ? ' active' : '') + '" onclick="irPaginaCajas(' + p + ')">' +
                p + '</button>';
        }

        if (hasta < totalPags) {
            html += (hasta < totalPags - 1 ? '<span style="padding:0 4px;color:#9CA3AF;">…</span>' : '') +
                '<button class="pag-btn" onclick="irPaginaCajas(' + totalPags + ')">' + totalPags + '</button>';
        }

        html += '<button class="pag-btn" onclick="irPaginaCajas(' + (actual + 1) + ')"' +
            (actual === totalPags ? ' disabled' : '') + '>›</button>';

        btns.innerHTML = html;
    }

    function irPaginaCajas(p) {
        var total = CAJ.filtradas.length;
        var totalPags = Math.max(1, Math.ceil(total / CAJ.porPagina));
        CAJ.paginaActual = Math.max(1, Math.min(p, totalPags));
        renderTablaCajas();
    }

    function mostrarSpinnerCajas() {
        document.getElementById('tbody-cajas').innerHTML =
            '<tr><td colspan="9"><div class="spinner-cell">' +
            '<div class="spinner"></div>Cargando cajas…</div></td></tr>';
    }

    /* ════════════════════════════════════════════════
       MÉTRICAS
    ════════════════════════════════════════════════ */
    function actualizarMetricasCajas() {
        var cajas = CAJ.filtradas;
        var total = cajas.length;
        var activas = cajas.filter(function(c) { return c.activa; }).length;
        var inactivas = total - activas;
        var sinCajero = cajas.filter(function(c) { return !c.user_id; }).length;

        document.getElementById('m-total').textContent = total;
        document.getElementById('m-total-sub').textContent = 'Según filtros aplicados';
        document.getElementById('m-activas').textContent = activas;
        document.getElementById('m-inactivas').textContent = inactivas;
        document.getElementById('m-sin-cajero').textContent = sinCajero;
    }

    /* ════════════════════════════════════════════════
       SELECCIÓN MÚLTIPLE
    ════════════════════════════════════════════════ */
    function toggleSeleccionCaja(id, chk) {
        if (chk.checked) CAJ.seleccionadas.add(id);
        else CAJ.seleccionadas.delete(id);
        actualizarBulkCajas();
    }

    function toggleTodasCajas(chk) {
        CAJ.seleccionadas.clear();
        if (chk.checked) {
            var desde = (CAJ.paginaActual - 1) * CAJ.porPagina;
            CAJ.filtradas.slice(desde, desde + CAJ.porPagina).forEach(function(c) {
                CAJ.seleccionadas.add(c.id);
            });
        }
        document.querySelectorAll('.chk-item-caja').forEach(function(c) { c.checked = chk.checked; });
        actualizarBulkCajas();
    }

    function actualizarBulkCajas() {
        var n = CAJ.seleccionadas.size;
        var bar = document.getElementById('bulk-toolbar-cajas');
        bar.classList.toggle('visible', n > 0);
        document.getElementById('bulk-text-cajas').textContent =
            n + ' caja' + (n !== 1 ? 's' : '') + ' seleccionada' + (n !== 1 ? 's' : '');
    }

    function confirmarAccionCaja(mensaje, accion) {
        var modal = document.getElementById('modalConfirm');
        var texto = document.getElementById('confirmMensaje');
        var boton = document.getElementById('btnConfirmarAccion');

        if (modal && texto && boton) {
            texto.textContent = mensaje;
            boton.onclick = function(evento) {
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

    function activarSeleccionCajas() { cambiarEstadoSeleccionCajas(true); }
    function desactivarSeleccionCajas() { cambiarEstadoSeleccionCajas(false); }

    function cambiarEstadoSeleccionCajas(activar) {
        var n = CAJ.seleccionadas.size;
        if (!n) return;
        confirmarAccionCaja(
            '¿' + (activar ? 'Activar' : 'Desactivar') + ' ' + n + ' caja' + (n !== 1 ? 's' : '') + '?',
            function() {
                var token = document.querySelector('meta[name="csrf-token"]')?.content;
                var ids = Array.from(CAJ.seleccionadas);
                Promise.all(ids.map(function(id) {
                    return fetch('/cajas/' + id, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': token,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ activa: activar ? 1 : 0 })
                    }).then(function(res) {
                        return res.json().then(function(data) { return { ok: res.ok, data: data }; });
                    });
                })).then(function(resultados) {
                    var exitosas = resultados.filter(function(r) { return r.ok; }).length;
                    var fallidas = resultados.length - exitosas;
                    if (exitosas) notifCajas('✅ ' + exitosas + ' caja' + (exitosas !== 1 ? 's actualizadas' : ' actualizada'), 'success');
                    if (fallidas) notifCajas(fallidas + ' caja' + (fallidas !== 1 ? 's no pudieron actualizarse' : ' no pudo actualizarse'), 'error');
                }).catch(function() {
                    notifCajas('No fue posible procesar la acción seleccionada', 'error');
                }).finally(function() {
                    CAJ.seleccionadas.clear();
                    actualizarBulkCajas();
                    cargarCajas();
                });
            }
        );
    }

    function eliminarSeleccionCajas() {
        var n = CAJ.seleccionadas.size;
        if (!n) return;
        confirmarAccionCaja('¿Eliminar ' + n + ' caja' + (n !== 1 ? 's' : '') + '? Esta acción no se puede deshacer.', function() {
            var token = document.querySelector('meta[name="csrf-token"]')?.content;
            var ids = Array.from(CAJ.seleccionadas);
            Promise.all(ids.map(function(id) {
                return fetch('/cajas/' + id, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                }).then(function(res) {
                    return res.json().then(function(data) { return { ok: res.ok, data: data }; });
                });
            })).then(function(resultados) {
                var exitosas = resultados.filter(function(r) { return r.ok; }).length;
                var fallidas = resultados.length - exitosas;
                if (exitosas) notifCajas('🗑️ ' + exitosas + ' caja' + (exitosas !== 1 ? 's eliminadas' : ' eliminada'), 'success');
                if (fallidas) notifCajas(fallidas + ' caja' + (fallidas !== 1 ? 's no pudieron eliminarse' : ' no pudo eliminarse'), 'error');
            }).catch(function() {
                notifCajas('No fue posible procesar la eliminación seleccionada', 'error');
            }).finally(function() {
                CAJ.seleccionadas.clear();
                actualizarBulkCajas();
                cargarCajas();
            });
        });
    }

    /* ════════════════════════════════════════════════
       ACCIONES INDIVIDUALES
    ════════════════════════════════════════════════ */
    function toggleEstadoCaja(id) {
        var c = CAJ.datos.find(function(x) { return x.id === id; });
        if (!c) return;
        var token = document.querySelector('meta[name="csrf-token"]')?.content;
        fetch('/cajas/' + id, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ activa: c.activa ? 0 : 1 })
            })
            .then(function(res) {
                return res.json().then(function(data) { return { ok: res.ok, data: data }; });
            })
            .then(function(resultado) {
                if (!resultado.ok) throw new Error(resultado.data.message || 'No se pudo actualizar el estado');
                notifCajas(c.activa ? '🚫 Caja desactivada' : '✅ Caja activada', 'success');
                cargarCajas();
            })
            .catch(function(error) {
                notifCajas(error.message || 'No se pudo actualizar el estado de la caja', 'error');
            });
    }

    function eliminarCaja(id, nombre) {
        confirmarAccionCaja('¿Eliminar la caja "' + nombre + '"? Esta acción no se puede deshacer.', function() {
            var token = document.querySelector('meta[name="csrf-token"]')?.content;
            fetch('/cajas/' + id, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json' }
                })
                .then(function(res) {
                    return res.json().then(function(data) { return { ok: res.ok, data: data }; });
                })
                .then(function(resultado) {
                    if (!resultado.ok) throw new Error(resultado.data.message || 'No se pudo eliminar la caja');
                    notifCajas('🗑️ Caja "' + nombre + '" eliminada', 'success');
                    cargarCajas();
                })
                .catch(function(error) {
                    notifCajas(error.message || 'No se pudo eliminar la caja', 'error');
                });
        });
    }

    /* ════════════════════════════════════════════════
       MODAL CREAR / EDITAR
    ════════════════════════════════════════════════ */
    function abrirModalCaja() {
        CAJ.editandoId = null;
        document.getElementById('mc-title').textContent = 'Nueva Caja';
        document.getElementById('mc-sub').textContent = 'Configuración de punto de venta';
        document.getElementById('mc-btn-save-text').textContent = 'Crear Caja';
        document.getElementById('mc-nombre').value = '';
        document.getElementById('mc-prefijo').value = '';
        document.getElementById('mc-proximo-numero').value = 1;
        document.getElementById('mc-bodega').value = '';
        document.getElementById('mc-impresora').value = '';
        document.getElementById('mc-user').value = '';
        document.getElementById('mc-activa').checked = true;
        document.getElementById('modal-caja').style.display = 'flex';
    }

    function editarCaja(id) {
        var c = CAJ.datos.find(function(x) { return x.id === id; });
        if (!c) {
            notifCajas('Caja no encontrada', 'error');
            return;
        }
        CAJ.editandoId = id;
        document.getElementById('mc-title').textContent = 'Editar ' + c.nombre;
        document.getElementById('mc-sub').textContent = 'Modifique los campos necesarios';
        document.getElementById('mc-btn-save-text').textContent = 'Guardar Cambios';
        document.getElementById('mc-nombre').value = c.nombre;
        document.getElementById('mc-prefijo').value = c.prefijo;
        document.getElementById('mc-proximo-numero').value = c.proximo_numero;
        document.getElementById('mc-bodega').value = c.bodega_id || '';
        document.getElementById('mc-impresora').value = c.impresora_id || '';
        document.getElementById('mc-user').value = c.user_id || '';
        document.getElementById('mc-activa').checked = c.activa;
        document.getElementById('modal-caja').style.display = 'flex';
    }

    function cerrarModalCaja() {
        document.getElementById('modal-caja').style.display = 'none';
    }

    function cerrarModalCajaBackdrop(e) {
        if (e.target === document.getElementById('modal-caja')) cerrarModalCaja();
    }

    function guardarCaja() {
        var nombre = document.getElementById('mc-nombre').value.trim();
        var prefijo = document.getElementById('mc-prefijo').value.trim();
        var proximoNumero = document.getElementById('mc-proximo-numero').value;

        if (!nombre) {
            notifCajas('⚠️ El nombre de la caja es requerido', 'error');
            return;
        }
        if (!prefijo) {
            notifCajas('⚠️ El prefijo es requerido', 'error');
            return;
        }
        if (!proximoNumero || proximoNumero < 1) {
            notifCajas('⚠️ Ingrese un próximo número válido', 'error');
            return;
        }

        var token = document.querySelector('meta[name="csrf-token"]')?.content;
        var body = {
            nombre: nombre,
            prefijo: prefijo,
            proximo_numero: parseInt(proximoNumero, 10),
            bodega_id: document.getElementById('mc-bodega').value || null,
            impresora_id: document.getElementById('mc-impresora').value || null,
            user_id: document.getElementById('mc-user').value || null,
            activa: document.getElementById('mc-activa').checked ? 1 : 0,
        };

        var url = CAJ.editandoId ? '/cajas/' + CAJ.editandoId : '/cajas';
        var method = CAJ.editandoId ? 'PUT' : 'POST';

        var btn = document.getElementById('mc-btn-save');
        btn.disabled = true;
        document.getElementById('mc-btn-save-text').textContent = 'Guardando…';

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
                return r.json().then(function(data) {
                    if (!r.ok) throw new Error(data.message || 'No se pudo guardar la caja');
                    return data;
                });
            })
            .then(function() {
                notifCajas(CAJ.editandoId ? '✅ Caja actualizada' : '✅ Caja creada correctamente', 'success');
                cerrarModalCaja();
                cargarCajas();
            })
            .catch(function(e) {
                notifCajas(e.message || 'No se pudo guardar la caja', 'error');
            })
            .finally(function() {
                btn.disabled = false;
                document.getElementById('mc-btn-save-text').textContent = CAJ.editandoId ? 'Guardar Cambios' : 'Crear Caja';
            });
    }

    /* ── Exportar ── */
    function exportarCajas() {
        var cajas = CAJ.filtradas;
        if (!cajas.length) {
            notifCajas('No hay cajas para exportar', 'warning');
            return;
        }

        var encabezados = ['Nombre', 'Prefijo', 'Próximo número', 'Bodega', 'Cajero', 'Impresora', 'Estado'];
        var filas = cajas.map(function(c) {
            return [
                c.nombre, c.prefijo, c.proximo_numero, c.bodega, c.cajero, c.impresora,
                c.activa ? 'Activa' : 'Inactiva'
            ].map(function(valor) {
                return '"' + String(valor).replace(/"/g, '""') + '"';
            }).join(',');
        });

        var csv = '\uFEFF' + encabezados.join(',') + '\n' + filas.join('\n');
        var archivo = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
        var enlace = document.createElement('a');
        enlace.href = URL.createObjectURL(archivo);
        enlace.download = 'cajas.csv';
        enlace.click();
        URL.revokeObjectURL(enlace.href);
        notifCajas('Exportación creada: ' + cajas.length + ' caja' + (cajas.length !== 1 ? 's' : ''), 'success');
    }

    /* ════════════════════════════════════════════════
       UTILIDADES
    ════════════════════════════════════════════════ */
    function esc(s) {
        return String(s || '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    function notifCajas(msg, tipo) {
        if (typeof mostrarNotificacion === 'function') {
            mostrarNotificacion(msg, tipo === 'error' ? 'error' : tipo === 'warning' ? 'warning' : 'success');
        } else {
            var cls = { success: '#059669', error: '#DC2626', info: '#1D4ED8', warning: '#D97706' };
            var el = document.createElement('div');
            el.textContent = msg;
            el.style.cssText = 'background:#fff;border:1px solid #EAECF0;border-left:4px solid ' +
                (cls[tipo] || cls.info) + ';border-radius:8px;padding:10px 16px;font-size:13px;' +
                'color:#111827;box-shadow:0 4px 12px rgba(0,0,0,0.08);pointer-events:auto;' +
                'min-width:220px;max-width:360px;';
            var cont = document.getElementById('notificaciones');
            if (cont) {
                cont.appendChild(el);
                setTimeout(function() { el.remove(); }, 3500);
            }
        }
    }
</script>
