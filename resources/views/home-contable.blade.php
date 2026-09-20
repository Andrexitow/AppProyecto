<x-user-layout>
    @php
        $nombre = explode(' ', auth()->user()->name)[0];
    @endphp

    <style>
        .dashboard { max-width: 1320px; margin: 0 auto; color: #1e2233; }
        .dash-hero { position: relative; overflow: hidden; min-height: 190px; border-radius: 20px; padding: 30px; color: #fff; background: linear-gradient(120deg, #1e2761, #3d3f9e 58%, #6d5cae); box-shadow: 0 15px 36px rgba(30, 39, 97, .2); }
        .dash-hero:after { content: ''; position: absolute; width: 270px; height: 270px; border: 28px solid rgba(163, 177, 245, .18); border-radius: 50%; right: -75px; top: -120px; }
        .dash-hero:before { content: ''; position: absolute; width: 160px; height: 160px; background: rgba(255,255,255,.06); transform: rotate(28deg); border-radius: 28px; right: 150px; bottom: -92px; }
        .dash-eyebrow { margin: 0 0 8px; position: relative; z-index: 1; font-size: 11px; font-weight: 700; letter-spacing: .12em; color: #c7cffb; text-transform: uppercase; }
        .dash-hero h1 { position: relative; z-index: 1; margin: 0; max-width: 640px; font-size: clamp(24px, 3vw, 34px); line-height: 1.1; letter-spacing: -1.2px; }
        .dash-hero p { position: relative; z-index: 1; margin: 12px 0 0; max-width: 550px; color: #dbdffb; font-size: 14px; }
        .dash-date { position: absolute; z-index: 2; top: 25px; right: 28px; display: flex; align-items: center; gap: 8px; color: #e4e6fb; font-size: 12px; }
        .dash-live { position: absolute; z-index: 2; right: 28px; bottom: 22px; color: #c7cdf7; font-size: 10px; opacity: .82; }

        .metric-grid { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; margin: 20px 0; }
        .metric { padding: 18px; border: 1px solid #e6e7f0; border-radius: 15px; background: #fff; box-shadow: 0 3px 11px rgba(30, 39, 97, .035); }
        .metric-label { display: flex; justify-content: space-between; align-items: center; color: #767a94; font-size: 12px; font-weight: 600; }
        .metric-icon { width: 30px; height: 30px; border-radius: 9px; display: grid; place-items: center; font-size: 15px; background: #ecedfa; }
        .metric-value { margin-top: 14px; color: #1e2233; font-size: 23px; font-weight: 700; letter-spacing: -1px; }
        .metric-value.alerta { color: #B45309; }
        .metric-value.a-favor { color: #047857; }
        .metric-value.is-updating { animation: dataPulse .7s ease; }
        .metric-note { margin-top: 5px; color: #9598ac; font-size: 11px; }

        .dash-grid { display: grid; grid-template-columns: 1.3fr .9fr; gap: 18px; }
        .dash-panel { border: 1px solid #e5e6f0; border-radius: 17px; background: #fff; overflow: hidden; }
        .panel-head { display: flex; justify-content: space-between; align-items: center; padding: 18px 19px; border-bottom: 1px solid #eff0f7; }
        .panel-head h2 { margin: 0; font-size: 15px; letter-spacing: -.3px; }
        .panel-head span { color: #8386a0; font-size: 11px; }

        .periodo-banner { margin: 14px 19px 0; padding: 12px 14px; border-radius: 11px; font-size: 12.5px; font-weight: 600; display: flex; align-items: center; gap: 8px; }
        .periodo-banner.abierto { background: #ECFDF5; color: #065F46; }
        .periodo-banner.cerrado { background: #FEF2F2; color: #991B1B; }

        .comp-list { padding: 4px 19px 16px; }
        .comp-row { display: grid; grid-template-columns: 38px 1fr auto; align-items: center; gap: 10px; padding: 13px 0; border-bottom: 1px solid #f1f1f8; }
        .comp-row:last-child { border: 0; }
        .comp-dot { width: 33px; height: 33px; border-radius: 10px; display: grid; place-items: center; background: #f0f1f9; color: #3d3f9e; font-size: 13px; font-weight: 700; }
        .comp-title { font-size: 12px; font-weight: 700; }
        .comp-sub { margin-top: 3px; color: #9598ac; font-size: 11px; }
        .comp-badge { font-size: 10px; font-weight: 700; padding: 3px 8px; border-radius: 20px; white-space: nowrap; }
        .comp-badge.borrador { background: #FFFBEB; color: #92400E; }
        .comp-badge.contabilizado { background: #ECFDF5; color: #065F46; }
        .comp-badge.anulado { background: #F3F4F6; color: #6B7280; }
        .empty-comp { padding: 38px 10px; color: #9598ac; text-align: center; font-size: 13px; }

        .quick-list { padding: 10px; display: grid; gap: 7px; }
        .quick { display: flex; align-items: center; gap: 11px; width: 100%; border: 0; border-radius: 11px; padding: 12px; background: transparent; text-align: left; cursor: pointer; transition: background .15s, transform .15s; }
        .quick:hover { background: #f2f2fb; transform: translateX(2px); }
        .quick-icon { width: 34px; height: 34px; display: grid; place-items: center; border-radius: 10px; background: #ecedfa; font-size: 16px; }
        .quick-title { color: #262a40; font-size: 12px; font-weight: 700; }
        .quick-copy { margin-top: 2px; color: #8f92a8; font-size: 10px; }
        .quick-arrow { margin-left: auto; color: #a3a6bd; font-size: 17px; }

        .dash-footer { display: flex; justify-content: space-between; gap: 14px; margin-top: 18px; padding: 14px 2px; color: #8f92a8; font-size: 11px; }

        @keyframes dataPulse { 0% { color: #1e2233; transform: translateY(0); } 45% { color: #4746a8; transform: translateY(-2px); } 100% { color: #1e2233; transform: translateY(0); } }
        @media (max-width: 900px) { .metric-grid { grid-template-columns: repeat(2, 1fr); } .dash-grid { grid-template-columns: 1fr; } }
        @media (max-width: 560px) { .dash-hero { padding: 25px 20px; } .dash-date { position: relative; top: auto; right: auto; margin-top: 16px; } .metric-grid { gap: 10px; } .metric { padding: 14px; } .metric-value { font-size: 19px; } .dash-footer { flex-direction: column; } }
    </style>

    <div class="dashboard">
        <section class="dash-hero">
            <p class="dash-eyebrow">Centro de control contable</p>
            <h1>Hola, {{ $nombre }}. Así está la contabilidad hoy.</h1>
            <p>Cartera, cuentas por pagar, tesorería e IVA del período — todo en un vistazo antes de entrar a
                comprobantes.</p>
            <div class="dash-date">{{ now()->translatedFormat('l, d \d\e F') }}</div>
            <div class="dash-live" id="dashboard-live">Actualizado ahora</div>
        </section>

        <section class="metric-grid">
            <article class="metric">
                <div class="metric-label"><span>Cartera por cobrar</span><span class="metric-icon">↙</span></div>
                <div class="metric-value" id="dc-cartera-total">$ {{ number_format($cartera['total'], 0, ',', '.') }}</div>
                <div class="metric-note" id="dc-cartera-nota">{{ $cartera['facturas_pendientes'] }} facturas pendientes</div>
            </article>
            <article class="metric">
                <div class="metric-label"><span>Cuentas por pagar</span><span class="metric-icon">↗</span></div>
                <div class="metric-value" id="dc-porpagar-total">$ {{ number_format($por_pagar['total'], 0, ',', '.') }}</div>
                <div class="metric-note" id="dc-porpagar-nota">{{ $por_pagar['compras_pendientes'] }} compras pendientes</div>
            </article>
            <article class="metric">
                <div class="metric-label"><span>Saldo en tesorería</span><span class="metric-icon">🏦</span></div>
                <div class="metric-value" id="dc-tesoreria-total">$ {{ number_format($tesoreria['saldo_total'], 0, ',', '.') }}</div>
                <div class="metric-note">Suma de cajas y bancos activos</div>
            </article>
            <article class="metric">
                <div class="metric-label"><span>IVA del período</span><span class="metric-icon">%</span></div>
                <div class="metric-value {{ $iva['aPagar'] ? 'alerta' : ($iva['saldoAFavor'] ? 'a-favor' : '') }}"
                    id="dc-iva-valor">$ {{ number_format($iva['valorAbsoluto'], 0, ',', '.') }}</div>
                <div class="metric-note" id="dc-iva-nota">
                    {{ $iva['aPagar'] ? 'A pagar este mes' : ($iva['saldoAFavor'] ? 'Saldo a favor' : 'Sin movimiento') }}
                </div>
            </article>
        </section>

        <section class="dash-grid">
            <article class="dash-panel">
                <div class="panel-head">
                    <h2>Últimos comprobantes</h2>
                    <button onclick="loadView('comprobantes')" style="border:0;background:transparent;color:#3d3f9e;font-size:11px;font-weight:700;cursor:pointer;">Ver comprobantes</button>
                </div>

                <div class="periodo-banner {{ $periodo['cerrado'] ? 'cerrado' : 'abierto' }}" id="dc-periodo-banner">
                    {{ $periodo['cerrado'] ? '🔒' : '🔓' }}
                    <span id="dc-periodo-texto">
                        @if ($periodo['cerrado'])
                            Período contable de hoy <strong>CERRADO</strong>{{ $periodo['nombre'] ? " ({$periodo['nombre']})" : '' }} — no se pueden registrar comprobantes sin reabrirlo.
                        @else
                            Período contable de hoy <strong>ABIERTO</strong>{{ $periodo['nombre'] ? " ({$periodo['nombre']})" : '' }}.
                        @endif
                    </span>
                </div>

                <div class="comp-list" id="dc-comprobantes-lista">
                    @forelse ($comprobantes['ultimos'] as $c)
                        <div class="comp-row">
                            <div class="comp-dot">{{ strtoupper(substr($c['tipo'] ?? 'C', 0, 1)) }}</div>
                            <div>
                                <div class="comp-title">{{ $c['numero'] }} · {{ $c['tipo'] }}</div>
                                <div class="comp-sub">{{ $c['fecha'] }} · {{ $c['usuario'] ?? 'Sin usuario' }}</div>
                            </div>
                            <span class="comp-badge {{ strtolower($c['estado']) }}">{{ ucfirst(strtolower($c['estado'])) }}</span>
                        </div>
                    @empty
                        <div class="empty-comp">Aún no hay comprobantes registrados.</div>
                    @endforelse
                </div>
            </article>

            <aside class="dash-panel">
                <div class="panel-head"><h2>Accesos rápidos</h2><span>Ir a un módulo</span></div>
                <div class="quick-list">
                    <button class="quick" onclick="loadView('comprobantes')"><span class="quick-icon">A</span><span><span class="quick-title">Comprobantes</span><span class="quick-copy">{{ $comprobantes['registrados_hoy'] }} hoy · {{ $comprobantes['borrador'] }} en borrador</span></span><span class="quick-arrow">›</span></button>
                    <button class="quick" onclick="loadView('cuentas-por-cobrar')"><span class="quick-icon">$</span><span><span class="quick-title">Cuentas por cobrar</span><span class="quick-copy">Cartera de clientes</span></span><span class="quick-arrow">›</span></button>
                    <button class="quick" onclick="loadView('cuentas-por-pagar')"><span class="quick-icon">$</span><span><span class="quick-title">Cuentas por pagar</span><span class="quick-copy">Deuda con proveedores</span></span><span class="quick-arrow">›</span></button>
                    <button class="quick" onclick="loadView('tesoreria')"><span class="quick-icon">🏦</span><span><span class="quick-title">Tesorería</span><span class="quick-copy">Caja, bancos y transferencias</span></span><span class="quick-arrow">›</span></button>
                    <button class="quick" onclick="loadView('periodos-contables')"><span class="quick-icon">🔒</span><span><span class="quick-title">Períodos contables</span><span class="quick-copy">Abrir / cerrar períodos</span></span><span class="quick-arrow">›</span></button>
                    <button class="quick" onclick="loadView('informes-contables')"><span class="quick-icon">📊</span><span><span class="quick-title">Informes contables</span><span class="quick-copy">Balance, resultados, IVA</span></span><span class="quick-arrow">›</span></button>
                </div>
            </aside>
        </section>

        <footer class="dash-footer"><span>Nexora · Panel contable</span><span>Datos actualizados al cargar esta pantalla</span></footer>
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

            function renderComprobantes(lista) {
                var cont = document.getElementById('dc-comprobantes-lista');
                if (!cont) return;
                if (!lista.length) {
                    cont.innerHTML = '<div class="empty-comp">Aún no hay comprobantes registrados.</div>';
                    return;
                }
                cont.innerHTML = lista.map(function (c) {
                    var inicial = (c.tipo || 'C').substring(0, 1).toUpperCase();
                    var estado = (c.estado || '').toLowerCase();
                    return '<div class="comp-row">' +
                        '<div class="comp-dot">' + textoSeguro(inicial) + '</div>' +
                        '<div><div class="comp-title">' + textoSeguro(c.numero) + ' · ' + textoSeguro(c.tipo) + '</div>' +
                        '<div class="comp-sub">' + textoSeguro(c.fecha) + ' · ' + textoSeguro(c.usuario || 'Sin usuario') + '</div></div>' +
                        '<span class="comp-badge ' + estado + '">' + textoSeguro(c.estado ? c.estado.charAt(0) + c.estado.slice(1).toLowerCase() : '') + '</span></div>';
                }).join('');
            }

            function actualizarDashboard() {
                if (actualizando || document.hidden || !document.getElementById('dashboard-live')) return;
                actualizando = true;
                fetch('/dashboard-contable/resumen', {
                    cache: 'no-store',
                    headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrf }
                })
                    .then(function (respuesta) {
                        if (!respuesta.ok) throw new Error('No se pudo actualizar');
                        return respuesta.json();
                    })
                    .then(function (datos) {
                        actualizarTexto('dc-cartera-total', dinero(datos.cartera.total));
                        actualizarTexto('dc-cartera-nota', datos.cartera.facturas_pendientes + ' facturas pendientes');
                        actualizarTexto('dc-porpagar-total', dinero(datos.por_pagar.total));
                        actualizarTexto('dc-porpagar-nota', datos.por_pagar.compras_pendientes + ' compras pendientes');
                        actualizarTexto('dc-tesoreria-total', dinero(datos.tesoreria.saldo_total));

                        var ivaValor = document.getElementById('dc-iva-valor');
                        if (ivaValor) {
                            actualizarTexto('dc-iva-valor', dinero(datos.iva.valorAbsoluto));
                            ivaValor.className = 'metric-value' + (datos.iva.aPagar ? ' alerta' : (datos.iva.saldoAFavor ? ' a-favor' : ''));
                        }
                        actualizarTexto('dc-iva-nota', datos.iva.aPagar ? 'A pagar este mes' : (datos.iva.saldoAFavor ? 'Saldo a favor' : 'Sin movimiento'));

                        var banner = document.getElementById('dc-periodo-banner');
                        if (banner) {
                            banner.className = 'periodo-banner ' + (datos.periodo.cerrado ? 'cerrado' : 'abierto');
                            var nombreParte = datos.periodo.nombre ? ' (' + textoSeguro(datos.periodo.nombre) + ')' : '';
                            document.getElementById('dc-periodo-texto').innerHTML = datos.periodo.cerrado
                                ? 'Período contable de hoy <strong>CERRADO</strong>' + nombreParte + ' — no se pueden registrar comprobantes sin reabrirlo.'
                                : 'Período contable de hoy <strong>ABIERTO</strong>' + nombreParte + '.';
                            banner.firstChild && (banner.firstChild.textContent = datos.periodo.cerrado ? '🔒' : '🔓');
                        }

                        renderComprobantes(datos.comprobantes.ultimos || []);

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

            window.setInterval(actualizarDashboard, 30000);
        })();
    </script>
</x-user-layout>
