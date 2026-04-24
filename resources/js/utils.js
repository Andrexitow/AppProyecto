window.csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

window.debounce = function (func, delay = 400) {
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

window.mostrarNotificacion = function (mensaje, tipo = 'info') {

    const contenedor = document.getElementById('notificaciones');
    if (!contenedor) {
        console.warn('Contenedor de notificaciones no encontrado');
        return;
    }

    const colores = {
        success: 'bg-green-500',
        error: 'bg-red-500',
        warning: 'bg-yellow-500',
        info: 'bg-blue-500'
    };

    const div = document.createElement('div');
    div.className = `${colores[tipo] || colores.info} text-white px-6 py-4 rounded-2xl shadow-lg transform transition-all duration-300 opacity-0 translate-y-2`;

    // 🔥 IMPORTANTE: permitir HTML (para <br>)
    div.innerHTML = mensaje;

    contenedor.appendChild(div);

    // animación entrada
    setTimeout(() => {
        div.classList.remove('opacity-0', 'translate-y-2');
    }, 50);

    // auto eliminar
    setTimeout(() => {
        div.classList.add('opacity-0', 'translate-y-2');

        setTimeout(() => {
            div.remove();
        }, 300);

    }, 3500);
};