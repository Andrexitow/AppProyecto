<style>
    .sec-header { display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; }
    .sec-title { font-size: 17px; font-weight: 600; color: #111827; letter-spacing: -0.3px; }
    .sec-subtitle { font-size: 12px; color: #6B7280; margin-top: 2px; }

    .btn-primary { display: inline-flex; align-items: center; gap: 6px; background: #1D4ED8; color: #fff; border: none; border-radius: 7px; padding: 7px 14px; font-size: 12px; font-weight: 600; cursor: pointer; }
    .btn-primary:hover { background: #1e40af; }
    .btn-outline { display: inline-flex; align-items: center; gap: 5px; background: #fff; color: #374151; border: 1px solid #D1D5DB; border-radius: 7px; padding: 7px 12px; font-size: 12px; font-weight: 500; cursor: pointer; }
    .btn-outline:hover { background: #F3F4F6; }

    .table-wrapper { background: #fff; border: 1px solid #EAECF0; border-radius: 10px; overflow: hidden; }
    .table-scroll { overflow-x: auto; }
    table.pf-tbl { width: 100%; border-collapse: collapse; font-size: 12.5px; }
    table.pf-tbl thead { background: #F8FAFC; border-bottom: 1px solid #EAECF0; }
    table.pf-tbl thead th { padding: 10px 12px; text-align: left; font-size: 11px; font-weight: 600; color: #6B7280; text-transform: uppercase; letter-spacing: .5px; }
    table.pf-tbl tbody tr { border-bottom: 1px solid #F3F4F6; }
    table.pf-tbl tbody tr:hover { background: #F8FAFC; }
    table.pf-tbl td { padding: 9px 12px; color: #374151; vertical-align: middle; }
    .td-mono { font-family: 'JetBrains Mono','Fira Mono',monospace; font-size: 12px; color: #1D4ED8; font-weight: 700; }

    .badge { display: inline-flex; padding: 3px 8px; border-radius: 20px; font-size: 11px; font-weight: 600; }
    .badge-green { background: #ECFDF5; color: #065F46; }
    .badge-red { background: #FEF2F2; color: #991B1B; }
    .act-btn { display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 6px; border: none; cursor: pointer; font-size: 12px; background: transparent; color: #6B7280; }
    .act-btn:hover { background: #F3F4F6; color: #111827; }
    .spinner-cell { display: flex; align-items: center; justify-content: center; padding: 30px; color: #6B7280; font-size: 13px; }

    .modal-backdrop-pf { background: rgba(17,24,39,.5); backdrop-filter: blur(4px); }
    .modal-pf { background: #fff; border-radius: 16px; width: 100%; max-width: 460px; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; }
    .modal-head-pf { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #EAECF0; }
    .modal-head-title { font-size: 15px; font-weight: 600; color: #111827; }
    .modal-body-pf { flex: 1; overflow-y: auto; padding: 20px; }
    .modal-foot-pf { padding: 14px 20px; border-top: 1px solid #EAECF0; display: flex; gap: 8px; justify-content: flex-end; }
    .pf-field { display: flex; flex-direction: column; gap: 3px; margin-bottom: 12px; }
    .pf-field label { font-size: 11px; font-weight: 500; color: #9CA3AF; text-transform: uppercase; }
    .pf-field input { border: 1px solid #D1D5DB; border-radius: 7px; padding: 8px 10px; font-size: 13px; background: #F9FAFB; outline: none; width: 100%; box-sizing: border-box; }
    .pf-field input:focus { border-color: #1D4ED8; background: #fff; }
</style>

<div id="view-prefijos">
    <div class="sec-header">
        <div>
            <p class="sec-title">🔖 Prefijos</p>
            <p class="sec-subtitle">Catálogo único de prefijos de numeración — asígnalos a cajas y a cualquier documento</p>
        </div>
        <button class="btn-primary" onclick="abrirModalPrefijo()">＋ Nuevo Prefijo</button>
    </div>

    <div class="table-wrapper"><div class="table-scroll">
        <table class="pf-tbl">
            <thead><tr><th>Código</th><th>Nombre</th><th>Descripción</th><th>Estado</th><th></th></tr></thead>
            <tbody id="pf-tbody"><tr><td colspan="5"><div class="spinner-cell">Cargando…</div></td></tr></tbody>
        </table>
    </div></div>
</div>

{{-- MODAL PREFIJO --}}
<div id="modalPrefijo" class="fixed inset-0 hidden items-center justify-center z-50 p-4 modal-backdrop-pf">
    <div class="modal-pf">
        <div class="modal-head-pf">
            <p class="modal-head-title" id="pf-titulo">Nuevo Prefijo</p>
            <button onclick="cerrarModalPrefijo()" style="border:none;background:transparent;font-size:20px;cursor:pointer;color:#6B7280;">✕</button>
        </div>
        <form id="formPrefijo" class="modal-body-pf" onsubmit="return false;">
            <div class="pf-field"><label>Código</label><input autocomplete="off" type="text" name="codigo" maxlength="10" placeholder="Ej: FR" oninput="this.value=this.value.toUpperCase()"></div>
            <div class="pf-field"><label>Nombre</label><input autocomplete="off" type="text" name="nombre" placeholder="Ej: Factura Restaurante"></div>
            <div class="pf-field"><label>Descripción (opcional)</label><input autocomplete="off" type="text" name="descripcion" placeholder="Para qué se usa este prefijo"></div>

            <p style="font-size:11px;font-weight:600;color:#6B7280;text-transform:uppercase;margin:16px 0 8px;border-top:1px solid #EAECF0;padding-top:14px;">
                Resolución DIAN (solo si facturas electrónicamente)
            </p>
            <div class="pf-field"><label>N° de resolución</label><input autocomplete="off" type="text" name="resolucion_numero" placeholder="Ej: 18760000001"></div>
            <div class="pf-field"><label>Fecha de la resolución</label><input autocomplete="off" type="date" name="resolucion_fecha"></div>
            <div style="display:flex;gap:10px;">
                <div class="pf-field" style="flex:1;"><label>Rango desde</label><input autocomplete="off" type="number" min="1" name="rango_desde" placeholder="1"></div>
                <div class="pf-field" style="flex:1;"><label>Rango hasta</label><input autocomplete="off" type="number" min="1" name="rango_hasta" placeholder="5000"></div>
            </div>
            <div style="display:flex;gap:10px;">
                <div class="pf-field" style="flex:1;"><label>Vigente desde</label><input autocomplete="off" type="date" name="vigencia_desde"></div>
                <div class="pf-field" style="flex:1;"><label>Vigente hasta</label><input autocomplete="off" type="date" name="vigencia_hasta"></div>
            </div>
            <div class="pf-field"><label>Clave técnica</label><input autocomplete="off" type="text" name="clave_tecnica" placeholder="La que te entregue la DIAN/tu proveedor"></div>
            <div class="pf-field"><label>ID de rango para facturas (opcional)</label><input autocomplete="off" type="number" min="1" name="numbering_range_id_factus" placeholder="Solo si el proveedor tiene más de un rango de FACTURA activo"></div>
            <div class="pf-field"><label>ID de rango para notas crédito (opcional)</label><input autocomplete="off" type="number" min="1" name="numbering_range_id_nota_credito_factus" placeholder="Solo si el proveedor tiene más de un rango de NOTA CRÉDITO activo"></div>
            <div class="pf-field"><label>ID de rango para notas débito (opcional)</label><input autocomplete="off" type="number" min="1" name="numbering_range_id_nota_debito_factus" placeholder="Solo si el proveedor tiene más de un rango de NOTA DÉBITO activo"></div>
        </form>
        <div class="modal-foot-pf">
            <button class="btn-outline" onclick="cerrarModalPrefijo()">Cancelar</button>
            <button class="btn-primary" id="btnGuardarPrefijo" onclick="guardarPrefijo()">💾 Guardar</button>
        </div>
    </div>
</div>

<script>
    var PF = { prefijos: [] };
    function tokenPF() { return document.querySelector('meta[name="csrf-token"]')?.content; }
    function escPF(s) { return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
    function notifPF(msg, tipo) { if (typeof mostrarNotificacion === 'function') { mostrarNotificacion(msg, tipo || 'success'); return; } window.alert(msg); }

    function cargarPrefijos() {
        fetch('/prefijos', { headers: { Accept: 'application/json' } }).then(function (r) { return r.json(); }).then(function (res) {
            PF.prefijos = res.data || [];
            var filas = PF.prefijos.map(function (p) {
                var badge = p.activo ? '<span class="badge badge-green">Activo</span>' : '<span class="badge badge-red">Inactivo</span>';
                return '<tr><td><span class="td-mono">' + escPF(p.codigo) + '</span></td><td>' + escPF(p.nombre) + '</td>' +
                    '<td>' + escPF(p.descripcion || '—') + '</td><td>' + badge + '</td>' +
                    '<td style="text-align:right;"><button class="act-btn" title="Editar" onclick="abrirModalPrefijo(' + p.id + ')">✏️</button>' +
                    '<button class="act-btn" title="' + (p.activo ? 'Desactivar' : 'Activar') + '" onclick="toggleEstadoPrefijo(' + p.id + ')">' + (p.activo ? '🚫' : '↩️') + '</button>' +
                    '<button class="act-btn" title="Eliminar" onclick="eliminarPrefijo(' + p.id + ')">🗑️</button></td></tr>';
            }).join('') || '<tr><td colspan="5"><div class="spinner-cell">Sin prefijos registrados</div></td></tr>';
            document.getElementById('pf-tbody').innerHTML = filas;
        });
    }

    window.abrirModalPrefijo = function (id) {
        window.prefijoEditandoId = id || null;
        var form = document.getElementById('formPrefijo');
        form.reset();
        document.getElementById('pf-titulo').textContent = id ? 'Editar Prefijo' : 'Nuevo Prefijo';
        // Las fechas llegan como ISO completo (2026-01-15T05:00:00.000000Z);
        // <input autocomplete="off" type="date"> solo acepta el AAAA-MM-DD.
        function soloFechaPF(valor) { return valor ? String(valor).slice(0, 10) : ''; }

        if (id) {
            var p = PF.prefijos.find(function (x) { return x.id === id; });
            if (p) {
                form.codigo.value = p.codigo; form.nombre.value = p.nombre; form.descripcion.value = p.descripcion || '';
                form.resolucion_numero.value = p.resolucion_numero || '';
                form.resolucion_fecha.value = soloFechaPF(p.resolucion_fecha);
                form.rango_desde.value = p.rango_desde || '';
                form.rango_hasta.value = p.rango_hasta || '';
                form.vigencia_desde.value = soloFechaPF(p.vigencia_desde);
                form.vigencia_hasta.value = soloFechaPF(p.vigencia_hasta);
                form.clave_tecnica.value = p.clave_tecnica || '';
                form.numbering_range_id_factus.value = p.numbering_range_id_factus || '';
                form.numbering_range_id_nota_credito_factus.value = p.numbering_range_id_nota_credito_factus || '';
                form.numbering_range_id_nota_debito_factus.value = p.numbering_range_id_nota_debito_factus || '';
            }
        }
        document.getElementById('modalPrefijo').classList.remove('hidden');
        document.getElementById('modalPrefijo').classList.add('flex');
    };
    window.cerrarModalPrefijo = function () { document.getElementById('modalPrefijo').classList.add('hidden'); document.getElementById('modalPrefijo').classList.remove('flex'); };

    window.guardarPrefijo = function () {
        var form = document.getElementById('formPrefijo');
        var datos = Object.fromEntries(new FormData(form).entries());
        var id = window.prefijoEditandoId;
        var btn = document.getElementById('btnGuardarPrefijo');
        btn.disabled = true;

        fetch(id ? '/prefijos/' + id : '/prefijos', {
            method: id ? 'PUT' : 'POST',
            headers: { 'X-CSRF-TOKEN': tokenPF(), 'Content-Type': 'application/json', Accept: 'application/json' },
            body: JSON.stringify(datos),
        }).then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
            .then(function (res) {
                if (!res.ok) throw new Error(res.d.errors ? Object.values(res.d.errors).flat().join('<br>') : res.d.message);
                notifPF(res.d.message, 'success');
                cerrarModalPrefijo();
                cargarPrefijos();
            }).catch(function (e) { notifPF(e.message, 'error'); }).finally(function () { btn.disabled = false; });
    };

    window.toggleEstadoPrefijo = function (id) {
        fetch('/prefijos/' + id + '/estado', { method: 'PUT', headers: { 'X-CSRF-TOKEN': tokenPF(), Accept: 'application/json' } })
            .then(function (r) { return r.json(); }).then(function (d) { notifPF(d.message, 'success'); cargarPrefijos(); });
    };

    window.eliminarPrefijo = function (id) {
        var confirmar = window.Swal
            ? Swal.fire({ title: '¿Eliminar este prefijo?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Eliminar' })
            : Promise.resolve({ isConfirmed: confirm('¿Eliminar este prefijo?') });
        confirmar.then(function (res) {
            if (!res.isConfirmed) return;
            fetch('/prefijos/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': tokenPF(), Accept: 'application/json' } })
                .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
                .then(function (res2) { if (!res2.ok) throw new Error(res2.d.message); notifPF(res2.d.message, 'success'); cargarPrefijos(); })
                .catch(function (e) { notifPF(e.message, 'error'); });
        });
    };

    cargarPrefijos();
</script>
