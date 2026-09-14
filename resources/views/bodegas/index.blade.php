<style>
    .sec-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 16px;
    }

    .sec-title { font-size: 17px; font-weight: 600; color: #111827; letter-spacing: -0.3px; }
    .sec-subtitle { font-size: 12px; color: #6B7280; margin-top: 2px; }

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
        top: 0; left: 0; right: 0;
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

    .metric-value { font-size: 20px; font-weight: 700; color: #111827; margin-top: 4px; letter-spacing: -0.5px; }
    .metric-value.money { font-size: 16px; }
    .metric-sub { font-size: 11px; color: #6B7280; margin-top: 2px; }

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

    .filter-bar .fi-group { display: flex; align-items: center; gap: 6px; flex: 1; min-width: 160px; }
    .fi-label { font-size: 12px; color: #6B7280; white-space: nowrap; }

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

    .fi-input:focus { border-color: #1D4ED8; background: #fff; }
    .fi-input::placeholder { color: #9CA3AF; }

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

    .btn-primary:hover { background: #1e40af; }

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

    .btn-outline:hover { background: #F3F4F6; border-color: #9CA3AF; }

    /* ── Tabla ── */
    .table-wrapper { background: #fff; border: 1px solid #EAECF0; border-radius: 10px; overflow: hidden; }
    .table-scroll { overflow-x: auto; }

    table.bod-tbl { width: 100%; border-collapse: collapse; font-size: 12.5px; }
    table.bod-tbl thead { background: #F8FAFC; border-bottom: 1px solid #EAECF0; }

    table.bod-tbl thead th {
        padding: 10px 12px;
        text-align: left;
        font-size: 11px;
        font-weight: 600;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    table.bod-tbl tbody tr { border-bottom: 1px solid #F3F4F6; transition: background 0.1s; }
    table.bod-tbl tbody tr:last-child { border-bottom: none; }
    table.bod-tbl tbody tr:hover { background: #F8FAFC; }
    table.bod-tbl td { padding: 9px 12px; color: #374151; vertical-align: middle; }

    .td-mono { font-family: 'JetBrains Mono', 'Fira Mono', monospace; font-size: 12px; color: #1D4ED8; font-weight: 600; }

    .bod-avatar {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: #EFF6FF;
        color: #1D4ED8;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 13px;
        border: 1px solid #DBEAFE;
        flex-shrink: 0;
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
    .badge-gray { background: #F3F4F6; color: #374151; }
    .dot { width: 5px; height: 5px; border-radius: 50%; display: inline-block; background: currentColor; }

    /* ── Acciones ── */
    .tbl-actions { display: flex; align-items: center; justify-content: flex-end; gap: 4px; }

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
    .act-btn.edit:hover { background: #ECFDF5; color: #059669; }
    .act-btn.del:hover { background: #FEF2F2; color: #DC2626; }

    .act-btn::after {
        content: attr(title);
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

    .spinner-cell {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
        color: #6B7280;
        font-size: 13px;
        gap: 10px;
    }

    /* ── Modal ── */
    .modal-backdrop-bod { background: rgba(17, 24, 39, 0.5); backdrop-filter: blur(4px); }

    .modal-bodega {
        background: #fff;
        border-radius: 16px;
        width: 100%;
        max-width: 460px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        overflow: hidden;
    }

    .modal-head-bod {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid #EAECF0;
    }

    .modal-head-title { font-size: 15px; font-weight: 600; color: #111827; }
    .modal-head-sub { font-size: 12px; color: #6B7280; margin-top: 2px; }
    .modal-body-bod { padding: 20px; }

    .modal-foot-bod {
        padding: 14px 20px;
        border-top: 1px solid #EAECF0;
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }

    .bod-field { display: flex; flex-direction: column; gap: 3px; margin-bottom: 14px; }
    .bod-field:last-child { margin-bottom: 0; }

    .bod-field label {
        font-size: 11px;
        font-weight: 500;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .bod-field input,
    .bod-field select {
        border: 1px solid #D1D5DB;
        border-radius: 7px;
        padding: 8px 10px;
        font-size: 13px;
        color: #111827;
        outline: none;
        background: #F9FAFB;
        transition: border 0.15s;
        font-family: inherit;
        width: 100%;
        box-sizing: border-box;
    }

    .bod-field input:focus,
    .bod-field select:focus { border-color: #1D4ED8; background: #fff; }

    @media (max-width: 640px) {
        .metrics-row { grid-template-columns: 1fr 1fr; }
    }
</style>

<div id="view-bodegas">

    {{-- ── ENCABEZADO ── --}}
    <div class="sec-header">
        <div>
            <p class="sec-title">🏬 Bodegas</p>
            <p class="sec-subtitle">Puntos de almacenamiento e inventario del negocio</p>
        </div>
        <button class="btn-primary" onclick="abrirNuevaBodega()">＋ Nueva Bodega</button>
    </div>

    {{-- ── MÉTRICAS ── --}}
    <div class="metrics-row">
        <div class="metric-card" style="--accent:#1D4ED8">
            <p class="metric-label">Total Bodegas</p>
            <p class="metric-value">{{ $metricas['total'] }}</p>
            <p class="metric-sub">Puntos registrados</p>
        </div>
        <div class="metric-card" style="--accent:#059669">
            <p class="metric-label">Con Existencias</p>
            <p class="metric-value">{{ $metricas['con_existencias'] }}</p>
            <p class="metric-sub">{{ $metricas['total'] ? round($metricas['con_existencias'] / $metricas['total'] * 100) : 0 }}% del total</p>
        </div>
        <div class="metric-card" style="--accent:#7C3AED">
            <p class="metric-label">Unidades en Stock</p>
            <p class="metric-value">{{ number_format($metricas['unidades'], 0, ',', '.') }}</p>
            <p class="metric-sub">Sumatoria de todas las bodegas</p>
        </div>
        <div class="metric-card" style="--accent:#D97706">
            <p class="metric-label">Valor Inventario</p>
            <p class="metric-value money">${{ number_format($metricas['valor_inventario'], 0, ',', '.') }}</p>
            <p class="metric-sub">Costo a precio de venta</p>
        </div>
    </div>

    {{-- ── FILTROS ── --}}
    <div class="filter-bar">
        <div class="fi-group" style="min-width:220px;">
            <span class="fi-label">🔍</span>
            <input type="text" id="buscarBodega" oninput="filtrarBodega()" placeholder="Buscar bodega por nombre…" class="fi-input">
        </div>
        <button class="btn-outline" onclick="document.getElementById('buscarBodega').value='';filtrarBodega();">✕ Limpiar</button>
    </div>

    {{-- ── TABLA ── --}}
    <div class="table-wrapper">
        <div class="table-scroll">
            <table class="bod-tbl">
                <thead>
                    <tr>
                        <th>Bodega</th>
                        <th style="text-align:center;">Existencias</th>
                        <th style="text-align:right;">Valor Inventario</th>
                        <th style="text-align:right;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-bodegas">
                    @forelse ($bodegas as $bodega)
                        @php
                            $stock = $bodega->inventarios->sum('stock');
                            $valor = $bodega->inventarios->sum(fn ($i) => $i->stock * ($i->producto->precio ?? 0));
                        @endphp
                        <tr data-desc="{{ strtolower($bodega->descripcion) }}">
                            <td>
                                <div style="display:flex;align-items:center;gap:10px;">
                                    <span class="bod-avatar">{{ strtoupper(substr($bodega->descripcion, 0, 1)) }}</span>
                                    <div>
                                        <div style="font-weight:500;color:#111827;">{{ $bodega->descripcion }}</div>
                                        <div class="td-mono" style="font-size:11px;">#{{ $bodega->id }}</div>
                                    </div>
                                </div>
                            </td>
                            <td style="text-align:center;">
                                @if ($stock > 0)
                                    <span class="badge badge-green"><span class="dot"></span>{{ number_format($stock, 0, ',', '.') }} unid.</span>
                                @else
                                    <span class="badge badge-gray">— Sin existencias</span>
                                @endif
                            </td>
                            <td style="text-align:right;font-weight:600;color:#111827;">
                                ${{ number_format($valor, 0, ',', '.') }}
                            </td>
                            <td>
                                <div class="tbl-actions">
                                    <button title="Editar bodega" onclick="editarBodega({{ $bodega->id }})" class="act-btn edit">✏️</button>
                                    <button title="Eliminar bodega" onclick="eliminarBodega({{ $bodega->id }})" class="act-btn del">🗑️</button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4"><div class="spinner-cell">📭 No hay bodegas registradas</div></td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div id="bod-sin-resultados" style="display:none;padding:10px 14px;color:#6B7280;font-size:12px;">
            📭 No se encontraron bodegas con ese nombre
        </div>
    </div>

</div>

{{-- ═══════════════════════════════════════════════
     MODAL BODEGA (crear / editar)
═══════════════════════════════════════════════ --}}
<div id="modalBodega" class="fixed inset-0 hidden items-center justify-center z-50 p-4 modal-backdrop-bod">
    <div class="modal-bodega">
        <div class="modal-head-bod">
            <div>
                <p class="modal-head-title" id="bodegaModalTitulo">Nueva Bodega</p>
                <p class="modal-head-sub">Punto de almacenamiento de inventario</p>
            </div>
            <button onclick="closeModalBodega()"
                style="border:none;background:transparent;font-size:20px;cursor:pointer;color:#6B7280;padding:4px;border-radius:6px;line-height:1;">✕</button>
        </div>

        <form id="formBodega" data-bodega-id="">
            <div class="modal-body-bod">
                <div class="bod-field">
                    <label>Descripción de la Bodega</label>
                    <input name="descripcion" placeholder="Ej: Bodega Central, Depósito Norte…" required>
                </div>
                <div class="bod-field">
                    <label>Punto de impresión</label>
                    <select name="punto_impresion" required>
                        <option value="RESTAURANTE">Restaurante</option>
                        <option value="DISCOTECA">Discoteca</option>
                    </select>
                    <p style="font-size:11px;color:#6B7280;margin-top:4px;">
                        Decide a qué impresoras de cocina/barra llegan las comandas de esta bodega.
                    </p>
                </div>
            </div>

            <div class="modal-foot-bod">
                <button type="button" onclick="closeModalBodega()" class="btn-outline">Cancelar</button>
                <button type="submit" class="btn-primary">💾 Guardar Bodega</button>
            </div>
        </form>
    </div>
</div>

<script>
    function filtrarBodega() {
        var texto = (document.getElementById('buscarBodega').value || '').toLowerCase().trim();
        var filas = document.querySelectorAll('#tbody-bodegas tr[data-desc]');
        var visibles = 0;
        filas.forEach(function (fila) {
            var coincide = !texto || fila.dataset.desc.includes(texto);
            fila.style.display = coincide ? '' : 'none';
            if (coincide) visibles++;
        });
        document.getElementById('bod-sin-resultados').style.display = (visibles === 0 && filas.length > 0) ? 'block' : 'none';
    }
</script>
