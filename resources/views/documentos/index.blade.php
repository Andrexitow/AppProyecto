<style>
    :root {
        --doc-bg: #F6F7F9;
        --doc-surface: #FFFFFF;
        --doc-border: #E4E7EC;
        --doc-border-strong: #D0D5DD;
        --doc-text: #101828;
        --doc-text-soft: #475467;
        --doc-text-faint: #98A2B3;
        --doc-blue: #2563EB;
        --doc-blue-dark: #1D4ED8;
        --doc-blue-bg: #EFF4FF;
        --doc-green: #059669;
        --doc-green-bg: #ECFDF5;
        --doc-red: #DC2626;
        --doc-red-bg: #FEF3F2;
        --doc-gray-bg: #F2F4F7;
        --doc-radius: 12px;
        --doc-radius-sm: 8px;
        --doc-shadow: 0 1px 2px rgba(16,24,40,.04), 0 1px 3px rgba(16,24,40,.06);
    }

    #view-documentos {
        color: var(--doc-text);
        background: var(--doc-bg);
    }

    #view-documentos .page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 20px;
    }

    #view-documentos .page-header h1 {
        font-size: 19px;
        font-weight: 700;
        color: var(--doc-text);
        letter-spacing: -0.4px;
        margin: 0;
    }

    #view-documentos .page-header p {
        font-size: 12.5px;
        color: var(--doc-text-faint);
        margin: 3px 0 0;
    }

    /* ── Filtros ── */
    #view-documentos .filters {
        background: var(--doc-surface);
        border: 1px solid var(--doc-border);
        border-radius: var(--doc-radius);
        padding: 12px 14px;
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        align-items: center;
        margin-bottom: 14px;
        box-shadow: var(--doc-shadow);
    }

    #view-documentos .filters input,
    #view-documentos .filters select {
        border: 1px solid var(--doc-border-strong);
        border-radius: var(--doc-radius-sm);
        padding: 8px 12px;
        font-size: 12.5px;
        color: var(--doc-text);
        background: var(--doc-bg);
        outline: none;
        transition: border-color .15s, background .15s, box-shadow .15s;
        font-family: inherit;
    }

    #view-documentos .filters input {
        flex: 1;
        min-width: 200px;
    }

    #view-documentos .filters input:focus,
    #view-documentos .filters select:focus {
        border-color: var(--doc-blue);
        background: var(--doc-surface);
        box-shadow: 0 0 0 3px var(--doc-blue-bg);
    }

    #view-documentos .filters select {
        min-width: 170px;
        cursor: pointer;
    }

    #view-documentos .btn-primary {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: var(--doc-blue-dark);
        color: #fff;
        border: none;
        border-radius: var(--doc-radius-sm);
        padding: 8px 18px;
        font-size: 12.5px;
        font-weight: 600;
        cursor: pointer;
        transition: background .15s, transform .1s;
        white-space: nowrap;
    }

    #view-documentos .btn-primary:hover {
        background: #1a3fb8;
    }

    #view-documentos .btn-primary:active {
        transform: translateY(1px);
    }

    /* ── Tabla ── */
    #view-documentos .table-wrap {
        background: var(--doc-surface);
        border: 1px solid var(--doc-border);
        border-radius: var(--doc-radius);
        overflow: hidden;
        box-shadow: var(--doc-shadow);
    }

    #view-documentos .table-wrap-scroll {
        overflow-x: auto;
    }

    #view-documentos table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
    }

    #view-documentos thead {
        background: var(--doc-bg);
        border-bottom: 1px solid var(--doc-border);
    }

    #view-documentos thead th {
        padding: 11px 14px;
        text-align: left;
        font-size: 11px;
        font-weight: 700;
        color: var(--doc-text-faint);
        letter-spacing: 0.3px;
        white-space: nowrap;
    }

    #view-documentos thead th.num {
        text-align: right;
    }

    #view-documentos tbody tr {
        border-bottom: 1px solid #F1F2F4;
        transition: background .1s;
    }

    #view-documentos tbody tr:last-child {
        border-bottom: none;
    }

    #view-documentos tbody tr:hover {
        background: #FAFBFC;
    }

    #view-documentos tbody tr.doc-anulado {
        opacity: .55;
    }

    #view-documentos tbody tr.doc-anulado td {
        text-decoration: line-through;
    }

    #view-documentos tbody tr.doc-anulado .badge {
        text-decoration: none;
    }

    #view-documentos td {
        padding: 11px 14px;
        color: var(--doc-text-soft);
        vertical-align: middle;
    }

    #view-documentos .td-tipo {
        font-weight: 600;
        color: var(--doc-text);
    }

    #view-documentos .td-numero {
        font-family: 'JetBrains Mono', 'Fira Mono', monospace;
        font-size: 12px;
        font-weight: 600;
        color: var(--doc-text);
        background: var(--doc-bg);
        padding: 2px 7px;
        border-radius: 5px;
        white-space: nowrap;
    }

    #view-documentos .td-money {
        font-weight: 700;
        color: var(--doc-green);
        text-align: right;
        font-variant-numeric: tabular-nums;
        white-space: nowrap;
    }

    #view-documentos .td-tercero {
        max-width: 220px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        font-weight: 500;
        color: var(--doc-text);
    }

    /* ── Estados ── */
    #view-documentos .badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 700;
        white-space: nowrap;
        text-transform: capitalize;
    }

    #view-documentos .badge .dot {
        width: 5px;
        height: 5px;
        border-radius: 50%;
        display: inline-block;
        background: currentColor;
    }

    #view-documentos .badge-registrado { background: var(--doc-green-bg); color: #065F46; }
    #view-documentos .badge-anulado { background: var(--doc-red-bg); color: #991B1B; }
    #view-documentos .badge-borrador { background: var(--doc-gray-bg); color: #344054; }

    /* ── Estados de tabla vacíos / carga ── */
    #view-documentos .estado-cell {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
        padding: 44px;
        color: var(--doc-text-faint);
        font-size: 13px;
    }

    @keyframes doc-spin { to { transform: rotate(360deg); } }

    #view-documentos .doc-spinner {
        width: 18px;
        height: 18px;
        border: 2px solid var(--doc-border);
        border-top-color: var(--doc-blue-dark);
        border-radius: 50%;
        animation: doc-spin .7s linear infinite;
    }

    @media (max-width: 640px) {
        #view-documentos thead th:nth-child(n+5) { display: none; }
        #view-documentos tbody td:nth-child(n+5) { display: none; }
    }
</style>

<div id="view-documentos" style="padding:24px">
    <div class="page-header">
        <div>
            <h1>Documentos</h1>
            <p>Historial comercial, inventario y contabilidad.</p>
        </div>
    </div>

    <div class="filters">
        <input id="doc-buscar" placeholder="Prefijo o número…" onkeydown="if(event.key==='Enter')cargarDocumentos()">
        <select id="doc-estado">
            <option value="">Todos los estados</option>
            <option value="borrador">Borrador</option>
            <option value="registrado">Registrado</option>
            <option value="anulado">Anulado</option>
        </select>
        <button class="btn-primary" onclick="cargarDocumentos()">🔍 Buscar</button>
    </div>

    <div class="table-wrap">
        <div class="table-wrap-scroll">
            <table>
                <thead>
                    <tr>
                        <th>Tipo</th>
                        <th>Número</th>
                        <th>Fecha</th>
                        <th>Tercero</th>
                        <th>Bodega</th>
                        <th class="num">Total</th>
                        <th>Estado</th>
                        <th>Usuario</th>
                    </tr>
                </thead>
                <tbody id="documentos-lista">
                    <tr>
                        <td colspan="8">
                            <div class="estado-cell"><div class="doc-spinner"></div>Cargando documentos…</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function cargarDocumentos() {
        var tbody = document.getElementById('documentos-lista');
        tbody.innerHTML = '<tr><td colspan="8"><div class="estado-cell"><div class="doc-spinner"></div>Cargando documentos…</div></td></tr>';

        let q = new URLSearchParams({
            buscar: document.getElementById('doc-buscar').value,
            estado: document.getElementById('doc-estado').value
        });

        fetch('/documentos?' + q)
            .then(r => r.json())
            .then(x => {
                var filas = (x.data.data || []).map(function(d) {
                    var estado = String(d.estado || '').toLowerCase();
                    var badgeCls = estado === 'registrado' ? 'badge-registrado' :
                        estado === 'anulado' ? 'badge-anulado' : 'badge-borrador';
                    var trCls = estado === 'anulado' ? 'doc-anulado' : '';

                    return '<tr class="' + trCls + '">' +
                        '<td class="td-tipo">' + docEsc(d.tipo?.nombre) + '</td>' +
                        '<td><span class="td-numero">' + docEsc(d.numero) + '</span></td>' +
                        '<td>' + docFecha(d.fecha) + '</td>' +
                        '<td class="td-tercero" title="' + docEsc(d.tercero?.razon_social || d.tercero?.nombre_completo || '—') + '">' +
                        docEsc(d.tercero?.razon_social || d.tercero?.nombre_completo || '—') + '</td>' +
                        '<td>' + docEsc(d.bodega?.descripcion || '—') + '</td>' +
                        '<td class="td-money">$ ' + Number(d.total || 0).toLocaleString('es-CO') + '</td>' +
                        '<td><span class="badge ' + badgeCls + '"><span class="dot"></span>' + docEsc(d.estado) + '</span></td>' +
                        '<td>' + docEsc(d.usuario?.name || '—') + '</td>' +
                        '</tr>';
                }).join('');

                tbody.innerHTML = filas || '<tr><td colspan="8"><div class="estado-cell">📭 Sin documentos</div></td></tr>';
            })
            .catch(function() {
                tbody.innerHTML = '<tr><td colspan="8"><div class="estado-cell">⚠️ No se pudieron cargar los documentos</div></td></tr>';
            });
    }

    function docFecha(s) {
        if (!s) return '—';
        var parts = String(s).slice(0, 10).split('-');
        return parts.length === 3 ? parts[2] + '/' + parts[1] + '/' + parts[0] : s;
    }

    function docEsc(s) {
        return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
    }

    cargarDocumentos();
</script>