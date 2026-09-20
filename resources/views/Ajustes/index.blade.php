<style>
    /* ═══════════════════════════════════════════════
       AJUSTES DE INVENTARIO — ESTILO NEXORA / FACTURAS
    ═══════════════════════════════════════════════ */

    .aj-sec-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 16px;
    }

    .aj-sec-title {
        font-size: 17px;
        font-weight: 600;
        color: #111827;
        letter-spacing: -0.3px;
    }

    .aj-sec-subtitle {
        font-size: 12px;
        color: #6B7280;
        margin-top: 2px;
    }

    /* ── Métricas ── */
    .aj-metrics-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 10px;
        margin-bottom: 16px;
    }

    .aj-metric-card {
        background: #fff;
        border: 1px solid #EAECF0;
        border-radius: 10px;
        padding: 12px 14px;
        position: relative;
        overflow: hidden;
    }

    .aj-metric-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: var(--accent, #1D4ED8);
    }

    .aj-metric-label {
        font-size: 11px;
        font-weight: 500;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .aj-metric-value {
        font-size: 20px;
        font-weight: 700;
        color: #111827;
        margin-top: 4px;
        letter-spacing: -0.5px;
    }

    .aj-metric-sub {
        font-size: 11px;
        color: #6B7280;
        margin-top: 2px;
    }

    /* ── Botones ── */
    .aj-btn-primary {
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

    .aj-btn-primary:hover {
        background: #1e40af;
    }

    .aj-btn-outline {
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

    .aj-btn-outline:hover {
        background: #F3F4F6;
        border-color: #9CA3AF;
    }

    .aj-btn-success {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #059669;
        color: #fff;
        border: none;
        border-radius: 7px;
        padding: 7px 14px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.15s;
    }

    .aj-btn-success:hover {
        background: #047857;
    }

    /* ── Barra búsqueda ── */
    .aj-filter-bar {
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

    .aj-fi-group {
        display: flex;
        align-items: center;
        gap: 6px;
        flex: 1;
        min-width: 220px;
    }

    .aj-fi-label {
        font-size: 12px;
        color: #6B7280;
        white-space: nowrap;
    }

    .aj-fi-input {
        flex: 1;
        width: 100%;
        border: 1px solid #D1D5DB;
        border-radius: 7px;
        padding: 6px 10px;
        font-size: 12px;
        color: #111827;
        background: #F9FAFB;
        outline: none;
        transition: border 0.15s;
    }

    .aj-fi-input:focus {
        border-color: #1D4ED8;
        background: #fff;
    }

    .aj-fi-input::placeholder {
        color: #9CA3AF;
    }

    /* ── Tabla principal ── */
    .aj-table-wrapper {
        background: #fff;
        border: 1px solid #EAECF0;
        border-radius: 10px;
        overflow: hidden;
    }

    .aj-table-scroll {
        overflow-x: auto;
    }

    table.ajustes-tbl {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
    }

    table.ajustes-tbl thead {
        background: #F8FAFC;
        border-bottom: 1px solid #EAECF0;
    }

    table.ajustes-tbl thead th {
        padding: 10px 12px;
        text-align: left;
        font-size: 11px;
        font-weight: 600;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    table.ajustes-tbl tbody tr {
        border-bottom: 1px solid #F3F4F6;
        transition: background 0.1s;
    }

    table.ajustes-tbl tbody tr:last-child {
        border-bottom: none;
    }

    table.ajustes-tbl tbody tr:hover {
        background: #F8FAFC;
    }

    table.ajustes-tbl td {
        padding: 9px 12px;
        color: #374151;
        vertical-align: middle;
    }

    .aj-td-mono {
        font-family: 'JetBrains Mono', 'Fira Mono', monospace;
        font-size: 12px;
        color: #111827;
        font-weight: 500;
    }

    .aj-td-num {
        font-size: 13px;
        font-weight: 700;
        color: #1D4ED8;
        letter-spacing: -0.3px;
    }

    .aj-td-money {
        font-weight: 600;
        color: #059669;
        text-align: right;
    }

    /* ── Badges ── */
    .aj-badge {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        padding: 3px 8px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        white-space: nowrap;
    }

    .aj-badge-green {
        background: #ECFDF5;
        color: #065F46;
    }

    .aj-badge-yellow {
        background: #FFFBEB;
        color: #92400E;
    }

    .aj-dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        display: inline-block;
        background: currentColor;
    }

    /* ── Acciones ── */
    .aj-tbl-actions {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 4px;
    }

    .aj-act-btn {
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

    .aj-act-btn:hover {
        background: #F3F4F6;
        color: #111827;
        transform: scale(1.05);
    }

    .aj-act-btn.resume:hover {
        background: #EFF6FF;
        color: #1D4ED8;
    }

    .aj-act-btn.edit:hover {
        background: #ECFDF5;
        color: #059669;
    }

    .aj-act-btn.del:hover {
        background: #FEF2F2;
        color: #DC2626;
    }

    .aj-act-btn.view:hover {
        background: #EFF6FF;
        color: #1D4ED8;
    }

    .aj-act-btn.rev:hover {
        background: #FFFBEB;
        color: #D97706;
    }

    .aj-act-btn::after {
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

    .aj-act-btn:hover::after {
        opacity: 1;
    }

    /* ── Modales ── */
    .aj-modal-backdrop {
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

    /* IMPORTANTE: evita que el display:flex del modal pise la clase hidden de Tailwind */
    .aj-modal-backdrop.hidden {
        display: none !important;
    }

    .aj-modal {
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

    .aj-modal.sm {
        max-width: 660px;
    }

    .aj-modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid #EAECF0;
        flex-shrink: 0;
    }

    .aj-modal-title {
        font-size: 15px;
        font-weight: 600;
        color: #111827;
    }

    .aj-modal-sub {
        font-size: 12px;
        color: #6B7280;
        margin-top: 2px;
    }

    .aj-modal-close {
        border: none;
        background: transparent;
        font-size: 20px;
        cursor: pointer;
        color: #6B7280;
        padding: 4px;
        border-radius: 6px;
        line-height: 1;
    }

    .aj-modal-close:hover {
        background: #F3F4F6;
        color: #111827;
    }

    .aj-modal-body {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
    }

    .aj-modal-foot {
        padding: 14px 20px;
        border-top: 1px solid #EAECF0;
        display: flex;
        gap: 8px;
        justify-content: flex-end;
        flex-shrink: 0;
    }

    /* ── Campos ── */
    .aj-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
    }

    .aj-field {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .aj-field.full {
        grid-column: 1 / -1;
    }

    .aj-field label {
        font-size: 11px;
        font-weight: 500;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .aj-field input,
    .aj-field select,
    .aj-field textarea {
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

    .aj-field input:focus,
    .aj-field select:focus,
    .aj-field textarea:focus {
        border-color: #1D4ED8;
        background: #fff;
    }

    .aj-field input[readonly] {
        background: #F3F4F6;
        color: #6B7280;
    }

    .aj-section-title {
        font-size: 12px;
        font-weight: 600;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        margin: 16px 0 10px;
        padding-bottom: 6px;
        border-bottom: 1px solid #F3F4F6;
    }

    /* ── Select tercero ── */
    .aj-search-row {
        display: flex;
        gap: 8px;
        align-items: stretch;
    }

    .aj-search-row input {
        flex: 1;
    }

    .aj-results {
        margin-top: 6px;
        border: 1px solid #EAECF0;
        border-radius: 8px;
        background: #fff;
        box-shadow: 0 10px 30px rgba(17, 24, 39, 0.08);
        max-height: 220px;
        overflow-y: auto;
    }

    /* ── Buscador con autocompletar (tercero / contrapartida) ── */
    .aj-autocomplete { position: relative; }

    .aj-results.floating {
        position: absolute;
        z-index: 30;
        top: calc(100% + 4px);
        left: 0;
        right: 0;
        margin-top: 0;
    }

    .aj-result-item {
        width: 100%;
        display: flex;
        align-items: baseline;
        justify-content: space-between;
        gap: 10px;
        padding: 8px 12px;
        border: 0;
        border-bottom: 1px solid #F3F4F6;
        background: #fff;
        color: #374151;
        text-align: left;
        cursor: pointer;
        font-size: 12.5px;
    }

    .aj-result-item:last-child { border-bottom: none; }
    .aj-result-item:hover, .aj-result-item.activo { background: #EFF6FF; }

    .aj-result-mono {
        font-family: 'JetBrains Mono', 'Fira Mono', monospace;
        font-size: 12px;
        color: #1D4ED8;
        font-weight: 600;
        flex-shrink: 0;
    }

    .aj-result-empty { padding: 14px 12px; text-align: center; color: #9CA3AF; font-size: 12px; }

    /* ── Tabla productos modal ── */
    .aj-items-wrap {
        overflow-x: auto;
        border: 1px solid #EAECF0;
        border-radius: 10px;
        overflow: hidden;
    }

    table.aj-items-tbl {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
    }

    table.aj-items-tbl thead th {
        padding: 8px 10px;
        text-align: left;
        font-size: 11px;
        color: #6B7280;
        font-weight: 600;
        text-transform: uppercase;
        background: #F8FAFC;
        letter-spacing: 0.4px;
    }

    table.aj-items-tbl tbody td {
        padding: 8px 10px;
        border-top: 1px solid #F3F4F6;
    }

    /* ── Tarjeta detalle ── */
    .aj-info-box {
        background: #F8FAFC;
        border: 1px solid #EAECF0;
        border-radius: 10px;
        padding: 14px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 16px;
    }

    .aj-info-item span:first-child {
        display: block;
        font-size: 10px;
        font-weight: 600;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 3px;
    }

    .aj-info-item span:last-child {
        font-size: 13px;
        color: #111827;
        font-weight: 600;
    }

    .aj-info-item.full {
        grid-column: 1 / -1;
        border-top: 1px solid #EAECF0;
        padding-top: 10px;
    }

    /* ── Responsive ── */
    @media (max-width: 640px) {
        .aj-metrics-row {
            grid-template-columns: 1fr 1fr;
        }

        .aj-grid,
        .aj-info-box {
            grid-template-columns: 1fr;
        }

        .aj-field.full,
        .aj-info-item.full {
            grid-column: 1;
        }

        .aj-search-row {
            flex-direction: column;
        }
    }
</style>

<div id="view-ajustes">

    <div id="notificaciones" class="fixed top-5 right-5 z-[9999] space-y-3"></div>

    {{-- ── ENCABEZADO ── --}}
    <div class="aj-sec-header">
        <div>
            <p class="aj-sec-title">📦 Ajustes de Inventario</p>
            <p class="aj-sec-subtitle">Control de entradas, salidas y movimientos de stock</p>
        </div>

        <button onclick="openModalAjuste()" class="aj-btn-primary">
            ＋ Añadir Ajuste
        </button>
    </div>

    {{-- ── MÉTRICAS ── --}}
    <div class="aj-metrics-row">
        <div class="aj-metric-card" style="--accent:#1D4ED8">
            <p class="aj-metric-label">Total ajustes</p>
            <p class="aj-metric-value">{{ $ajustes->count() }}</p>
            <p class="aj-metric-sub">Documentos registrados</p>
        </div>

        <div class="aj-metric-card" style="--accent:#D97706">
            <p class="aj-metric-label">Pendientes</p>
            <p class="aj-metric-value">{{ $ajustes->where('registrado', false)->count() }}</p>
            <p class="aj-metric-sub">Por completar</p>
        </div>

        <div class="aj-metric-card" style="--accent:#059669">
            <p class="aj-metric-label">Registrados</p>
            <p class="aj-metric-value">{{ $ajustes->where('registrado', true)->count() }}</p>
            <p class="aj-metric-sub">Aplicados al inventario</p>
        </div>

        <div class="aj-metric-card" style="--accent:#7C3AED">
            <p class="aj-metric-label">Valor total</p>
            <p class="aj-metric-value" style="font-size:17px;">
                ${{ number_format($ajustes->sum('total'), 0, ',', '.') }}
            </p>
            <p class="aj-metric-sub">Valor acumulado</p>
        </div>
    </div>

    {{-- ── FILTRO ── --}}
    <div class="aj-filter-bar">
        <div class="aj-fi-group" style="flex:2;min-width:240px;">
            <span class="aj-fi-label">🔍</span>
            <input autocomplete="off" class="aj-fi-input" type="text" id="buscarAjuste"
                placeholder="Número, observaciones, usuario…">
        </div>
    </div>

    {{-- ── TABLA ── --}}
    <div class="aj-table-wrapper">
        <div class="aj-table-scroll">
            <table class="ajustes-tbl">
                <thead>
                    <tr>
                        <th>Documento</th>
                        <th>Observaciones</th>
                        <th style="text-align:right;">Total</th>
                        <th style="text-align:center;">Estado</th>
                        <th>Usuario</th>
                        <th>Fecha</th>
                        <th style="text-align:center;">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($ajustes as $a)
                        <tr>
                            <td>
                                <span class="aj-td-mono">{{ $a->prefijo }}</span>
                                <span class="aj-td-num">{{ $a->numero }}</span>
                            </td>

                            <td style="max-width:260px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                                title="{{ $a->observaciones ?: 'Sin observaciones' }}">
                                {{ $a->observaciones ?: 'Sin observaciones' }}
                            </td>

                            <td class="aj-td-money">
                                ${{ number_format($a->total, 0, ',', '.') }}
                            </td>

                            <td style="text-align:center;">
                                @if (!$a->registrado)
                                    <span class="aj-badge aj-badge-yellow">
                                        <span class="aj-dot"></span>
                                        Pendiente
                                    </span>
                                @else
                                    <span class="aj-badge aj-badge-green">
                                        <span class="aj-dot"></span>
                                        Registrado
                                    </span>
                                @endif
                            </td>

                            <td>{{ $a->user->name ?? '-' }}</td>
                            <td>{{ $a->fecha }}</td>

                            <td>
                                <div class="aj-tbl-actions">
                                    @if (!$a->registrado)
                                        <button onclick="retomarAjuste({{ $a->id }})"
                                            class="aj-act-btn resume"
                                            data-tip="Completar">▶</button>

                                        <button onclick="editarAjuste({{ $a->id }})"
                                            class="aj-act-btn edit"
                                            data-tip="Editar">✏️</button>

                                        <button onclick="eliminarAjuste({{ $a->id }})"
                                            class="aj-act-btn del"
                                            data-tip="Eliminar">🗑️</button>
                                    @else
                                        <button onclick="verAjuste({{ $a->id }})"
                                            class="aj-act-btn view"
                                            data-tip="Ver detalle">👁️</button>

                                        <button onclick="revertirAjuste({{ $a->id }})"
                                            class="aj-act-btn rev"
                                            data-tip="Revertir">↩️</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align:center;padding:36px;color:#9CA3AF;">
                                No hay ajustes registrados
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     MODAL CREAR / EDITAR AJUSTE
═══════════════════════════════════════════════ --}}
<div id="modalAjuste" class="aj-modal-backdrop hidden">
    <div class="aj-modal">

        <div class="aj-modal-head">
            <div>
                <p class="aj-modal-title">Ajuste de Inventario</p>
                <p class="aj-modal-sub">Complete los datos del movimiento</p>
            </div>

            <button onclick="closeModalAjuste()" class="aj-modal-close">✕</button>
        </div>

        <div class="aj-modal-body">

            <p class="aj-section-title">Encabezado</p>

            <div class="aj-grid">
                <div class="aj-field full">
                    <label>Documento</label>
                    <div style="display:grid;grid-template-columns:110px 1fr;gap:8px;">
                        <select id="prefijo">
                            <option value="AJ">AJ</option>
                            <option value="EF">EF</option>
                            <option value="BR">BR</option>
                        </select>

                        <input autocomplete="off" type="text" id="numero" value="0001" readonly>
                    </div>
                </div>

                <div class="aj-field">
                    <label>Fecha</label>
                    <input autocomplete="off" type="date" id="fecha">
                </div>

                <div class="aj-field">
                    <label>Bodega</label>
                    <select id="bodega_id">
                        <option value="">— Seleccione una bodega —</option>
                        @foreach ($bodegas as $b)
                            <option value="{{ $b->id }}">{{ $b->descripcion }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="aj-field full aj-autocomplete">
                    <label>Tercero</label>
                    <input type="text" id="aj-tercero-buscar" autocomplete="off"
                        placeholder="Buscar por nombre, cédula o NIT…">
                    <input type="hidden" id="tercero_id">
                    <div id="aj-tercero-resultados" class="aj-results floating hidden"></div>
                </div>

                <div class="aj-field aj-autocomplete">
                    <label>Cuenta de contrapartida</label>
                    <input type="text" id="aj-contraparte-buscar" autocomplete="off"
                        placeholder="Buscar por código o nombre…">
                    <input type="hidden" id="contraparte_cuenta_id">
                    <div id="aj-contraparte-resultados" class="aj-results floating hidden"></div>
                    <small style="color:#667085;display:block;margin-top:5px">Entrada: acredita esta cuenta. Salida: la debita.</small>
                </div>

                <div class="aj-field full">
                    <label>Observaciones</label>
                    <textarea id="observaciones"
                        rows="2"
                        placeholder="Motivo del ajuste…"
                        style="resize:vertical;"></textarea>
                </div>
            </div>

            <p class="aj-section-title">Productos del ajuste</p>

            <div class="aj-field aj-autocomplete" style="margin-bottom:12px;">
                <label>Buscar producto</label>
                <input autocomplete="off" type="text" id="buscarProducto"
                    onkeyup="buscarProducto()"
                    placeholder="Nombre o código del producto…">

                <div id="resultadosProducto"
                    class="aj-results floating hidden"></div>
            </div>

            <div class="aj-items-wrap">
                <table class="aj-items-tbl">
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th style="text-align:center;width:120px;">Cantidad</th>
                            <th style="text-align:center;width:150px;">Tipo</th>
                            <th style="text-align:center;width:50px;"></th>
                        </tr>
                    </thead>

                    <tbody id="tablaProductos"></tbody>
                </table>
            </div>
        </div>

        <div class="aj-modal-foot">
            <button onclick="closeModalAjuste()" class="aj-btn-outline">
                Cancelar
            </button>

            <button id="btnGuardarBorrador"
                onclick="guardarBorradorAjuste()"
                class="aj-btn-outline">
                💾 Guardar borrador
            </button>

            <button id="btnRegistrarAjuste"
                onclick="registrarAjusteCompleto()"
                class="aj-btn-success">
                ✅ Registrar Ajuste
            </button>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════
     MODAL VER AJUSTE
═══════════════════════════════════════════════ --}}
<div id="modalVerAjuste" class="aj-modal-backdrop hidden">
    <div class="aj-modal" style="max-width:720px;">

        <div class="aj-modal-head">
            <div>
                <p class="aj-modal-title">Detalle del Ajuste</p>
                <p class="aj-modal-sub" id="ver_doc_subtitle">
                    Consulta la información registrada
                </p>
            </div>

            <button onclick="closeModalVerAjuste()" class="aj-modal-close">✕</button>
        </div>

        <div class="aj-modal-body">

            <div class="aj-info-box">
                <div class="aj-info-item">
                    <span>Documento</span>
                    <span id="ver_doc"></span>
                </div>

                <div class="aj-info-item">
                    <span>Fecha de registro</span>
                    <span id="ver_fecha"></span>
                </div>

                <div class="aj-info-item">
                    <span>Tercero / Responsable</span>
                    <span id="ver_tercero"></span>
                </div>

                <div class="aj-info-item">
                    <span>Bodega</span>
                    <span id="ver_bodega"></span>
                </div>

                <div class="aj-info-item full">
                    <span>Observaciones</span>
                    <span id="ver_obs" style="font-weight:400;color:#6B7280;"></span>
                </div>
            </div>

            <div style="display:flex;align-items:center;justify-content:space-between;gap:10px;margin-bottom:10px;">
                <p class="aj-section-title" style="margin:0;border:none;padding:0;">
                    Productos Ajustados
                </p>

                <span class="aj-badge"
                    style="background:#EFF6FF;color:#1D4ED8;">
                    Total:&nbsp;<strong id="ver_total"></strong>
                </span>
            </div>

            <div class="aj-items-wrap">
                <table class="aj-items-tbl">
                    <thead>
                        <tr>
                            <th>Descripción del Producto</th>
                            <th style="text-align:center;width:130px;">Cantidad</th>
                        </tr>
                    </thead>

                    <tbody id="ver_detalles"></tbody>
                </table>
            </div>
        </div>

        <div class="aj-modal-foot">
            <button onclick="closeModalVerAjuste()" class="aj-btn-outline">
                Cerrar
            </button>
        </div>
    </div>
</div>

<script>
    // Catálogo de cuentas contables para el buscador de "Cuenta de contrapartida".
    window.AJ_CUENTAS = @json($cuentasContables->map(fn ($c) => ['id' => $c->id, 'codigo' => $c->codigo, 'nombre' => $c->nombre]));
</script>
