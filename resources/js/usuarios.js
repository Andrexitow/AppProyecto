let editMode = false;
let currentUserId = null;

window.editarUsuario = function (id) {
    editMode = true;
    currentUserId = id;

    fetch(`/usuarios/${id}/edit`)
        .then(res => res.json())
        .then(user => {
            const form = document.getElementById('formUsuario');
            form.name.value = user.name;
            form.username.value = user.username;
            form.rol_id.value = user.rol_id;
            form.password.value = '';

            // Cambiar estética para edición
            document.querySelector('#modalUsuario h2').innerText = 'EDITAR ACCESO';
            // Seleccionamos el botón de guardar específicamente
            const btnSubmit = document.querySelector('#modalUsuario button[onclick="guardarUsuario()"]');
            btnSubmit.innerText = 'Guardar Cambios';

            openModalUsuario();
        });
}

window.guardarUsuario = function () {
    const form = document.getElementById('formUsuario');
    const formData = new FormData(form);

    // DETERMINAR RUTA Y TEXTO SEGÚN EL MODO
    const url = editMode ? `/usuarios/update/${currentUserId}` : '/usuarios/store';
    const successMsg = editMode ? '¡Acceso actualizado!' : '¡Acceso creado exitosamente!';

    const btnSubmit = event.target;
    btnSubmit.disabled = true;
    const originalText = btnSubmit.innerText;
    btnSubmit.innerText = 'Procesando...';

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value ||
                document.querySelector('meta[name="csrf-token"]')?.content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(async res => {
            const data = await res.json();

            if (res.status === 422) {
                let errores = Object.values(data.errors).map(e => e[0]).join('<br>');
                mostrarNotificacion(errores, 'error');
                return;
            }

            if (res.ok) {
                mostrarNotificacion(successMsg, 'success');
                closeModalUsuario(); // Esta función debe resetear el editMode

                if (typeof loadView === 'function') {
                    loadView('usuarios');
                } else {
                    location.reload();
                }
            }
        })
        .catch(err => {
            console.error(err);
            mostrarNotificacion('Error de conexión con el servidor', 'error');
        })
        .finally(() => {
            btnSubmit.disabled = false;
            btnSubmit.innerText = editMode ? 'Guardar Cambios' : 'Crear Acceso';
        });
};

let idAEliminar = null; // Variable global para guardar el ID temporalmente

window.eliminarUsuario = function (id) {
    idAEliminar = id; // Guardamos el ID
    const modal = document.getElementById('modalConfirm');
    const mensaje = document.getElementById('confirmMensaje');

    // Inyectamos el texto personalizado
    mensaje.innerText = '¿ESTÁS SEGURO DE ELIMINAR ESTE ACCESO?';

    // Mostramos el modal (quitamos hidden y ponemos flex)
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

window.cerrarConfirm = function () {
    const modal = document.getElementById('modalConfirm');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    idAEliminar = null;
}

// Evento para el botón "Confirmar" de tu modal
document.getElementById('btnConfirmarAccion').onclick = function () {
    if (!idAEliminar) return;

    const btn = this;
    btn.disabled = true;
    btn.innerText = 'Eliminando...';

    fetch(`/usuarios/${idAEliminar}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content ||
                document.querySelector('input[name="_token"]')?.value,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(async res => {
            const data = await res.json();
            if (res.ok) {
                mostrarNotificacion('Usuario eliminado correctamente', 'success');
                cerrarConfirm();

                if (typeof loadView === 'function') {
                    loadView('usuarios');
                } else {
                    location.reload();
                }
            } else {
                mostrarNotificacion(data.error || 'Error al eliminar', 'error');
                cerrarConfirm();
            }
        })
        .catch(err => {
            console.error(err);
            mostrarNotificacion('Error de conexión', 'error');
            cerrarConfirm();
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerText = 'Confirmar';
        });
}

window.closeModalUsuario = function () {
    const modal = document.getElementById('modalUsuario');
    const form = document.getElementById('formUsuario');

    modal.classList.add('hidden');
    modal.classList.remove('flex');

    // Resetear variables y textos
    editMode = false;
    currentUserId = null;
    form.reset();
    document.querySelector('#modalUsuario h2').innerText = 'NUEVO ACCESO';
    document.querySelector('#modalUsuario button[onclick="guardarUsuario()"]').innerText = 'Crear Acceso';
}

window.editarRol = function (id) {
    let editModeRol = false;
    let currentRolId = null;

    window.editarRol = function (id) {
        editModeRol = true;
        currentRolId = id;

        fetch(`/roles/${id}/edit`)
            .then(res => res.json())
            .then(data => {
                // data ahora debe contener: data.rol y data.permisosIds (los IDs que tiene el rol)
                const rol = data.rol;
                const permisosDelRol = data.permisosIds;

                const form = document.getElementById('formRol');
                form.nombre.value = rol.nombre;
                form.descripcion.value = rol.descripcion || '';

                // 1. Resetear todos los checkboxes primero
                form.querySelectorAll('input[type="checkbox"]').forEach(cb => cb.checked = false);

                // 2. Marcar los permisos que el rol ya tiene
                permisosDelRol.forEach(pId => {
                    const checkbox = form.querySelector(`input[value="${pId}"]`);
                    if (checkbox) checkbox.checked = true;
                });

                // 3. Cambiar estética del modal (Selectores ajustados a tu HTML)
                document.querySelector('#modalRol h2').innerText = 'EDITAR ROL';
                document.querySelector('#modalRol p').innerText = 'Modificando nivel de acceso';

                // Buscamos el botón que tiene la función guardarRol
                const btnGuardar = document.querySelector('button[onclick="guardarRol()"]');
                if (btnGuardar) btnGuardar.innerText = 'Actualizar Configuración';

                openModalRol();
            })
            .catch(err => {
                console.error(err);
                mostrarNotificacion('No se pudieron cargar los datos del rol', 'error');
            });
    }

    window.closeModalRol = function () {
        const modal = document.getElementById('modalRol');
        const form = document.getElementById('formRol');

        modal.classList.add('hidden');
        modal.classList.remove('flex');

        // Resetear al cerrar
        editModeRol = false;
        currentRolId = null;
        form.reset();
        document.querySelector('#modalRol h2').innerText = 'GESTIÓN DE ROLES';
        document.querySelector('button[onclick="guardarRol()"]').innerText = 'Guardar Configuración';
    }

    window.guardarRol = function () {
        const form = document.getElementById('formRol');
        const formData = new FormData(form);

        // 1. Determinar si es creación o actualización
        // Usamos la variable 'currentRolId' que definimos en la función editarRol
        const url = editModeRol ? `/roles/${currentRolId}` : '/roles';
        const method = editModeRol ? 'PUT' : 'POST';

        // 2. Convertir FormData a un objeto plano para manejar los checkboxes (permisos)
        // Laravel necesita recibir permisos[] como un array
        const data = {
            nombre: form.nombre.value,
            descripcion: form.descripcion.value,
            permisos: Array.from(form.querySelectorAll('input[name="permisos[]"]:checked')).map(cb => cb.value)
        };

        // 3. Animación de carga en el botón
        const btn = document.querySelector('button[onclick="guardarRol()"]');
        const originalText = btn.innerText;
        btn.disabled = true;
        btn.innerText = 'PROCESANDO...';

        fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify(data)
        })
            .then(async res => {
                const result = await res.json();
                if (res.ok) {
                    mostrarNotificacion(result.success, 'success');
                    closeModalRol();

                    // Recargar la vista para ver los cambios
                    if (typeof loadView === 'function') {
                        loadView('usuarios');
                    } else {
                        location.reload();
                    }
                } else {
                    // Manejo de errores de validación
                    let errorMsg = result.error || 'Error al guardar';
                    if (result.errors) errorMsg = Object.values(result.errors)[0][0];
                    mostrarNotificacion(errorMsg, 'error');
                }
            })
            .catch(err => {
                console.error(err);
                mostrarNotificacion('Error de conexión con el servidor', 'error');
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerText = originalText;
            });
    }

}