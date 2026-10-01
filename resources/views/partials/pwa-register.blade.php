{{-- Registro del service worker — incluir antes de </body> en toda
     página completa. Silencioso si el navegador no soporta service
     workers (ej. navegadores muy viejos), no bloquea nada de la app. --}}
<script>
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', function () {
            navigator.serviceWorker.register('/sw.js').catch(function (error) {
                console.warn('No se pudo registrar el service worker de NussoraPos:', error);
            });
        });
    }
</script>
