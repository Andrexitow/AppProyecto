<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png" href="{{ asset('imgs/nexora-logo.png') }}?v={{ filemtime(public_path('imgs/nexora-logo.png')) }}">
    <title>Nexora | Cocina</title>
    <link href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --ink:#17211d; --muted:#718078; --paper:#f4f0e7; --card:#fffdf8; --line:#ded8ca; --olive:#2c5545; --lime:#c9e265; --hot:#db5c3d; }
        * { box-sizing:border-box; }
        body { margin:0; min-height:100vh; color:var(--ink); background:radial-gradient(circle at top right,#dae7b5 0,transparent 28%),var(--paper); font-family:"Space Grotesk",sans-serif; }
        .top { min-height:94px; padding:20px clamp(20px,4vw,62px); display:flex; align-items:center; justify-content:space-between; gap:18px; border-bottom:1px solid rgba(44,85,69,.18); background:rgba(255,253,248,.74); backdrop-filter:blur(12px); position:sticky; top:0; z-index:5; }
        .brand { display:flex; align-items:center; gap:14px; }
        .brand-mark { width:45px; height:45px; overflow:hidden; border-radius:14px; box-shadow:0 8px 18px rgba(44,85,69,.16); } .brand-mark img { width:100%; height:100%; object-fit:contain; }
        h1 { font-size:22px; letter-spacing:-.8px; margin:0; } .eyebrow { margin:0 0 3px; font:11px "DM Mono",monospace; color:var(--olive); letter-spacing:.1em; text-transform:uppercase; }
        .status { display:flex; align-items:center; gap:9px; font:12px "DM Mono",monospace; color:var(--muted); } .pulse { width:9px; height:9px; border-radius:50%; background:#6fa23b; box-shadow:0 0 0 5px rgba(111,162,59,.15); }
        .workspace { max-width:1580px; padding:32px clamp(20px,4vw,62px) 60px; margin:auto; }
        .summary { display:flex; align-items:end; justify-content:space-between; gap:20px; margin-bottom:25px; }
        .summary h2 { margin:0; font-size:31px; letter-spacing:-1.5px; } .summary p { margin:6px 0 0; color:var(--muted); font-size:14px; }
        .counter { background:var(--ink); color:#fff; padding:10px 14px; border-radius:10px; font:13px "DM Mono",monospace; white-space:nowrap; } .counter b { color:var(--lime); }
        .orders { display:grid; grid-template-columns:repeat(auto-fill,minmax(305px,1fr)); gap:18px; align-items:start; }
        .order { border:1px solid var(--line); border-radius:18px; background:var(--card); overflow:hidden; box-shadow:0 8px 23px rgba(44,44,27,.06); animation:enter .32s ease both; }
        .order-head { padding:16px 17px 14px; border-bottom:1px solid var(--line); background:#f7f2e7; display:flex; justify-content:space-between; gap:10px; }
        .mesa { font-size:21px; font-weight:700; letter-spacing:-.7px; } .zone { color:var(--muted); font-size:12px; margin-top:3px; }
        .time { text-align:right; font:11px "DM Mono",monospace; color:var(--hot); } .time strong { display:block; font:15px "Space Grotesk",sans-serif; color:var(--ink); margin-top:2px; }
        .meta { padding:10px 17px; background:#fffdf8; font:11px "DM Mono",monospace; color:var(--muted); display:flex; justify-content:space-between; border-bottom:1px solid #eee8dc; }
        .items { padding:8px 17px; margin:0; list-style:none; } .items li { display:flex; gap:11px; padding:11px 0; border-bottom:1px dashed #e5dfd3; } .items li:last-child { border:0; }
        .qty { min-width:30px; height:27px; border-radius:8px; display:grid; place-items:center; background:var(--lime); color:#243420; font:13px "DM Mono",monospace; font-weight:500; }
        .item-name { font-size:14px; font-weight:600; line-height:1.35; } .note { font:11px "DM Mono",monospace; color:var(--hot); margin-top:4px; }
        .fallback { margin:17px; padding:12px; border-radius:9px; background:#faf4e8; color:#6f6558; font:11px "DM Mono",monospace; white-space:pre-wrap; max-height:180px; overflow:auto; }
        .finish { width:calc(100% - 34px); margin:10px 17px 17px; padding:13px 14px; border:0; border-radius:10px; cursor:pointer; background:var(--olive); color:#fff; font:600 13px "Space Grotesk",sans-serif; transition:transform .15s,background .15s; } .finish:hover { background:#1e4134; transform:translateY(-1px); } .finish:disabled { opacity:.65; cursor:wait; transform:none; }
        .empty { grid-column:1/-1; min-height:300px; border:1px dashed #c7c2b5; border-radius:18px; display:grid; place-items:center; text-align:center; color:var(--muted); background:rgba(255,253,248,.5); } .empty strong { display:block; font-size:19px; color:var(--ink); margin:9px 0 5px; }
        .empty-icon { font-size:40px; } .top-actions { display:flex; align-items:center; gap:8px; } .history-link, .logout { color:var(--olive); text-decoration:none; font:12px "DM Mono",monospace; border:1px solid #cdd5bd; border-radius:8px; padding:9px 11px; background:transparent; }
        @keyframes enter { from { opacity:0; transform:translateY(10px); } to { opacity:1; transform:translateY(0); } }
        @media(max-width:560px) { .top{min-height:78px;padding:14px 18px}.brand-mark{width:39px;height:39px}.brand h1{font-size:18px}.workspace{padding:25px 18px}.summary{align-items:flex-start;flex-direction:column}.summary h2{font-size:26px}.orders{grid-template-columns:1fr}.status{display:none} }
    </style>
</head>
<body>
    <header class="top">
        <div class="brand">
            <div class="brand-mark"><img src="{{ asset('imgs/nexora-logo.png') }}?v={{ filemtime(public_path('imgs/nexora-logo.png')) }}" alt="Nexora"></div>
            <div><p class="eyebrow">Nexora / Produccion</p><h1>Chef Cocina</h1></div>
        </div>
        <div class="status"><span class="pulse"></span> Actualizacion automatica</div>
        <div class="top-actions">
            <a class="history-link" href="{{ route('cocina.historial') }}">Historial</a>
            <form method="POST" action="/logout">@csrf <button class="logout" type="submit">Salir</button></form>
        </div>
    </header>
    <main class="workspace">
        <section class="summary">
            <div><h2>Ordenes por preparar</h2><p>Las comandas llegan en el orden en que fueron enviadas.</p></div>
            <div class="counter"><b id="contador">0</b> EN PREPARACION</div>
        </section>
        <section id="ordenes" class="orders" aria-live="polite"></section>
    </main>
    <script>
        const destino = document.getElementById('ordenes');
        const csrf = document.querySelector('meta[name="csrf-token"]').content;
        function escapar(valor) { const div = document.createElement('div'); div.textContent = String(valor || ''); return div.innerHTML; }
        function tiempoDesde(fecha) {
            const minutos = Math.max(0, Math.floor((Date.now() - new Date(fecha).getTime()) / 60000));
            return minutos < 1 ? 'AHORA' : minutos + ' MIN';
        }
        function renderizar(comandas) {
            document.getElementById('contador').textContent = comandas.length;
            if (!comandas.length) {
                destino.innerHTML = '<div class="empty"><div><div class="empty-icon">+</div><strong>Cocina al dia</strong><span>No hay comandas pendientes por preparar.</span></div></div>';
                return;
            }
            destino.innerHTML = comandas.map(c => {
                const items = c.items.map(i => '<li><span class="qty">' + escapar(i.cantidad) + 'x</span><div><div class="item-name">' + escapar(i.producto) + '</div>' + (i.observacion ? '<div class="note">NOTA: ' + escapar(i.observacion) + '</div>' : '') + '</div></li>').join('');
                const respaldo = !items && c.contenido_respaldo ? '<pre class="fallback">' + escapar(c.contenido_respaldo) + '</pre>' : '';
                return '<article class="order" id="comanda-' + c.id + '"><div class="order-head"><div><div class="mesa">Mesa ' + escapar(c.mesa) + '</div><div class="zone">' + escapar(c.zona || c.impresora) + '</div></div><div class="time">EN COCINA<strong>' + tiempoDesde(c.creado_en) + '</strong></div></div><div class="meta"><span>MESERO: ' + escapar(c.mesero) + '</span><span>#' + c.id + '</span></div><ul class="items">' + items + '</ul>' + respaldo + '<button class="finish" onclick="finalizar(' + c.id + ', this)">Pedido finalizado y listo</button></article>';
            }).join('');
        }
        function leerJson(respuesta) {
            return respuesta.text().then(function(texto) {
                try {
                    return JSON.parse(texto);
                } catch (error) {
                    // Una actualización defectuosa no debe borrar el tablero de cocina.
                    throw new Error('La actualización de cocina llegó incompleta. Se reintentará automáticamente.');
                }
            });
        }
        function cargar() {
            fetch('/cocina/comandas', {cache:'no-store', headers:{'Accept':'application/json'}})
                .then(r => leerJson(r).then(d => ({ok:r.ok,d})))
                .then(({ok,d}) => { if (!ok) throw new Error(d.message || 'No se pudieron cargar las comandas.'); renderizar(d.data || []); })
                .catch(function(error) {
                    if (!destino.children.length) {
                        destino.innerHTML = '<div class="empty"><div><strong>No fue posible actualizar cocina</strong><span>' + escapar(error.message) + '</span></div></div>';
                    }
                });
        }
        function finalizar(id, boton) {
            boton.disabled = true; boton.textContent = 'Finalizando...';
            fetch('/cocina/comandas/' + id + '/finalizar', {method:'POST', cache:'no-store', headers:{'X-CSRF-TOKEN':csrf,'Accept':'application/json'}})
                .then(r => leerJson(r).then(d => ({ok:r.ok,d})))
                .then(({ok,d}) => { if (!ok) throw new Error(d.message || 'No se pudo finalizar.'); document.getElementById('comanda-' + id)?.remove(); cargar(); })
                .catch(error => { boton.disabled = false; boton.textContent = error.message; setTimeout(() => boton.textContent = 'Pedido finalizado y listo', 2500); });
        }
        cargar();
        setInterval(cargar, 7000);
    </script>
</body>
</html>
