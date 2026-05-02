// ── categorias_pos.js ──

function loadView(view) {
    if (view === 'categorias_pos') cargarCategoriasPos();
}

function cargarCategoriasPos() {
    fetch('/categorias-pos')
        .then(r => r.json())
        .then(data => renderCategoriasPos(data));
}

function renderCategoriasPos(cats) {
    const contenedor = document.getElementById('main-content');
    contenedor.innerHTML = `
    <div style="max-width:680px; margin:0 auto;">

        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:20px;">
            <div>
                <h2 style="font-size:18px; font-weight:600; color:#111827; margin:0;">Categorías POS</h2>
                <p style="font-size:12px; color:#6B7280; margin:4px 0 0;">Botones de filtro que aparecen en el POS Terminal</p>
            </div>
            <button onclick="abrirModalCat()"
                style="padding:8px 16px; background:#1D4ED8; color:#fff; font-size:13px; font-weight:500; border:none; border-radius:8px; cursor:pointer;">
                + Nueva categoría
            </button>
        </div>

        ${cats.length === 0 ? `
        <div style="text-align:center; padding:60px 20px; background:#fff; border:1px solid #EAECF0; border-radius:12px;">
            <div style="font-size:40px; margin-bottom:12px;">📂</div>
            <p style="color:#6B7280; font-size:14px; margin:0;">No hay categorías creadas aún</p>
            <p style="color:#9CA3AF; font-size:12px; margin:6px 0 0;">Crea una para que aparezca en el POS</p>
        </div>` : `
        <div style="display:flex; flex-direction:column; gap:10px;" id="lista-cats">
            ${cats.map((c, i) => tarjetaCat(c, i, cats.length)).join('')}
        </div>`}

        <p style="font-size:11px; color:#9CA3AF; margin-top:16px; text-align:center;">
            El orden determina la posición del botón en el POS. Menor número = primero.
        </p>
    </div>

    <!-- MODAL -->
    <div id="modal-cat" style="display:none; position:fixed; inset:0; background:rgba(17,24,39,0.5);
        backdrop-filter:blur(4px); align-items:center; justify-content:center; z-index:9999;">
        <div style="background:#fff; border-radius:16px; padding:28px 24px; width:100%; max-width:360px;">
            <h3 style="font-size:15px; font-weight:600; color:#111827; margin:0 0 20px;" id="modal-cat-titulo">
                Nueva categoría
            </h3>

            <input type="hidden" id="cat-id">

            <div style="margin-bottom:14px;">
                <label style="font-size:11px; font-weight:500; color:#6B7280; display:block; margin-bottom:5px;">
                    ICONO (emoji)
                </label>
                <input id="cat-icono" type="text" maxlength="4" placeholder="🍔"
                    style="width:100%; border:1px solid #E5E7EB; border-radius:8px; padding:10px;
                    font-size:28px; text-align:center; outline:none;">
            </div>

            <div style="margin-bottom:14px;">
                <label style="font-size:11px; font-weight:500; color:#6B7280; display:block; margin-bottom:5px;">
                    NOMBRE
                </label>
                <input id="cat-nombre" type="text" placeholder="Ej: Bebidas"
                    style="width:100%; border:1px solid #E5E7EB; border-radius:8px; padding:10px 12px;
                    font-size:14px; outline:none;">
            </div>

            <div style="margin-bottom:20px;">
                <label style="font-size:11px; font-weight:500; color:#6B7280; display:block; margin-bottom:5px;">
                    ORDEN
                </label>
                <input id="cat-orden" type="number" min="0" placeholder="0"
                    style="width:100%; border:1px solid #E5E7EB; border-radius:8px; padding:10px 12px;
                    font-size:14px; outline:none;">
            </div>

            <div style="display:flex; gap:8px;">
                <button onclick="cerrarModalCat()"
                    style="flex:1; padding:10px; background:#F3F4F6; color:#374151; font-size:13px;
                    font-weight:500; border:none; border-radius:8px; cursor:pointer;">
                    Cancelar
                </button>
                <button onclick="guardarCat()"
                    style="flex:1; padding:10px; background:#1D4ED8; color:#fff; font-size:13px;
                    font-weight:500; border:none; border-radius:8px; cursor:pointer;">
                    Guardar
                </button>
            </div>
        </div>
    </div>`;
}

function tarjetaCat(c, i, total) {
    return `
    <div style="display:flex; align-items:center; justify-content:space-between;
        background:#fff; border:1px solid #EAECF0; border-radius:12px; padding:14px 16px;">
        <div style="display:flex; align-items:center; gap:14px;">
            <div style="width:48px; height:48px; background:#EFF6FF; border-radius:12px;
                display:flex; align-items:center; justify-content:center; font-size:24px;">
                ${c.icono}
            </div>
            <div>
                <p style="font-size:14px; font-weight:600; color:#111827; margin:0;">${c.nombre}</p>
                <p style="font-size:11px; color:#9CA3AF; margin:2px 0 0;">Orden: ${c.orden}</p>
            </div>
        </div>
        <div style="display:flex; gap:6px;">
            <button onclick="editarCat(${c.id}, '${c.nombre}', '${c.icono}', ${c.orden})"
                style="padding:6px 12px; background:#F3F4F6; color:#374151; font-size:12px;
                font-weight:500; border:none; border-radius:7px; cursor:pointer;">
                Editar
            </button>
            <button onclick="eliminarCat(${c.id})"
                style="padding:6px 12px; background:#FEF2F2; color:#DC2626; font-size:12px;
                font-weight:500; border:none; border-radius:7px; cursor:pointer;">
                Eliminar
            </button>
        </div>
    </div>`;
}

function abrirModalCat() {
    document.getElementById('cat-id').value    = '';
    document.getElementById('cat-nombre').value = '';
    document.getElementById('cat-icono').value  = '';
    document.getElementById('cat-orden').value  = '0';
    document.getElementById('modal-cat-titulo').textContent = 'Nueva categoría';
    document.getElementById('modal-cat').style.display = 'flex';
}

function editarCat(id, nombre, icono, orden) {
    document.getElementById('cat-id').value     = id;
    document.getElementById('cat-nombre').value = nombre;
    document.getElementById('cat-icono').value  = icono;
    document.getElementById('cat-orden').value  = orden;
    document.getElementById('modal-cat-titulo').textContent = 'Editar categoría';
    document.getElementById('modal-cat').style.display = 'flex';
}

function cerrarModalCat() {
    document.getElementById('modal-cat').style.display = 'none';
}

function guardarCat() {
    const id     = document.getElementById('cat-id').value;
    const nombre = document.getElementById('cat-nombre').value.trim();
    const icono  = document.getElementById('cat-icono').value.trim();
    const orden  = parseInt(document.getElementById('cat-orden').value) || 0;

    if (!nombre || !icono) {
        mostrarNotif('Completa nombre e icono', 'error');
        return;
    }

    const url    = id ? `/categorias-pos/${id}` : '/categorias-pos';
    const method = id ? 'PUT' : 'POST';

    fetch(url, {
        method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ nombre, icono, orden })
    })
    .then(r => r.json())
    .then(() => {
        cerrarModalCat();
        cargarCategoriasPos();
        mostrarNotif(id ? 'Categoría actualizada' : 'Categoría creada', 'success');
    });
}

function eliminarCat(id) {
    mostrarConfirm('¿Eliminar esta categoría? Los productos vinculados quedarán sin categoría.', () => {
        fetch(`/categorias-pos/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
        })
        .then(() => {
            cargarCategoriasPos();
            mostrarNotif('Categoría eliminada', 'success');
        });
    });
}