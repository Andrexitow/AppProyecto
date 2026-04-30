// cajas.js
var editModeCaja = false;
var currentCajaId = null;

window.openModalCaja = function () {
    var modal = document.getElementById('modalCaja');
    if (modal) { modal.classList.remove('hidden'); modal.classList.add('flex'); }
};

window.closeModalCaja = function () {
    var modal = document.getElementById('modalCaja');
    var form = document.getElementById('formCaja');
    if (modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }
    editModeCaja = false;
    currentCajaId = null;
    if (form) form.reset();
    var titulo = document.getElementById('modalCajaTitulo');
    var btn = document.getElementById('btnGuardarCaja');
    if (titulo) titulo.innerText = 'NUEVA CAJA';
    if (btn) btn.innerText = 'Crear Caja';
};

window.editarCaja = function (id) {
    editModeCaja = true;
    currentCajaId = id;

    fetch('/cajas/' + id + '/edit')
        .then(function (res) {
            if (!res.ok) throw new Error('Error ' + res.status);
            return res.json();
        })
        .then(function (caja) {
            var form = document.getElementById('formCaja');
            if (!form) return;

            // ← nombres corregidos: user_id (no cajero_id), sin proximo_numero
            form.nombre.value    = caja.nombre    || '';
            form.prefijo.value   = caja.prefijo   || '';
            form.bodega_id.value = caja.bodega_id || '';
            form.user_id.value   = caja.user_id   || '';
            document.getElementById('checkActiva').checked = !!caja.activa;

            var titulo = document.getElementById('modalCajaTitulo');
            var btn    = document.getElementById('btnGuardarCaja');
            if (titulo) titulo.innerText = 'EDITAR CAJA';
            if (btn)    btn.innerText    = 'Guardar Cambios';

            window.openModalCaja();
        })
        .catch(function (err) {
            console.error(err);
            mostrarNotificacion('Error al cargar datos de la caja', 'error');
        });
};

window.guardarCaja = function () {
    var form     = document.getElementById('formCaja');
    var formData = new FormData(form);

    // Checkbox no envía valor si no está marcado — lo forzamos
    if (!document.getElementById('checkActiva').checked) {
        formData.set('activa', '0');
    } else {
        formData.set('activa', '1');
    }

    var url        = editModeCaja ? '/cajas/update/' + currentCajaId : '/cajas/store';
    var successMsg = editModeCaja ? '¡Caja actualizada!' : '¡Caja creada exitosamente!';

    var btn = document.getElementById('btnGuardarCaja');
    if (btn) { btn.disabled = true; btn.innerText = 'Procesando...'; }

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(function (res) {
        return res.json().then(function (data) { return { res: res, data: data }; });
    })
    .then(function (obj) {
        if (obj.res.status === 422) {
            var errores = Object.values(obj.data.errors || {})
                .map(function (e) { return e[0]; }).join('<br>');
            mostrarNotificacion(errores || obj.data.message, 'error');
            return;
        }
        if (obj.res.ok) {
            mostrarNotificacion(successMsg, 'success');
            window.closeModalCaja();
            loadView('cajas');
        }
    })
    .catch(function (err) {
        console.error(err);
        mostrarNotificacion('Error de conexión', 'error');
    })
    .finally(function () {
        if (btn) {
            btn.disabled = false;
            btn.innerText = editModeCaja ? 'Guardar Cambios' : 'Crear Caja';
        }
    });
};

window.eliminarCaja = function (id) {
    var btnConfirmar = document.getElementById('btnConfirmarAccion');
    var mensaje      = document.getElementById('confirmMensaje');

    if (mensaje) mensaje.innerText = '¿Estás seguro de eliminar esta caja?';

    if (btnConfirmar) {
        // Clonar para remover listeners anteriores y evitar duplicados
        var nuevo = btnConfirmar.cloneNode(true);
        btnConfirmar.parentNode.replaceChild(nuevo, btnConfirmar);

        nuevo.onclick = function () {
            nuevo.disabled  = true;
            nuevo.innerText = 'Eliminando...';

            fetch('/cajas/' + id, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(function (res) {
                return res.json().then(function (data) { return { res: res, data: data }; });
            })
            .then(function (obj) {
                if (obj.res.ok) {
                    mostrarNotificacion('Caja eliminada correctamente', 'success');
                    window.cerrarConfirm();
                    loadView('cajas');
                } else {
                    mostrarNotificacion(obj.data.message || 'Error al eliminar', 'error');
                    window.cerrarConfirm();
                }
            })
            .catch(function (err) {
                console.error(err);
                mostrarNotificacion('Error de conexión', 'error');
                window.cerrarConfirm();
            })
            .finally(function () {
                nuevo.disabled  = false;
                nuevo.innerText = 'Confirmar';
            });
        };
    }

    var modal = document.getElementById('modalConfirm');
    if (modal) { modal.classList.remove('hidden'); modal.classList.add('flex'); }
};