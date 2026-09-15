<x-user-layout>
    @php
        $esAdministrador = (auth()->user()->rol->nombre ?? '') === 'Administrador';
        $esPlanPro = config('nexora.plan') === 'pro';
        $nombre = explode(' ', auth()->user()->name)[0];
    @endphp

    <style>
        .dashboard { max-width: 1320px; margin: 0 auto; color: #1e2b26; }
        .dash-hero { position: relative; overflow: hidden; min-height: 205px; border-radius: 20px; padding: 30px; color: #fff; background: linear-gradient(120deg, #183d31, #29624f 58%, #4e7d5c); box-shadow: 0 15px 36px rgba(23, 61, 47, .18); }
        .dash-hero:after { content: ''; position: absolute; width: 270px; height: 270px; border: 28px solid rgba(211, 232, 140, .19); border-radius: 50%; right: -75px; top: -120px; }
        .dash-hero:before { content: ''; position: absolute; width: 160px; height: 160px; background: rgba(255,255,255,.06); transform: rotate(28deg); border-radius: 28px; right: 150px; bottom: -92px; }
        .dash-eyebrow { margin: 0 0 8px; position: relative; z-index: 1; font-size: 11px; font-weight: 700; letter-spacing: .12em; color: #d6ec93; text-transform: uppercase; }
        .dash-hero h1 { position: relative; z-index: 1; margin: 0; max-width: 640px; font-size: clamp(26px, 3vw, 38px); line-height: 1.08; letter-spacing: -1.5px; }
        .dash-hero p { position: relative; z-index: 1; margin: 12px 0 0; max-width: 550px; color: #d8e6df; font-size: 14px; }
        .dash-date { position: absolute; z-index: 2; top: 25px; right: 28px; display: flex; align-items: center; gap: 8px; color: #e5efd2; font-size: 12px; }
        .dash-live { position: absolute; z-index: 2; right: 91px; bottom: 39px; color: #cfe3d7; font-size: 10px; opacity: .82; transition: opacity .25s; }
        .dash-mark { position: absolute; z-index: 2; right: 32px; bottom: 26px; width: 48px; height: 48px; border-radius: 15px; display: grid; place-items: center; background: #d4e985; color: #1f4638; font-size: 28px; font-weight: 700; }
        .metric-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; margin: 20px 0; }
        .metric { padding: 18px; border: 1px solid #e6e8e4; border-radius: 15px; background: #fff; box-shadow: 0 3px 11px rgba(24, 45, 34, .035); }
        .metric-label { display: flex; justify-content: space-between; align-items: center; color: #718078; font-size: 12px; font-weight: 600; }
        .metric-icon { width: 30px; height: 30px; border-radius: 9px; display: grid; place-items: center; font-size: 15px; background: #edf5e5; }
        .metric-value { margin-top: 14px; color: #1f2f29; font-size: 23px; font-weight: 700; letter-spacing: -1px; }
        .metric-value.is-updating, .sale-row.is-updating { animation: dataPulse .7s ease; }
        .metric-note { margin-top: 5px; color: #96a29c; font-size: 11px; }
        .cutoff-control { display: flex; align-items: center; gap: 7px; margin-top: 12px; padding-top: 11px; border-top: 1px solid #edf0ea; }
        .cutoff-control label { color: #718078; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: .04em; }
        .cutoff-control input { width: 76px; border: 1px solid #dce5da; border-radius: 7px; padding: 5px 6px; color: #244335; font: inherit; font-size: 11px; }
        .cutoff-control button { border: 0; border-radius: 7px; padding: 6px 8px; background: #2d624b; color: #fff; font-size: 10px; font-weight: 700; cursor: pointer; }
        .cutoff-control button:disabled { cursor: wait; opacity: .62; }
        .cutoff-display { margin-top: 12px; padding-top: 11px; border-top: 1px solid #edf0ea; color: #76847d; font-size: 10px; }
        .cutoff-status { min-height: 13px; margin-top: 5px; color: #4b805d; font-size: 10px; }
        .station-counts { display: flex; gap: 7px; margin-top: 9px; }
        .station-count { flex: 1; border-radius: 8px; padding: 7px 8px; background: #f4f7f2; color: #66766e; font-size: 10px; }
        .station-count strong { display: block; margin-bottom: 2px; color: #2a4d3d; font-size: 14px; }
        .dash-grid { display: grid; grid-template-columns: 1.45fr .8fr; gap: 18px; }
        .dash-panel { border: 1px solid #e5e8e3; border-radius: 17px; background: #fff; overflow: hidden; }
        .panel-head { display: flex; justify-content: space-between; align-items: center; padding: 18px 19px; border-bottom: 1px solid #eff0ed; }
        .panel-head h2 { margin: 0; font-size: 15px; letter-spacing: -.3px; } .panel-head span { color: #819087; font-size: 11px; }
        .sales-list { padding: 4px 19px 10px; } .sale-row { display: grid; grid-template-columns: 38px 1fr auto; align-items: center; gap: 10px; padding: 13px 0; border-bottom: 1px solid #f0f1ef; } .sale-row:last-child { border: 0; }
        .sale-dot { width: 33px; height: 33px; border-radius: 10px; display: grid; place-items: center; background: #f1f5ed; color: #39634d; font-size: 13px; font-weight: 700; }
        .sale-title { font-size: 12px; font-weight: 700; } .sale-sub { margin-top: 3px; color: #8a9690; font-size: 11px; } .sale-money { color: #285642; font-size: 13px; font-weight: 700; }
        .empty-sales { padding: 38px 10px; color: #89958e; text-align: center; font-size: 13px; }
        .quick-list { padding: 10px; display: grid; gap: 7px; } .quick { display: flex; align-items: center; gap: 11px; width: 100%; border: 0; border-radius: 11px; padding: 12px; background: transparent; text-align: left; cursor: pointer; transition: background .15s, transform .15s; } .quick:hover { background: #f0f5eb; transform: translateX(2px); }
        .quick-icon { width: 34px; height: 34px; display: grid; place-items: center; border-radius: 10px; background: #edf3e9; font-size: 16px; } .quick-title { color: #2c3b34; font-size: 12px; font-weight: 700; } .quick-copy { margin-top: 2px; color: #8b9690; font-size: 10px; } .quick-arrow { margin-left: auto; color: #9aa59f; font-size: 17px; }
        .dash-footer { display: flex; justify-content: space-between; gap: 14px; margin-top: 18px; padding: 14px 2px; color: #839088; font-size: 11px; }
        @keyframes dataPulse { 0% { color: #1f2f29; transform: translateY(0); } 45% { color: #5a8a46; transform: translateY(-2px); } 100% { color: #1f2f29; transform: translateY(0); } }
        @media (max-width: 900px) { .metric-grid { grid-template-columns: repeat(2, 1fr); } .dash-grid { grid-template-columns: 1fr; } }
        @media (max-width: 560px) { .dash-hero { padding: 25px 20px; min-height: 225px; } .dash-date { position: relative; top: auto; right: auto; margin-top: 16px; } .dash-mark { bottom: 20px; right: 20px; } .metric-grid { gap: 10px; } .metric { padding: 14px; } .metric-value { font-size: 19px; } .dash-footer { flex-direction: column; } }
    </style>

    <div class="dashboard">
        <section class="dash-hero">
            <p class="dash-eyebrow">Centro de control</p>
            <h1>Hola, {{ $nombre }}. Todo el negocio en una sola mirada.</h1>
            <p>Revisa el movimiento de ventas, el estado de cocina y los pendientes operativos antes de comenzar.</p>
            <div class="dash-date">{{ now()->translatedFormat('l, d \d\e F') }}</div>
            <div class="dash-live" id="dashboard-live">Actualizado ahora</div>
            <div class="dash-mark">+</div>
        </section>

        <section class="metric-grid">
            <article class="metric">
                <div class="metric-label"><span id="dash-ventas-label">Ventas del día operativo</span><span class="metric-icon">$$</span></div>
                <div class="metric-value" id="dash-ventas-hoy">$ {{ number_format($estadisticas['ventas_hoy'], 0, ',', '.') }}</div>
                <div class="metric-note" id="dash-facturas-hoy">{{ $estadisticas['facturas_hoy'] }} facturas desde las {{ $horaCorteOperativo }}</div>
                @if($esAdministrador)
                    <div class="cutoff-control">
                        <label for="hora-corte-operativo">Corte 24 h</label>
                        <input id="hora-corte-operativo" type="time" value="{{ $horaCorteOperativo }}" aria-label="Hora de inicio del día operativo">
                        <button id="guardar-hora-corte" type="button">Guardar</button>
                    </div>
                    <div class="cutoff-status" id="cutoff-status">El día operativo inicia a las {{ $horaCorteOperativo }}.</div>
                @else
                    <div class="cutoff-display" id="cutoff-status">Día operativo desde las {{ $horaCorteOperativo }}.</div>
                @endif
            </article>
            <article class="metric"><div class="metric-label"><span>Ventas del mes</span><span class="metric-icon">↑</span></div><div class="metric-value" id="dash-ventas-mes">$ {{ number_format($estadisticas['ventas_mes'], 0, ',', '.') }}</div><div class="metric-note">Acumulado del mes actual</div></article>
            <article class="metric"><div class="metric-label"><span>Pedidos activos</span><span class="metric-icon">#</span></div><div class="metric-value" id="dash-pedidos-activos">{{ $estadisticas['pedidos_activos'] }}</div><div class="metric-note" id="dash-mesas-ocupadas">{{ $estadisticas['mesas_ocupadas'] }} mesas en servicio</div></article>
            <article class="metric">
                <div class="metric-label"><span>Pedidos por preparar</span><span class="metric-icon">+</span></div>
                <div class="metric-note" style="margin-top: 14px;">Comandas pendientes por estación</div>
                <div class="station-counts">
                    <div class="station-count"><strong id="dash-cocina-total">{{ $estadisticas['comandas_pendientes'] }}</strong>Cocina</div>
                    <div class="station-count"><strong id="dash-comandas-barra">{{ $estadisticas['comandas_barra'] }}</strong>Barra</div>
                </div>
            </article>
        </section>

        <section class="dash-grid">
            <article class="dash-panel">
                <div class="panel-head"><h2>Últimas facturas</h2><button onclick="loadView('facturas')" style="border:0;background:transparent;color:#35654c;font-size:11px;font-weight:700;cursor:pointer;">Ver facturas</button></div>
                <div class="sales-list" id="dash-ultimas-facturas">
                    @forelse($ultimasFacturas as $factura)
                        <div class="sale-row">
                            <div class="sale-dot">{{ $factura->estado === 'anulada' ? '!' : '$' }}</div>
                            <div><div class="sale-title">{{ $factura->numero_factura }}</div><div class="sale-sub">Mesa {{ $factura->mesa ?? '---' }} · {{ $factura->usuario ?? 'Sin usuario' }}</div></div>
                            <div class="sale-money">$ {{ number_format($factura->total, 0, ',', '.') }}</div>
                        </div>
                    @empty
                        <div class="empty-sales">Aún no hay facturas registradas.</div>
                    @endforelse
                </div>
            </article>

            <aside class="dash-panel">
                <div class="panel-head"><h2>Accesos rápidos</h2><span>Ir a un módulo</span></div>
                <div class="quick-list">
                    <button class="quick" onclick="loadView('facturas')"><span class="quick-icon">F</span><span><span class="quick-title">Facturas</span><span class="quick-copy">Consulta ventas y documentos</span></span><span class="quick-arrow">›</span></button>
                    @if($esAdministrador)
                        <button class="quick" onclick="loadView('cierres-caja')"><span class="quick-icon">X</span><span><span class="quick-title">Cierres de caja</span><span class="quick-copy">Consulta arqueos realizados</span></span><span class="quick-arrow">›</span></button>
                    @endif
                    @if($esPlanPro)
                        <button class="quick" onclick="loadView('compras')"><span class="quick-icon">C</span><span><span class="quick-title">Compras</span><span class="quick-copy">Registra ingresos de proveedor</span></span><span class="quick-arrow">›</span></button>
                        <button class="quick" onclick="loadView('comprobantes')"><span class="quick-icon">A</span><span><span class="quick-title">Comprobantes</span><span class="quick-copy">Movimientos y contabilidad</span></span><span class="quick-arrow">›</span></button>
                    @endif
                </div>
            </aside>
        </section>

        <footer class="dash-footer"><span>AppSystem · Panel administrativo</span><span>Datos actualizados al cargar esta pantalla</span></footer>
    </div>

    <script>
        (function () {
            var actualizando = false;
            var csrf = document.querySelector('meta[name="csrf-token"]')?.content;

            function dinero(valor) {
                return '$ ' + Number(valor || 0).toLocaleString('es-CO', { maximumFractionDigits: 0 });
            }

            function textoSeguro(valor) {
                var nodo = document.createElement('span');
                nodo.textContent = String(valor || '');
                return nodo.innerHTML;
            }

            function actualizarTexto(id, valor) {
                var elemento = document.getElementById(id);
                if (!elemento || elemento.textContent === valor) return;
                elemento.textContent = valor;
                elemento.classList.remove('is-updating');
                void elemento.offsetWidth;
                elemento.classList.add('is-updating');
            }

            function renderFacturas(facturas) {
                var lista = document.getElementById('dash-ultimas-facturas');
                if (!lista) return;
                if (!facturas.length) {
                    lista.innerHTML = '<div class="empty-sales">Aún no hay facturas registradas.</div>';
                    return;
                }
                lista.innerHTML = facturas.map(function (factura) {
                    return '<div class="sale-row">' +
                        '<div class="sale-dot">' + (factura.estado === 'anulada' ? '!' : '$') + '</div>' +
                        '<div><div class="sale-title">' + textoSeguro(factura.numero_factura) + '</div>' +
                        '<div class="sale-sub">Mesa ' + textoSeguro(factura.mesa || '---') + ' · ' + textoSeguro(factura.usuario || 'Sin usuario') + '</div></div>' +
                        '<div class="sale-money">' + dinero(factura.total) + '</div></div>';
                }).join('');
            }

            function actualizarDashboard() {
                if (actualizando || document.hidden || !document.getElementById('dashboard-live')) return;
                actualizando = true;
                fetch('/dashboard/resumen', {
                    cache: 'no-store',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }
                })
                    .then(function (respuesta) {
                        if (!respuesta.ok) throw new Error('No se pudo actualizar');
                        return respuesta.json();
                    })
                    .then(function (datos) {
                        var e = datos.estadisticas || {};
                        actualizarTexto('dash-ventas-hoy', dinero(e.ventas_hoy));
                        actualizarTexto('dash-facturas-hoy', (e.facturas_hoy || 0) + ' facturas desde las ' + (datos.hora_corte_operativo || '00:00'));
                        actualizarTexto('dash-ventas-mes', dinero(e.ventas_mes));
                        actualizarTexto('dash-pedidos-activos', String(e.pedidos_activos || 0));
                        actualizarTexto('dash-mesas-ocupadas', (e.mesas_ocupadas || 0) + ' mesas en servicio');
                        actualizarTexto('dash-cocina-total', String(e.comandas_pendientes || 0));
                        actualizarTexto('dash-comandas-barra', String(e.comandas_barra || 0));
                        renderFacturas(datos.ultimas_facturas || []);
                        actualizarTexto('cutoff-status', 'El día operativo inicia a las ' + (datos.hora_corte_operativo || '00:00') + '.');
                        var horaCorte = document.getElementById('hora-corte-operativo');
                        if (horaCorte) horaCorte.value = datos.hora_corte_operativo || '00:00';
                        var estado = document.getElementById('dashboard-live');
                        if (estado) estado.textContent = 'Actualizado ahora';
                    })
                    .catch(function () {
                        var estado = document.getElementById('dashboard-live');
                        if (estado) estado.textContent = 'Esperando conexión...';
                    })
                    .finally(function () {
                        actualizando = false;
                    });
            }

            var botonHoraCorte = document.getElementById('guardar-hora-corte');
            if (botonHoraCorte) {
                botonHoraCorte.addEventListener('click', function () {
                    var campoHora = document.getElementById('hora-corte-operativo');
                    var estado = document.getElementById('cutoff-status');
                    if (!campoHora || !campoHora.value) return;

                    botonHoraCorte.disabled = true;
                    if (estado) estado.textContent = 'Guardando hora de corte...';

                    fetch('/dashboard/hora-corte', {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrf
                        },
                        body: JSON.stringify({ hora_corte_operativo: campoHora.value })
                    })
                        .then(function (respuesta) {
                            if (!respuesta.ok) throw new Error('No se pudo guardar');
                            return respuesta.json();
                        })
                        .then(function (datos) {
                            if (estado) estado.textContent = 'Guardado. El día operativo inicia a las ' + datos.hora_corte_operativo + '.';
                            actualizarDashboard();
                        })
                        .catch(function () {
                            if (estado) estado.textContent = 'No fue posible guardar la hora. Inténtalo nuevamente.';
                        })
                        .finally(function () {
                            botonHoraCorte.disabled = false;
                        });
                });
            }

            window.setInterval(actualizarDashboard, 15000);
        })();
    </script>
</x-user-layout>
