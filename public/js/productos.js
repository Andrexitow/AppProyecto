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
    const integracion_contable_id = formData.get('integracion_contable_id');
    const afecta = formData.get('afecta_inventario');

    let errores = [];

    if (!codigo) errores.push('El código es obligatorio.');
    if (!descripcion) errores.push('La descripción es obligatoria.');
    if (!precio || Number(precio) < 0) errores.push('Ingrese un precio válido.');
    if (!grupo_menu_id) errores.push('Seleccione un Grupo de Menú (Destino).'); // <--- VALIDACIÓN
    if (!integracion_contable_id) errores.push('Seleccione la Integración Contable (pestaña Impuestos y Precios) — sin esto no se podrá vender el producto.');
    if (afecta === null || afecta === '') errores.push('Seleccione si afecta inventario.');

    if (document.getElementById('prod_es_ensamblado')?.checked) {
        if (!formData.get('producto_base_id')) errores.push('Seleccione el producto base (insumo) del ensamble.');
        const factor = formData.get('factor_consumo');
        if (!factor || Number(factor) <= 0) errores.push('Indique cuántas unidades del insumo consume una unidad vendida.');
        if (document.getElementById('prod_usar_bodega_origen')?.checked && !document.getElementById('prod_bodega_origen_id')?.value) {
            errores.push('Seleccione la bodega de origen del insumo, o desmarque la casilla.');
        }
    }

    if (document.getElementById('prod_tiene_acompanamiento')?.checked && !formData.get('acompanamiento_grupo_id')) {
        errores.push('Seleccione el grupo de acompañamiento.');
    }

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
            document.getElementById('prod_base_seleccionado').textContent = '';
            toggleEnsambleProducto();
            toggleAcompanamientoProducto();
            switchProductoTab('info');
            closeModalProducto();
            loadView('productos');
        })
        .catch(err => console.error('Error guardarProducto:', err));
};

// ══════════════════════════
// PRODUCTO ENSAMBLADO (tab "Ensamble")
// ══════════════════════════
window.toggleEnsambleProducto = function () {
    const marcado = document.getElementById('prod_es_ensamblado')?.checked;
    const campos = document.getElementById('prod_ensamble_campos');
    if (!campos) return;

    if (marcado) {
        campos.classList.remove('hidden');
        campos.style.display = 'contents';
        // No tiene sentido ser ensamblado Y tener acompañamiento a la vez.
        const acomp = document.getElementById('prod_tiene_acompanamiento');
        if (acomp && acomp.checked) { acomp.checked = false; toggleAcompanamientoProducto(); }
    } else {
        campos.classList.add('hidden');
        campos.style.display = 'none';
        // Si se desmarca, no debe quedar un insumo/factor "fantasma" guardado.
        document.getElementById('prod_base_id').value = '';
        document.getElementById('buscarProductoBase').value = '';
        document.getElementById('prod_base_seleccionado').textContent = '';
        document.getElementById('prod_factor_consumo').value = '';
        document.getElementById('prod_usar_bodega_origen').checked = false;
        toggleBodegaOrigenProducto();
    }
};

// Bodega de origen del insumo (ej. la carne vive en Cocina, no en la
// bodega de la caja que vende la hamburguesa) — ver
// FacturacionController::resolverDescuentosInventario().
window.toggleBodegaOrigenProducto = function () {
    const marcado = document.getElementById('prod_usar_bodega_origen')?.checked;
    const campo = document.getElementById('prod_bodega_origen_campo');
    const select = document.getElementById('prod_bodega_origen_id');
    if (!campo || !select) return;

    if (marcado) {
        campo.classList.remove('hidden');
        select.disabled = false;
    } else {
        campo.classList.add('hidden');
        select.disabled = true; // disabled: FormData no lo envía, no queda un valor fantasma.
        select.value = '';
    }
};

// ══════════════════════════
// ACOMPAÑAMIENTO (mezcla a elegir, tab "Ensamble")
// ══════════════════════════
window.toggleAcompanamientoProducto = function () {
    const marcado = document.getElementById('prod_tiene_acompanamiento')?.checked;
    const campos = document.getElementById('prod_acompanamiento_campos');
    if (!campos) return;

    if (marcado) {
        campos.classList.remove('hidden');
        // No tiene sentido tener acompañamiento Y ser ensamblado a la vez.
        const ensamble = document.getElementById('prod_es_ensamblado');
        if (ensamble && ensamble.checked) { ensamble.checked = false; toggleEnsambleProducto(); }
    } else {
        campos.classList.add('hidden');
        document.getElementById('prod_acompanamiento_grupo_id').value = '';
    }
};

window.ejecutarBusquedaProductoBase = debounce(function () {
    const query = document.getElementById('buscarProductoBase')?.value.trim();
    const contenedor = document.getElementById('resultadosProductoBase');
    const idActual = document.getElementById('producto_id')?.value;

    if (!query || query.length < 2 || !contenedor) {
        if (contenedor) contenedor.classList.add('hidden');
        return;
    }

    fetch(`/productos/buscar?query=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            // Un producto no puede ser su propio insumo base.
            const opciones = data.filter(p => String(p.id) !== String(idActual));
            let html = '';

            if (opciones.length === 0) {
                html = '<div class="p-3 text-gray-400 text-sm">No se encontraron productos</div>';
            } else {
                opciones.forEach(p => {
                    const descripcion = (p.descripcion || '').replace(/'/g, "\\'");
                    const codigo = p.codigo ?? '-';
                    html += `
                        <div onclick="seleccionarProductoBase(${p.id}, '${descripcion}')"
                            style="display:flex;gap:10px;padding:9px 12px;cursor:pointer;border-bottom:1px solid #F3F4F6;font-size:12.5px;"
                            onmouseover="this.style.background='#F5F3FF'" onmouseout="this.style.background='transparent'">
                            <span style="font-family:'IBM Plex Mono',monospace;color:#7C3AED;width:80px;flex-shrink:0;">${codigo}</span>
                            <span style="color:#374151;">${p.descripcion}</span>
                        </div>`;
                });
            }

            contenedor.innerHTML = html;
            contenedor.classList.remove('hidden');
        });
}, 300);

window.seleccionarProductoBase = function (id, descripcion) {
    document.getElementById('prod_base_id').value = id;
    document.getElementById('buscarProductoBase').value = '';
    document.getElementById('prod_base_seleccionado').textContent = '✓ Insumo seleccionado: ' + descripcion;
    document.getElementById('resultadosProductoBase').classList.add('hidden');
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
            document.querySelector('[name="iva_ventas"]').value = data.iva_ventas ?? '';
            document.querySelector('[name="integracion_contable_id"]').value = data.integracion_contable_id ?? '';
            // Sin esto el <select> se quedaba con lo que haya seleccionado
            // por defecto (siempre "Sí", la primera <option>) en vez de lo
            // que el producto tiene guardado de verdad.
            document.querySelector('[name="afecta_inventario"]').value = data.afecta_inventario ? '1' : '0';

            // ASIGNAR EL GRUPO DE MENU
            const selectGrupo = document.querySelector('[name="grupo_menu_id"]');
            if (selectGrupo) {
                selectGrupo.value = data.grupo_menu_id ?? '';
            }

            // ENSAMBLE
            document.getElementById('prod_es_ensamblado').checked = !!data.es_ensamblado;
            document.getElementById('prod_base_id').value = data.producto_base_id ?? '';
            document.getElementById('prod_factor_consumo').value = data.factor_consumo ?? '';
            document.getElementById('buscarProductoBase').value = '';
            document.getElementById('prod_base_seleccionado').textContent = data.producto_base
                ? '✓ Insumo seleccionado: ' + data.producto_base.descripcion
                : '';
            document.getElementById('prod_usar_bodega_origen').checked = !!data.bodega_origen_id;
            document.getElementById('prod_bodega_origen_id').value = data.bodega_origen_id ?? '';
            toggleEnsambleProducto();
            toggleBodegaOrigenProducto();

            // ACOMPAÑAMIENTO
            document.getElementById('prod_tiene_acompanamiento').checked = !!data.acompanamiento_grupo_id;
            document.getElementById('prod_acompanamiento_grupo_id').value = data.acompanamiento_grupo_id ?? '';
            toggleAcompanamientoProducto();

            switchProductoTab('info');
            openModalProducto();
        })
        .catch(error => {
            console.error(error);
            mostrarNotificacion(error.message || 'Error cargando producto', 'error');
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

                    const data = await res.json().catch(() => ({}));

                    if (!res.ok) throw new Error(data.message || `Error al cambiar estado (${res.status})`);

                    return data;
                })
                .then(data => {

                    mostrarNotificacion(data.message, 'success');

                    loadView('productos');

                })
                .catch(error => {
                    console.error(error);
                    mostrarNotificacion(error.message || 'Error al cambiar estado', 'error');
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

// Página actual de la tabla de productos. Cambia un filtro -> vuelve a la 1.
let paginaProducto = 1;

window.filtrarProducto = debounce(function () {
    paginaProducto = 1;
    cargarTablaProductos();
}, 300);

window.irAPaginaProducto = function (pagina) {
    if (pagina < 1) return;
    paginaProducto = pagina;
    cargarTablaProductos();
};

function cargarTablaProductos() {
    const texto = document.getElementById('buscarTablaProducto')?.value.trim() || '';
    const estado = document.getElementById('filtroEstadoProducto')?.value ?? '';
    const tabla = document.getElementById('tablaProductos');

    if (!tabla) return;

    const params = new URLSearchParams({ texto, estado, page: paginaProducto });

    fetch(`/productos/buscar-admin?${params.toString()}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(async res => {
            if (!res.ok) {
                const data = await res.json().catch(() => ({}));
                throw new Error(data.message || `Error al buscar productos (${res.status})`);
            }
            return res.text();
        })
        .then(html => {
            tabla.innerHTML = html;
        })
        .catch(error => {
            console.error('Error filtrando productos:', error);
            mostrarNotificacion(error.message || 'Error al buscar productos', 'error');
        });
}

window.switchProductoTab = function (tab) {
    document.querySelectorAll('.producto-tab-panel').forEach(panel => panel.classList.add('hidden'));
    document.getElementById('tab-' + tab).classList.remove('hidden');

    document.querySelectorAll('.producto-tab').forEach(btn => {
        btn.classList.remove('border-blue-600', 'text-blue-600');
        btn.classList.add('border-transparent', 'text-gray-500');
    });
    const activeBtn = document.querySelector(`.producto-tab[data-tab="${tab}"]`);
    activeBtn.classList.remove('border-transparent', 'text-gray-500');
    activeBtn.classList.add('border-blue-600', 'text-blue-600');
};