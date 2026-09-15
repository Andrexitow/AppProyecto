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
    }

    .btn-outline:hover {
        background: #F3F4F6;
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

    table.cc-tbl {
        width: 100%;
        border-collapse: collapse;
        font-size: 12.5px;
    }

    table.cc-tbl thead {
        background: #F8FAFC;
        border-bottom: 1px solid #EAECF0;
    }

    table.cc-tbl thead th {
        padding: 10px 12px;
        text-align: left;
        font-size: 11px;
        font-weight: 600;
        color: #6B7280;
        text-transform: uppercase;
        letter-spacing: .5px;
    }

    table.cc-tbl tbody tr {
        border-bottom: 1px solid #F3F4F6;
    }

    table.cc-tbl tbody tr:hover {
        background: #F8FAFC;
    }

    table.cc-tbl td {
        padding: 9px 12px;
        color: #374151;
        vertical-align: middle;
    }

    .badge {
        display: inline-flex;
        padding: 3px 8px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
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
        color: #1E40AF;
    }

    .badge-amber {
        background: #FFFBEB;
        color: #92400E;
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

    .spinner-cell {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 30px;
        color: #6B7280;
        font-size: 13px;
    }

    .modal-backdrop-cc {
        background: rgba(17, 24, 39, .5);
        backdrop-filter: blur(4px);
    }

    .modal-cc {
        background: #fff;
        border-radius: 16px;
        width: 100%;
        max-width: 420px;
        max-height: 90vh;
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .modal-head-cc {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 16px 20px;
        border-bottom: 1px solid #EAECF0;
    }

    .modal-head-title {
        font-size: 15px;
        font-weight: 600;
        color: #111827;
    }

    .modal-body-cc {
        flex: 1;
        overflow-y: auto;
        padding: 20px;
    }

    .modal-foot-cc {
        padding: 14px 20px;
        border-top: 1px solid #EAECF0;
        display: flex;
        gap: 8px;
        justify-content: flex-end;
    }

    .cc-field {
        display: flex;
        flex-direction: column;
        gap: 3px;
        margin-bottom: 12px;
    }

    .cc-field label {
        font-size: 11px;
        font-weight: 500;
        color: #9CA3AF;
        text-transform: uppercase;
    }

    .cc-field input,
    .cc-field select {
        border: 1px solid #D1D5DB;
        border-radius: 7px;
        padding: 8px 10px;
        font-size: 13px;
        background: #F9FAFB;
        outline: none;
        width: 100%;
        box-sizing: border-box;
    }

    .cc-field input:focus,
    .cc-field select:focus {
        border-color: #1D4ED8;
        background: #fff;
    }
</style>

<div id="view-conceptos-caja">
    <div class="sec-header">
        <div>
            <p class="sec-title">💵 Conceptos de Caja</p>
            <p class="sec-subtitle">Catálogo de motivos para ingresos y salidas de caja — ej. pago de turno a meseros,
                compras generales, etc.</p>
        </div>
        <button class="btn-primary" onclick="abrirModalConcepto()">＋ Nuevo Concepto</button>
    </div>

    <div class="table-wrapper">
        <div class="table-scroll">
            <table class="cc-tbl">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Aplica a</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="cc-tbody">
                    <tr>
                        <td colspan="5">
                            <div class="spinner-cell">Cargando…</div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL CONCEPTO --}}
<div id="modalConcepto" class="fixed inset-0 hidden items-center justify-center z-50 p-4 modal-backdrop-cc">
    <div class="modal-cc">
        <div class="modal-head-cc">
            <p class="modal-head-title" id="cc-titulo">Nuevo Concepto</p>
            <button onclick="cerrarModalConcepto()"
                style="border:none;background:transparent;font-size:20px;cursor:pointer;color:#6B7280;">✕</button>
        </div>
        <form id="formConcepto" class="modal-body-cc" onsubmit="return false;">
            <div class="cc-field"><label>Nombre</label><input type="text" name="nombre" maxlength="150"
                    placeholder="Ej: Pago turno meseros"></div>
            <div class="cc-field">
                <label>Aplica a</label>
                <select name="tipo">
                    <option value="ambos">Ingresos y salidas</option>
                    <option value="salida">Solo salidas</option>
                    <option value="ingreso">Solo ingresos</option>
                </select>
            </div>
            <div class="cc-field"><label>Descripción (opcional)</label><input type="text" name="descripcion"
                    placeholder="Para qué se usa este concepto"></div>
        </form>
        <div class="modal-foot-cc">
            <button class="btn-outline" onclick="cerrarModalConcepto()">Cancelar</button>
            <button class="btn-primary" id="btnGuardarConcepto" onclick="guardarConcepto()">💾 Guardar</button>
        </div>
    </div>
</div>

<script>
    var CC = {
        conceptos: []
    };

    function tokenCC() {
        return document.querySelector('meta[name="csrf-token"]')?.content;
    }

    function escCC(s) {
        return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g,
            '&quot;');
    }

    function notifCC(msg, tipo) {
        if (typeof mostrarNotificacion === 'function') {
            mostrarNotificacion(msg, tipo || 'success');
            return;
        }
        window.alert(msg);
    }

    var ETIQUETAS_TIPO_CC = {
        ambos: '<span class="badge badge-blue">Ingresos y salidas</span>',
        salida: '<span class="badge badge-amber">Solo salidas</span>',
        ingreso: '<span class="badge badge-amber">Solo ingresos</span>'
    };

    function cargarConceptosCaja() {
        fetch('/conceptos-caja', {
            headers: {
                Accept: 'application/json'
            }
        }).then(function(r) {
            return r.json();
        }).then(function(res) {
            CC.conceptos = res.data || [];
            var filas = CC.conceptos.map(function(c) {
                    var badgeEstado = c.activo ? '<span class="badge badge-green">Activo</span>' :
                        '<span class="badge badge-red">Inactivo</span>';
                    return '<tr><td><strong>' + escCC(c.nombre) + '</strong></td><td>' + (ETIQUETAS_TIPO_CC[
                            c.tipo] || c.tipo) + '</td>' +
                        '<td>' + escCC(c.descripcion || '—') + '</td><td>' + badgeEstado + '</td>' +
                        '<td style="text-align:right;"><button class="act-btn" title="Editar" onclick="abrirModalConcepto(' +
                        c.id + ')">✏️</button>' +
                        '<button class="act-btn" title="' + (c.activo ? 'Desactivar' : 'Activar') +
                        '" onclick="toggleEstadoConcepto(' + c.id + ')">' + (c.activo ? '🚫' : '↩️') +
                        '</button>' +
                        '<button class="act-btn" title="Eliminar" onclick="eliminarConcepto(' + c.id +
                        ')">🗑️</button></td></tr>';
                }).join('') ||
                '<tr><td colspan="5"><div class="spinner-cell">Sin conceptos registrados</div></td></tr>';
            document.getElementById('cc-tbody').innerHTML = filas;
        });
    }

    window.abrirModalConcepto = function(id) {
        window.conceptoEditandoId = id || null;
        var form = document.getElementById('formConcepto');
        form.reset();
        document.getElementById('cc-titulo').textContent = id ? 'Editar Concepto' : 'Nuevo Concepto';

        if (id) {
            var c = CC.conceptos.find(function(x) {
                return x.id === id;
            });
            if (c) {
                form.nombre.value = c.nombre;
                form.tipo.value = c.tipo;
                form.descripcion.value = c.descripcion || '';
            }
        }
        document.getElementById('modalConcepto').classList.remove('hidden');
        document.getElementById('modalConcepto').classList.add('flex');
    };
    window.cerrarModalConcepto = function() {
        document.getElementById('modalConcepto').classList.add('hidden');
        document.getElementById('modalConcepto').classList.remove('flex');
    };

    window.guardarConcepto = function() {
        var form = document.getElementById('formConcepto');
        var datos = Object.fromEntries(new FormData(form).entries());
        var id = window.conceptoEditandoId;
        var btn = document.getElementById('btnGuardarConcepto');
        btn.disabled = true;

        fetch(id ? '/conceptos-caja/' + id : '/conceptos-caja', {
                method: id ? 'PUT' : 'POST',
                headers: {
                    'X-CSRF-TOKEN': tokenCC(),
                    'Content-Type': 'application/json',
                    Accept: 'application/json'
                },
                body: JSON.stringify(datos),
            }).then(function(r) {
                return r.json().then(function(d) {
                    return {
                        ok: r.ok,
                        d: d
                    };
                });
            })
            .then(function(res) {
                if (!res.ok) throw new Error(res.d.errors ? Object.values(res.d.errors).flat().join('<br>') :
                    res.d.message);
                notifCC(res.d.message, 'success');
                cerrarModalConcepto();
                cargarConceptosCaja();
            }).catch(function(e) {
                notifCC(e.message, 'error');
            }).finally(function() {
                btn.disabled = false;
            });
    };

    window.toggleEstadoConcepto = function(id) {
        fetch('/conceptos-caja/' + id + '/estado', {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': tokenCC(),
                    Accept: 'application/json'
                }
            })
            .then(function(r) {
                return r.json();
            }).then(function(d) {
                notifCC(d.message, 'success');
                cargarConceptosCaja();
            });
    };

    window.eliminarConcepto = function(id) {
        var confirmar = window.Swal ?
            Swal.fire({
                title: '¿Eliminar este concepto?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Eliminar'
            }) :
            Promise.resolve({
                isConfirmed: confirm('¿Eliminar este concepto?')
            });
        confirmar.then(function(res) {
            if (!res.isConfirmed) return;
            fetch('/conceptos-caja/' + id, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': tokenCC(),
                        Accept: 'application/json'
                    }
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
                    if (!res2.ok) throw new Error(res2.d.message);
                    notifCC(res2.d.message, 'success');
                    cargarConceptosCaja();
                })
                .catch(function(e) {
                    notifCC(e.message, 'error');
                });
        });
    };

    cargarConceptosCaja();
</script>
