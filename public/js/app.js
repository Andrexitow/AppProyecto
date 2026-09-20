window.loadView = function (view) {
    fetch('/views/' + view)
        .then(function (res) {
            // Si la sesión expiró (timeout de inactividad, la cerró un
            // admin, etc.), Laravel redirige esta petición a /login — y
            // fetch sigue esa redirección solo. Sin este chequeo, la página
            // de login completa (con su propio <html><body>) quedaba
            // insertada DENTRO del panel de contenido de la app, encimada
            // sobre lo que ya había en pantalla. Con la sesión vencida no
            // tiene caso seguir en la SPA: se manda la pestaña entera al
            // login de verdad.
            if (res.redirected && res.url.indexOf('/login') !== -1) {
                window.location.href = res.url;
                return null;
            }
            if (!res.ok) {
                // Antes la página de error completa se insertaba en el panel.
                return res.json().catch(function () { return {}; }).then(function (d) {
                    var msg = (d && d.message) || ('No se pudo cargar la pantalla (error ' + res.status + ').');
                    if (typeof mostrarNotificacion === 'function') mostrarNotificacion(msg, 'error');
                    return null;
                });
            }
            return res.text();
        })
        .then(function (html) {
            if (html === null) return;
            var container = document.getElementById('main-content');
            container.innerHTML = html;

            // Los <script> insertados vía innerHTML NO se ejecutan solos.
            // Hay que recrearlos para que el navegador sí los corra.
            var scripts = container.querySelectorAll('script');
            scripts.forEach(function (oldScript) {
                var newScript = document.createElement('script');
                if (oldScript.src) {
                    newScript.src = oldScript.src;
                } else {
                    newScript.textContent = oldScript.textContent;
                }
                oldScript.replaceWith(newScript);
            });

            if (typeof initEventos === 'function') {
                initEventos();
            }
        })
        .catch(function (err) { console.error(err); });
};

document.addEventListener('DOMContentLoaded', function () {
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