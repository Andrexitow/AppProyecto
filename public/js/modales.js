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
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

window.closeModalBodega = function () {
    window.ajusteActivoId = null;
    const modal = document.getElementById('modalBodega');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

window.openModalAjuste = function () {
    const modal = document.getElementById('modalAjuste');
    if (!modal) return;

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    document.querySelector('#modalAjuste h2').innerText = 'Nuevo Ajuste';

    document.getElementById('paso1').classList.remove('hidden');
    document.getElementById('paso2').classList.add('hidden');
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
        if (el.hasAttribute('data-no-reset') || el.readOnly && el.id !== 'inputNombre') return;

        if (el.tagName === 'SELECT') {
            el.selectedIndex = 0;
        } else {
            el.value = '';
        }
    });

    document.getElementById('inputNombre')?.setAttribute('value', '');
    document.getElementById('tercero_id')?.setAttribute('value', '');
    document.getElementById('resultadosTercero')?.classList.add('hidden');

    const tbody = document.querySelector('#paso2 tbody');
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