// =============================================
// CONFIG GLOBAL
// =============================================
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

// =============================================
// UTILIDADES
// =============================================

// Debounce para evitar múltiples peticiones mientras se escribe
function debounce(func, delay = 400) {
    let timeout;
    return function (...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), delay);
    };
}

window.activeTab = null;

window.toggleTab = function (tabId) {
    const ribbon = document.getElementById('ribbon');

    if (activeTab === tabId) {
        ribbon.classList.add('hidden');
        document.getElementById(tabId).classList.add('hidden');
        activeTab = null;
        return;
    }

    ribbon.classList.remove('hidden');

    document.querySelectorAll('.tab-content').forEach(el => {
        el.classList.add('hidden');
    });

    document.getElementById(tabId).classList.remove('hidden');
    activeTab = tabId;
};

// =============================================
// MODALES
// =============================================
window.openModalAjuste = function () {
    const modal = document.getElementById('modalAjuste');
    if (!modal) return;

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    // Asegurar que siempre empiece en paso 1
    document.getElementById('paso1').classList.remove('hidden');
    document.getElementById('paso2').classList.add('hidden');
    obtenerSiguienteNumero();
};

window.closeModalAjuste = function () {
    const modal = document.getElementById('modalAjuste');
    if (!modal) return;

    modal.classList.add('hidden');
    modal.classList.remove('flex');

    // Limpiar todo el formulario
    resetModalAjuste();
};

function resetModalAjuste() {
    // Limpiar todos los inputs excepto los que no queremos resetear
    const inputs = document.querySelectorAll('#modalAjuste input, #modalAjuste textarea, #modalAjuste select');
    inputs.forEach(el => {
        if (el.hasAttribute('data-no-reset') || el.readOnly && el.id !== 'inputNombre') return;

        if (el.tagName === 'SELECT') {
            el.selectedIndex = 0;
        } else {
            el.value = '';
        }
    });

    // Limpiar campos específicos del tercero
    document.getElementById('inputNombre')?.setAttribute('value', '');
    document.getElementById('tercero_id')?.setAttribute('value', '');

    // Ocultar dropdown si existe
    document.getElementById('resultadosTercero')?.classList.add('hidden');

    // Limpiar tabla de productos
    const tbody = document.querySelector('#paso2 tbody');
    if (tbody) tbody.innerHTML = '';
}

// =============================================
// PASOS DEL MODAL
// =============================================
window.irPaso2 = function () {
    const paso1 = document.getElementById('paso1');
    const paso2 = document.getElementById('paso2');

    // Pequeña validación básica antes de avanzar
    if (!document.getElementById('tercero_id')?.value) {
        alert('Debes seleccionar un tercero antes de continuar');
        return;
    }

    paso1?.classList.add('hidden');
    paso2?.classList.remove('hidden');
};

window.volverPaso1 = function () {
    document.getElementById('paso1')?.classList.remove('hidden');
    document.getElementById('paso2')?.classList.add('hidden');
};

// =============================================
// BUSCAR TERCERO POR DOCUMENTO (MEJORADO)
// =============================================
window.buscarTerceroPorDocumento = function () {
    const inputDoc = document.getElementById('inputDocumento');
    const inputNombre = document.getElementById('inputNombre');
    const hiddenId = document.getElementById('tercero_id');

    if (!inputDoc) {
        console.warn('inputDocumento no encontrado');
        return;
    }

    const doc = inputDoc.value.trim();

    // Limpiar si está vacío
    if (doc.length === 0) {
        if (inputNombre) inputNombre.value = '';
        if (hiddenId) hiddenId.value = '';
        return;
    }

    // Esperar mínimo 5 caracteres
    if (doc.length < 5) return;

    console.log('Buscando documento:', doc); // ← Para debug

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

// =============================================
// BUSCAR TERCERO GENERAL (Autocomplete)
// =============================================
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

// =============================================
// MODAL DE LISTA DE TERCEROS
// =============================================
window.abrirModalTerceros = function () {
    const modal = document.getElementById('modalTerceros');
    if (modal) {
        modal.classList.remove('hidden');
        buscarTerceroModal('');   // carga inicial
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

// =============================================
// PRODUCTOS
// =============================================
document.addEventListener('change', function (e) {
    if (e.target.id === 'prefijo') {
        obtenerSiguienteNumero();
    }
});
window.obtenerSiguienteNumero = function () {

    let prefijo = document.getElementById('prefijo').value;

    fetch(`/ajustes/siguiente-numero?prefijo=${prefijo}`)
        .then(res => res.json())
        .then(data => {

            let numeroFormateado = String(data.numero).padStart(4, '0');

            document.getElementById('numero').value = numeroFormateado;
        });
};
window.buscarProducto = debounce(function () {
    const query = document.getElementById('buscarProducto')?.value.trim();
    const contenedor = document.getElementById('resultadosProducto');

    if (!query || query.length < 2 || !contenedor) return;
    fetch(`/productos/buscar?query=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            // console.log(data);
            contenedor.innerHTML = '';
            contenedor.classList.remove('hidden');

            data.forEach(p => {

                let descripcion = (p.descripcion || '').replace(/'/g, "\\'");
                let codigo = p.codigo ?? '-';

                contenedor.innerHTML += `
    <div onclick="agregarProducto(${p.id}, '${descripcion}', ${p.precio})"
        class="flex gap-3 p-3 hover:bg-gray-100 cursor-pointer border-b text-sm">

        <div class="text-gray-500 w-24">${codigo}</div>
        <div class="flex-1">${p.descripcion}</div>

    </div>
`;
            });
        });
}, 300);

// ... (agregarProducto, eliminarProducto y guardarAjuste se mantienen igual, solo les agregué protección)

window.agregarProducto = function (id, descripcion, precio) {

    const tabla = document.getElementById('tablaProductos');

    // validar tabla y evitar duplicados
    if (!tabla || document.getElementById('prod_' + id)) return;

    tabla.innerHTML += `
        <tr id="prod_${id}" class="hover:bg-gray-50">

            <td class="p-4">${descripcion}</td>

            <td class="p-4">
                <input type="number" value="1"
                    class="cantidad w-20 mx-auto block border rounded text-center">
            </td>

            <td class="p-4">
                <input type="number" value="${precio}"
                    class="precio w-24 border rounded text-center">
            </td>

            <td class="p-4">
                <select class="tipo border rounded">
                    <option value="1">+ Entrada</option>
                    <option value="-1">- Salida</option>
                </select>
            </td>

            <td class="p-4 text-center">
                <button onclick="eliminarProducto(${id})" class="text-red-500">
                    Eliminar
                </button>
            </td>

        </tr>
    `;

    // limpiar buscador
    let input = document.getElementById('buscarProducto');
    let resultados = document.getElementById('resultadosProducto');

    if (input) input.value = '';
    if (resultados) resultados.classList.add('hidden');
};

window.eliminarProducto = function (id) {
    document.getElementById('prod_' + id)?.remove();
};

window.guardarAjuste = function () {

    const tercero_id = document.getElementById('tercero_id')?.value;
    const fecha = document.getElementById('fecha')?.value
        || document.querySelector('#paso1 input[type="date"]')?.value;

    const observaciones = document.querySelector('#paso1 textarea')?.value;
    const contraparte = document.querySelector('#paso1 input[placeholder="Cuenta contable"]')?.value;

    const filas = document.querySelectorAll('#tablaProductos tr');

    // 🔴 VALIDACIONES
    if (!tercero_id) return alert('Debes seleccionar un tercero');
    if (filas.length === 0) return alert('Debes agregar al menos un producto');

    let detalles = [];

    let total = 0;

    filas.forEach(fila => {

        let id = fila.id.replace('prod_', '');

        let cantidad = parseFloat(fila.querySelector('.cantidad').value) || 0;
        let precio = parseFloat(fila.querySelector('.precio').value) || 0;
        let tipo = parseInt(fila.querySelector('.tipo').value);

        let subtotal = cantidad * precio;

        total += subtotal;

        detalles.push({
            producto_id: id,
            cantidad,
            precio,
            tipo
        });
    });

    let data = {
        prefijo: 'AJ',
        // numero: 1, // luego lo automatizamos
        fecha,
        tercero_id,
        contraparte,
        observaciones,
        total,
        detalles
    };

    fetch('/ajustes', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(data)
    })
        .then(async res => {

            let response;

            try {
                response = await res.json();
            } catch {
                response = {};
            }

            if (!res.ok) {
                console.error('ERROR BACKEND:', response);
                throw new Error(response.error || 'Error al guardar');
            }

            return response;
        })
        .then(() => {
            alert('Ajuste guardado correctamente');
            closeModalAjuste();
            loadView('ajustes');
        })
        .catch(err => {
            console.error(err);
            alert(err.message);
        });
};

window.loadView = function (view) {
    fetch(`/views/${view}`)
        .then(res => res.text())
        .then(html => {
            document.getElementById('main-content').innerHTML = html;

            // Re-inicializar eventos específicos si es necesario
            if (typeof initEventos === 'function') {
                initEventos();
            }
        })
        .catch(err => console.error(err));
};

// =============================================
// INICIALIZACIÓN
// =============================================
document.addEventListener('DOMContentLoaded', () => {
    // Si cargas vistas dinámicamente con loadView(), llama a initEventos() dentro de esa función
    console.log('JavaScript cargado correctamente');
});