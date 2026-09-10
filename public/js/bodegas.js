function respuestaBodega(response) {
    return response.json().then(function (data) {
        if (!response.ok) {
            throw new Error(data.message || data.errors?.descripcion?.[0] || 'No fue posible procesar la bodega');
        }
        return data;
    });
}

function notificarBodega(mensaje, tipo) {
    if (typeof window.mostrarNotificacion === 'function') {
        window.mostrarNotificacion(mensaje, tipo);
        return;
    }
    window.alert(mensaje);
}

function tokenBodega() {
    return document.querySelector('meta[name="csrf-token"]')?.content;
}

window.abrirNuevaBodega = function () {
    var form = document.getElementById('formBodega');
    if (!form) return;

    form.reset();
    form.dataset.bodegaId = '';
    document.getElementById('bodegaModalTitulo').textContent = 'Nueva Bodega';
    window.openModalBodega();
};

window.editarBodega = function (id) {
    fetch('/bodegas/' + id + '/edit', { headers: { Accept: 'application/json' } })
        .then(respuestaBodega)
        .then(function (bodega) {
            var form = document.getElementById('formBodega');
            form.dataset.bodegaId = bodega.id;
            form.descripcion.value = bodega.descripcion;
            document.getElementById('bodegaModalTitulo').textContent = 'Editar Bodega';
            window.openModalBodega();
        })
        .catch(function (error) {
            notificarBodega(error.message, 'error');
        });
};

window.eliminarBodega = function (id) {
    window.abrirConfirm('¿Deseas eliminar esta bodega? Solo es posible si no tiene inventario ni movimientos asociados.', function () {
        fetch('/bodegas/' + id, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': tokenBodega(), Accept: 'application/json' },
        })
            .then(respuestaBodega)
            .then(function (respuesta) {
                notificarBodega(respuesta.message, 'success');
                window.loadView('bodegas');
            })
            .catch(function (error) {
                notificarBodega(error.message, 'error');
            });
    });
};

document.addEventListener('submit', function (event) {
    if (event.target.id !== 'formBodega') return;
    event.preventDefault();

    var form = event.target;
    var id = form.dataset.bodegaId;
    var boton = form.querySelector('button[type="submit"]');

    boton.disabled = true;
    fetch(id ? '/bodegas/' + id : '/bodegas', {
        method: id ? 'PUT' : 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': tokenBodega(),
            Accept: 'application/json',
        },
        body: JSON.stringify({ descripcion: form.descripcion.value.trim() }),
    })
        .then(respuestaBodega)
        .then(function (respuesta) {
            notificarBodega(respuesta.message || 'Bodega guardada correctamente.', 'success');
            window.closeModalBodega();
            window.loadView('bodegas');
        })
        .catch(function (error) {
            notificarBodega(error.message, 'error');
        })
        .finally(function () {
            boton.disabled = false;
        });
});
