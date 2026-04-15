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
    document.getElementById('inputDocumento').value = documento;
    document.getElementById('inputNombre').value = nombre;
    document.getElementById('tercero_id').value = id;
    document.getElementById('resultadosTercero')?.classList.add('hidden');
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