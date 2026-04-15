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

// PASO 1 — Guarda o actualiza cabecera y avanza
window.irPaso2 = function () {

    const tercero_id = document.getElementById('tercero_id')?.value;
    const bodega_id = document.getElementById('bodega_id')?.value;
    const fecha = document.getElementById('fecha')?.value;
    const prefijo = document.getElementById('prefijo')?.value;
    const numero = document.getElementById('numero')?.value;
    const contraparte = document.getElementById('contraparte')?.value;
    const observaciones = document.getElementById('observaciones')?.value;

    if (!tercero_id) return alert('Debes seleccionar un tercero');
    if (!bodega_id) return alert('Debes seleccionar una bodega');
    if (!fecha) return alert('Debes ingresar una fecha');

    const data = {
        prefijo,
        numero,
        fecha,
        tercero_id,
        bodega_id,
        contraparte,
        observaciones
    };

    if (window.ajusteActivoId) {

        fetch(`/ajustes/${window.ajusteActivoId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
            .then(async res => {
                const response = await res.json();
                if (!res.ok) throw new Error(response.error || 'Error al actualizar');
                return response;
            })
            .then(() => {
                pasarPaso2();
            })
            .catch(err => {
                console.error(err);
                alert(err.message);
            });

    } else {

        // 🆕 CREAR NUEVO
        fetch('/ajustes', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        })
            .then(async res => {
                const response = await res.json();
                if (!res.ok) throw new Error(response.error || 'Error al guardar');
                return response;
            })
            .then(ajuste => {

                window.ajusteActivoId = ajuste.id;

                // 🔥 solo si quieres verlo sin recargar
                if (typeof agregarFilaTabla === 'function') {
                    agregarFilaTabla(ajuste);
                }

                pasarPaso2();
            })
            .catch(err => {
                console.error(err);
                alert(err.message);
            });
    }
};


// 🔁 cambio de vista (reutilizable)
function pasarPaso2() {
    document.getElementById('paso1').classList.add('hidden');
    document.getElementById('paso2').classList.remove('hidden');
}

window.retomarAjuste = function (id) {

    fetch(`/ajustes/${id}`, {
        credentials: 'same-origin'
    })
        .then(res => res.json())
        .then(a => {

            console.log('AJUSTE:', a); // 🔍 debug

            // 🔥 TODO VA AQUÍ ADENTRO
            window.ajusteActivoId = a.id;

            openModalAjuste();

            document.getElementById('prefijo').value = a.prefijo;
            document.getElementById('numero').value = String(a.numero).padStart(4, '0');
            document.getElementById('fecha').value = a.fecha;
            document.getElementById('bodega_id').value = a.bodega_id;

            document.getElementById('tercero_id').value = a.tercero_id;

            // 🔥 IMPORTANTE: usa nombre_completo
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
            if (!res.ok) throw new Error(response.error || 'Error al registrar');
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
            alert(err.message);
        });

    window.verAjuste = function (id) {

        fetch(`/ajustes/${id}`, {
            credentials: 'same-origin'
        })
            .then(res => res.json())
            .then(a => {

                console.log('VER AJUSTE:', a);

                openModalVerAjuste();

                // CABECERA
                document.getElementById('ver_doc').innerText = a.prefijo + '-' + String(a.numero).padStart(4, '0');
                document.getElementById('ver_fecha').innerText = a.fecha;
                document.getElementById('ver_tercero').innerText = a.tercero?.nombre_completo || '';
                document.getElementById('ver_obs').innerText = a.observaciones ?? '';
                document.getElementById('ver_total').innerText = '$' + Number(a.total).toLocaleString();

                // 🔥 SI LUEGO TRAES DETALLES:
                // renderDetalles(a.detalles);

            })
            .catch(err => {
                console.error(err);
                alert('Error cargando ajuste');
            });
    };
};