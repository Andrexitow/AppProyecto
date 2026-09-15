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

    .metrics-row {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
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
        font-size: 18px;
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

    .fi-group {
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

    .fi-select {
        border: 1px solid #D1D5DB;
        border-radius: 7px;
        padding: 6px 10px;
        font-size: 12px;
        color: #111827;
        background: #F9FAFB;
        outline: none;
        cursor: pointer;
        flex: none;
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
        transition: background .15s;
        white-space: nowrap;
    }

    .btn-primary:hover {
        background: #1e40af;
    }

    .btn-primary:disabled {
        opacity: .5;
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
        transition: all .15s;
        white-space: nowrap;
    }

    .btn-outline:hover {
        background: #F3F4F6;
        border-color: #9CA3AF;
    }

    .table-wrapper {
        background: #fff;
        border: 1px solid #EAECF0;
        border-radius: 10px;
        overflow: hidden;
    }

    .table-scroll {
        overflow-x: auto;
    }

    table.af-tbl {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
    }

    table.af-tbl thead {
        background: #F8FAFC;
        border-bottom: 1px solid #EAECF0;
    }

    table.af-tbl thead th {
        padding: 10px 12px;
        text-align: left;
        font-size: 11px;
        font-weight: 600;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: .5px;
        white-space: nowrap;
    }

    table.af-tbl tbody tr {
        border-bottom: 1px solid #F3F4F6;
    }

    table.af-tbl tbody tr:hover {
        background: #F8FAFC;
    }

    table.af-tbl tbody tr.de-baja {
        opacity: .55;
    }

    table.af-tbl td {
        padding: 9px 12px;
        color: #374151;
        vertical-align: middle;
    }

    .td-mono {
        font-family: 'JetBrains Mono', 'Fira Mono', monospace;
        font-size: 12px;
        color: #1D4ED8;
        font-weight: 600;
    }

    .td-money {
        font-weight: 600;
        color: #111827;
        text-align: right;
        white-space: nowrap;
    }

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

    .badge-gray {
        background: #F3F4F6;
        color: #374151;
    }

    .badge-blue {
        background: #EFF6FF;
        color: #1e3a8a;
    }

    .act-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 26px;
        height: 26px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        font-size: 12px;
        background: transparent;
        color: #6B7280;
    }

    .act-btn:hover {
        background: #F3F4F6;
        color: #111827;
    }

    .act-btn.del:hover {
        background: #FEF2F2;
        color: #DC2626;
    }

    .spinner-cell {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px;
        color: #6B7280;
        font-size: 13px;
        gap: 10px;
    }

    @keyframes spin-af {
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
        animation: spin-af .7s linear infinite;
    }

    .modal-backdrop-af {
        background: rgba(17, 24, 39, .5);
        backdrop-filter: blur(4px);
    }

    .modal-af {
        background: #fff;
        border-radius: 16px;
        width: 100%;
        max-width: 560px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(0, 0, 0, .15);
    }

    .modal-head-af {
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

    .modal-body-af {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
    }

    .modal-foot-af {
        padding: 14px 20px;
        border-top: 1px solid #EAECF0;
        display: flex;
        gap: 8px;
        justify-content: flex-end;
        flex-shrink: 0;
    }

    .af-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 12px;
    }

    @media (max-width: 480px) {
        .af-grid {
            grid-template-columns: 1fr;
        }
    }

    .af-field {
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .af-field label {
        font-size: 11px;
        font-weight: 500;
        color: #9CA3AF;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .af-field input,
    .af-field select,
    .af-field textarea {
        border: 1px solid #D1D5DB;
        border-radius: 7px;
        padding: 8px 10px;
        font-size: 13px;
        color: #111827;
        outline: none;
        background: #F9FAFB;
        font-family: inherit;
        width: 100%;
        box-sizing: border-box;
    }

    .af-field input:focus,
    .af-field select:focus,
    .af-field textarea:focus {
        border-color: #1D4ED8;
        background: #fff;
    }

    .af-hint {
        font-size: 11px;
        color: #9CA3AF;
        margin-top: 8px;
    }

    @media (max-width: 640px) {
        .metrics-row {
            grid-template-columns: 1fr 1fr;
        }
    }
</style>

<div id="view-activos-fijos">
    <div class="sec-header">
        <div>
            <p class="sec-title">🏢 Activos Fijos</p>
            <p class="sec-subtitle">Registro, depreciación en línea recta y baja de activos</p>
        </div>
        <div style="display:flex;gap:8px;flex-wrap:wrap;">
            <button class="btn-outline" onclick="abrirModalDepreciar()">📉 Depreciar mes</button>
            <button class="btn-primary" onclick="abrirModalNuevoActivo()">＋ Nuevo Activo</button>
        </div>
    </div>

    <div class="metrics-row" id="af-metrics"></div>

    <div class="filter-bar">
        <div class="fi-group" style="flex:none;">
            <select class="fi-select" id="af-estado" onchange="cargarActivosFijos()">
                <option value="">Todos los estados</option>
                <option value="activo">Activos</option>
                <option value="de_baja">Dados de baja</option>
            </select>
        </div>
        <div class="fi-group" style="flex:none;">
            <select class="fi-select" id="af-categoria" onchange="cargarActivosFijos()">
                <option value="">Todas las categorías</option>
            </select>
        </div>
    </div>

    <div class="table-wrapper">
        <div class="table-scroll">
            <div id="af-resultado">
                <div class="spinner-cell">
                    <div class="spinner"></div>Cargando activos…
                </div>
            </div>
        </div>
    </div>
</div>

{{-- MODAL: NUEVO ACTIVO --}}
<div id="modalNuevoActivo" class="fixed inset-0 hidden items-center justify-center z-50 p-4 modal-backdrop-af">
    <div class="modal-af">
        <div class="modal-head-af">
            <div>
                <p class="modal-head-title">Nuevo Activo Fijo</p>
                <p class="modal-head-sub">Se contabiliza el alta automáticamente</p>
            </div>
            <button onclick="cerrarModalNuevoActivo()"
                style="border:none;background:transparent;font-size:20px;cursor:pointer;color:#6B7280;">✕</button>
        </div>
        <form id="formActivo" class="modal-body-af" onsubmit="return false;">
            <div class="af-grid">
                <div class="af-field" style="grid-column:1/-1;">
                    <label>Nombre del activo</label>
                    <input type="text" name="nombre" placeholder="Ej: Computador Dell Recepción">
                </div>
                <div class="af-field" style="grid-column:1/-1;">
                    <label>Categoría</label>
                    <select name="categoria" id="af-categoria-select" onchange="actualizarHintCategoria()"></select>
                </div>
                <div class="af-field">
                    <label>Fecha de adquisición</label>
                    <input type="date" name="fecha_adquisicion">
                </div>
                <div class="af-field">
                    <label>Vida útil (meses)</label>
                    <input type="number" name="vida_util_meses" min="1" placeholder="Ej: 60">
                </div>
                <div class="af-field">
                    <label>Valor de adquisición</label>
                    <input type="number" name="valor_adquisicion" min="0" step="0.01" placeholder="0">
                </div>
                <div class="af-field">
                    <label>Valor de salvamento</label>
                    <input type="number" name="valor_residual" min="0" step="0.01" placeholder="0"
                        value="0">
                </div>
                <div class="af-field" style="grid-column:1/-1;">
                    <label>Se paga con / contrapartida</label>
                    <select name="cuenta_contrapartida_id" id="af-contrapartida-select"></select>
                </div>
                <div class="af-field" style="grid-column:1/-1;">
                    <label>Observaciones (opcional)</label>
                    <textarea name="observaciones" rows="2"></textarea>
                </div>
            </div>
            <p class="af-hint" id="af-hint-cuentas"></p>
        </form>
        <div class="modal-foot-af">
            <button type="button" onclick="cerrarModalNuevoActivo()" class="btn-outline">Cancelar</button>
            <button type="button" onclick="guardarActivoFijo()" class="btn-primary" id="btnGuardarActivo">💾 Registrar
                y Contabilizar</button>
        </div>
    </div>
</div>

{{-- MODAL: DEPRECIAR MES --}}
<div id="modalDepreciar" class="fixed inset-0 hidden items-center justify-center z-50 p-4 modal-backdrop-af">
    <div class="modal-af" style="max-width:420px;">
        <div class="modal-head-af">
            <div>
                <p class="modal-head-title">Depreciar Período</p>
                <p class="modal-head-sub">Genera un solo comprobante con la cuota de todos los activos pendientes</p>
            </div>
            <button onclick="cerrarModalDepreciar()"
                style="border:none;background:transparent;font-size:20px;cursor:pointer;color:#6B7280;">✕</button>
        </div>
        <div class="modal-body-af">
            <div class="af-field">
                <label>Período (mes)</label>
                <input type="month" id="af-periodo-depreciar">
            </div>
            <p class="af-hint">Se puede ejecutar varias veces sin riesgo: los activos ya depreciados ese mes se saltan
                automáticamente.</p>
        </div>
        <div class="modal-foot-af">
            <button type="button" onclick="cerrarModalDepreciar()" class="btn-outline">Cancelar</button>
            <button type="button" onclick="ejecutarDepreciacion()" class="btn-primary" id="btnDepreciar">📉
                Contabilizar Depreciación</button>
        </div>
    </div>
</div>

<script>
    var AF = {
        activos: [],
        cuentas: []
    };

    function tokenAF() {
        return document.querySelector('meta[name="csrf-token"]')?.content;
    }

    function fmtMoneyAF(n) {
        return '$ ' + (Number(n) || 0).toLocaleString('es-CO', {
            minimumFractionDigits: 0,
            maximumFractionDigits: 0
        });
    }

    function fmtFechaAF(s) {
        if (!s) return '—';
        var p = String(s).slice(0, 10).split('-');
        return p.length === 3 ? p[2] + '/' + p[1] + '/' + p[0] : s;
    }

    function notifAF(msg, tipo) {
        if (typeof mostrarNotificacion === 'function') {
            mostrarNotificacion(msg, tipo || 'success');
            return;
        }
        window.alert(msg);
    }

    function cargarCategoriasSelects(categorias) {
        var filtro = document.getElementById('af-categoria');
        var formSel = document.getElementById('af-categoria-select');
        Object.keys(categorias).forEach(function(key) {
            filtro.insertAdjacentHTML('beforeend', '<option value="' + key + '">' + categorias[key].nombre +
                '</option>');
            formSel.insertAdjacentHTML('beforeend', '<option value="' + key + '">' + categorias[key].nombre +
                '</option>');
        });
        AF.categorias = categorias;
    }

    window.actualizarHintCategoria = function() {
        var cat = document.getElementById('af-categoria-select').value;
        var info = AF.categorias && AF.categorias[cat];
        document.getElementById('af-hint-cuentas').textContent = info ?
            'Se contabilizará en la cuenta ' + info.activo + ' (activo) / ' + info.depreciacion +
            ' (depreciación) / ' + info.gasto + ' (gasto mensual).' :
            '';
    };

    function cargarActivosFijos() {
        var contenedor = document.getElementById('af-resultado');
        contenedor.innerHTML = '<div class="spinner-cell"><div class="spinner"></div>Cargando activos…</div>';

        var params = new URLSearchParams();
        var estado = document.getElementById('af-estado').value;
        var categoria = document.getElementById('af-categoria').value;
        if (estado) params.set('estado', estado);
        if (categoria) params.set('categoria', categoria);

        fetch('/activos-fijos?' + params.toString(), {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(function(r) {
                return r.json();
            })
            .then(function(res) {
                AF.activos = res.data || [];
                if (!AF.categorias) cargarCategoriasSelects(res.categorias || {});
                renderMetricasAF(res.metricas);
                renderTablaAF(AF.activos);
            })
            .catch(function() {
                contenedor.innerHTML =
                '<div class="spinner-cell">⚠️ No fue posible cargar los activos fijos.</div>';
            });
    }

    function renderMetricasAF(m) {
        document.getElementById('af-metrics').innerHTML = [{
                label: 'Total Activos',
                value: m.total,
                accent: '#1D4ED8'
            },
            {
                label: 'Activos en uso',
                value: m.activos,
                accent: '#059669'
            },
            {
                label: 'Dados de baja',
                value: m.de_baja,
                accent: '#DC2626'
            },
            {
                label: 'Valor en libros (activos)',
                value: fmtMoneyAF(m.valor_libros_total),
                accent: '#7C3AED'
            },
        ].map(function(t) {
            return '<div class="metric-card" style="--accent:' + t.accent + '"><p class="metric-label">' + t
                .label + '</p><p class="metric-value">' + t.value + '</p></div>';
        }).join('');
    }

    function renderTablaAF(activos) {
        var filas = activos.map(function(a) {
            var badge = a.estado === 'activo' ? '<span class="badge badge-green">● Activo</span>' :
                '<span class="badge badge-gray">Dado de baja</span>';
            var accionBaja = a.estado === 'activo' ?
                '<button class="act-btn del" title="Dar de baja" onclick="darDeBajaActivo(' + a.id +
                ')">🗑️</button>' : '';
            return '<tr class="' + (a.estado !== 'activo' ? 'de-baja' : '') + '">' +
                '<td><span class="td-mono">' + a.codigo + '</span></td>' +
                '<td>' + a.nombre + '<div style="font-size:11px;color:#9CA3AF;">' + a.categoria_nombre +
                '</div></td>' +
                '<td>' + fmtFechaAF(a.fecha_adquisicion) + '</td>' +
                '<td class="td-money">' + fmtMoneyAF(a.valor_adquisicion) + '</td>' +
                '<td class="td-money">' + fmtMoneyAF(a.depreciacion_mensual) + '</td>' +
                '<td class="td-money">' + fmtMoneyAF(a.depreciacion_acumulada) + '</td>' +
                '<td class="td-money"><b>' + fmtMoneyAF(a.valor_libros) + '</b></td>' +
                '<td>' + badge + '</td>' +
                '<td style="text-align:right;">' + accionBaja + '</td>' +
                '</tr>';
        }).join('') || '<tr><td colspan="9"><div class="spinner-cell">📭 No hay activos registrados</div></td></tr>';

        document.getElementById('af-resultado').innerHTML =
            '<table class="af-tbl"><thead><tr><th>Código</th><th>Activo</th><th>Adquisición</th>' +
            '<th style="text-align:right;">Valor Compra</th><th style="text-align:right;">Cuota/Mes</th>' +
            '<th style="text-align:right;">Dep. Acumulada</th><th style="text-align:right;">Valor en Libros</th><th>Estado</th><th></th></tr></thead>' +
            '<tbody>' + filas + '</tbody></table>';
    }

    window.abrirModalNuevoActivo = function() {
        document.getElementById('formActivo').reset();
        document.getElementById('af-hint-cuentas').textContent = '';
        if (!AF.cuentas.length) {
            fetch('/activos-fijos/cuentas-contrapartida', {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(function(r) {
                    return r.json();
                }).then(function(res) {
                    AF.cuentas = res.data || [];
                    var sel = document.getElementById('af-contrapartida-select');
                    sel.innerHTML = AF.cuentas.map(function(c) {
                        return '<option value="' + c.id + '">' + c.codigo + ' - ' + c.nombre +
                            '</option>';
                    }).join('');
                });
        }
        document.getElementById('modalNuevoActivo').classList.remove('hidden');
        document.getElementById('modalNuevoActivo').classList.add('flex');
    };

    window.cerrarModalNuevoActivo = function() {
        document.getElementById('modalNuevoActivo').classList.add('hidden');
        document.getElementById('modalNuevoActivo').classList.remove('flex');
    };

    window.guardarActivoFijo = function() {
        var form = document.getElementById('formActivo');
        var formData = new FormData(form);
        var btn = document.getElementById('btnGuardarActivo');
        btn.disabled = true;

        fetch('/activos-fijos', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': tokenAF(),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
            })
            .then(function(r) {
                return r.json().then(function(d) {
                    return {
                        ok: r.ok,
                        d: d
                    };
                });
            })
            .then(function(res) {
                if (!res.ok) {
                    var msgs = res.d.errors ? Object.values(res.d.errors).flat().join('<br>') : (res.d
                        .message || 'No se pudo registrar el activo.');
                    throw new Error(msgs);
                }
                notifAF(res.d.message, 'success');
                cerrarModalNuevoActivo();
                cargarActivosFijos();
            })
            .catch(function(e) {
                notifAF(e.message, 'error');
            })
            .finally(function() {
                btn.disabled = false;
            });
    };

    window.abrirModalDepreciar = function() {
        var hoy = new Date();
        document.getElementById('af-periodo-depreciar').value = hoy.getFullYear() + '-' + String(hoy.getMonth() + 1)
            .padStart(2, '0');
        document.getElementById('modalDepreciar').classList.remove('hidden');
        document.getElementById('modalDepreciar').classList.add('flex');
    };

    window.cerrarModalDepreciar = function() {
        document.getElementById('modalDepreciar').classList.add('hidden');
        document.getElementById('modalDepreciar').classList.remove('flex');
    };

    window.ejecutarDepreciacion = function() {
        var periodo = document.getElementById('af-periodo-depreciar').value;
        if (!periodo) {
            notifAF('Seleccione el período.', 'error');
            return;
        }
        var btn = document.getElementById('btnDepreciar');
        btn.disabled = true;

        fetch('/activos-fijos/depreciar', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': tokenAF(),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    periodo: periodo
                }),
            })
            .then(function(r) {
                return r.json().then(function(d) {
                    return {
                        ok: r.ok,
                        d: d
                    };
                });
            })
            .then(function(res) {
                if (!res.ok) throw new Error(res.d.message || 'No se pudo contabilizar la depreciación.');
                notifAF(res.d.message, res.d.procesados > 0 ? 'success' : 'warning');
                cerrarModalDepreciar();
                cargarActivosFijos();
            })
            .catch(function(e) {
                notifAF(e.message, 'error');
            })
            .finally(function() {
                btn.disabled = false;
            });
    };

    window.darDeBajaActivo = function(id) {
        var confirmar = window.Swal ?
            Swal.fire({
                title: '¿Dar de baja este activo?',
                text: 'Se contabilizará la pérdida por el valor en libros restante.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Dar de baja'
            }) :
            Promise.resolve({
                isConfirmed: confirm('¿Dar de baja este activo?')
            });

        confirmar.then(function(res) {
            if (!res.isConfirmed) return;
            fetch('/activos-fijos/' + id + '/baja', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': tokenAF(),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        fecha: new Date().toISOString().slice(0, 10)
                    }),
                })
                .then(function(r) {
                    return r.json().then(function(d) {
                        return {
                            ok: r.ok,
                            d: d
                        };
                    });
                })
                .then(function(res2) {
                    if (!res2.ok) throw new Error(res2.d.message ||
                    'No se pudo dar de baja el activo.');
                    notifAF(res2.d.message, 'success');
                    cargarActivosFijos();
                })
                .catch(function(e) {
                    notifAF(e.message, 'error');
                });
        });
    };

    cargarActivosFijos();
</script>
