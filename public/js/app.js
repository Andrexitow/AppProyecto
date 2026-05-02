window.loadView = function (view) {
    fetch('/views/' + view)
        .then(function(res) { return res.text(); })
        .then(function(html) {
            document.getElementById('main-content').innerHTML = html;
            if (typeof initEventos === 'function') {
                initEventos();
            }
        })
        .catch(function(err) { console.error(err); });
};

document.addEventListener('DOMContentLoaded', function() {
    console.log('App cargada correctamente');
});

function toggleTab(tabId) {
    const ribbon = document.getElementById('ribbon');
    const contents = document.querySelectorAll('.tab-content');
    const buttons = document.querySelectorAll('.tab-btn');
    const target = document.getElementById(tabId);

    // Si la pestaña ya está abierta y hacemos clic, cerramos el ribbon
    if (!target.classList.contains('hidden') && !ribbon.classList.contains('hidden')) {
        ribbon.classList.add('hidden');
        return;
    }

    // Ocultar todos los contenidos y quitar estilos a botones
    contents.forEach(c => c.classList.add('hidden'));
    buttons.forEach(b => {
        b.classList.remove('text-blue-600', 'border-blue-600');
        b.classList.add('text-gray-500', 'border-transparent');
    });

    // Mostrar el seleccionado
    ribbon.classList.remove('hidden');
    target.classList.remove('hidden');
    
    // Estilizar botón activo
    const activeBtn = document.getElementById('btn-' + tabId);
    activeBtn.classList.add('text-blue-600', 'border-blue-600');
    activeBtn.classList.remove('text-gray-500', 'border-transparent');
}