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

    .metric-value.money { font-size: 16px; }

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
    .table-wrapper {
        background: #fff;
        border: 1px solid #EAECF0;
        border-radius: 10px;
        overflow: hidden;
    }

    .table-scroll { overflow-x: auto; }

    table.prod-tbl {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
    }

    table.prod-tbl thead { background: #F8FAFC; border-bottom: 1px solid #EAECF0; }

    table.prod-tbl thead th {
        padding: 10px 12px;
        text-align: left;
        font-size: 11px;
        font-weight: 600;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
    }

    table.prod-tbl tbody tr {
        border-bottom: 1px solid #F3F4F6;
        transition: background 0.1s;
    }

    table.prod-tbl tbody tr:last-child { border-bottom: none; }
    table.prod-tbl tbody tr:hover { background: #F8FAFC; }
    table.prod-tbl tbody tr.inactivo { opacity: 0.6; }

    table.prod-tbl td { padding: 9px 12px; color: #374151; vertical-align: middle; }

    .td-mono {
        font-family: 'JetBrains Mono', 'Fira Mono', monospace;
        font-size: 12px;
        color: #1D4ED8;
        font-weight: 600;
    }

    .td-money { font-weight: 600; color: #111827; }

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
    .badge-gray { background: #F3F4F6; color: #374151; }
    .badge-amber { background: #FFFBEB; color: #92400E; }

    .dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        display: inline-block;
        background: currentColor;
    }

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
    .act-btn.state:hover { background: #FFFBEB; color: #D97706; }
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
    .modal-backdrop-prod {
        background: rgba(17, 24, 39, 0.5);
        backdrop-filter: blur(4px);
    }

    .modal-producto {
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

    .modal-head-prod {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid #EAECF0;
        flex-shrink: 0;
    }

    .modal-head-title { font-size: 15px; font-weight: 600; color: #111827; }
    .modal-head-sub { font-size: 12px; color: #6B7280; margin-top: 2px; }
    .modal-body-prod { flex: 1; overflow-y: auto; padding: 20px; }

    .modal-foot-prod {
        padding: 14px 20px;
        border-top: 1px solid #EAECF0;
        display: flex;
        gap: 8px;
        justify-content: flex-end;
        flex-shrink: 0;
    }

    .prod-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    @media (max-width: 480px) { .prod-grid { grid-template-columns: 1fr; } }

    .prod-field { display: flex; flex-direction: column; gap: 3px; }

    .prod-field label {
        font-size: 11px;
        font-weight: 500;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .prod-field input,
    .prod-field select,
    .prod-field textarea {
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

    .prod-field input:focus,
    .prod-field select:focus,
    .prod-field textarea:focus { border-color: #1D4ED8; background: #fff; }

    .spinner-inline {
        display: inline-block;
        width: 14px;
        height: 14px;
        border: 2px solid rgba(255,255,255,.5);
        border-top-color: #fff;
        border-radius: 50%;
        animation: spin-prod .7s linear infinite;
        vertical-align: -2px;
    }

    @keyframes spin-prod { to { transform: rotate(360deg); } }

    @media (max-width: 640px) {
        .metrics-row { grid-template-columns: 1fr 1fr; }
    }
</style>

<div id="view-productos">

    {{-- ── ENCABEZADO ── --}}
    <div class="sec-header">
        <div>
            <p class="sec-title">📦 Catálogo de Productos</p>
            <p class="sec-subtitle">Gestión de productos, precios e impuestos del punto de venta</p>
        </div>
        <button class="btn-primary" onclick="switchProductoTab('info'); document.getElementById('formProducto').reset(); document.getElementById('producto_id').value=''; document.getElementById('prod_base_seleccionado').textContent=''; toggleEnsambleProducto(); toggleAcompanamientoProducto(); openModalProducto();">
            ＋ Nuevo Producto
        </button>
    </div>

    {{-- ── MÉTRICAS ── --}}
    <div class="metrics-row">
        <div class="metric-card" style="--accent:#1D4ED8">
            <p class="metric-label">Total Productos</p>
            <p class="metric-value">{{ $metricas['total'] }}</p>
            <p class="metric-sub">En el catálogo</p>
        </div>
        <div class="metric-card" style="--accent:#059669">
            <p class="metric-label">Activos</p>
            <p class="metric-value">{{ $metricas['activos'] }}</p>
            <p class="metric-sub">Disponibles para venta</p>
        </div>
        <div class="metric-card" style="--accent:#DC2626">
            <p class="metric-label">Inactivos</p>
            <p class="metric-value">{{ $metricas['inactivos'] }}</p>
            <p class="metric-sub">{{ $metricas['total'] ? round($metricas['inactivos'] / $metricas['total'] * 100) : 0 }}% del total</p>
        </div>
        <div class="metric-card" style="--accent:#D97706">
            <p class="metric-label">Sin Stock</p>
            <p class="metric-value">{{ $metricas['sin_stock'] }}</p>
            <p class="metric-sub">Afectan inventario</p>
        </div>
        <div class="metric-card" style="--accent:#7C3AED">
            <p class="metric-label">Valor Inventario</p>
            <p class="metric-value money">${{ number_format($metricas['valor_inventario'], 0, ',', '.') }}</p>
            <p class="metric-sub">Costo a precio de venta</p>
        </div>
    </div>

    {{-- ── FILTROS ── --}}
    <div class="filter-bar">
        <div class="fi-group" style="flex:2;min-width:220px;">
            <span class="fi-label">🔍</span>
            <input autocomplete="off" type="text" id="buscarTablaProducto" oninput="filtrarProducto()"
                placeholder="Buscar producto por nombre o código…" class="fi-input">
        </div>
        <div class="fi-group">
            <select class="fi-select" id="filtroEstadoProducto" onchange="filtrarProducto()">
                <option value="">Todos los estados</option>
                <option value="0">Activo</option>
                <option value="1">Inactivo</option>
            </select>
        </div>
        <button class="btn-outline" onclick="limpiarFiltrosProducto()">✕ Limpiar</button>
    </div>

    {{-- ── TABLA ── --}}
    <div id="tablaProductos">
        @include('productos.partials.tabla')
    </div>

</div>

{{-- ═══════════════════════════════════════════════
     MODAL PRODUCTO (crear / editar)
═══════════════════════════════════════════════ --}}
<div id="modalProducto" class="fixed inset-0 hidden items-center justify-center z-50 p-4 modal-backdrop-prod">
    <div class="modal-producto">

        <div class="modal-head-prod">
            <div>
                <p class="modal-head-title" id="prod-modal-title">Registrar Nuevo Producto</p>
                <p class="modal-head-sub">Complete la información, precio e impuestos</p>
            </div>
            <button onclick="closeModalProducto()"
                style="border:none;background:transparent;font-size:20px;cursor:pointer;color:#6B7280;padding:4px;border-radius:6px;line-height:1;">✕</button>
        </div>

        {{-- TABS --}}
        <div class="flex border-b border-gray-100 px-5" style="flex-shrink:0;">
            <button type="button" onclick="switchProductoTab('info')" data-tab="info"
                class="producto-tab px-4 py-3 text-sm font-semibold border-b-2 border-blue-600 text-blue-600 transition-colors">
                Información
            </button>
            <button type="button" onclick="switchProductoTab('impuestos')" data-tab="impuestos"
                class="producto-tab px-4 py-3 text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition-colors">
                Impuestos y Precios
            </button>
            <button type="button" onclick="switchProductoTab('ensamble')" data-tab="ensamble"
                class="producto-tab px-4 py-3 text-sm font-semibold border-b-2 border-transparent text-gray-500 hover:text-gray-700 transition-colors">
                Ensamble
            </button>
        </div>

        <form id="formProducto" class="modal-body-prod">
            <input type="hidden" id="producto_id">

            {{-- HOJA 1: INFORMACIÓN --}}
            <div id="tab-info" class="producto-tab-panel">
              <div class="prod-grid">
                <div class="prod-field">
                    <label>Código del Producto</label>
                    <input autocomplete="off" name="codigo" placeholder="Ej: PROD-001">
                </div>

                <div class="prod-field">
                    <label>Categoría</label>
                    <select name="categoria">
                        <option value="">Seleccione…</option>
                        <option value="General">General</option>
                    </select>
                </div>

                <div class="prod-field" style="grid-column:1/-1;">
                    <label>Grupo / Destino de Impresión</label>
                    <select name="grupo_menu_id" id="prod_grupo_menu_id" style="font-weight:600;color:#1D4ED8;">
                        <option value="">Sin impresión (No genera ticket)</option>
                        @foreach ($grupos as $grupo)
                            <option value="{{ $grupo->id }}">📂 {{ $grupo->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="prod-field">
                    <label>Unidad de Medida</label>
                    <input autocomplete="off" name="und_detal" placeholder="Ej: Unidad, Kg, Paquete">
                </div>

                <div class="prod-field">
                    <label>Afecta Inventario</label>
                    <select name="afecta_inventario">
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                </div>

                <div class="prod-field" style="grid-column:1/-1;">
                    <label>Nombre / Descripción</label>
                    <input autocomplete="off" name="descripcion" placeholder="Nombre completo del producto">
                </div>

                <div class="prod-field" style="grid-column:1/-1;">
                    <label>Características Adicionales</label>
                    <textarea name="caracteristicas" rows="3" placeholder="Detalles técnicos, colores, etc." style="resize:vertical;"></textarea>
                </div>
              </div>
            </div>

            {{-- HOJA 2: IMPUESTOS Y PRECIOS --}}
            <div id="tab-impuestos" class="producto-tab-panel hidden">
              <div class="prod-grid">
                <div class="prod-field">
                    <label>Precio de Venta</label>
                    <input autocomplete="off" type="number" name="precio" placeholder="0.00">
                </div>

                <div class="prod-field">
                    <label>IVA Venta (%)</label>
                    <select name="iva_ventas">
                        <option value="">Heredar del Grupo Contable</option>
                        <option value="0">0% (Excluido)</option>
                        <option value="5">5%</option>
                        <option value="19">19%</option>
                    </select>
                </div>

                <div class="prod-field" style="grid-column:1/-1;">
                    <label>Integración Contable</label>
                    <select name="integracion_contable_id">
                        <option value="">Seleccione…</option>
                        @foreach ($integracionesContables as $ic)
                            <option value="{{ $ic->id }}">{{ $ic->nombre }}</option>
                        @endforeach
                    </select>
                    <p style="font-size:11px;color:#9CA3AF;margin-top:4px;">
                        Obligatorio: sin esto, el sistema rechaza cualquier venta de este producto al cerrar la
                        mesa. Define en qué categoría contable cae (Cervezas, Licores, Comida, etc.).
                    </p>
                </div>
              </div>
            </div>

            {{-- HOJA 3: ENSAMBLE --}}
            <div id="tab-ensamble" class="producto-tab-panel hidden">
              <div class="prod-grid">
                <div class="prod-field" style="grid-column:1/-1;">
                    <label style="display:flex;align-items:center;gap:8px;font-weight:600;">
                        <input type="checkbox" name="es_ensamblado" id="prod_es_ensamblado" value="1"
                            onchange="toggleEnsambleProducto()" style="width:16px;height:16px;">
                        Producto ensamblado
                    </label>
                    <p style="font-size:11.5px;color:#9CA3AF;margin-top:4px;">
                        Márcalo si este producto se arma a partir de otro (ej: un "Cubetazo Poker" que en realidad
                        descuenta varias unidades de "Poker" del inventario). Este producto NO llevará su propio
                        inventario — se descuenta el del producto base.
                    </p>
                </div>

                <div id="prod_ensamble_campos" class="hidden" style="grid-column:1/-1;display:contents;">
                    <div class="prod-field aj-autocomplete" style="grid-column:1/-1;position:relative;">
                        <label>Producto base (insumo real)</label>
                        <input type="text" id="buscarProductoBase" autocomplete="off"
                            oninput="ejecutarBusquedaProductoBase()"
                            placeholder="Buscar por nombre o código…">
                        <input type="hidden" name="producto_base_id" id="prod_base_id">
                        <div id="resultadosProductoBase"
                            class="aj-results floating hidden"
                            style="position:absolute;top:100%;left:0;right:0;background:#fff;border:1px solid #E5E7EB;border-radius:8px;box-shadow:0 8px 20px -6px rgba(0,0,0,.15);max-height:220px;overflow-y:auto;z-index:20;"></div>
                        <div id="prod_base_seleccionado" style="margin-top:6px;font-size:12.5px;color:#1D4ED8;font-weight:600;"></div>
                    </div>

                    <div class="prod-field">
                        <label>Cantidad consumida por unidad vendida</label>
                        <input autocomplete="off" type="number" name="factor_consumo" id="prod_factor_consumo" step="0.01" min="0.01" placeholder="Ej: 6">
                        <p style="font-size:11px;color:#9CA3AF;margin-top:4px;">Ej: si un Cubetazo trae 6 Poker, aquí va 6.</p>
                    </div>

                    <div class="prod-field" style="grid-column:1/-1;">
                        <label style="display:flex;align-items:center;gap:8px;font-weight:600;">
                            <input type="checkbox" id="prod_usar_bodega_origen" onchange="toggleBodegaOrigenProducto()" style="width:16px;height:16px;">
                            Descontar de una bodega de origen específica
                        </label>
                        <p style="font-size:11.5px;color:#9CA3AF;margin-top:4px;">
                            Márcalo si el insumo real vive en OTRA bodega distinta a la de la caja que vende este
                            producto (ej. la carne de una hamburguesa vendida en la caja de Discoteca en realidad
                            está en la bodega de Cocina). Sin marcar, se sigue revisando/descontando de la bodega
                            de la caja, como siempre.
                        </p>
                        <div id="prod_bodega_origen_campo" class="hidden" style="margin-top:8px;">
                            <select name="bodega_origen_id" id="prod_bodega_origen_id">
                                <option value="">Selecciona la bodega de origen…</option>
                                @foreach ($bodegas as $b)
                                    <option value="{{ $b->id }}">{{ $b->descripcion }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>

                <div style="grid-column:1/-1;border-top:1px dashed #E5E7EB;margin:6px 0 2px;padding-top:14px;">
                    <label style="display:flex;align-items:center;gap:8px;font-weight:600;">
                        <input type="checkbox" name="tiene_acompanamiento" id="prod_tiene_acompanamiento" value="1"
                            onchange="toggleAcompanamientoProducto()" style="width:16px;height:16px;">
                        Insertar acompañamiento (mezcla a elegir)
                    </label>
                    <p style="font-size:11.5px;color:#9CA3AF;margin-top:4px;">
                        Para combos donde el mesero reparte varias unidades entre distintas opciones al comandar
                        (ej. "Cubetazo Mix" = hasta 10 unidades entre Poker/Águila/Costeña, a elección del cliente).
                        Este producto se vende siempre a su propio precio fijo — la mezcla solo decide qué se
                        descuenta del inventario. No se puede combinar con "Producto ensamblado".
                    </p>
                </div>

                <div id="prod_acompanamiento_campos" class="hidden" style="grid-column:1/-1;">
                    <div class="prod-field" style="grid-column:1/-1;">
                        <label>Grupo de acompañamiento</label>
                        <select name="acompanamiento_grupo_id" id="prod_acompanamiento_grupo_id">
                            <option value="">Seleccione…</option>
                            @foreach ($gruposAcompanamiento as $ga)
                                <option value="{{ $ga->id }}">{{ $ga->descripcion }} (hasta {{ $ga->cantidad_maxima }} und.)</option>
                            @endforeach
                        </select>
                        <p style="font-size:11px;color:#9CA3AF;margin-top:4px;">
                            ¿No existe el grupo todavía? Créalo primero en "Acompañamientos" en el menú.
                        </p>
                    </div>
                </div>
              </div>
            </div>
        </form>

        <div class="modal-foot-prod">
            <button type="button" onclick="closeModalProducto()" class="btn-outline">Cancelar</button>
            <button type="button" onclick="guardarProducto()" class="btn-primary">💾 Guardar Producto</button>
        </div>
    </div>
</div>

<script>
    function limpiarFiltrosProducto() {
        document.getElementById('buscarTablaProducto').value = '';
        document.getElementById('filtroEstadoProducto').value = '';
        filtrarProducto();
    }
</script>
