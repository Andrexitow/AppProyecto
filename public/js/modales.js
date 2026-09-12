window.openModalProducto = function () {
    const modal = document.getElementById('modalProducto');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

window.closeModalProducto = function () {
    const modal = document.getElementById('modalProducto');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

window.openModalBodega = function () {
    const modal = document.getElementById('modalBodega');
    if (!modal) return;
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

window.closeModalBodega = function () {
    const modal = document.getElementById('modalBodega');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

window.openModalAjuste = function () {
    const modal = document.getElementById('modalAjuste');
    if (!modal) return;

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    const titulo = modal.querySelector('.aj-modal-title');
    if (titulo) titulo.innerText = 'Nuevo Ajuste';

    obtenerSiguienteNumero();
};

window.closeModalAjuste = function () {
    window.ajusteActivoId = null;
    const modal = document.getElementById('modalAjuste');
    if (!modal) return;

    modal.classList.add('hidden');
    modal.classList.remove('flex');

    resetModalAjuste();
};

function resetModalAjuste() {
    const inputs = document.querySelectorAll('#modalAjuste input, #modalAjuste textarea, #modalAjuste select');
    inputs.forEach(el => {
        if (el.hasAttribute('data-no-reset') || el.readOnly) return;

        if (el.tagName === 'SELECT') {
            el.selectedIndex = 0;
        } else {
            el.value = '';
        }
    });

    document.getElementById('aj-tercero-resultados')?.classList.add('hidden');
    document.getElementById('aj-contraparte-resultados')?.classList.add('hidden');
    document.getElementById('resultadosProducto')?.classList.add('hidden');

    const tbody = document.getElementById('tablaProductos');
    if (tbody) tbody.innerHTML = '';
}


window.openModalVerAjuste = function () {
    document.getElementById('modalVerAjuste').classList.remove('hidden');
};

window.closeModalVerAjuste = function () {
    document.getElementById('modalVerAjuste').classList.add('hidden');
};

// Abrir el modal
window.openModalUsuario = function() {
    const modal = document.getElementById('modalUsuario');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
};

// Cerrar el modal
window.closeModalUsuario = function() {
    const modal = document.getElementById('modalUsuario');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
};

// Abrir el modal
window.openModalRol = function() {
    const modal = document.getElementById('modalRol');
    if (modal) {
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }
};

// Cerrar el modal
window.closeModalRol = function() {
    const modal = document.getElementById('modalRol');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
};
