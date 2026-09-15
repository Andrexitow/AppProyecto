<style>
    .sec-header { display: flex; align-items: flex-start; justify-content: space-between; flex-wrap: wrap; gap: 12px; margin-bottom: 16px; }
    .sec-title { font-size: 17px; font-weight: 600; color: #111827; letter-spacing: -0.3px; }
    .sec-subtitle { font-size: 12px; color: #6B7280; margin-top: 2px; }

    .btn-primary { display: inline-flex; align-items: center; gap: 6px; background: #1D4ED8; color: #fff; border: none; border-radius: 7px; padding: 7px 14px; font-size: 12px; font-weight: 600; cursor: pointer; }
    .btn-primary:hover { background: #1e40af; }
    .btn-outline { display: inline-flex; align-items: center; gap: 5px; background: #fff; color: #374151; border: 1px solid #D1D5DB; border-radius: 7px; padding: 7px 12px; font-size: 12px; font-weight: 500; cursor: pointer; }
    .btn-outline:hover { background: #F3F4F6; }
    .btn-mini { display: inline-flex; align-items: center; gap: 4px; background: #EFF6FF; color: #1D4ED8; border: 1px solid #DBEAFE; border-radius: 6px; padding: 5px 9px; font-size: 11px; font-weight: 600; cursor: pointer; }
    .btn-mini:hover { background: #DBEAFE; }

    .mz-empty { background: #fff; border: 1px dashed #D1D5DB; border-radius: 12px; padding: 40px 20px; text-align: center; color: #6B7280; font-size: 13px; }
    .mz-zonas { display: flex; flex-direction: column; gap: 14px; }
    .mz-zona { background: #fff; border: 1px solid #EAECF0; border-radius: 12px; overflow: hidden; }
    .mz-zona-head { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 12px 16px; background: #F8FAFC; border-bottom: 1px solid #EAECF0; flex-wrap: wrap; }
    .mz-zona-titulo { font-size: 14px; font-weight: 700; color: #111827; }
    .mz-zona-sub { font-size: 11px; color: #6B7280; margin-top: 1px; }
    .mz-zona-acciones { display: flex; gap: 6px; }
    .act-btn { display: inline-flex; align-items: center; justify-content: center; width: 26px; height: 26px; border-radius: 6px; border: none; cursor: pointer; font-size: 12px; background: transparent; color: #6B7280; }
    .act-btn:hover { background: #F3F4F6; color: #111827; }

    .mz-mesas { display: flex; flex-wrap: wrap; gap: 10px; padding: 14px 16px; }
    .mz-mesa { position: relative; width: 128px; border: 1px solid #E5E7EB; border-radius: 10px; padding: 10px 10px 8px; background: #FAFAFA; }
    .mz-mesa-num { font-size: 15px; font-weight: 700; color: #111827; }
    .mz-mesa-cap { font-size: 11px; color: #6B7280; margin-top: 1px; }
    .mz-mesa-acciones { display: flex; gap: 4px; margin-top: 8px; }

    .badge { display: inline-flex; padding: 3px 8px; border-radius: 20px; font-size: 10px; font-weight: 600; margin-top: 6px; }
    .badge-green { background: #ECFDF5; color: #065F46; }
    .badge-amber { background: #FFFBEB; color: #92400E; }
    .badge-red { background: #FEF2F2; color: #991B1B; }
    .badge-gray { background: #F3F4F6; color: #374151; }

    .mz-sin-mesas { padding: 14px 16px; color: #9CA3AF; font-size: 12px; font-style: italic; }

    .modal-backdrop-mz { background: rgba(17,24,39,.5); backdrop-filter: blur(4px); }
    .modal-mz { background: #fff; border-radius: 16px; width: 100%; max-width: 400px; max-height: 90vh; display: flex; flex-direction: column; overflow: hidden; }
    .modal-head-mz { display: flex; align-items: center; justify-content: space-between; padding: 16px 20px; border-bottom: 1px solid #EAECF0; }
    .modal-head-title { font-size: 15px; font-weight: 600; color: #111827; }
    .modal-body-mz { flex: 1; overflow-y: auto; padding: 20px; }
    .modal-foot-mz { padding: 14px 20px; border-top: 1px solid #EAECF0; display: flex; gap: 8px; justify-content: flex-end; }
    .mz-field { display: flex; flex-direction: column; gap: 3px; margin-bottom: 12px; }
    .mz-field label { font-size: 11px; font-weight: 500; color: #9CA3AF; text-transform: uppercase; }
    .mz-field input, .mz-field select { border: 1px solid #D1D5DB; border-radius: 7px; padding: 8px 10px; font-size: 13px; background: #F9FAFB; outline: none; width: 100%; box-sizing: border-box; }
    .mz-field input:focus, .mz-field select:focus { border-color: #1D4ED8; background: #fff; }
</style>

<div id="view-mesas">
    <div class="sec-header">
        <div>
            <p class="sec-title">🍽️ Mesas y Zonas</p>
            <p class="sec-subtitle">El piso que ve el mesero en "Comandar" — crea las zonas del local y las mesas de cada una.</p>
        </div>
        <button class="btn-primary" onclick="abrirModalZona()">＋ Nueva Zona</button>
    </div>

    <div id="mz-contenedor" class="mz-zonas"><div class="mz-empty">Cargando…</div></div>
</div>

{{-- MODAL ZONA --}}
<div id="modalZona" class="fixed inset-0 hidden items-center justify-center z-50 p-4 modal-backdrop-mz">
    <div class="modal-mz">
        <div class="modal-head-mz">
            <p class="modal-head-title" id="mz-zona-titulo">Nueva Zona</p>
            <button onclick="cerrarModalZona()" style="border:none;background:transparent;font-size:20px;cursor:pointer;color:#6B7280;">✕</button>
        </div>
        <form id="formZona" class="modal-body-mz" onsubmit="return false;">
            <div class="mz-field"><label>Nombre</label><input type="text" name="nombre" placeholder="Ej: Restaurante, Discoteca, Karaoke 1"></div>
            <div class="mz-field">
                <label>Bodega (para descontar inventario / imprimir en cocina) — opcional</label>
                <select name="bodega_id"><option value="">Sin bodega asignada</option>@foreach ($bodegas as $b)<option value="{{ $b->id }}">{{ $b->descripcion }}</option>@endforeach</select>
            </div>
        </form>
        <div class="modal-foot-mz">
            <button class="btn-outline" onclick="cerrarModalZona()">Cancelar</button>
            <button class="btn-primary" id="btnGuardarZona" onclick="guardarZona()">💾 Guardar</button>
        </div>
    </div>
</div>

{{-- MODAL MESA --}}
<div id="modalMesa" class="fixed inset-0 hidden items-center justify-center z-50 p-4 modal-backdrop-mz">
    <div class="modal-mz">
        <div class="modal-head-mz">
            <p class="modal-head-title" id="mz-mesa-titulo">Nueva Mesa</p>
            <button onclick="cerrarModalMesa()" style="border:none;background:transparent;font-size:20px;cursor:pointer;color:#6B7280;">✕</button>
        </div>
        <form id="formMesa" class="modal-body-mz" onsubmit="return false;">
            <div class="mz-field">
                <label>Zona</label>
                <select name="zona_id" id="mz-mesa-zona">@foreach ($zonas as $z)<option value="{{ $z->id }}">{{ $z->nombre }}</option>@endforeach</select>
            </div>
            <div class="mz-field"><label>Número / Nombre de la mesa</label><input type="text" name="numero" placeholder="Ej: 01, Barra 3, VIP"></div>
            <div class="mz-field"><label>Capacidad (personas)</label><input type="number" name="capacidad" min="1" max="50" value="4"></div>
        </form>
        <div class="modal-foot-mz">
            <button class="btn-outline" onclick="cerrarModalMesa()">Cancelar</button>
            <button class="btn-primary" id="btnGuardarMesa" onclick="guardarMesa()">💾 Guardar</button>
        </div>
    </div>
</div>

<script>
    var MZ = { zonas: @json($zonas) };
    function tokenMZ() { return document.querySelector('meta[name="csrf-token"]')?.content; }
    function escMZ(s) { return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;'); }
    function notifMZ(msg, tipo) { if (typeof mostrarNotificacion === 'function') { mostrarNotificacion(msg, tipo || 'success'); return; } window.alert(msg); }

    function badgeEstadoMesa(estado) {
        var mapa = { disponible: ['badge-green', 'Disponible'], ocupada: ['badge-red', 'Ocupada'], cuenta_pedida: ['badge-amber', 'Cuenta pedida'], seleccionada: ['badge-amber', 'Seleccionada'] };
        var par = mapa[estado] || ['badge-gray', estado];
        return '<span class="badge ' + par[0] + '">' + par[1] + '</span>';
    }

    function renderZonasMesas() {
        var cont = document.getElementById('mz-contenedor');
        if (!MZ.zonas.length) {
            cont.innerHTML = '<div class="mz-empty">Todavía no hay zonas creadas. Crea una zona (ej. "Restaurante") y luego agrégale mesas.</div>';
            return;
        }

        cont.innerHTML = MZ.zonas.map(function (z) {
            var mesasHtml = (z.mesas || []).map(function (m) {
                return '<div class="mz-mesa">' +
                    '<div class="mz-mesa-num">' + escMZ(m.numero) + '</div>' +
                    '<div class="mz-mesa-cap">' + m.capacidad + ' pers.</div>' +
                    badgeEstadoMesa(m.estado) +
                    '<div class="mz-mesa-acciones">' +
                        '<button class="act-btn" title="Editar" onclick="abrirModalMesa(' + z.id + ', ' + m.id + ')">✏️</button>' +
                        '<button class="act-btn" title="Eliminar" onclick="eliminarMesa(' + m.id + ')">🗑️</button>' +
                    '</div>' +
                '</div>';
            }).join('') || '<div class="mz-sin-mesas">Sin mesas todavía.</div>';

            return '<div class="mz-zona">' +
                '<div class="mz-zona-head">' +
                    '<div><div class="mz-zona-titulo">' + escMZ(z.nombre) + '</div><div class="mz-zona-sub">' + (z.bodega ? 'Bodega: ' + escMZ(z.bodega.descripcion) : 'Sin bodega asignada') + ' · ' + (z.mesas || []).length + ' mesa(s)</div></div>' +
                    '<div class="mz-zona-acciones">' +
                        '<button class="btn-mini" onclick="abrirModalMesa(' + z.id + ')">＋ Mesa</button>' +
                        '<button class="act-btn" title="Editar zona" onclick="abrirModalZona(' + z.id + ')">✏️</button>' +
                        '<button class="act-btn" title="Eliminar zona" onclick="eliminarZona(' + z.id + ')">🗑️</button>' +
                    '</div>' +
                '</div>' +
                '<div class="mz-mesas">' + mesasHtml + '</div>' +
            '</div>';
        }).join('');
    }

    // Más simple que reconstruir el estado en JS: se vuelve a pedir esta
    // misma vista completa (igual que hacen productos.js/prefijos tras
    // guardar), así el HTML y el JSON embebido de zonas/mesas quedan
    // siempre en el mismo request y no se pueden desincronizar.
    function recargarMZ() {
        if (typeof loadView === 'function') { loadView('mesas'); return; }
        window.location.reload();
    }

    // ── ZONA ──
    window.abrirModalZona = function (id) {
        window.zonaEditandoId = id || null;
        var form = document.getElementById('formZona');
        form.reset();
        document.getElementById('mz-zona-titulo').textContent = id ? 'Editar Zona' : 'Nueva Zona';
        if (id) {
            var z = MZ.zonas.find(function (x) { return x.id === id; });
            if (z) { form.nombre.value = z.nombre; form.bodega_id.value = z.bodega_id || ''; }
        }
        document.getElementById('modalZona').classList.remove('hidden');
        document.getElementById('modalZona').classList.add('flex');
    };
    window.cerrarModalZona = function () { document.getElementById('modalZona').classList.add('hidden'); document.getElementById('modalZona').classList.remove('flex'); };

    window.guardarZona = function () {
        var form = document.getElementById('formZona');
        var datos = Object.fromEntries(new FormData(form).entries());
        var id = window.zonaEditandoId;
        var btn = document.getElementById('btnGuardarZona');
        btn.disabled = true;

        fetch(id ? '/zonas/' + id : '/zonas', {
            method: id ? 'PUT' : 'POST',
            headers: { 'X-CSRF-TOKEN': tokenMZ(), 'Content-Type': 'application/json', Accept: 'application/json' },
            body: JSON.stringify(datos),
        }).then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
            .then(function (res) {
                if (!res.ok) throw new Error(res.d.errors ? Object.values(res.d.errors).flat().join('<br>') : res.d.message);
                notifMZ(res.d.message, 'success');
                cerrarModalZona();
                recargarMZ();
            }).catch(function (e) { notifMZ(e.message, 'error'); }).finally(function () { btn.disabled = false; });
    };

    window.eliminarZona = function (id) {
        var confirmar = window.Swal
            ? Swal.fire({ title: '¿Eliminar esta zona?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Eliminar' })
            : Promise.resolve({ isConfirmed: confirm('¿Eliminar esta zona?') });
        confirmar.then(function (res) {
            if (!res.isConfirmed) return;
            fetch('/zonas/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': tokenMZ(), Accept: 'application/json' } })
                .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
                .then(function (res2) { if (!res2.ok) throw new Error(res2.d.message); notifMZ(res2.d.message, 'success'); recargarMZ(); })
                .catch(function (e) { notifMZ(e.message, 'error'); });
        });
    };

    // ── MESA ──
    window.abrirModalMesa = function (zonaId, mesaId) {
        window.mesaEditandoId = mesaId || null;
        var form = document.getElementById('formMesa');
        form.reset();
        document.getElementById('mz-mesa-titulo').textContent = mesaId ? 'Editar Mesa' : 'Nueva Mesa';
        document.getElementById('mz-mesa-zona').value = zonaId;

        if (mesaId) {
            var zona = MZ.zonas.find(function (z) { return z.id === zonaId; });
            var m = zona ? (zona.mesas || []).find(function (x) { return x.id === mesaId; }) : null;
            if (m) { form.numero.value = m.numero; form.capacidad.value = m.capacidad; }
        } else {
            form.capacidad.value = 4;
        }

        document.getElementById('modalMesa').classList.remove('hidden');
        document.getElementById('modalMesa').classList.add('flex');
    };
    window.cerrarModalMesa = function () { document.getElementById('modalMesa').classList.add('hidden'); document.getElementById('modalMesa').classList.remove('flex'); };

    window.guardarMesa = function () {
        var form = document.getElementById('formMesa');
        var datos = Object.fromEntries(new FormData(form).entries());
        var id = window.mesaEditandoId;
        var btn = document.getElementById('btnGuardarMesa');
        btn.disabled = true;

        fetch(id ? '/mesas-admin/' + id : '/mesas-admin', {
            method: id ? 'PUT' : 'POST',
            headers: { 'X-CSRF-TOKEN': tokenMZ(), 'Content-Type': 'application/json', Accept: 'application/json' },
            body: JSON.stringify(datos),
        }).then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
            .then(function (res) {
                if (!res.ok) throw new Error(res.d.errors ? Object.values(res.d.errors).flat().join('<br>') : res.d.message);
                notifMZ(res.d.message, 'success');
                cerrarModalMesa();
                recargarMZ();
            }).catch(function (e) { notifMZ(e.message, 'error'); }).finally(function () { btn.disabled = false; });
    };

    window.eliminarMesa = function (id) {
        var confirmar = window.Swal
            ? Swal.fire({ title: '¿Eliminar esta mesa?', icon: 'warning', showCancelButton: true, confirmButtonText: 'Eliminar' })
            : Promise.resolve({ isConfirmed: confirm('¿Eliminar esta mesa?') });
        confirmar.then(function (res) {
            if (!res.isConfirmed) return;
            fetch('/mesas-admin/' + id, { method: 'DELETE', headers: { 'X-CSRF-TOKEN': tokenMZ(), Accept: 'application/json' } })
                .then(function (r) { return r.json().then(function (d) { return { ok: r.ok, d: d }; }); })
                .then(function (res2) { if (!res2.ok) throw new Error(res2.d.message); notifMZ(res2.d.message, 'success'); recargarMZ(); })
                .catch(function (e) { notifMZ(e.message, 'error'); });
        });
    };

    renderZonasMesas();
</script>
