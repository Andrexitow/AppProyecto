window.buscarTerceroPorDocumento = function () {
    const inputDoc = document.getElementById('inputDocumento');
    const inputNombre = document.getElementById('inputNombre');
    const hiddenId = document.getElementById('tercero_id');

    if (!inputDoc) {
        console.warn('inputDocumento no encontrado');
        return;
    }

    const doc = inputDoc.value.trim();

    if (doc.length === 0) {
        if (inputNombre) inputNombre.value = '';
        if (hiddenId) hiddenId.value = '';
        return;
    }

    if (doc.length < 5) return;

    fetch(`/terceros/buscar-doc?doc=${encodeURIComponent(doc)}`)
        .then(res => {
            if (!res.ok) throw new Error('Error HTTP ' + res.status);
            return res.json();
        })
        .then(data => {
            if (data && data.id) {
                const nombreCompleto = data.razon_social
                    ? data.razon_social
                    : `${data.nombre || ''} ${data.apellido || ''}`.trim();

                if (inputNombre) inputNombre.value = nombreCompleto;
                if (hiddenId) hiddenId.value = data.id;
            } else {
                if (inputNombre) inputNombre.value = 'Tercero no encontrado';
                if (hiddenId) hiddenId.value = '';
            }
        })
        .catch(err => {
            console.error('Error buscando tercero:', err);
            if (inputNombre) inputNombre.value = 'Error al buscar';
            if (hiddenId) hiddenId.value = '';
        });
};

window.buscarTercero = debounce(function () {
    const query = document.getElementById('buscarTercero')?.value.trim();
    const contenedor = document.getElementById('resultadosTercero');

    if (!query || query.length < 2 || !contenedor) {
        contenedor?.classList.add('hidden');
        return;
    }

    fetch(`/terceros/buscar?query=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            contenedor.innerHTML = `
                <div class="grid grid-cols-2 font-semibold text-xs bg-gray-100 p-3 sticky top-0">
                    <div>Identificación</div>
                    <div>Nombre</div>
                </div>
            `;

            if (data.length === 0) {
                contenedor.innerHTML += `<div class="p-4 text-gray-500 text-center">No se encontraron resultados</div>`;
            } else {
                data.forEach(t => {
                    const identificacion = t.cedula ?? t.nit ?? '-';
                    const nombre = t.nombre_completo || `${t.nombre || ''} ${t.apellido || ''}`.trim();

                    contenedor.innerHTML += `
                        <div onclick="seleccionarTercero(${t.id}, '${identificacion}', '${nombre.replace(/'/g, "\\'")}')"
                            class="grid grid-cols-2 p-3 hover:bg-gray-100 cursor-pointer border-t text-sm">
                            <div class="font-medium">${identificacion}</div>
                            <div>${nombre}</div>
                        </div>
                    `;
                });
            }

            contenedor.classList.remove('hidden');
        })
        .catch(err => console.error('Error en buscarTercero:', err));
}, 300);

window.seleccionarTercero = function (id, documento, nombre) {

    const inputDoc = document.getElementById('inputDocumento');
    const inputNombre = document.getElementById('inputNombre');
    const hiddenId = document.getElementById('tercero_id');
    const resultados = document.getElementById('resultadosTercero');

    // 🔥 Validación clave
    if (!inputDoc || !inputNombre || !hiddenId) {
        console.warn('No estás en una vista con inputs de tercero');
        return;
    }

    inputDoc.value = documento;
    inputNombre.value = nombre;
    hiddenId.value = id;

    resultados?.classList.add('hidden');
};

window.abrirModalTerceros = function () {
    const modal = document.getElementById('modalTerceros');
    if (modal) {
        modal.classList.remove('hidden');
        buscarTerceroModal('');
    }
};

window.cerrarModalTerceros = function () {
    document.getElementById('modalTerceros')?.classList.add('hidden');
};

window.buscarTerceroModal = debounce(function (query) {
    const lista = document.getElementById('listaTerceros');
    if (!lista) return;

    fetch(`/terceros/buscar?query=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            lista.innerHTML = `
                <div class="grid grid-cols-2 font-semibold text-sm bg-gray-100 p-3 sticky top-0">
                    <div>Identificación</div>
                    <div>Nombre</div>
                </div>
            `;

            data.forEach(t => {
                const identificacion = t.cedula ?? t.nit ?? '-';
                const nombre = t.nombre_completo || `${t.nombre || ''} ${t.apellido || ''}`.trim();

                lista.innerHTML += `
                    <div onclick="seleccionarDesdeModal(${t.id}, '${identificacion}', '${nombre.replace(/'/g, "\\'")}')"
                        class="grid grid-cols-2 p-3 hover:bg-gray-100 cursor-pointer border-t">
                        <div>${identificacion}</div>
                        <div>${nombre}</div>
                    </div>
                `;
            });
        })
        .catch(err => console.error(err));
}, 300);

window.seleccionarDesdeModal = function (id, documento, nombre) {
    seleccionarTercero(id, documento, nombre);
    cerrarModalTerceros();
};

window.toggleTipoTercero = function (tipo) {
    const camposPersona = document.querySelectorAll('.campo-persona');
    const camposEmpresa = document.querySelectorAll('.campo-empresa');

    if (tipo === 'persona') {
        camposPersona.forEach(el => el.classList.remove('hidden'));
        camposEmpresa.forEach(el => el.classList.add('hidden'));
    } else {
        camposPersona.forEach(el => el.classList.add('hidden'));
        camposEmpresa.forEach(el => el.classList.remove('hidden'));
    }
}

window.openModalNuevoTercero = function () {
    document.getElementById('modalNuevoTercero').classList.remove('hidden');
    document.getElementById('modalNuevoTercero').classList.add('flex');
}

window.closeModalNuevoTercero = function () {
    document.getElementById('modalNuevoTercero').classList.add('hidden');
    document.getElementById('modalNuevoTercero').classList.remove('flex');
    document.getElementById('formTercero').reset();
    toggleTipoTercero('persona'); // Reset a persona por defecto
}

window.guardarTercero = function () {

    const form = document.getElementById('formTercero');
    if (!form) return;

    const btn = form.querySelector('button');
    const formData = new FormData(form);

    // 🔥 evitar doble click
    btn.disabled = true;

    fetch('/terceros', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(async res => {

            // 🔥 VALIDACIONES LARAVEL
            if (res.status === 422) {
                const data = await res.json();

                let mensajes = [];

                for (let campo in data.errors) {
                    mensajes.push(data.errors[campo][0]);
                }

                mostrarNotificacion(mensajes.join('<br>'), 'error');
                throw new Error('Validación fallida');
            }

            // 🔥 OTROS ERRORES (500, etc)
            if (!res.ok) {
                let errorText = await res.text();
                mostrarNotificacion('Error del servidor', 'error');
                throw new Error(errorText);
            }

            return res.json();
        })
        .then(data => {

            const nombreCompleto = data.data.razon_social
                ? data.data.razon_social
                : `${data.data.nombre || ''} ${data.data.apellido || ''}`.trim();

            const documento = data.data.cedula || data.data.nit;

            // 🔥 puede fallar si no estás en esa vista (ya lo corregimos antes)
            if (typeof seleccionarTercero === 'function') {
                seleccionarTercero(data.data.id, documento, nombreCompleto);
            }

            closeModalNuevoTercero();

            mostrarNotificacion(data.message || 'Guardado correctamente', 'success');

            form.reset();

        })
        .catch(error => {
            if (error.message !== 'Validación fallida') {
                console.error('Error inesperado:', error);
            }
        })
        .finally(() => {
        // 🔥 volver a habilitar botón SIEMPRE
        btn.disabled = false;
    });
};