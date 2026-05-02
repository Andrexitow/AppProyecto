window.guardarProducto = function () {
    const form = document.getElementById('formProducto');
    const id = document.getElementById('producto_id').value;

    if (!form) return;

    const formData = new FormData(form);
    let url = '/productos';

    if (id) {
        url = `/productos/${id}`;
        formData.append('_method', 'PUT');
    }

    // --- VALIDACIONES FRONT ---
    const codigo = formData.get('codigo')?.trim();
    const descripcion = formData.get('descripcion')?.trim();
    const precio = formData.get('precio')?.trim();
    const grupo_menu_id = formData.get('grupo_menu_id'); // <--- NUEVO CAMPO
    const afecta = formData.get('afecta_inventario');

    let errores = [];

    if (!codigo) errores.push('El código es obligatorio.');
    if (!descripcion) errores.push('La descripción es obligatoria.');
    if (!precio || Number(precio) < 0) errores.push('Ingrese un precio válido.');
    if (!grupo_menu_id) errores.push('Seleccione un Grupo de Menú (Destino).'); // <--- VALIDACIÓN
    if (afecta === null || afecta === '') errores.push('Seleccione si afecta inventario.');

    if (errores.length > 0) {
        mostrarNotificacion(errores.join('\n'), 'error');
        return;
    }

    fetch(url, {
        method: 'POST',
        body: formData,
        headers: {
            // Asegúrate de que el input _token exista en tu HTML o usa el meta tag
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(async res => {
            const data = await res.json();

            if (res.status === 422) {
                let mensajes = [];
                if (data.errors) {
                    for (let campo in data.errors) {
                        mensajes.push(data.errors[campo][0]);
                    }
                } else {
                    mensajes.push(data.message || 'Error de validación.');
                }
                mostrarNotificacion(mensajes.join('\n'), 'error');
                throw new Error('Validación fallida');
            }

            if (!res.ok) {
                mostrarNotificacion(data.message || 'Error al guardar producto.', 'error');
                throw new Error(data.message);
            }
            return data;
        })
        .then(data => {
            mostrarNotificacion(data.message || 'Producto guardado', 'success');
            form.reset();
            document.getElementById('producto_id').value = '';
            closeModalProducto();
            loadView('productos');
        })
        .catch(err => console.error('Error guardarProducto:', err));
};

window.editarProducto = function (id) {
    fetch(`/productos/${id}/edit`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('producto_id').value = data.id;
            document.querySelector('[name="codigo"]').value = data.codigo;
            document.querySelector('[name="categoria"]').value = data.categoria;
            document.querySelector('[name="descripcion"]').value = data.descripcion;
            document.querySelector('[name="und_detal"]').value = data.und_detal;
            document.querySelector('[name="precio"]').value = data.precio;
            document.querySelector('[name="caracteristicas"]').value = data.caracteristicas ?? '';

            // ASIGNAR EL GRUPO DE MENU
            const selectGrupo = document.querySelector('[name="grupo_menu_id"]');
            if (selectGrupo) {
                selectGrupo.value = data.grupo_menu_id ?? '';
            }

            openModalProducto();
        })
        .catch(error => {
            console.error(error);
            mostrarNotificacion('Error cargando producto', 'error');
        });
};

window.cambiarEstadoProducto = function (id) {

    abrirConfirm(
        '¿Deseas cambiar el estado de este producto?',
        function () {

            fetch(`/productos/${id}/estado`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams({
                    _method: 'PUT'
                })
            })
                .then(async res => {

                    if (!res.ok) throw new Error(await res.text());

                    return res.json();
                })
                .then(data => {

                    mostrarNotificacion(data.message, 'success');

                    loadView('productos');

                })
                .catch(error => {
                    console.error(error);
                    mostrarNotificacion('Error al cambiar estado', 'error');
                });

        }
    );

};

window.eliminarrProducto = function (id) {

    abrirConfirm('¿Deseas eliminar este producto?', function () {

        fetch(`/productos/${id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: new URLSearchParams({
                _method: 'DELETE'
            })
        })
            .then(async res => {

                const data = await res.json();

                if (!res.ok) {
                    mostrarNotificacion(data.message, 'error');
                    throw new Error(data.message);
                }

                return data;
            })
            .then(data => {

                mostrarNotificacion(data.message, 'success');
                loadView('productos');

            })
            .catch(error => console.error(error));

    });

};

window.buscarProducto = debounce(function () {
    const query = document.getElementById('buscarProducto')?.value.trim();
    const contenedor = document.getElementById('resultadosProducto');

    if (!query || query.length < 2 || !contenedor) {
        if (contenedor) contenedor.classList.add('hidden');
        return;
    }

    fetch(`/productos/buscar?query=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            // --- MEJORA: Construir el HTML en una variable para rendimiento ---
            let html = '';

            if (data.length === 0) {
                html = '<div class="p-3 text-gray-400 text-sm">No se encontraron productos</div>';
            } else {
                data.forEach(p => {
                    let descripcion = (p.descripcion || '').replace(/'/g, "\\'");
                    let codigo = p.codigo ?? '-';
                    html += `
                        <div onclick="agregarProducto(${p.id}, '${descripcion}', ${p.precio})"
                            class="flex gap-3 p-3 hover:bg-blue-50 cursor-pointer border-b text-sm transition-colors">
                            <div class="text-blue-600 font-mono w-24">${codigo}</div>
                            <div class="flex-1 font-medium text-gray-700">${p.descripcion}</div>
                        </div>`;
                });
            }

            contenedor.innerHTML = html;
            contenedor.classList.remove('hidden');
        });
}, 300);

window.agregarProducto = function (id, descripcion, precio) {
    const tabla = document.getElementById('tablaProductos');

    if (!tabla || document.getElementById('prod_' + id)) return;

    tabla.innerHTML += `
        <tr id="prod_${id}" class="hover:bg-gray-50">
            <td class="p-4">${descripcion}</td>
            <td class="p-4">
                <input type="number" value="1" class="cantidad w-20 mx-auto block border rounded text-center">
            </td>
            <td class="p-4">
                <input type="number" value="${precio}" class="precio w-24 border rounded text-center">
            </td>
            <td class="p-4">
                <select class="tipo border rounded">
                    <option value="entrada">+ Entrada</option>
                    <option value="salida">- Salida</option>
                </select>
            </td>
            <td class="p-4 text-center">
                <button onclick="eliminarProducto(${id})" class="text-red-500">Eliminar</button>
            </td>
        </tr>
    `;

    const input = document.getElementById('buscarProducto');
    const resultados = document.getElementById('resultadosProducto');

    if (input) input.value = '';
    if (resultados) resultados.classList.add('hidden');
};

window.eliminarProducto = function (id) {
    document.getElementById('prod_' + id)?.remove();
};

window.filtrarProducto = debounce(function () {

    const texto = document.getElementById('buscarTablaProducto')?.value.trim();
    const tabla = document.getElementById('tablaProductos');

    if (!tabla) return;

    fetch(`/productos/buscar-admin?texto=${encodeURIComponent(texto)}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(res => res.text())
        .then(html => {
            tabla.innerHTML = html;
        })
        .catch(error => {
            console.error('Error filtrando productos:', error);
            mostrarNotificacion('Error al buscar productos', 'error');
        });

}, 300);