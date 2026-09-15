<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="icon" type="image/png"
        href="{{ asset('imgs/nexora-logo.png') }}?v={{ filemtime(public_path('imgs/nexora-logo.png')) }}">
    <title>Nexora | Historial de Cocina</title>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Mono:wght@400;500&family=Space+Grotesk:wght@500;600;700&display=swap"
        rel="stylesheet">
    <style>
        :root {
            --ink: #17211d;
            --muted: #718078;
            --paper: #f4f0e7;
            --card: #fffdf8;
            --line: #ded8ca;
            --olive: #2c5545;
            --lime: #c9e265;
            --hot: #db5c3d;
            --blue: #315e72;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            color: var(--ink);
            background: radial-gradient(circle at 92% 3%, #dae7b5 0, transparent 27%), var(--paper);
            font-family: "Space Grotesk", sans-serif;
        }

        .top {
            min-height: 94px;
            padding: 20px clamp(20px, 4vw, 62px);
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 18px;
            border-bottom: 1px solid rgba(44, 85, 69, .18);
            background: rgba(255, 253, 248, .76);
            backdrop-filter: blur(12px);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        .brand-mark {
            width: 45px;
            height: 45px;
            overflow: hidden;
            border-radius: 14px;
            box-shadow: 0 8px 18px rgba(44, 85, 69, .16);
        }

        .brand-mark img {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        h1 {
            margin: 0;
            font-size: 22px;
            letter-spacing: -.8px;
        }

        .eyebrow {
            margin: 0 0 3px;
            color: var(--olive);
            font: 11px "DM Mono", monospace;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .nav-link {
            color: var(--olive);
            text-decoration: none;
            font: 12px "DM Mono", monospace;
            border: 1px solid #cdd5bd;
            border-radius: 8px;
            padding: 9px 11px;
        }

        .workspace {
            max-width: 1420px;
            padding: 34px clamp(20px, 4vw, 62px) 60px;
            margin: auto;
        }

        .hero {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 25px;
            margin-bottom: 25px;
        }

        .hero h2 {
            margin: 0;
            font-size: 32px;
            letter-spacing: -1.6px;
        }

        .hero p {
            margin: 7px 0 0;
            color: var(--muted);
            font-size: 14px;
        }

        .filters {
            display: flex;
            align-items: end;
            gap: 9px;
            padding: 12px;
            border: 1px solid var(--line);
            border-radius: 13px;
            background: rgba(255, 253, 248, .82);
        }

        .filters label {
            display: grid;
            gap: 5px;
            color: var(--muted);
            font: 10px "DM Mono", monospace;
            text-transform: uppercase;
        }

        .filters input {
            border: 1px solid #d9d5ca;
            border-radius: 7px;
            padding: 7px;
            color: var(--ink);
            background: #fffdf8;
            font: 12px "DM Mono", monospace;
        }

        .filters button {
            border: 0;
            border-radius: 8px;
            padding: 9px 12px;
            background: var(--olive);
            color: #fff;
            font: 600 12px "Space Grotesk", sans-serif;
            cursor: pointer;
        }

        .filters button:disabled {
            opacity: .65;
            cursor: wait;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 13px;
            margin-bottom: 20px;
        }

        .stat {
            min-height: 118px;
            padding: 17px;
            border: 1px solid var(--line);
            border-radius: 16px;
            background: var(--card);
            box-shadow: 0 8px 20px rgba(44, 44, 27, .04);
        }

        .stat-label {
            color: var(--muted);
            font: 10px "DM Mono", monospace;
            letter-spacing: .06em;
            text-transform: uppercase;
        }

        .stat-value {
            margin-top: 18px;
            color: var(--olive);
            font-size: 29px;
            font-weight: 700;
            letter-spacing: -1.4px;
        }

        .stat-note {
            margin-top: 3px;
            color: #88938d;
            font-size: 11px;
        }

        .grid {
            display: grid;
            grid-template-columns: 1.04fr .96fr;
            gap: 18px;
        }

        .panel {
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 17px;
            background: var(--card);
            box-shadow: 0 8px 23px rgba(44, 44, 27, .045);
        }

        .panel-head {
            display: flex;
            justify-content: space-between;
            align-items: end;
            padding: 19px;
            border-bottom: 1px solid #e9e4d9;
        }

        .panel-head h3 {
            margin: 0;
            font-size: 16px;
            letter-spacing: -.5px;
        }

        .panel-head span {
            color: var(--muted);
            font: 10px "DM Mono", monospace;
        }

        .products {
            padding: 6px 19px 15px;
        }

        .product {
            display: grid;
            grid-template-columns: 32px 1fr auto;
            align-items: center;
            gap: 10px;
            padding: 13px 0;
            border-bottom: 1px solid #eee8dc;
        }

        .product:last-child {
            border: 0;
        }

        .rank {
            width: 27px;
            height: 27px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            background: #edf1df;
            color: var(--olive);
            font: 11px "DM Mono", monospace;
        }

        .product-name {
            font-size: 13px;
            font-weight: 600;
        }

        .bar {
            height: 5px;
            margin-top: 6px;
            overflow: hidden;
            border-radius: 99px;
            background: #edf0e9;
        }

        .bar i {
            display: block;
            height: 100%;
            border-radius: inherit;
            background: var(--lime);
        }

        .quantity {
            color: var(--olive);
            font: 700 15px "DM Mono", monospace;
        }

        .quantity small {
            display: block;
            color: var(--muted);
            font-size: 9px;
            font-weight: 400;
            text-align: right;
        }

        .history {
            padding: 4px 19px 12px;
        }

        .history-row {
            display: grid;
            grid-template-columns: 46px 1fr auto;
            gap: 10px;
            align-items: center;
            padding: 13px 0;
            border-bottom: 1px solid #eee8dc;
        }

        .history-row:last-child {
            border: 0;
        }

        .table-tag {
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            border-radius: 10px;
            background: #e8f0ec;
            color: var(--olive);
            font-size: 11px;
            font-weight: 700;
        }

        .history-title {
            font-size: 13px;
            font-weight: 700;
        }

        .history-sub {
            margin-top: 3px;
            color: var(--muted);
            font-size: 10px;
        }

        .history-time {
            color: var(--blue);
            font: 10px "DM Mono", monospace;
            text-align: right;
        }

        .history-time b {
            display: block;
            margin-top: 3px;
            color: var(--ink);
            font-size: 11px;
            font-weight: 500;
        }

        .empty {
            min-height: 260px;
            display: grid;
            place-items: center;
            padding: 30px;
            text-align: center;
            color: var(--muted);
            font-size: 13px;
        }

        .empty strong {
            display: block;
            margin-bottom: 6px;
            color: var(--ink);
            font-size: 16px;
        }

        .loading {
            color: var(--muted);
            font: 12px "DM Mono", monospace;
        }

        .status {
            min-height: 15px;
            margin: 12px 2px 0;
            color: var(--muted);
            font: 10px "DM Mono", monospace;
        }

        @media(max-width:950px) {
            .hero {
                align-items: flex-start;
                flex-direction: column
            }

            .stats {
                grid-template-columns: repeat(2, 1fr)
            }

            .grid {
                grid-template-columns: 1fr
            }
        }

        @media(max-width:560px) {
            .top {
                min-height: 78px;
                padding: 14px 18px
            }

            .brand-mark {
                width: 39px;
                height: 39px
            }

            .brand h1 {
                font-size: 18px
            }

            .workspace {
                padding: 25px 18px
            }

            .hero h2 {
                font-size: 27px
            }

            .filters {
                width: 100%;
                display: grid;
                grid-template-columns: 1fr 1fr
            }

            .filters button {
                grid-column: 1/-1
            }

            .stats {
                gap: 9px
            }

            .stat {
                min-height: 103px;
                padding: 13px
            }

            .stat-value {
                font-size: 24px
            }

            .history-row {
                grid-template-columns: 38px 1fr
            }

            .history-time {
                grid-column: 2;
                text-align: left
            }

            .product {
                grid-template-columns: 28px 1fr auto
            }
        }
    </style>
</head>

<body>
    <header class="top">
        <div class="brand">
            <div class="brand-mark"><img
                    src="{{ asset('imgs/nexora-logo.png') }}?v={{ filemtime(public_path('imgs/nexora-logo.png')) }}"
                    alt="Nexora"></div>
            <div>
                <p class="eyebrow">Nexora / Produccion</p>
                <h1>Historial de Cocina</h1>
            </div>
        </div>
        <a class="nav-link" href="{{ route('cocina.index') }}">&larr; Volver a pedidos</a>
    </header>
    <main class="workspace">
        <section class="hero">
            <div>
                <p class="eyebrow">Analisis de produccion</p>
                <h2>Lo que salió de cocina.</h2>
                <p>Consulta el rendimiento por producto y las comandas que ya fueron entregadas.</p>
            </div>
            <form class="filters" id="filtros">
                <label>Desde<input type="date" id="desde" value="{{ now()->toDateString() }}"></label>
                <label>Hasta<input type="date" id="hasta" value="{{ now()->toDateString() }}"></label>
                <button type="submit" id="buscar">Actualizar</button>
            </form>
        </section>
        <section class="stats">
            <article class="stat">
                <div class="stat-label">Comandas terminadas</div>
                <div class="stat-value" id="stat-comandas">0</div>
                <div class="stat-note">En el periodo seleccionado</div>
            </article>
            <article class="stat">
                <div class="stat-label">Unidades preparadas</div>
                <div class="stat-value" id="stat-unidades">0</div>
                <div class="stat-note">Productos que salieron de cocina</div>
            </article>
            <article class="stat">
                <div class="stat-label">Productos distintos</div>
                <div class="stat-value" id="stat-productos">0</div>
                <div class="stat-note">Variedad preparada</div>
            </article>
            <article class="stat">
                <div class="stat-label">Tiempo promedio</div>
                <div class="stat-value" id="stat-tiempo">0 min</div>
                <div class="stat-note">Desde comanda hasta finalizar</div>
            </article>
        </section>
        <section class="grid">
            <article class="panel">
                <div class="panel-head">
                    <h3>Productos más preparados</h3><span id="productos-etiqueta">POR CANTIDAD</span>
                </div>
                <div class="products" id="productos">
                    <div class="loading">Cargando producción...</div>
                </div>
            </article>
            <article class="panel">
                <div class="panel-head">
                    <h3>Últimas comandas finalizadas</h3><span>HASTA 50 REGISTROS</span>
                </div>
                <div class="history" id="historial">
                    <div class="loading">Cargando historial...</div>
                </div>
            </article>
        </section>
        <div class="status" id="estado"></div>
    </main>
    <script>
        const productosDestino = document.getElementById('productos');
        const historialDestino = document.getElementById('historial');
        const estado = document.getElementById('estado');
        const boton = document.getElementById('buscar');

        function escapar(valor) {
            const div = document.createElement('div');
            div.textContent = String(valor || '');
            return div.innerHTML;
        }

        function numero(valor) {
            return Number(valor || 0).toLocaleString('es-CO', {
                maximumFractionDigits: 0
            });
        }

        function fecha(valor) {
            return valor ? new Intl.DateTimeFormat('es-CO', {
                day: '2-digit',
                month: 'short',
                hour: 'numeric',
                minute: '2-digit'
            }).format(new Date(valor)) : 'Sin fecha';
        }

        function vacio(destino, titulo, texto) {
            destino.innerHTML = '<div class="empty"><div><strong>' + titulo + '</strong>' + texto + '</div></div>';
        }

        function renderizar(datos) {
            const resumen = datos.resumen || {};
            document.getElementById('stat-comandas').textContent = numero(resumen.comandas_finalizadas);
            document.getElementById('stat-unidades').textContent = numero(resumen.unidades_preparadas);
            document.getElementById('stat-productos').textContent = numero(resumen.productos_distintos);
            document.getElementById('stat-tiempo').textContent = numero(resumen.minutos_promedio) + ' min';
            const productos = datos.productos || [];
            if (!productos.length) vacio(productosDestino, 'Sin producción registrada',
                'No hay productos finalizados en este periodo.');
            else {
                const mayor = Math.max(...productos.map(p => Number(p.cantidad || 0)), 1);
                productosDestino.innerHTML = productos.map((p, index) => '<div class="product"><div class="rank">' + (
                        index + 1) + '</div><div><div class="product-name">' + escapar(p.producto) +
                    '</div><div class="bar"><i style="width:' + Math.max(4, Number(p.cantidad || 0) / mayor * 100) +
                    '%"></i></div></div><div class="quantity">' + numero(p.cantidad) +
                    '<small>unidades</small></div></div>').join('');
            }
            const historial = datos.historial || [];
            if (!historial.length) vacio(historialDestino, 'Sin comandas finalizadas',
                'El historial aparecerá cuando cocina marque pedidos como listos.');
            else historialDestino.innerHTML = historial.map(c => '<div class="history-row"><div class="table-tag">M ' +
                escapar(c.mesa) + '</div><div><div class="history-title">' + numero(c.unidades) +
                ' unidades preparadas</div><div class="history-sub">' + escapar(c.mesero) + (c.zona ? ' · ' + escapar(c
                    .zona) : '') + '</div></div><div class="history-time">' + fecha(c.finalizado_en) + '<b>' + numero(c
                    .minutos_preparacion) + ' min</b></div></div>').join('');
            estado.textContent = 'Actualizado ' + new Intl.DateTimeFormat('es-CO', {
                hour: 'numeric',
                minute: '2-digit'
            }).format(new Date());
        }

        function cargar() {
            const desde = document.getElementById('desde').value;
            const hasta = document.getElementById('hasta').value;
            boton.disabled = true;
            boton.textContent = 'Consultando...';
            estado.textContent = '';
            fetch('/cocina/historial/datos?desde=' + encodeURIComponent(desde) + '&hasta=' + encodeURIComponent(hasta), {
                    cache: 'no-store',
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(r => r.json().then(d => ({
                    ok: r.ok,
                    d
                })))
                .then(({
                    ok,
                    d
                }) => {
                    if (!ok) throw new Error(d.message || 'No se pudo consultar el historial.');
                    renderizar(d);
                })
                .catch(error => {
                    vacio(productosDestino, 'No fue posible cargar', escapar(error.message));
                    vacio(historialDestino, 'No fue posible cargar', 'Intenta nuevamente.');
                    estado.textContent = '';
                })
                .finally(() => {
                    boton.disabled = false;
                    boton.textContent = 'Actualizar';
                });
        }
        document.getElementById('filtros').addEventListener('submit', event => {
            event.preventDefault();
            cargar();
        });
        cargar();
    </script>
</body>

</html>
