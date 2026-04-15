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
    const modal = document.getElementById('modalBodega');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

window.openModalAjuste = function () {
    const modal = document.getElementById('modalAjuste');
    if (!modal) return;

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    document.getElementById('paso1').classList.remove('hidden');
    document.getElementById('paso2').classList.add('hidden');
    obtenerSiguienteNumero();
};

window.closeModalAjuste = function () {
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

window.irPaso2 = function () {
    const paso1 = document.getElementById('paso1');
    const paso2 = document.getElementById('paso2');

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

window.openModalVerAjuste = function () {
    document.getElementById('modalVerAjuste').classList.remove('hidden');
};

window.closeModalVerAjuste = function () {
    document.getElementById('modalVerAjuste').classList.add('hidden');
};