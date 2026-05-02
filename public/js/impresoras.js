window.openModalImpresora = function() {
    document.getElementById('formImpresora').reset();
    document.getElementById('imp_id').value = '';
    const modal = document.getElementById('modalImpresora');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

window.closeModalImpresora = function() {
    const modal = document.getElementById('modalImpresora');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

window.editarImpresora = function(data) {
    document.getElementById('imp_id').value = data.id;
    document.getElementById('imp_nombre').value = data.nombre;
    document.getElementById('imp_ip').value = data.ip;
    document.getElementById('imp_puerto').value = data.puerto;
    
    const modal = document.getElementById('modalImpresora');
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

window.guardarImpresora = function() {
    const form = document.getElementById('formImpresora');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());

    fetch('/api/impresoras/guardar', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(res => {
        if(res.status === 'success') {
            closeModalImpresora();
            // Refrescar vista
            if(typeof loadView === 'function') loadView('impresoras');
        } else {
            alert("Error: " + res.message);
        }
    })
    .catch(error => console.error('Error:', error));
}