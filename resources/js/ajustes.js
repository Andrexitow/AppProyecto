window.obtenerSiguienteNumero = function () {

    let prefijo = document.getElementById('prefijo').value;

    fetch(`/ajustes/siguiente-numero?prefijo=${prefijo}`)
        .then(res => res.json())
        .then(data => {
            let numeroFormateado = String(data.numero).padStart(4, '0');
            document.getElementById('numero').value = numeroFormateado;
        });

};

document.addEventListener('change', function (e) {
    if (e.target.id === 'prefijo') {
        obtenerSiguienteNumero();
    }
});

window.agregarFilaTabla = function (a) {
    const tbody = document.querySelector('tbody');

    const fila = `
        <tr class="hover:bg-gray-50">
            <td class="px-6 py-4">${a.prefijo}</td>
            <td class="px-6 py-4">${a.numero}</td>
            <td class="px-6 py-4">${a.observaciones ?? ''}</td>
            <td class="px-6 py-4 text-right font-medium">$0</td>
            <td class="px-6 py-4 text-center">
                <span class="text-red-500">✖</span>
            </td>
            <td class="px-6 py-4">Tú</td>
            <td class="px-6 py-4">${a.fecha}</td>
            <td class="px-6 py-4 text-center">
                <button onclick="retomarAjuste(${a.id})"
                    class="text-blue-600 hover:underline text-sm">
                    Completar
                </button>
            </td>
        </tr>
    `;

    tbody.insertAdjacentHTML('afterbegin', fila);
}

window.guardarCabecera = function () {

    const tercero_id = document.getElementById('tercero_id')?.value;
    const bodega_id = document.getElementById('bodega_id')?.value;
    const fecha = document.getElementById('fecha')?.value;
    const prefijo = document.getElementById('prefijo')?.value;
    const numero = document.getElementById('numero')?.value;
    const contraparte = document.getElementById('contraparte')?.value || null;
    const observaciones = document.getElementById('observaciones')?.value || null;

    if (!tercero_id) {
        mostrarNotificacion('Debes seleccionar un tercero', 'error');
        return;
    }

    if (!bodega_id) {
        mostrarNotificacion('Debes seleccionar una bodega', 'error');
        return;
    }

    if (!fecha) {
        mostrarNotificacion('Debes ingresar una fecha', 'error');
        return;
    }

    if (!prefijo || !numero) {
        mostrarNotificacion('Error con el documento', 'error');
        return;
    }

    const data = {
        prefijo,
        numero,
        fecha,
        tercero_id,
        bodega_id,
        contraparte,
        observaciones
    };

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    if (window.ajusteActivoId) {
        fetch(`/ajustes/${window.ajusteActivoId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(data)
        })
            .then(async res => {
                const response = await res.json();
                if (!res.ok) throw new Error(response.error || 'Error al actualizar el ajuste');
                return response;
            })
            .then(() => {
                mostrarNotificacion('Ajuste actualizado correctamente', 'success');
                closeModalAjuste();
                loadView('ajustes');
            })
            .catch(err => {
                console.error(err);
                alert(err.message);
            });

    } else {
        // === CREAR NUEVO AJUSTE ===
        fetch('/ajustes', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken
            },
            body: JSON.stringify(data)
        })
            .then(async res => {
                const response = await res.json();
                if (!res.ok) throw new Error(response.error || 'Error al guardar el ajuste');
                return response;
            })
            .then(ajuste => {
                window.ajusteActivoId = ajuste.id;   // por si lo necesitas después
                if (typeof agregarFilaTabla === 'function') {
                    agregarFilaTabla(ajuste);
                }
                mostrarNotificacion('Ajuste guardado correctamente', 'success');
                closeModalAjuste();
                loadView('ajustes');
            })
            .catch(err => {
                console.error(err);
                alert(err.message);
            });
    }
};


window.retomarAjuste = function (id) {

    fetch(`/ajustes/${id}`, {
        credentials: 'same-origin'
    })
        .then(res => res.json())
        .then(a => {

            // 🔥 TODO VA AQUÍ ADENTRO
            window.ajusteActivoId = a.id;

            openModalAjuste();

            document.getElementById('prefijo').value = a.prefijo;
            document.getElementById('numero').value = String(a.numero).padStart(4, '0');
            document.getElementById('fecha').value = a.fecha;
            document.getElementById('bodega_id').value = a.bodega_id;

            document.getElementById('tercero_id').value = a.tercero_id;

            document.getElementById('inputNombre').value = a.tercero?.nombre_completo || '';
            document.getElementById('inputDocumento').value = a.tercero?.cedula || a.tercero?.nit || '';

            document.getElementById('observaciones').value = a.observaciones ?? '';
            document.getElementById('contraparte').value = a.contraparte ?? '';

            document.getElementById('paso1').classList.add('hidden');
            document.getElementById('paso2').classList.remove('hidden');
        })
        .catch(err => {
            console.error(err);
            alert('Error cargando ajuste');
        });
};

// PASO 2 — Guarda detalles y registra

window.guardarAjuste = function () {
    const filas = document.querySelectorAll('#tablaProductos tr');

    if (filas.length === 0) return alert('Debes agregar al menos un producto');
    if (!window.ajusteActivoId) return alert('Error: no hay ajuste activo');

    let detalles = [];

    filas.forEach(fila => {
        detalles.push({
            producto_id: fila.id.replace('prod_', ''),
            cantidad: parseFloat(fila.querySelector('.cantidad').value) || 0,
            precio: parseFloat(fila.querySelector('.precio').value) || 0,
            tipo: fila.querySelector('.tipo').value,
        });
    });
    fetch(`/ajustes/${window.ajusteActivoId}/detalles`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ detalles })
    })
        .then(async res => {
            const response = await res.json();

            if (!res.ok) {
                const msg = response.error?.detalle || response.error?.mensaje || response.error || 'Error al registrar';
                throw new Error(msg);
            }

            return response;
        })
        .then(() => {
            alert('Ajuste registrado correctamente');
            window.ajusteActivoId = null;
            closeModalAjuste();
            loadView('ajustes');
        })
        .catch(err => {
            console.error(err);
            mostrarNotificacion(err.message, 'error');
        });


}
window.verAjuste = function (id) {

    fetch(`/ajustes/${id}`, {
        credentials: 'same-origin'
    })
        .then(res => res.json())
        .then(a => {

            openModalVerAjuste();

            // ================= CABECERA =================
            document.getElementById('ver_doc').innerText =
                a.prefijo + '-' + String(a.numero).padStart(4, '0');

            document.getElementById('ver_fecha').innerText = a.fecha;

            document.getElementById('ver_tercero').innerText =
                a.tercero?.nombre_completo || '';

            document.getElementById('ver_bodega').innerText =
                a.bodega?.descripcion || 'Sin bodega';

            document.getElementById('ver_obs').innerText =
                a.observaciones ?? '';

            document.getElementById('ver_total').innerText =
                '$' + Number(a.total).toLocaleString();

            // ================= DETALLES (🔥 LO QUE TE FALTABA) =================
            const tbody = document.getElementById('ver_detalles');
            tbody.innerHTML = '';

            if (!a.detalles || a.detalles.length === 0) {
                tbody.innerHTML = `
                <tr>
                    <td colspan="2" class="text-center p-3 text-gray-400">
                        Sin productos
                    </td>
                </tr>
            `;
                return;
            }

            a.detalles.forEach(d => {
                tbody.innerHTML += `
                <tr class="border-t">
                    <td class="p-2">
                        ${d.producto?.descripcion ?? 'Sin nombre'}
                    </td>
                    <td class="p-2 text-center">
                        ${d.cantidad}
                    </td>
                </tr>
            `;
            });

        })
        .catch(err => {
            console.error(err);
            mostrarNotificacion(err.message, 'error');
        });
};



window.editarAjuste = function (id) {

    fetch(`/ajustes/${id}`, {
        credentials: 'same-origin'
    })
        .then(res => res.json())
        .then(a => {

            window.ajusteActivoId = a.id;

            openModalAjuste();

            document.querySelector('#modalAjuste h2').innerText = 'Editar Ajuste';

            document.getElementById('prefijo').value = a.prefijo;
            document.getElementById('numero').value = String(a.numero).padStart(4, '0');
            document.getElementById('fecha').value = a.fecha;
            document.getElementById('bodega_id').value = a.bodega_id;

            document.getElementById('tercero_id').value = a.tercero_id;
            document.getElementById('inputNombre').value = a.tercero?.nombre_completo || '';
            document.getElementById('inputDocumento').value = a.tercero?.cedula || a.tercero?.nit || '';

            document.getElementById('observaciones').value = a.observaciones ?? '';
            document.getElementById('contraparte').value = a.contraparte ?? '';

            document.getElementById('paso1').classList.remove('hidden');
            document.getElementById('paso2').classList.add('hidden');

        })
        .catch(err => {
            console.error(err);
            mostrarNotificacion(err.message, 'error');
        });
};

window.eliminarAjuste = function (id) {

    abrirConfirm('¿Seguro que deseas eliminar este ajuste?', () => {

        fetch(`/ajustes/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
            .then(async res => {
                const response = await res.json();
                if (!res.ok) throw new Error(response.error || 'Error al eliminar');
                return response;
            })
            .then(() => {

                const btn = document.querySelector(`button[onclick="eliminarAjuste(${id})"]`);
                const fila = btn.closest('tr');
                fila.remove();

                mostrarNotificacion('Ajuste eliminado correctamente', 'success');

            })
            .catch(err => {
                console.error(err);
                mostrarNotificacion(err.message, 'error');
            });

    });

};

let accionConfirmada = null;

window.abrirConfirm = function (mensaje, callback) {

    const modal = document.getElementById('modalConfirm');

    if (!modal) return console.error('NO EXISTE modalConfirm');

    document.getElementById('confirmMensaje').innerText = mensaje;

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    accionConfirmada = callback;
};

window.cerrarConfirm = function () {
    const modal = document.getElementById('modalConfirm');

    modal.classList.add('hidden');
    modal.classList.remove('flex'); // 👈 importante

    accionConfirmada = null;
};

// botón confirmar (versión PRO)
document.addEventListener('click', function (e) {
    if (e.target.closest('#btnConfirmarAccion')) {
        if (accionConfirmada) accionConfirmada();
        cerrarConfirm();
    }
});

window.revertirAjuste = function (id) {

    abrirConfirm('¿Seguro que deseas revertir este ajuste?', () => {

        fetch(`/ajustes/${id}/revertir`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
            .then(async res => {
                const response = await res.json();
                if (!res.ok) throw new Error(response.error || 'Error al revertir');
                return response;
            })
            .then(() => {

                mostrarNotificacion('Ajuste revertido correctamente', 'success');

                // 🔥 recargar vista
                loadView('ajustes');

            })
            .catch(err => {
                console.error(err);
                mostrarNotificacion(err.message, 'error');
            });

    });
};