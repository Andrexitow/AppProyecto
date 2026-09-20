<style>
    :root {
        --co-bg: #F6F7F9;
        --co-surface: #FFFFFF;
        --co-border: #E4E7EC;
        --co-border-strong: #D0D5DD;
        --co-text: #101828;
        --co-text-soft: #475467;
        --co-text-faint: #98A2B3;
        --co-blue: #2563EB;
        --co-blue-dark: #1D4ED8;
        --co-blue-bg: #EFF4FF;
        --co-green: #059669;
        --co-green-bg: #ECFDF5;
        --co-red: #DC2626;
        --co-red-bg: #FEF3F2;
        --co-amber: #D97706;
        --co-amber-bg: #FFFAEB;
        --co-violet: #7C3AED;
        --co-violet-bg: #F5F3FF;
        --co-radius: 12px;
        --co-radius-sm: 8px;
        --co-shadow: 0 1px 2px rgba(16,24,40,.04), 0 1px 3px rgba(16,24,40,.06);
        --co-shadow-lift: 0 8px 24px rgba(16,24,40,.10);
    }

    #view-compras { color: var(--co-text); }

    /* ── Encabezado ── */
    .sec-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 14px;
        margin-bottom: 20px;
    }

    .sec-title {
        font-size: 19px;
        font-weight: 700;
        color: var(--co-text);
        letter-spacing: -0.4px;
    }

    .sec-subtitle {
        font-size: 12.5px;
        color: var(--co-text-faint);
        margin-top: 3px;
    }

    /* ── Métricas resumen ── */
    .metrics-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
        gap: 12px;
        margin-bottom: 18px;
    }

    .metric-card {
        background: var(--co-surface);
        border: 1px solid var(--co-border);
        border-radius: var(--co-radius);
        padding: 14px 16px;
        box-shadow: var(--co-shadow);
        transition: border-color .15s, box-shadow .15s;
    }

    .metric-card:hover {
        border-color: var(--co-border-strong);
        box-shadow: var(--co-shadow-lift);
    }

    .metric-label {
        font-size: 11px;
        font-weight: 600;
        color: var(--co-text-faint);
        letter-spacing: 0.3px;
    }

    .metric-value {
        font-size: 22px;
        font-weight: 700;
        color: var(--co-text);
        margin-top: 6px;
        letter-spacing: -0.5px;
        line-height: 1.1;
    }

    .metric-value.money {
        font-size: 18px;
    }

    .metric-sub {
        font-size: 11.5px;
        color: var(--co-text-faint);
        margin-top: 3px;
    }

    /* acentos por tarjeta, apoyados en color de fondo del ícono en vez de barra superior */
    .metric-card[style*="1D4ED8"] .metric-value { color: var(--co-blue-dark); }
    .metric-card[style*="059669"] .metric-value { color: var(--co-green); }
    .metric-card[style*="7C3AED"] .metric-value { color: var(--co-violet); }
    .metric-card[style*="D97706"] .metric-value { color: var(--co-amber); }
    .metric-card[style*="DC2626"] .metric-value { color: var(--co-red); }

    /* ── Barra de filtros ── */
    .filter-bar {
        background: var(--co-surface);
        border: 1px solid var(--co-border);
        border-radius: var(--co-radius);
        padding: 12px 14px;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
        margin-bottom: 14px;
        box-shadow: var(--co-shadow);
    }

    .filter-bar .fi-group {
        display: flex;
        align-items: center;
        gap: 8px;
        flex: 1;
        min-width: 170px;
    }

    .fi-label {
        font-size: 12px;
        color: var(--co-text-faint);
        white-space: nowrap;
    }

    .fi-input {
        flex: 1;
        border: 1px solid var(--co-border-strong);
        border-radius: var(--co-radius-sm);
        padding: 7px 11px;
        font-size: 12.5px;
        color: var(--co-text);
        background: var(--co-bg);
        outline: none;
        transition: border-color .15s, background .15s, box-shadow .15s;
    }

    .fi-input:focus {
        border-color: var(--co-blue);
        background: var(--co-surface);
        box-shadow: 0 0 0 3px var(--co-blue-bg);
    }

    .fi-input::placeholder {
        color: var(--co-text-faint);
    }

    .fi-select {
        border: 1px solid var(--co-border-strong);
        border-radius: var(--co-radius-sm);
        padding: 7px 11px;
        font-size: 12.5px;
        color: var(--co-text);
        background: var(--co-bg);
        outline: none;
        cursor: pointer;
    }

    .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--co-blue-dark);
        color: #fff;
        border: none;
        border-radius: var(--co-radius-sm);
        padding: 8px 16px;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        transition: background .15s, transform .1s;
        white-space: nowrap;
    }

    .btn-primary:hover {
        background: #1a3fb8;
    }

    .btn-primary:active {
        transform: translateY(1px);
    }

    .btn-primary:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .btn-outline {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: var(--co-surface);
        color: var(--co-text-soft);
        border: 1px solid var(--co-border-strong);
        border-radius: var(--co-radius-sm);
        padding: 8px 14px;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        transition: all .15s;
        white-space: nowrap;
    }

    .btn-outline:hover {
        background: var(--co-bg);
        border-color: #9CA3AF;
    }

    .btn-outline.danger {
        color: var(--co-red);
        border-color: #FCA5A5;
    }

    .btn-outline.danger:hover {
        background: var(--co-red-bg);
        border-color: var(--co-red);
    }

    /* ── Tabla ── */
    .table-wrapper {
        background: var(--co-surface);
        border: 1px solid var(--co-border);
        border-radius: var(--co-radius);
        overflow: hidden;
        box-shadow: var(--co-shadow);
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
        background: var(--co-bg);
        border-bottom: 1px solid var(--co-border);
    }

    table.compras-tbl thead th {
        padding: 11px 14px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        color: var(--co-text-faint);
        letter-spacing: 0.3px;
        white-space: nowrap;
        cursor: pointer;
        user-select: none;
        transition: color .15s;
    }

    table.compras-tbl thead th:hover {
        color: var(--co-blue-dark);
    }

    table.compras-tbl thead th .sort-icon {
        display: inline-block;
        margin-left: 4px;
        opacity: 0.35;
        font-size: 10px;
    }

    table.compras-tbl thead th.sorted {
        color: var(--co-blue-dark);
    }

    table.compras-tbl thead th.sorted .sort-icon {
        opacity: 1;
    }

    table.compras-tbl tbody tr {
        border-bottom: 1px solid #F1F2F4;
        transition: background .1s;
    }

    table.compras-tbl tbody tr:last-child {
        border-bottom: none;
    }

    table.compras-tbl tbody tr:hover {
        background: #FAFBFC;
    }

    table.compras-tbl tbody tr.anulada {
        opacity: 0.55;
    }

    table.compras-tbl tbody tr.anulada td {
        text-decoration: line-through;
    }

    table.compras-tbl tbody tr.anulada .badge,
    table.compras-tbl tbody tr.anulada .tbl-actions {
        text-decoration: none;
    }

    table.compras-tbl td {
        padding: 11px 14px;
        color: var(--co-text-soft);
        vertical-align: middle;
    }

    .td-mono {
        font-family: 'JetBrains Mono', 'Fira Mono', monospace;
        font-size: 12px;
        color: var(--co-text);
        font-weight: 600;
        background: var(--co-bg);
        padding: 2px 7px;
        border-radius: 5px;
    }

    .td-proveedor {
        line-height: 1.35;
    }

    .td-proveedor .prov-nombre {
        font-weight: 600;
        color: var(--co-text);
        max-width: 220px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .td-proveedor .prov-nit {
        font-size: 11px;
        color: var(--co-text-faint);
    }

    .td-money {
        font-weight: 700;
        color: var(--co-green);
        text-align: right;
        font-variant-numeric: tabular-nums;
    }

    /* ── Badges ── */
    .badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
    }

    .badge-green { background: var(--co-green-bg); color: #065F46; }
    .badge-red { background: var(--co-red-bg); color: #991B1B; }
    .badge-gray { background: #F2F4F7; color: #344054; }

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
        gap: 2px;
    }

    .act-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        border-radius: var(--co-radius-sm);
        border: none;
        cursor: pointer;
        font-size: 13px;
        transition: all .15s;
        background: transparent;
        color: var(--co-text-faint);
        position: relative;
    }

    .act-btn:hover {
        background: var(--co-bg);
        color: var(--co-text);
    }

    .act-btn.view:hover { background: var(--co-blue-bg); color: var(--co-blue-dark); }
    .act-btn.reg:hover { background: var(--co-green-bg); color: var(--co-green); }
    .act-btn.rev:hover { background: var(--co-amber-bg); color: var(--co-amber); }
    .act-btn.undo:hover { background: var(--co-blue-bg); color: var(--co-blue-dark); }
    .act-btn.del:hover { background: var(--co-red-bg); color: var(--co-red); }

    .act-btn::after {
        content: attr(data-tip);
        position: absolute;
        bottom: calc(100% + 6px);
        left: 50%;
        transform: translateX(-50%);
        background: #1F2937;
        color: #fff;
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 6px;
        white-space: nowrap;
        pointer-events: none;
        opacity: 0;
        transition: opacity .15s;
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
        padding: 12px 16px;
        border-top: 1px solid #F1F2F4;
        flex-wrap: wrap;
        gap: 8px;
        background: var(--co-bg);
    }

    .pag-info {
        font-size: 12px;
        color: var(--co-text-faint);
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
        border-radius: var(--co-radius-sm);
        border: 1px solid var(--co-border);
        background: var(--co-surface);
        font-size: 12px;
        color: var(--co-text-soft);
        cursor: pointer;
        transition: all .15s;
    }

    .pag-btn:hover {
        background: var(--co-bg);
        border-color: var(--co-border-strong);
    }

    .pag-btn.active {
        background: var(--co-blue-dark);
        color: #fff;
        border-color: var(--co-blue-dark);
    }

    .pag-btn:disabled {
        opacity: 0.4;
        cursor: not-allowed;
    }

    /* ── Modal ── */
    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(16, 24, 40, .55);
        backdrop-filter: blur(4px);
        z-index: 9000;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }

    .modal-compra {
        background: var(--co-surface);
        border-radius: 18px;
        width: 100%;
        max-width: 1040px;
        max-height: 94vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 24px 64px rgba(16,24,40,.22);
    }

    .modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 18px 22px;
        border-bottom: 1px solid var(--co-border);
        flex-shrink: 0;
    }

    .modal-head-title {
        font-size: 16px;
        font-weight: 700;
        color: var(--co-text);
    }

    .modal-head-sub {
        font-size: 12px;
        color: var(--co-text-faint);
        margin-top: 2px;
    }

    .modal-body {
        flex: 1;
        overflow-y: auto;
        padding: 22px;
        background: var(--co-bg);
    }

    .modal-foot {
        padding: 14px 22px;
        border-top: 1px solid var(--co-border);
        display: flex;
        gap: 10px;
        align-items: center;
        justify-content: space-between;
        flex-shrink: 0;
        flex-wrap: wrap;
        background: var(--co-surface);
    }

    .co-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
        margin-bottom: 6px;
    }

    @media (max-width: 480px) {
        .co-grid { grid-template-columns: 1fr; }
    }

    .co-field {
        display: flex;
        flex-direction: column;
        gap: 4px;
        position: relative;
    }

    .co-field label {
        font-size: 11px;
        font-weight: 600;
        color: var(--co-text-faint);
        letter-spacing: 0.3px;
    }

    .co-field input,
    .co-field select,
    .co-field textarea {
        border: 1px solid var(--co-border-strong);
        border-radius: var(--co-radius-sm);
        padding: 8px 11px;
        font-size: 13px;
        color: var(--co-text);
        outline: none;
        background: var(--co-surface);
        transition: border-color .15s, box-shadow .15s;
        font-family: inherit;
        width: 100%;
    }

    .co-field input:focus,
    .co-field select:focus,
    .co-field textarea:focus {
        border-color: var(--co-blue);
        box-shadow: 0 0 0 3px var(--co-blue-bg);
    }

    .co-product-wrap { position: relative; min-width: 250px; }
    .co-product-results {
        position: relative;
        z-index: 20;
        margin-top: 6px;
        min-width: 330px;
        max-height: 360px;
        overflow-y: auto;
        border: 1px solid var(--co-border);
        border-radius: 10px;
        background: var(--co-surface);
        box-shadow: var(--co-shadow-lift);
    }
    .co-product-result {
        width: 100%;
        padding: 10px 12px;
        border: 0;
        border-bottom: 1px solid #F1F2F4;
        background: var(--co-surface);
        color: var(--co-text-soft);
        text-align: left;
        cursor: pointer;
        font-size: 12px;
        transition: background .1s;
    }
    .co-product-result:hover { background: var(--co-blue-bg); }
    .co-product-result strong { display: block; font-size: 12.5px; color: var(--co-text); }
    .co-product-result small { color: var(--co-text-faint); }

    .co-tabs {
        display: flex;
        gap: 2px;
        border-bottom: 1px solid var(--co-border);
        margin-top: 20px;
        overflow-x: auto;
    }
    .co-tab {
        border: 0;
        background: transparent;
        color: var(--co-text-faint);
        padding: 10px 14px;
        cursor: pointer;
        font: 700 12px inherit;
        white-space: nowrap;
        border-bottom: 2px solid transparent;
        transition: color .15s;
    }
    .co-tab:hover { color: var(--co-text-soft); }
    .co-tab.active { color: var(--co-blue-dark); border-bottom-color: var(--co-blue-dark); }
    .co-tab-panel { padding-top: 4px; }

    .co-item-extra td { background: var(--co-bg); padding: 12px 14px !important; }
    .co-item-fields { display: grid; grid-template-columns: repeat(4, minmax(105px, 1fr)); gap: 10px; }
    .co-item-toggle {
        border: 1px solid #BFD4FF;
        border-radius: var(--co-radius-sm);
        background: var(--co-blue-bg);
        color: var(--co-blue-dark);
        cursor: pointer;
        padding: 6px 9px;
        font-size: 11px;
        font-weight: 600;
        transition: background .15s;
    }
    .co-item-toggle:hover { background: #DCE7FF; }

    .co-payment-row { display: grid; grid-template-columns: 1fr 1fr 1.3fr 30px; gap: 8px; align-items: end; margin-bottom: 8px; }
    .co-help { color: var(--co-text-faint); font-size: 11.5px; margin: 6px 0 14px; }

    @media (max-width: 700px) {
        .co-item-fields { grid-template-columns: 1fr 1fr; }
        .co-payment-row { grid-template-columns: 1fr 1fr; }
    }

    .fac-section-title {
        font-size: 12px;
        font-weight: 700;
        color: var(--co-text-soft);
        letter-spacing: 0.2px;
        margin: 18px 0 12px;
        padding-bottom: 8px;
        border-bottom: 1px solid var(--co-border);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    /* ── Buscador de proveedor (combobox) ── */
    .prov-combo { position: relative; }
    .prov-combo-input-wrap { position: relative; }
    .prov-combo-input-wrap .prov-icon {
        position: absolute;
        left: 11px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 12px;
        color: var(--co-text-faint);
        pointer-events: none;
    }

    #co-proveedor-input { padding-left: 32px !important; }

    #co-proveedor-input.selected {
        background: var(--co-blue-bg) !important;
        border-color: #BFD4FF !important;
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
        color: var(--co-text-faint);
        cursor: pointer;
        font-size: 13px;
        display: none;
        padding: 3px 5px;
        border-radius: 5px;
    }
    .prov-clear:hover { color: var(--co-red); background: var(--co-red-bg); }
    .prov-combo-input-wrap.has-selection .prov-clear { display: block; }

    .prov-results {
        position: absolute;
        top: calc(100% + 5px);
        left: 0;
        right: 0;
        background: var(--co-surface);
        border: 1px solid var(--co-border);
        border-radius: 10px;
        box-shadow: var(--co-shadow-lift);
        max-height: 220px;
        overflow-y: auto;
        z-index: 200;
    }

    .prov-result-item {
        padding: 9px 12px;
        cursor: pointer;
        border-bottom: 1px solid #F1F2F4;
        display: flex;
        flex-direction: column;
        gap: 1px;
        transition: background .1s;
    }
    .prov-result-item:last-child { border-bottom: none; }
    .prov-result-item:hover,
    .prov-result-item.active { background: var(--co-blue-bg); }

    .prov-result-nombre { font-size: 12.5px; font-weight: 600; color: var(--co-text); }
    .prov-result-nit { font-size: 11px; color: var(--co-text-faint); }

    .prov-results-empty {
        padding: 16px 12px;
        text-align: center;
        font-size: 12px;
        color: var(--co-text-faint);
    }

    .prov-hint { font-size: 11px; color: var(--co-text-faint); margin-top: 4px; }
    .prov-hint.ok { color: var(--co-green); font-weight: 600; }

    /* ── Items de compra ── */
    .items-tbl-wrap {
        overflow: visible;
        border: 1px solid var(--co-border);
        border-radius: 10px;
        background: var(--co-surface);
    }

    table.items-tbl { width: 100%; border-collapse: collapse; font-size: 12.5px; }

    table.items-tbl thead th {
        padding: 9px 11px;
        text-align: left;
        font-size: 11px;
        color: var(--co-text-faint);
        font-weight: 700;
        background: var(--co-bg);
        letter-spacing: 0.3px;
    }
    table.items-tbl thead th:first-child { border-top-left-radius: 10px; }
    table.items-tbl thead th:last-child { border-top-right-radius: 10px; }

    table.items-tbl tbody td { padding: 9px 11px; border-top: 1px solid #F1F2F4; }

    .items-input {
        border: 1px solid var(--co-border-strong);
        border-radius: 7px;
        padding: 6px 9px;
        font-size: 12px;
        width: 100%;
        outline: none;
        background: var(--co-surface);
        transition: border-color .15s, box-shadow .15s;
    }
    .items-input:focus { border-color: var(--co-blue); box-shadow: 0 0 0 3px var(--co-blue-bg); }

    /* Totales */
    .totales-box {
        background: var(--co-surface);
        border: 1px solid var(--co-border);
        border-radius: var(--co-radius);
        padding: 14px 16px;
        margin-top: 18px;
    }

    .totales-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 5px 0;
        font-size: 13px;
        color: var(--co-text-soft);
    }

    .totales-row.total-final {
        border-top: 1px solid var(--co-border);
        margin-top: 8px;
        padding-top: 12px;
        font-size: 17px;
        font-weight: 700;
        color: var(--co-text);
    }

    /* Spinner */
    .spinner-cell {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 44px;
        color: var(--co-text-faint);
        font-size: 13px;
        gap: 10px;
    }

    @keyframes spin { to { transform: rotate(360deg); } }

    .spinner {
        width: 18px;
        height: 18px;
        border: 2px solid var(--co-border);
        border-top-color: var(--co-blue-dark);
        border-radius: 50%;
        animation: spin .7s linear infinite;
    }

    @media (max-width: 640px) {
        .metrics-row { grid-template-columns: 1fr 1fr; }

        /* Ocultamos Total/Estado de pago/Saldo/Usuario/Fecha de registro
           (5-9). Antes se ocultaba desde la columna 5 en adelante, lo que
           también tapaba "Estado" (10) y "Acciones" (11) sin ninguna forma
           de llegar a ellas en el celular. */
        table.compras-tbl thead th:nth-child(n+5):nth-child(-n+9) { display: none; }
        table.compras-tbl tbody td:nth-child(n+5):nth-child(-n+9) { display: none; }

        .modal-compra { max-height: 96vh; }
        .co-product-wrap { min-width: 190px; }
        .co-product-results { min-width: 0; max-height: 300px; }
        .items-tbl-wrap { overflow-x: auto; }
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
            <input autocomplete="off" class="fi-input" type="text" id="co-buscar" placeholder="Factura, proveedor, NIT…"
                oninput="aplicarFiltrosCompras()">
        </div>
        <div class="fi-group">
            <span class="fi-label">Desde</span>
            <input autocomplete="off" class="fi-input" type="date" id="co-desde" onchange="aplicarFiltrosCompras()">
        </div>
        <div class="fi-group">
            <span class="fi-label">Hasta</span>
            <input autocomplete="off" class="fi-input" type="date" id="co-hasta" onchange="aplicarFiltrosCompras()">
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
                        <th onclick="sortTablaCompras('prefijo')" data-col="prefijo">
                            Prefijo <span class="sort-icon">↕</span>
                        </th>
                        <th onclick="sortTablaCompras('consecutivo')" data-col="consecutivo">
                            Consecutivo <span class="sort-icon">↕</span>
                        </th>
                        <th onclick="sortTablaCompras('numero_factura')" data-col="numero_factura">
                            N° factura <span class="sort-icon">↕</span>
                        </th>
                        <th onclick="sortTablaCompras('proveedor')" data-col="proveedor">
                            Proveedor <span class="sort-icon">↕</span>
                        </th>
                        <th onclick="sortTablaCompras('total')" data-col="total" style="text-align:right;">
                            Total <span class="sort-icon">↕</span>
                        </th>
                        <th onclick="sortTablaCompras('estado_pago')" data-col="estado_pago">Estado de pago <span class="sort-icon">↕</span></th>
                        <th onclick="sortTablaCompras('saldo_pendiente')" data-col="saldo_pendiente" style="text-align:right;">Saldo <span class="sort-icon">↕</span></th>
                        <th onclick="sortTablaCompras('usuario')" data-col="usuario">Usuario <span class="sort-icon">↕</span></th>
                        <th onclick="sortTablaCompras('fecha_registro')" data-col="fecha_registro">Fecha de registro <span class="sort-icon">↕</span></th>
                        <th>Estado</th>
                        <th style="text-align:center;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="compras-lista">
                    <tr>
                        <td colspan="11">
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
                    <input autocomplete="off" type="text" id="co-prefijo" placeholder="Ej: FC" maxlength="10" value="FC"
                        oninput="this.value=this.value.toUpperCase()" onchange="cargarSiguienteConsecutivoCompra()">
                </div>
                <div class="co-field">
                    <label>Consecutivo *</label>
                    <input autocomplete="off" type="number" id="co-consecutivo" min="1" step="1" required>
                </div>
                <div class="co-field">
                    <label>N° Factura proveedor *</label>
                    <input autocomplete="off" type="text" id="co-numero" placeholder="Número de la factura del proveedor">
                </div>
                <div class="co-field">
                    <label>Fecha *</label>
                    <input autocomplete="off" type="date" id="co-fecha">
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

            <div class="co-tabs">
                <button class="co-tab active" data-tab="productos" onclick="cambiarPestanaCompra('productos', this)">Productos</button>
                <button class="co-tab" data-tab="retenciones" onclick="cambiarPestanaCompra('retenciones', this)">Retenciones</button>
                <button class="co-tab" data-tab="costos" onclick="cambiarPestanaCompra('costos', this)">Costos adicionales</button>
                <button class="co-tab" data-tab="pagos" onclick="cambiarPestanaCompra('pagos', this)">Formas de pago</button>
            </div>

            <div class="co-tab-panel" id="co-panel-productos">
            <p class="fac-section-title">Productos
                <button class="btn-primary" onclick="agregarLineaCompra()" style="font-size:11px;padding:4px 10px;">+ Agregar producto</button>
            </p>
            <p class="co-help">Al agregar un producto se abre “Impuestos y detalles”, donde registra el valor ICL, ADV, INC o ICO de esa línea.</p>
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

            </div>

            <div class="co-tab-panel" id="co-panel-retenciones" style="display:none;">
                <p class="fac-section-title">Retenciones del documento</p>
                <p class="co-help">Se calculan sobre la base gravable después de descuentos y se descuentan del total a pagar.</p>
                <div class="co-grid">
                    <div class="co-field"><label>Retefuente %</label><input autocomplete="off" type="number" min="0" step="0.01" id="co-retefuente" value="0" oninput="calcularTotalesCompra()"></div>
                    <div class="co-field"><label>ReteIVA %</label><input autocomplete="off" type="number" min="0" step="0.01" id="co-reteiva" value="0" oninput="calcularTotalesCompra()"></div>
                    <div class="co-field"><label>ReteICA %</label><input autocomplete="off" type="number" min="0" step="0.01" id="co-reteica" value="0" oninput="calcularTotalesCompra()"></div>
                </div>
            </div>

            <div class="co-tab-panel" id="co-panel-costos" style="display:none;">
                <p class="fac-section-title">Costos adicionales</p>
                <p class="co-help">Fletes, seguros, acarreos u otros valores que aumentan el total de esta compra.</p>
                <div class="co-grid"><div class="co-field"><label>Valor total de costos adicionales</label><input autocomplete="off" type="number" min="0" step="0.01" id="co-otros-cargos" value="0" oninput="calcularTotalesCompra()"></div></div>
            </div>

            <div class="co-tab-panel" id="co-panel-pagos" style="display:none;">
                <p class="fac-section-title">Formas de pago <button class="btn-primary" onclick="agregarPagoCompra()" style="font-size:11px;padding:4px 10px;">+ Agregar pago</button></p>
                <p class="co-help">Registre uno o varios medios de pago y su referencia, si la factura ya fue pagada.</p>
                <div id="co-pagos-body"></div>
            </div>

            <div class="totales-box">
                <div class="totales-row">
                    <span>Subtotal bruto</span>
                    <span id="co-tot-subtotal">$ 0</span>
                </div>
                <div class="totales-row"><span>Descuentos</span><span id="co-tot-descuentos">$ 0</span></div>
                <div class="totales-row">
                    <span>IVA</span>
                    <span id="co-tot-iva">$ 0</span>
                </div>
                <div class="totales-row"><span>Impuestos adicionales (ICL / ADV / INC / ICO)</span><span id="co-tot-otros-impuestos">$ 0</span></div>
                <div class="totales-row"><span>Costos adicionales</span><span id="co-tot-cargos">$ 0</span></div>
                <div class="totales-row"><span>Retenciones</span><span id="co-tot-retenciones">$ 0</span></div>
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

<div id="modal-abono-compra" style="display:none;" class="modal-backdrop">
    <div class="modal-compra" style="max-width:520px;">
        <div class="modal-head"><div><p class="modal-head-title">Registrar abono al proveedor</p><p class="modal-head-sub" id="abono-compra-ref"></p></div><button onclick="cerrarAbonoCompra()" style="border:0;background:transparent;font-size:20px;cursor:pointer;">✕</button></div>
        <div class="modal-body"><input type="hidden" id="abono-compra-id"><div class="co-grid"><div class="co-field"><label>Fecha *</label><input autocomplete="off" id="abono-fecha" type="date"></div><div class="co-field"><label>Saldo pendiente</label><input autocomplete="off" id="abono-saldo" readonly></div><div class="co-field"><label>Medio de pago *</label><select id="abono-metodo"></select></div><div class="co-field"><label>Valor *</label><input autocomplete="off" id="abono-valor" type="number" min="0.01" step="0.01"></div><div class="co-field" style="grid-column:1/-1"><label>Referencia</label><input autocomplete="off" id="abono-referencia" placeholder="Transferencia, recibo, comprobante..."></div></div></div>
        <div class="modal-foot"><button class="btn-outline" onclick="cerrarAbonoCompra()">Cancelar</button><button class="btn-primary" onclick="guardarAbonoCompra()">Registrar abono</button></div>
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
        pagos: [],
        itemExpandido: null,
        catalogos: {
            productos: [],
            bodegas: [],
            proveedores: [],
            metodosPago: []
        },
        proveedorSel: null,
        editandoId: null,
        accionEnCurso: false,
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
                prefijo: c.prefijo || '',
                consecutivo: Number(c.consecutivo || 0),
                numero_factura: c.numero_factura || c.numero || '',
                factura: (c.prefijo || '') + '-' + (c.numero_factura || c.numero || ''),
                fecha: c.fecha,
                fecha_registro: c.registrado_at || '',
                proveedor: c.proveedor?.razon_social || c.proveedor?.nombre || '—',
                proveedor_nit: nitDeProveedor(c.proveedor),
                usuario: c.usuario?.name || 'Sin registro de usuario',
                cantidad_items: (c.items && c.items.length) || c.cantidad_items || 0,
                total: Number(c.total || 0),
                total_pagado: Number(c.total_pagado || 0),
                saldo_pendiente: Number(c.saldo_pendiente || 0),
                estado_pago: c.estado_pago || 'pendiente',
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
                prefijo: 'FC',
                consecutivo: 4000 + i,
                numero_factura: String(4000 + i),
                fecha: fecha.toISOString().slice(0, 10),
                fecha_registro: fecha.toISOString(),
                proveedor: proveedores[i % proveedores.length],
                proveedor_nit: '9' + (10000000 + i * 137),
                usuario: 'Administrador',
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
            tbody.innerHTML = '<tr><td colspan="11">' +
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
            var pagoBadge = c.estado_pago === 'pagada' ?
                '<span class="badge badge-green"><span class="dot"></span>Pagada</span>' :
                (c.estado_pago === 'parcialmente_pagada' ?
                    '<span class="badge" style="background:#FFF7ED;color:#C2410C;"><span class="dot" style="background:#F97316;"></span>Parcialmente pagada</span>' :
                    '<span class="badge badge-gray"><span class="dot"></span>Pendiente</span>');

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
                '<td><span class="td-mono">' + esc(c.prefijo) + '</span></td>' +
                '<td><span class="td-mono">' + esc(String(c.consecutivo || '—')) + '</span></td>' +
                '<td><span class="td-mono">' + esc(c.numero_factura) + '</span></td>' +
                '<td class="td-proveedor">' +
                '<div class="prov-nombre" title="' + esc(c.proveedor) + '">' + esc(c.proveedor) + '</div>' +
                (c.proveedor_nit ? '<div class="prov-nit">NIT ' + esc(c.proveedor_nit) + '</div>' : '') +
                '</td>' +
                '<td class="td-money">' + fmtMoneyCO(c.total) + '</td>' +
                '<td>' + (c.estado === 'confirmada' ? pagoBadge : '—') + '</td>' +
                '<td class="td-money">' + (c.estado === 'confirmada' ? fmtMoneyCO(c.saldo_pendiente || 0) : '—') + '</td>' +
                '<td>' + esc(c.usuario) + '</td>' +
                '<td>' + fmtFechaRegistroCO(c.fecha_registro) + '</td>' +
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
                c.subtotal = Number(full.subtotal ?? c.subtotal ?? 0);
                c.descuentos = Number(full.descuentos ?? c.descuentos ?? 0);
                c.iva = Number(full.iva ?? c.iva ?? 0);
                c.ico = Number(full.ico ?? c.ico ?? 0);
                c.imp_saludable = Number(full.imp_saludable ?? c.imp_saludable ?? 0);
                c.retenciones = Number(full.retenciones ?? c.retenciones ?? 0);
                c.otros_cargos = Number(full.otros_cargos ?? c.otros_cargos ?? 0);
                c.pagos = full.pagos || [];
                c.pagos_proveedor = full.pagos_proveedor || [];
                c.total_pagado = Number(full.total_pagado || 0);
                c.saldo_pendiente = Number(full.saldo_pendiente || 0);
                c.estado_pago = full.estado_pago || 'pendiente';
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
            var sub = Number(it.total || ((it.cantidad || 0) * (it.costo_unitario || it.costo || 0)));
            return '<tr>' +
                '<td>' + esc(it.producto?.descripcion || it.producto?.nombre || it.producto_nombre || 'Producto') + '</td>' +
                '<td style="text-align:center;">' + (it.cantidad || 0) + '</td>' +
                '<td style="text-align:right;">' + fmtMoneyCO(it.costo_unitario || it.costo || 0) + '</td>' +
                '<td style="text-align:center;">' + (it.iva_porcentaje || it.iva || 0) + '% ' + (it.bonificado ? '<small>(bonif.)</small>' : '') + '</td>' +
                '<td style="text-align:right;font-weight:600;">' + fmtMoneyCO(sub) + '</td>' +
                '</tr>';
        }).join('') || '<tr><td colspan="5" style="text-align:center;color:#9CA3AF;">' +
            (c.items ? 'Sin ítems registrados' : 'Cargando ítems…') + '</td></tr>';

        document.getElementById('vco-body').innerHTML =
            '<div class="co-grid" style="grid-template-columns:2fr 1fr;">' +
            fldCO('Proveedor', c.proveedor + (c.proveedor_nit ? ' · NIT ' + c.proveedor_nit : '')) +
            fldCO('Fecha', fmtFechaCO(c.fecha)) +
            fldCO('Estado de pago', String(c.estado_pago || 'pendiente').replaceAll('_', ' ')) +
            fldCO('Saldo pendiente', fmtMoneyCO(c.saldo_pendiente || 0)) +
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
            ((c.pagos || []).length ? '<p class="fac-section-title">Formas de pago</p><div class="items-tbl-wrap"><table class="items-tbl"><thead><tr><th>Medio</th><th>Referencia</th><th style="text-align:right;">Valor</th></tr></thead><tbody>' + c.pagos.map(function(p) { return '<tr><td>' + esc(p.metodo_pago || '') + '</td><td>' + esc(p.referencia || '—') + '</td><td style="text-align:right;">' + fmtMoneyCO(p.valor || 0) + '</td></tr>'; }).join('') + '</tbody></table></div>' : '') +
            '<div class="totales-box">' +
            '<div class="totales-row"><span>Subtotal bruto</span><span>' + fmtMoneyCO(c.subtotal || 0) + '</span></div>' +
            '<div class="totales-row"><span>Descuentos</span><span>- ' + fmtMoneyCO(c.descuentos || 0) + '</span></div>' +
            '<div class="totales-row"><span>IVA</span><span>' + fmtMoneyCO(c.iva || 0) + '</span></div>' +
            '<div class="totales-row"><span>Impuestos adicionales (ICL / ADV / INC / ICO)</span><span>' + fmtMoneyCO((c.ico || 0) + (c.imp_saludable || 0)) + '</span></div>' +
            '<div class="totales-row"><span>Costos adicionales</span><span>' + fmtMoneyCO(c.otros_cargos || 0) + '</span></div>' +
            '<div class="totales-row"><span>Retenciones</span><span>- ' + fmtMoneyCO(c.retenciones || 0) + '</span></div>' +
            '<div class="totales-row total-final"><span>TOTAL</span><span>' +
            fmtMoneyCO(c.total) + '</span></div></div>';

        /* Pie del modal: acción principal según el estado de la compra */
        var foot = document.getElementById('vco-foot-actions');
        if (c.estado === 'borrador') {
            foot.innerHTML = '<button class="btn-outline" onclick="editarCompra(' + c.id +
                ')">&#9998; Editar</button><button class="btn-primary" onclick="registrarCompra(' + c.id +
                ')">✅ Registrar compra</button>';
        } else if (c.estado === 'confirmada') {
            foot.innerHTML = ((Number(c.saldo_pendiente || 0) > 0) ? '<button class="btn-primary" onclick="abrirAbonoCompra(' + c.id + ')">+ Registrar abono</button>' : '') + '<button class="btn-outline" onclick="revertirRegistroCompra(' + c.id +
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

    function abrirAbonoCompra(id) {
        var c = CO.datos.find(function(x) { return x.id === id; });
        if (!c) return;
        fetch('/metodos-pago-contables/opciones', {headers:{Accept:'application/json'}}).then(function(r){return r.json();}).then(function(res) {
            // "Crédito" no es un medio de pago válido para pagar un abono (es
            // precisamente lo que se está saldando) — mismo criterio que en CxC.
            var metodos = (res.data || []).filter(function(m) { return m.metodo_pago !== 'credito'; });
            if (!metodos.length) throw new Error('No hay medios de pago contables activos. Parametrícelos primero.');
            document.getElementById('abono-compra-id').value = id;
            document.getElementById('abono-compra-ref').textContent = c.factura + ' · ' + c.proveedor;
            document.getElementById('abono-fecha').value = new Date().toISOString().slice(0,10);
            document.getElementById('abono-saldo').value = fmtMoneyCO(c.saldo_pendiente || 0);
            document.getElementById('abono-valor').value = Number(c.saldo_pendiente || 0);
            document.getElementById('abono-referencia').value = '';
            document.getElementById('abono-metodo').innerHTML = metodos.map(function(m){return '<option value="'+m.id+'">'+esc(m.metodo_pago)+'</option>';}).join('');
            document.getElementById('modal-abono-compra').style.display = 'flex';
        }).catch(function(e){ notifCO(e.message, 'error'); });
    }
    function cerrarAbonoCompra(){ document.getElementById('modal-abono-compra').style.display='none'; }
    function guardarAbonoCompra(){
        var id=document.getElementById('abono-compra-id').value;
        var data={fecha:document.getElementById('abono-fecha').value,valor:Number(document.getElementById('abono-valor').value),metodo_pago_contable_id:Number(document.getElementById('abono-metodo').value),referencia:document.getElementById('abono-referencia').value};
        fetch('/compras/'+id+'/pagos',{method:'POST',headers:{'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]').content},body:JSON.stringify(data)}).then(async function(r){return {ok:r.ok,data:await r.json()};}).then(function(r){if(!r.ok)throw new Error(r.data.message||'No se pudo registrar el abono');cerrarAbonoCompra();cerrarVerCompra();notifCO('Abono registrado y contabilizado correctamente.', 'success');cargarCompras();}).catch(function(e){notifCO(e.message,'error');});
    }

    /* ════════════════════════════════════════════════
       MODAL NUEVA COMPRA
    ════════════════════════════════════════════════ */
    function abrirCompra() {
        CO.items = [];
        CO.pagos = [];
        CO.itemExpandido = null;
        CO.proveedorSel = null;
        CO.editandoId = null;

        document.getElementById('co-title').textContent = 'Nueva compra';
        document.getElementById('co-prefijo').value = 'FC';
        document.getElementById('co-consecutivo').value = '';
        document.getElementById('co-numero').value = '';
        document.getElementById('co-fecha').value = new Date().toISOString().slice(0, 10);
        document.getElementById('co-observaciones').value = '';
        document.getElementById('co-retefuente').value = 0;
        document.getElementById('co-reteiva').value = 0;
        document.getElementById('co-reteica').value = 0;
        document.getElementById('co-otros-cargos').value = 0;
        document.getElementById('co-confirmar').checked = true;
        limpiarProveedor();

        renderItemsCompra();
        renderPagosCompra();
        calcularTotalesCompra();
        document.getElementById('modal-compra').style.display = 'flex';
        cargarSiguienteConsecutivoCompra();
        cambiarPestanaCompra('productos');

        if (!CO.catalogos.productos.length && !CO.catalogos.proveedores.length) {
            cargarCatalogosCompra().then(function(){ renderItemsCompra(); renderPagosCompra(); });
        }
    }

    function cerrarCompra() {
        document.getElementById('modal-compra').style.display = 'none';
    }

    function cerrarCompraBackdrop(e) {
        if (e.target === document.getElementById('modal-compra')) cerrarCompra();
    }

    function cargarSiguienteConsecutivoCompra() {
        if (CO.editandoId) return;
        var prefijo = document.getElementById('co-prefijo').value.trim() || 'FC';

        fetch('/compras/siguiente-consecutivo?prefijo=' + encodeURIComponent(prefijo), {
                headers: { Accept: 'application/json' },
                cache: 'no-store'
            })
            .then(function(r) { return r.json(); })
            .then(function(datos) {
                document.getElementById('co-consecutivo').value = datos.consecutivo || 1;
            })
            .catch(function() {
                document.getElementById('co-consecutivo').value = 1;
            });
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
            fetch('/metodos-pago-contables/opciones', {headers:{Accept:'application/json'}}).then(r => r.json()).catch(() => null),
        ]).then(function(x) {
            var terceros = x[0] ? (x[0].data || x[0]) : null;
            var productos = x[1] ? (x[1].data || x[1]) : null;
            var bodegas = x[2] ? (x[2].data || x[2]) : null;

            CO.catalogos.proveedores = (terceros && terceros.length) ? terceros : datosDemoProveedores();
            CO.catalogos.productos = (productos && productos.length) ? productos : datosDemoProductos();
            CO.catalogos.bodegas = (bodegas && bodegas.length) ? bodegas : datosDemoBodegas();
            CO.catalogos.metodosPago = x[3]?.data || [];
        }).catch(function() {
            CO.catalogos.proveedores = datosDemoProveedores();
            CO.catalogos.productos = datosDemoProductos();
            CO.catalogos.bodegas = datosDemoBodegas();
            CO.catalogos.metodosPago = [];
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
                            iva_porcentaje: Number(item.iva_porcentaje || 0),
                            ico_porcentaje: Number(item.ico_porcentaje || 0),
                            valor_ico: item.valor_ico === null ? null : Number(item.valor_ico),
                            imp_saludable_porcentaje: Number(item.imp_saludable_porcentaje || 0),
                            descuento_porcentaje: Number(item.descuento_porcentaje || 0),
                            descuento_2_porcentaje: Number(item.descuento_2_porcentaje || 0),
                            descuento_financiero_porcentaje: Number(item.descuento_financiero_porcentaje || 0),
                            unidad: item.unidad || (item.producto && item.producto.und_detal) || '',
                            observacion: item.observacion || '',
                            bonificado: Boolean(item.bonificado),
                            entrada_pos: Boolean(item.entrada_pos)
                        };
                    });
                    CO.itemExpandido = CO.items.length ? 0 : null;
                    CO.pagos = (compra.pagos || []).map(function(p) { return { metodo_pago: p.metodo_pago || 'efectivo', valor: Number(p.valor || 0), referencia: p.referencia || '' }; });
                    document.getElementById('co-title').textContent = 'Editar compra ' + compra.prefijo + '-' + compra.numero_factura;
                    document.getElementById('co-prefijo').value = compra.prefijo || '';
                    document.getElementById('co-consecutivo').value = compra.consecutivo || '';
                    document.getElementById('co-numero').value = compra.numero_factura || '';
                    document.getElementById('co-fecha').value = String(compra.fecha || '').slice(0, 10);
                    document.getElementById('co-observaciones').value = compra.observaciones || '';
                    document.getElementById('co-retefuente').value = compra.retefuente_porcentaje || 0;
                    document.getElementById('co-reteiva').value = compra.reteiva_porcentaje || 0;
                    document.getElementById('co-reteica').value = compra.reteica_porcentaje || 0;
                    document.getElementById('co-otros-cargos').value = compra.otros_cargos || 0;
                    document.getElementById('co-confirmar').checked = false;
                    seleccionarProveedor(compra.proveedor_id);
                    document.getElementById('co-btn-save').textContent = 'Guardar cambios';
                    renderItemsCompra();
                    renderPagosCompra();
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
            ico_porcentaje: 0,
            valor_ico: null,
            imp_saludable_porcentaje: 0,
            descuento_porcentaje: 0,
            descuento_2_porcentaje: 0,
            descuento_financiero_porcentaje: 0,
            unidad: '', observacion: '', bonificado: false, entrada_pos: false
        });
        CO.itemExpandido = CO.items.length - 1;
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
            var base = it.bonificado ? 0 : (it.cantidad || 0) * (it.costo_unitario || 0);
            var d1 = base * (it.descuento_porcentaje || 0) / 100;
            var d2 = (base - d1) * (it.descuento_2_porcentaje || 0) / 100;
            var df = (base - d1 - d2) * (it.descuento_financiero_porcentaje || 0) / 100;
            var sub = base - d1 - d2 - df;

            var productoActual = CO.catalogos.productos.find(function(p) {
                return String(p.id) === String(it.producto_id);
            });
            var nombreProducto = productoActual ? (productoActual.descripcion || productoActual.nombre || '') : '';

            var optsBod = '<option value="">— Bodega —</option>' +
                CO.catalogos.bodegas.map(function(b) {
                    var sel = String(b.id) === String(it.bodega_id) ? 'selected' : '';
                    return '<option value="' + b.id + '" ' + sel + '>' + esc(b.descripcion || b.nombre) +
                        '</option>';
                }).join('');

            var detalles = CO.itemExpandido === idx;
            return '<tr>' +
                '<td><div class="co-product-wrap"><input class="items-input" autocomplete="off" placeholder="Buscar nombre o código…" value="' + esc(nombreProducto) +
                '" oninput="buscarProductoCompra(' + idx + ',this.value)" onblur="cerrarBusquedaProductoCompra(' + idx + ')"><div class="co-product-results" id="co-product-results-' + idx + '" style="display:none"></div></div></td>' +
                '<td><select class="items-input" onchange="itemCompraChange(' + idx +
                ',\'bodega_id\',this.value)">' + optsBod + '</select></td>' +
                '<td><input autocomplete="off" type="number" class="items-input" min="1" value="' + it.cantidad +
                '" onchange="itemCompraChange(' + idx + ',\'cantidad\',+this.value)"></td>' +
                '<td><input autocomplete="off" type="number" class="items-input" min="0" value="' + it.costo_unitario +
                '" onchange="itemCompraChange(' + idx + ',\'costo_unitario\',+this.value)"></td>' +
                '<td><input autocomplete="off" type="number" class="items-input" min="0" value="' + it.iva_porcentaje +
                '" onchange="itemCompraChange(' + idx + ',\'iva_porcentaje\',+this.value)"></td>' +
                '<td style="text-align:right;font-weight:600;font-size:12px;">' + fmtMoneyCO(sub) + '</td>' +
                '<td><button class="co-item-toggle" onclick="toggleDetallesCompra(' + idx + ')">Impuestos y detalles</button><button onclick="eliminarItemCompra(' + idx + ')" style="border:none;background:transparent;cursor:pointer;font-size:14px;color:#DC2626;">✕</button></td>' +
                '</tr>' + (detalles ? '<tr class="co-item-extra"><td colspan="7"><div class="co-item-fields">' +
                campoItemCompra(idx, 'Unidad', 'unidad', it.unidad, 'text') + campoItemCompra(idx, 'Dcto. 1 %', 'descuento_porcentaje', it.descuento_porcentaje, 'number') +
                campoItemCompra(idx, 'Dcto. 2 %', 'descuento_2_porcentaje', it.descuento_2_porcentaje, 'number') + campoItemCompra(idx, 'Dcto. financiero %', 'descuento_financiero_porcentaje', it.descuento_financiero_porcentaje, 'number') +
                campoItemCompra(idx, 'ICO % (si aplica por porcentaje)', 'ico_porcentaje', it.ico_porcentaje, 'number') + campoItemCompra(idx, 'Valor impuesto adicional: ICL / ADV / INC / ICO', 'valor_ico', it.valor_ico, 'number') + campoItemCompra(idx, 'Imp. saludable %', 'imp_saludable_porcentaje', it.imp_saludable_porcentaje, 'number') +
                '<div class="co-field"><label>Producto bonificado</label><label style="text-transform:none;letter-spacing:0;color:#344054;display:flex;gap:6px;align-items:center;"><input type="checkbox" ' + (it.bonificado ? 'checked' : '') + ' onchange="itemCompraChange(' + idx + ',\'bonificado\',this.checked)"> Sin cobro</label></div>' +
                '<div class="co-field"><label>Entrada tipo POS</label><label style="text-transform:none;letter-spacing:0;color:#344054;display:flex;gap:6px;align-items:center;"><input type="checkbox" ' + (it.entrada_pos ? 'checked' : '') + ' onchange="itemCompraChange(' + idx + ',\'entrada_pos\',this.checked)"> Marcar entrada</label></div>' +
                '<div class="co-field" style="grid-column:span 2;"><label>Observación del producto</label><input autocomplete="off" value="' + esc(it.observacion || '') + '" oninput="itemCompraChange(' + idx + ',\'observacion\',this.value, null, true)"></div>' +
                '</div></td></tr>' : '');
        }).join('');

        calcularTotalesCompra();
    }

    function campoItemCompra(idx, etiqueta, campo, valor, tipo) {
        return '<div class="co-field"><label>' + etiqueta + '</label><input autocomplete="off" type="' + tipo + '" ' + (tipo === 'number' ? 'min="0" step="0.01" ' : '') + 'value="' + esc(valor || '') + '" oninput="itemCompraChange(' + idx + ',\'' + campo + '\', this.value, null, true)"></div>';
    }

    function toggleDetallesCompra(idx) { CO.itemExpandido = CO.itemExpandido === idx ? null : idx; renderItemsCompra(); }

    function buscarProductoCompra(idx, termino) {
        var box = document.getElementById('co-product-results-' + idx);
        if (!box) return;
        var q = String(termino || '').trim().toLowerCase();
        if (CO.items[idx]) CO.items[idx].producto_id = '';

        if (!q) {
            box.style.display = 'none';
            return;
        }

        var resultados = (CO.catalogos.productos || []).filter(function(p) {
            var nombre = String(p.descripcion || p.nombre || '').toLowerCase();
            var codigo = String(p.codigo || '').toLowerCase();
            var barras = String(p.codigo_barras || '').toLowerCase();
            var referencia = String(p.referencia || '').toLowerCase();
            return nombre.includes(q) || codigo.includes(q) || barras.includes(q) || referencia.includes(q);
        }).slice(0, 8);

        if (!resultados.length) {
            box.innerHTML = '<div style="padding:9px;color:#98A2B3;font-size:11px;">Sin coincidencias.</div>';
            box.style.display = 'block';
            return;
        }

        box.innerHTML = resultados.map(function(p) {
            var codigo = p.codigo || p.codigo_barras || p.referencia || 'Sin código';
            return '<button type="button" class="co-product-result" onmousedown="seleccionarProductoCompra(' + idx + ',' + p.id + ')">' +
                '<strong>' + esc(p.descripcion || p.nombre) + '</strong><small>Código: ' + esc(codigo) + '</small></button>';
        }).join('');
        box.style.display = 'block';
    }

    function seleccionarProductoCompra(idx, productoId) {
        var producto = (CO.catalogos.productos || []).find(function(p) {
            return String(p.id) === String(productoId);
        });
        if (!producto || !CO.items[idx]) return;

        CO.items[idx].producto_id = producto.id;
        if (!CO.items[idx].iva_porcentaje) CO.items[idx].iva_porcentaje = Number(producto.iva_compras || producto.iva_ventas || 0);
        CO.items[idx].ico_porcentaje = Number(producto.ico_compras || producto.ico_ventas || CO.items[idx].ico_porcentaje || 0);
        CO.items[idx].unidad = producto.und_detal || producto.unidad || CO.items[idx].unidad || '';
        renderItemsCompra();
    }

    function cerrarBusquedaProductoCompra(idx) {
        setTimeout(function() {
            var box = document.getElementById('co-product-results-' + idx);
            if (box) box.style.display = 'none';
        }, 150);
    }

    function itemCompraChange(idx, campo, val, selEl, sinRender) {
        CO.items[idx][campo] = val;
        if (campo === 'producto_id' && selEl) {
            var opt = selEl.options[selEl.selectedIndex];
            var ivaDefault = Number(opt?.dataset?.iva || 0);
            if (!CO.items[idx].iva_porcentaje) CO.items[idx].iva_porcentaje = ivaDefault;
        }
        if (sinRender) calcularTotalesCompra(); else renderItemsCompra();
    }

    function eliminarItemCompra(idx) {
        CO.items.splice(idx, 1);
        renderItemsCompra();
    }

    function calcularTotalesCompra() {
        var subtotal = 0, descuentos = 0, iva = 0, ico = 0, saludable = 0;
        CO.items.forEach(function(it) {
            var base = it.bonificado ? 0 : (it.cantidad || 0) * (it.costo_unitario || 0);
            var d1 = base * Number(it.descuento_porcentaje || 0) / 100;
            var d2 = (base - d1) * Number(it.descuento_2_porcentaje || 0) / 100;
            var df = (base - d1 - d2) * Number(it.descuento_financiero_porcentaje || 0) / 100;
            var gravable = base - d1 - d2 - df;
            subtotal += base;
            descuentos += d1 + d2 + df;
            iva += gravable * Number(it.iva_porcentaje || 0) / 100;
            ico += it.valor_ico !== null && it.valor_ico !== '' ? Number(it.valor_ico) : gravable * Number(it.ico_porcentaje || 0) / 100;
            saludable += gravable * Number(it.imp_saludable_porcentaje || 0) / 100;
        });
        var gravableTotal = subtotal - descuentos;
        var rete = gravableTotal * (Number(document.getElementById('co-retefuente').value || 0) + Number(document.getElementById('co-reteiva').value || 0) + Number(document.getElementById('co-reteica').value || 0)) / 100;
        var cargos = Number(document.getElementById('co-otros-cargos').value || 0);
        var totalFactura = gravableTotal + iva + ico + saludable + cargos - rete;
        document.getElementById('co-tot-subtotal').textContent = fmtMoneyCO(subtotal);
        document.getElementById('co-tot-descuentos').textContent = '- ' + fmtMoneyCO(descuentos);
        document.getElementById('co-tot-iva').textContent = fmtMoneyCO(iva);
        document.getElementById('co-tot-otros-impuestos').textContent = fmtMoneyCO(ico + saludable);
        document.getElementById('co-tot-cargos').textContent = fmtMoneyCO(cargos);
        document.getElementById('co-tot-retenciones').textContent = '- ' + fmtMoneyCO(rete);
        document.getElementById('co-tot-total').textContent = fmtMoneyCO(totalFactura);
        return totalFactura;
    }

    function cambiarPestanaCompra(tab, boton) {
        document.querySelectorAll('.co-tab-panel').forEach(function(panel) { panel.style.display = panel.id === 'co-panel-' + tab ? 'block' : 'none'; });
        document.querySelectorAll('.co-tab').forEach(function(btn) { btn.classList.toggle('active', btn.dataset.tab === tab); });
    }

    function agregarPagoCompra() { CO.pagos.push({ metodo_pago: 'credito_proveedores', valor: 0, referencia: '' }); renderPagosCompra(); }
    function eliminarPagoCompra(idx) { CO.pagos.splice(idx, 1); renderPagosCompra(); }
    function pagoCompraChange(idx, campo, valor) { CO.pagos[idx][campo] = valor; }
    function renderPagosCompra() {
        var box = document.getElementById('co-pagos-body');
        if (!CO.pagos.length) { box.innerHTML = '<p class="co-help">Agregue efectivo, transferencia, tarjeta u <strong>Crédito proveedores</strong>. La suma debe cubrir exactamente el total de la factura.</p>'; return; }
        var medios = [{metodo_pago:'credito_proveedores', etiqueta:'Crédito proveedores (saldo pendiente)'}].concat(CO.catalogos.metodosPago || []);
        var totalPagos = CO.pagos.reduce(function(s,p){ return s + Number(p.valor || 0); }, 0);
        var totalFactura = calcularTotalesCompra();
        box.innerHTML = CO.pagos.map(function(p, idx) { var opciones = medios.map(function(m){ return '<option value="'+esc(m.metodo_pago)+'" '+(p.metodo_pago===m.metodo_pago?'selected':'')+'>'+esc(m.etiqueta || m.metodo_pago)+'</option>'; }).join(''); return '<div class="co-payment-row"><div class="co-field"><label>Medio</label><select onchange="pagoCompraChange(' + idx + ',\'metodo_pago\',this.value);renderPagosCompra()">' + opciones + '</select></div><div class="co-field"><label>Valor</label><input autocomplete="off" type="number" min="0" step="0.01" value="' + Number(p.valor || 0) + '" onchange="pagoCompraChange(' + idx + ',\'valor\',this.value);renderPagosCompra()"></div><div class="co-field"><label>Referencia</label><input autocomplete="off" value="' + esc(p.referencia || '') + '" oninput="pagoCompraChange(' + idx + ',\'referencia\',this.value)"></div><button type="button" onclick="eliminarPagoCompra(' + idx + ')" style="border:0;background:transparent;color:#DC2626;font-size:16px;cursor:pointer;padding-bottom:8px;">✕</button></div>'; }).join('') + '<p class="co-help" style="margin-top:10px;color:' + (Math.abs(totalPagos-totalFactura)<0.01?'#027A48':'#B42318') + '">Formas de pago: <strong>' + fmtMoneyCO(totalPagos) + '</strong> · Total factura: <strong>' + fmtMoneyCO(totalFactura) + '</strong></p>';
    }

    /* ── Guardar ── */
    function guardarCompra() {
        var prefijo = document.getElementById('co-prefijo').value.trim();
        var consecutivo = Number(document.getElementById('co-consecutivo').value);
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
        if (!consecutivo || consecutivo < 1) {
            notifCO('⚠️ Ingrese un consecutivo válido', 'error');
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
        if (document.getElementById('co-confirmar').checked) {
            var totalFactura = calcularTotalesCompra();
            var totalPagos = CO.pagos.reduce(function(s, p) { return s + Number(p.valor || 0); }, 0);
            if (!CO.pagos.length) {
                notifCO('⚠️ Agregue una forma de pago o Crédito proveedores.', 'error');
                cambiarPestanaCompra('pagos');
                return;
            }
            if (Math.abs(totalPagos - totalFactura) > 0.01) {
                notifCO('⚠️ Las formas de pago deben sumar exactamente el total de la factura.', 'error');
                cambiarPestanaCompra('pagos');
                return;
            }
        }

        var body = {
            prefijo: prefijo,
            consecutivo: consecutivo,
            numero_factura: numero,
            fecha: fecha,
            proveedor_id: proveedorId,
            observaciones: document.getElementById('co-observaciones').value,
            items: CO.items,
            retefuente_porcentaje: Number(document.getElementById('co-retefuente').value || 0),
            reteiva_porcentaje: Number(document.getElementById('co-reteiva').value || 0),
            reteica_porcentaje: Number(document.getElementById('co-reteica').value || 0),
            otros_cargos: Number(document.getElementById('co-otros-cargos').value || 0),
            pagos: CO.pagos,
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
        if (CO.accionEnCurso) return;
        CO.accionEnCurso = true;
        fetch('/compras/' + id + '/' + accion, {method: 'POST', headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content, Accept: 'application/json'}})
            .then(function(r) { return r.json().then(function(data) { return {ok: r.ok, data: data}; }); })
            .then(function(res) {
                var detalleError = res.data.errors ? Object.values(res.data.errors)[0]?.[0] : null;
                if (!res.ok) throw new Error(detalleError || res.data.message || 'No se pudo actualizar');
                notifCO(res.data.message || 'Compra actualizada correctamente', 'success');
                cerrarVerCompra();
                cargarCompras();
            })
            .catch(function(e) { notifCO(e.message, 'error'); })
            .finally(function() { CO.accionEnCurso = false; });
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

    function fmtFechaRegistroCO(s) {
        if (!s) return '—';
        var fecha = new Date(s);
        if (isNaN(fecha.getTime())) return fmtFechaCO(s);
        return new Intl.DateTimeFormat('es-CO', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: 'numeric',
            minute: '2-digit'
        }).format(fecha);
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
