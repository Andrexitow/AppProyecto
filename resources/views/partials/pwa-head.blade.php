{{-- Metadatos de PWA — incluir dentro de <head> en toda página completa
     (login, panel admin, POS, cocina). Ver public/sw.js para la estrategia
     de caché (deliberadamente conservadora: nada de datos de venta/stock
     en caché, solo assets estáticos). --}}
<link rel="manifest" href="{{ asset('manifest.json') }}">
<meta name="theme-color" content="#1D4ED8">
<link rel="apple-touch-icon" href="{{ asset('icons/apple-touch-icon.png') }}">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Nexora">
<meta name="mobile-web-app-capable" content="yes">
