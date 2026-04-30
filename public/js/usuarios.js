// usuarios.js — sin Vite, carga tradicional

// ============================================================
// ESTADO GLOBAL
// ============================================================
var editMode = false;
var currentUserId = null;
var editModeRol = false;
var currentRolId = null;
var idAEliminar = null;

// ============================================================
// MODALES USUARIO
// ============================================================
window.openModalUsuario = function () {
    var modal = document.getElementById('modalUsuario');
    if (modal) { modal.classList.remove('hidden'); modal.classList.add('flex'); }
};

window.closeModalUsuario = function () {
    var modal = document.getElementById('modalUsuario');
    var form = document.getElementById('formUsuario');
    if (modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }
    editMode = false;
    currentUserId = null;
    if (form) form.reset();
    var h2 = document.querySelector('#modalUsuario h2');
    var btn = document.querySelector('#modalUsuario button[onclick="guardarUsuario()"]');
    if (h2) h2.innerText = 'NUEVO ACCESO';
    if (btn) btn.innerText = 'Crear Acceso';
};

// ============================================================
// CRUD USUARIOS
// ============================================================
window.editarUsuario = function (id) {
    editMode = true;
    currentUserId = id;

    fetch('/usuarios/' + id + '/edit')
        .then(function (res) { return res.json(); })
        .then(function (user) {
            var form = document.getElementById('formUsuario');
            if (!form) return;
            form.name.value = user.name;
            form.username.value = user.username;
            form.rol_id.value = user.rol_id;
            form.caja_id.value = user.caja_id || '';
            form.password.value = '';

            var h2 = document.querySelector('#modalUsuario h2');
            var btn = document.querySelector('#modalUsuario button[onclick="guardarUsuario()"]');
            if (h2) h2.innerText = 'EDITAR ACCESO';
            if (btn) btn.innerText = 'Guardar Cambios';

            window.openModalUsuario();
        });
};

window.guardarUsuario = function () {
    var form = document.getElementById('formUsuario');
    var formData = new FormData(form);
    var url = editMode ? '/usuarios/update/' + currentUserId : '/usuarios/store';
    var successMsg = editMode ? '¡Acceso actualizado!' : '¡Acceso creado exitosamente!';

    var btnSubmit = document.querySelector('#modalUsuario button[onclick="guardarUsuario()"]');
    if (btnSubmit) { btnSubmit.disabled = true; btnSubmit.innerText = 'Procesando...'; }

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
            var errores = Object.values(obj.data.errors).map(function (e) { return e[0]; }).join('<br>');
            mostrarNotificacion(errores, 'error');
            return;
        }
        if (obj.res.ok) {
            mostrarNotificacion(successMsg, 'success');
            window.closeModalUsuario();
            loadView('usuarios');
        }
    })
    .catch(function (err) {
        console.error(err);
        mostrarNotificacion('Error de conexión con el servidor', 'error');
    })
    .finally(function () {
        if (btnSubmit) {
            btnSubmit.disabled = false;
            btnSubmit.innerText = editMode ? 'Guardar Cambios' : 'Crear Acceso';
        }
    });
};

window.eliminarUsuario = function (id) {
    idAEliminar = id;

    var mensaje = document.getElementById('confirmMensaje');
    if (mensaje) mensaje.innerText = '¿ESTÁS SEGURO DE ELIMINAR ESTE ACCESO?';

    // Asignar acción al botón confirmar justo antes de abrir el modal
    var btnConfirmar = document.getElementById('btnConfirmarAccion');
    if (btnConfirmar) {
        btnConfirmar.onclick = function () {
            btnConfirmar.disabled = true;
            btnConfirmar.innerText = 'Eliminando...';

            fetch('/usuarios/' + idAEliminar, {
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
                    mostrarNotificacion('Usuario eliminado correctamente', 'success');
                    window.cerrarConfirm();
                    loadView('usuarios');
                } else {
                    mostrarNotificacion(obj.data.error || 'Error al eliminar', 'error');
                    window.cerrarConfirm();
                }
            })
            .catch(function (err) {
                console.error(err);
                mostrarNotificacion('Error de conexión', 'error');
                window.cerrarConfirm();
            })
            .finally(function () {
                btnConfirmar.disabled = false;
                btnConfirmar.innerText = 'Confirmar';
            });
        };
    }

    var modal = document.getElementById('modalConfirm');
    if (modal) { modal.classList.remove('hidden'); modal.classList.add('flex'); }
};

window.cerrarConfirm = function () {
    var modal = document.getElementById('modalConfirm');
    if (modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }
    idAEliminar = null;
};

// ============================================================
// MODALES ROL
// ============================================================
window.openModalRol = function () {
    var modal = document.getElementById('modalRol');
    if (modal) { modal.classList.remove('hidden'); modal.classList.add('flex'); }
};

window.closeModalRol = function () {
    var modal = document.getElementById('modalRol');
    var form = document.getElementById('formRol');
    if (modal) { modal.classList.add('hidden'); modal.classList.remove('flex'); }
    editModeRol = false;
    currentRolId = null;
    if (form) form.reset();
    var h2 = document.querySelector('#modalRol h2');
    var btn = document.querySelector('button[onclick="guardarRol()"]');
    if (h2) h2.innerText = 'GESTIÓN DE ROLES';
    if (btn) btn.innerText = 'Guardar Configuración';
};

// ============================================================
// CRUD ROLES
// ============================================================
window.editarRol = function (id) {
    editModeRol = true;
    currentRolId = id;

    fetch('/roles/' + id + '/edit')
        .then(function (res) { return res.json(); })
        .then(function (data) {
            var rol = data.rol;
            var permisosDelRol = data.permisosIds;
            var form = document.getElementById('formRol');
            if (!form) return;

            form.nombre.value = rol.nombre;
            form.descripcion.value = rol.descripcion || '';

            // Resetear checkboxes y marcar los que corresponden
            form.querySelectorAll('input[type="checkbox"]').forEach(function (cb) {
                cb.checked = false;
            });
            permisosDelRol.forEach(function (pId) {
                var checkbox = form.querySelector('input[value="' + pId + '"]');
                if (checkbox) checkbox.checked = true;
            });

            var h2 = document.querySelector('#modalRol h2');
            var p = document.querySelector('#modalRol p');
            var btn = document.querySelector('button[onclick="guardarRol()"]');
            if (h2) h2.innerText = 'EDITAR ROL';
            if (p) p.innerText = 'Modificando nivel de acceso';
            if (btn) btn.innerText = 'Actualizar Configuración';

            window.openModalRol();
        })
        .catch(function (err) {
            console.error(err);
            mostrarNotificacion('No se pudieron cargar los datos del rol', 'error');
        });
};

window.guardarRol = function () {
    var form = document.getElementById('formRol');
    var url = editModeRol ? '/roles/' + currentRolId : '/roles';
    var method = editModeRol ? 'PUT' : 'POST';

    var data = {
        nombre: form.nombre.value,
        descripcion: form.descripcion.value,
        permisos: Array.from(form.querySelectorAll('input[name="permisos[]"]:checked'))
                       .map(function (cb) { return cb.value; })
    };

    var btn = document.querySelector('button[onclick="guardarRol()"]');
    var originalText = btn ? btn.innerText : '';
    if (btn) { btn.disabled = true; btn.innerText = 'PROCESANDO...'; }

    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify(data)
    })
    .then(function (res) {
        return res.json().then(function (result) { return { res: res, result: result }; });
    })
    .then(function (obj) {
        if (obj.res.ok) {
            mostrarNotificacion(obj.result.success, 'success');
            window.closeModalRol();
            loadView('usuarios');
        } else {
            var errorMsg = obj.result.error || 'Error al guardar';
            if (obj.result.errors) errorMsg = Object.values(obj.result.errors)[0][0];
            mostrarNotificacion(errorMsg, 'error');
        }
    })
    .catch(function (err) {
        console.error(err);
        mostrarNotificacion('Error de conexión con el servidor', 'error');
    })
    .finally(function () {
        if (btn) { btn.disabled = false; btn.innerText = originalText; }
    });
};