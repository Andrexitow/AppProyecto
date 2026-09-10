// Función auxiliar para agregar filas de impresoras dinámicamente en el modal
window.agregarFilaImpresora = function (impresoraId = '', punto = '') {
    const contenedor = document.getElementById('contenedor-impresoras');
    const plantilla = document.getElementById('plantilla-impresoras').innerHTML;
    
    const div = document.createElement('div');
    div.className = 'flex gap-2 items-center fila-impresora';
    div.innerHTML = `
        <select required class="flex-1 px-3 py-2 rounded-xl border border-gray-200 font-bold text-sm text-gray-700 outline-none select-imp">
            ${plantilla}
        </select>
        <select required class="w-1/3 px-3 py-2 rounded-xl border border-gray-200 font-bold text-sm text-gray-700 outline-none select-punto">
            <option value="">Punto...</option>
            <option value="RESTAURANTE" ${punto === 'RESTAURANTE' ? 'selected' : ''}>Restaurante</option>
            <option value="DISCOTECA" ${punto === 'DISCOTECA' ? 'selected' : ''}>Discoteca</option>
        </select>
        <button type="button" onclick="this.parentElement.remove()" class="text-red-500 hover:bg-red-50 p-2 rounded-xl transition-colors">❌</button>
    `;
    contenedor.appendChild(div);
    
    if (impresoraId) {
        div.querySelector('.select-imp').value = impresoraId;
    }
};

window.abrirModalGrupo = function () {
    document.getElementById('formGrupo').reset();
    document.getElementById('grupo_id').value = '';
    document.getElementById('modalTitulo').textContent = 'Nuevo Grupo';
    
    // Limpiamos el contenedor e insertamos una fila inicial vacía
    document.getElementById('contenedor-impresoras').innerHTML = '';
    window.agregarFilaImpresora();
    
    document.getElementById('modalGrupo').classList.replace('hidden', 'flex');
};

window.cerrarModalGrupo = function () {
    document.getElementById('modalGrupo').classList.replace('flex', 'hidden');
};

// Modificado para recibir la colección completa de impresoras en formato JSON/Array
window.editarGrupo = function (id, nombre, impresorasAsociadas) {
    document.getElementById('formGrupo').reset();
    document.getElementById('modalTitulo').textContent = 'Editar Grupo';
    document.getElementById('grupo_id').value = id;
    document.getElementById('nombre_grupo').value = nombre;
    
    const contenedor = document.getElementById('contenedor-impresoras');
    contenedor.innerHTML = '';
    
    // Si tiene impresoras previas, las pintamos todas. Si no, metemos una vacía.
    if (impresorasAsociadas && impresorasAsociadas.length > 0) {
        impresorasAsociadas.forEach(imp => {
            window.agregarFilaImpresora(imp.id, imp.pivot.punto);
        });
    } else {
        window.agregarFilaImpresora();
    }
    
    document.getElementById('modalGrupo').classList.replace('hidden', 'flex');
};

window.guardarGrupo = async function (e) {
    e.preventDefault();
    const id = document.getElementById('grupo_id').value;
    const url = id ? `/grupos/update/${id}` : '/grupos/store';

    // 🔄 Recolectamos dinámicamente las impresoras y sus puntos de la interfaz
    const impresoras = [];
    document.querySelectorAll('.fila-impresora').forEach(fila => {
        const impId = fila.querySelector('.select-imp').value;
        const puntoVal = fila.querySelector('.select-punto').value;
        if (impId && puntoVal) {
            impresoras.push({ id: impId, punto: puntoVal });
        }
    });

    // Cambiamos el payload para mandar el array estructurado tal como lo espera el Backend
    const payload = {
        nombre: document.getElementById('nombre_grupo').value,
        impresoras: impresoras
    };

    try {
        const res = await fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': window.csrfToken,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        });

        const data = await res.json();

        if (res.ok && data.status === 'success') {
            window.mostrarNotificacion(data.message, 'success');
            cerrarModalGrupo();
            loadView('grupos');
        } else {
            window.mostrarNotificacion(data.message || 'Error al procesar', 'error');
        }
    } catch (error) {
        console.error('Error:', error);
        window.mostrarNotificacion('Error de conexión con el servidor', 'error');
    }
};

window.eliminarGrupo = function (id) {
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
