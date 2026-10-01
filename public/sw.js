// Service worker de NussoraPos.
//
// IMPORTANTE — por qué esta estrategia es deliberadamente conservadora:
// este es un sistema de ventas en vivo (mesas, inventario, comandas de
// cocina). Cachear páginas HTML o respuestas de la API podría mostrarle a
// un mesero una mesa "disponible" que ya está ocupada, o dejarlo comandar
// contra un stock que ya no existe. Por eso:
//   - Los archivos ESTÁTICOS (JS, CSS, íconos, fuentes) sí se cachean —
//     no cambian el resultado de una venta, solo la velocidad de carga.
//   - Las páginas (navegación) y CUALQUIER petición a la app (fetch/XHR)
//     SIEMPRE van a la red primera — nunca a caché. Si no hay red, se
//     muestra offline.html en vez de datos viejos.
// Esto da instalar-como-app y carga más rápida de assets, NO trabajo
// completo sin conexión para tomar pedidos reales — eso necesitaría cola
// de sincronización y validación de stock al reconectar, que es un
// proyecto aparte.

const VERSION = 'nexora-v1';
const CACHE_ESTATICOS = `${VERSION}-estaticos`;

const ESTATICOS_PRECARGA = [
    '/offline.html',
    '/manifest.json',
    '/icons/icon-192.png',
    '/icons/icon-512.png',
];

self.addEventListener('install', (evento) => {
    evento.waitUntil(
        caches.open(CACHE_ESTATICOS).then((cache) => cache.addAll(ESTATICOS_PRECARGA))
    );
    self.skipWaiting();
});

self.addEventListener('activate', (evento) => {
    evento.waitUntil(
        caches.keys().then((claves) => Promise.all(
            claves.filter((clave) => clave.startsWith('nexora-') && clave !== CACHE_ESTATICOS)
                .map((clave) => caches.delete(clave))
        ))
    );
    self.clients.claim();
});

function esEstatico(url) {
    return /\.(js|css|png|jpg|jpeg|svg|webp|woff2?|ttf)$/i.test(url.pathname)
        || url.pathname.startsWith('/icons/')
        || url.pathname.startsWith('/build/');
}

self.addEventListener('fetch', (evento) => {
    const url = new URL(evento.request.url);

    // Nunca intervenir peticiones a otros dominios (CDNs de Tailwind,
    // SweetAlert, Google Fonts, etc.) — que las maneje el navegador normal.
    if (url.origin !== self.location.origin) return;

    // Solo GET tiene sentido cachear/interceptar; un POST (guardar pedido,
    // cerrar mesa...) siempre debe ir directo a la red, sin pasar por aquí.
    if (evento.request.method !== 'GET') return;

    if (esEstatico(url)) {
        // Estáticos: cache-first (rápido), y de paso se refresca en
        // segundo plano por si el archivo cambió (stale-while-revalidate).
        evento.respondWith(
            caches.open(CACHE_ESTATICOS).then(async (cache) => {
                const enCache = await cache.match(evento.request);
                const enRed = fetch(evento.request).then((respuesta) => {
                    if (respuesta.ok) cache.put(evento.request, respuesta.clone());
                    return respuesta;
                }).catch(() => enCache);
                return enCache || enRed;
            })
        );
        return;
    }

    // Todo lo demás (páginas, /cocina/comandas, /mesas/actualizar, etc.):
    // siempre red. Si falla y es una navegación de página completa, se
    // muestra la pantalla de "sin conexión" en vez del error del navegador.
    evento.respondWith(
        fetch(evento.request).catch(() => {
            if (evento.request.mode === 'navigate') {
                return caches.match('/offline.html');
            }
            return Response.error();
        })
    );
});
