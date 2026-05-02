window.abrirModalGrupo = function () {
    document.getElementById('formGrupo').reset();
    document.getElementById('grupo_id').value = '';
    document.getElementById('modalTitulo').textContent = 'Nuevo Grupo';
    document.getElementById('modalGrupo').classList.replace('hidden', 'flex');
};

window.cerrarModalGrupo = function () {
    document.getElementById('modalGrupo').classList.replace('flex', 'hidden');
};

window.editarGrupo = function (id, nombre, impresoraId) {
    abrirModalGrupo();
    document.getElementById('modalTitulo').textContent = 'Editar Grupo';
    document.getElementById('grupo_id').value = id;
    document.getElementById('nombre_grupo').value = nombre;
    document.getElementById('impresora_id').value = impresoraId;
};

window.guardarGrupo = async function (e) {
    e.preventDefault();
    const id = document.getElementById('grupo_id').value;
    const url = id ? `/grupos/update/${id}` : '/grupos/store';

    const payload = {
        nombre: document.getElementById('nombre_grupo').value,
        impresora_id: document.getElementById('impresora_id').value
    };

    try {
        const res = await fetch(url, {
            method: 'POST', // Siguiendo tu estructura de rutas
            headers: {
                'X-CSRF-TOKEN': window.csrfToken, // Usando tu variable global de utils.js
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (res.ok && data.status === 'success') {
            // ✅ USANDO TU NOTIFICACIÓN
            window.mostrarNotificacion(data.message, 'success');
            cerrarModalGrupo();
            loadView('grupos');
        } else {
            // ✅ USANDO TU NOTIFICACIÓN DE ERROR
            window.mostrarNotificacion(data.message || 'Error al procesar', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        window.mostrarNotificacion('Error de conexión con el servidor', 'error');
    }
};

window.eliminarGrupo = function (id) {
    // ✅ USANDO TU MODAL DE CONFIRMACIÓN PERSONALIZADO
    window.abrirConfirm('¿Estás seguro de eliminar este grupo? Los productos perderán su destino de impresión.', async function () {
        try {
            const res = await fetch(`/grupos/${id}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': window.csrfToken,
                    'Accept': 'application/json'
                }
            });

            const data = await res.json();

            if (data.status === 'success') {
                window.mostrarNotificacion('Grupo eliminado correctamente', 'success');
                loadView('grupos');
            } else {
                window.mostrarNotificacion(data.message || 'No se pudo eliminar', 'error');
            }
        } catch (e) {
            window.mostrarNotificacion('Error al intentar eliminar', 'error');
        }
    });
};