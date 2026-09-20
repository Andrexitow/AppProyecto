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

    // Evita apilar el mismo aviso (p. ej. el fetch global y el handler de la
    // pantalla reportando el mismo error a la vez).
    window.__ultimasNotificaciones = window.__ultimasNotificaciones || {};
    const ahora = Date.now();
    const clave = tipo + '|' + mensaje;
    if (window.__ultimasNotificaciones[clave] && ahora - window.__ultimasNotificaciones[clave] < 3500) {
        return;
    }
    window.__ultimasNotificaciones[clave] = ahora;

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

window.abrirConfirm = function (mensaje, callback) {

    const modal = document.getElementById('modalConfirm');
    const texto = document.getElementById('confirmMensaje');
    const btn = document.getElementById('btnConfirmarAccion');

    if (!modal || !texto || !btn) {
        console.warn('Modal confirmación no encontrado');
        return;
    }

    texto.innerText = mensaje;

    // limpiar click anterior
    btn.onclick = null;

    btn.onclick = function () {
        callback();
        cerrarConfirm();
    };

    modal.classList.remove('hidden');
    modal.classList.add('flex');
};

window.cerrarConfirm = function () {

    const modal = document.getElementById('modalConfirm');

    modal.classList.add('hidden');
    modal.classList.remove('flex');
};

// ─────────────────────────────────────────────────────────────────────────
// Errores de red/servidor con motivo legible
//
// Envuelve fetch() para que TODA respuesta de error (o fallo de red) llegue a
// los handlers con un `message` claro. Así, el `data.message` / `e.message` que
// ya muestran las pantallas nunca es "Server Error", HTML o "Failed to fetch".
// Las respuestas JSON con un mensaje útil del backend pasan sin tocarse.
// ─────────────────────────────────────────────────────────────────────────
(function () {
    if (window.__fetchErroresClaros) return;
    window.__fetchErroresClaros = true;

    var originalFetch = window.fetch.bind(window);

    var porEstado = {
        400: 'La solicitud no es válida.',
        401: 'Tu sesión expiró. Vuelve a iniciar sesión.',
        403: 'No tienes permiso para realizar esta acción.',
        404: 'No se encontró lo solicitado (la ruta o el registro no existe).',
        405: 'Método no permitido para esta acción.',
        408: 'El servidor tardó demasiado en responder. Inténtalo de nuevo.',
        413: 'Los datos o archivos enviados son demasiado grandes.',
        419: 'La sesión de seguridad expiró. Recarga la página (F5) e inténtalo de nuevo.',
        422: 'Hay datos inválidos en el formulario.',
        429: 'Demasiadas solicitudes seguidas. Espera un momento e inténtalo de nuevo.',
        500: 'Error interno del servidor. Revisa storage/logs/laravel.log para ver el motivo.',
        502: 'El servidor no respondió correctamente (502). Inténtalo de nuevo en unos segundos.',
        503: 'El servidor no está disponible o está en mantenimiento (503).',
        504: 'El servidor tardó demasiado en responder (504). Inténtalo de nuevo.'
    };

    var genericos = ['', 'Server Error', 'Unauthenticated.', 'Not Found', 'Forbidden',
        'The given data was invalid.', 'Whoops, looks like something went wrong.'];

    function mensajePorEstado(status) {
        return porEstado[status] || ('El servidor respondió con un error (' + status + ').');
    }

    // Ningún error de servidor (5xx) ni de sesión de seguridad (419) queda mudo,
    // aunque la pantalla que hizo la petición no muestre nada.
    function avisar(res, mensaje) {
        if ((res.status >= 500 || res.status === 419) && typeof window.mostrarNotificacion === 'function') {
            window.mostrarNotificacion(mensaje, 'error');
        }
    }

    function respuestaJson(cuerpo, res) {
        return new Response(JSON.stringify(cuerpo), {
            status: res.status,
            statusText: res.statusText,
            headers: { 'Content-Type': 'application/json' }
        });
    }

    window.fetch = function (input, init) {
        return originalFetch(input, init).then(function (res) {
            if (res.ok || res.redirected || res.type === 'opaque' || res.type === 'opaqueredirect') {
                return res;
            }

            var tipo = (res.headers.get('content-type') || '').toLowerCase();

            // El navegador pide un archivo (PDF, imagen, descarga): no se toca.
            if (tipo && tipo.indexOf('json') === -1 && tipo.indexOf('html') === -1 && tipo.indexOf('text/plain') === -1) {
                return res;
            }

            if (tipo.indexOf('json') === -1) {
                // HTML de error (página 500/419/404, WAF de hosting, etc.)
                avisar(res, mensajePorEstado(res.status));
                return respuestaJson({
                    success: false,
                    message: mensajePorEstado(res.status),
                    error: mensajePorEstado(res.status)
                }, res);
            }

            return res.clone().json().then(function (data) {
                if (!data || typeof data !== 'object') return res;

                var msg = typeof data.message === 'string' ? data.message.trim() : '';
                if (genericos.indexOf(msg) !== -1) {
                    var lista = data.errors && typeof data.errors === 'object'
                        ? [].concat.apply([], Object.keys(data.errors).map(function (k) { return data.errors[k]; }))
                        : [];
                    data.message = lista.length ? lista.join('<br>') : mensajePorEstado(res.status);
                    if (typeof data.error !== 'string' || !data.error) data.error = data.message;
                    avisar(res, data.message);
                    return respuestaJson(data, res);
                }
                avisar(res, msg);
                return res;
            }).catch(function () { return res; });
        }, function (err) {
            // Las cancelaciones voluntarias (AbortController) se dejan pasar tal cual.
            if (err && err.name === 'AbortError') throw err;
            // fetch solo rechaza por fallo de red (sin conexión, DNS, CORS, corte).
            var e = new Error('No se pudo comunicar con el servidor. Revisa tu conexión a internet e inténtalo de nuevo.');
            e.original = err;
            throw e;
        });
    };
})();
