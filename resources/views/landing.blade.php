<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>NussoraPos — Punto de venta, inventario y contabilidad para cualquier negocio</title>
    <meta name="description" content="NussoraPos es el sistema todo en uno para restaurantes, talleres, tiendas de repuestos y cualquier negocio en Colombia: punto de venta, inventario, contabilidad, nómina y facturación electrónica DIAN.">
    <meta name="theme-color" content="#030b3a">
    <script>document.documentElement.classList.add('js')</script>
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}?v=3">
    <link rel="stylesheet" href="{{ asset('css/landing-blue.css') }}?v=2">
</head>
<body id="inicio">
<div class="progress" id="progress" aria-hidden="true"></div>
<a class="skip-link" href="#contenido">Saltar al contenido</a>

<header class="nav" id="nav">
    <div class="wrap nav-in">
        <a href="#inicio" class="brand" aria-label="NussoraPos, inicio"><img class="mark" src="{{ asset('imgs/nussorapos-logo.png') }}" alt="" width="46" height="34"><span class="wordmark">NussoraPos</span></a>
        <button class="burger" id="burger" aria-label="Abrir menú" aria-expanded="false" aria-controls="links">☰</button>
        <nav class="links" id="links" aria-label="Navegación principal">
            <a href="#funciones">Funciones</a>
            <a href="#como-funciona">Cómo funciona</a>
            <a href="#planes">Planes</a>
            <a href="#dian">Facturación DIAN</a>
            <a href="#faq">Preguntas</a>
            <a href="{{ url('/pos') }}">Ingresar</a>
            <a href="https://wa.me/573157444356?text=Hola%2C%20quiero%20m%C3%A1s%20informaci%C3%B3n%20de%20NussoraPos" target="_blank" rel="noopener" class="btn btn-primary">Escríbenos</a>
        </nav>
    </div>
</header>

<main id="contenido">
    <!-- HERO -->
    <section class="hero">
        <div class="wrap hero-grid">
            <div>
                <span class="pill"><i></i> Para restaurantes, talleres, repuestos y más</span>
                <h1>Tu negocio.<br><em>Todo conectado.</em><br>Todo bajo control.</h1>
                <p class="lead">Del primer pedido al cierre de caja. Conecta tus ventas, inventario, caja y contabilidad en un solo lugar, con facturación electrónica DIAN.</p>
                <div class="hero-cta">
                    <a href="https://wa.me/573157444356?text=Hola%2C%20quiero%20conocer%20NussoraPos" target="_blank" rel="noopener" class="btn btn-primary">Conocer NussoraPos <span aria-hidden="true">↗</span></a>
                    <a href="#planes" class="btn btn-ghost">Ver planes y precios <span aria-hidden="true">→</span></a>
                </div>
                <div class="trust">
                    <span>Sin permanencia mínima</span>
                    <span>Instalable como app (PWA)</span>
                    <span>Capacitación incluida</span>
                </div>
            </div>

            <div class="mock" aria-hidden="true">
                <div class="win">
                    <div class="win-bar"><img class="mini-mark" src="{{ asset('imgs/nussorapos-logo.png') }}" alt="" width="30" height="22"><b>nussorapos <span>/ Punto de venta</span></b><span class="live-dot">En línea</span></div><div class="app-heading"><div><small>VISTA GENERAL</small><strong>Mostrador principal</strong></div><span>9 órdenes</span></div>
                    <div class="win-body">
                        <div class="tables">
                            <h6>Órdenes en curso <span>● En tiempo real</span></h6>
                            <div class="tb free"><b>1</b>Nueva</div>
                            <div class="tb busy"><b>2</b>$84.500</div>
                            <div class="tb busy"><b>3</b>$132.000</div>
                            <div class="tb pay"><b>4</b>Por cobrar</div>
                            <div class="tb free"><b>5</b>Nueva</div>
                            <div class="tb busy"><b>6</b>$61.200</div>
                            <div class="tb free"><b>7</b>Nueva</div>
                            <div class="tb busy"><b>8</b>$47.800</div>
                            <div class="tb pay"><b>9</b>Por cobrar</div>
                        </div>
                        <div class="ticket">
                            <h6>Orden 3 · Venta</h6>
                            <div class="row"><span>2× Aceite 15W-40</span><span>$68.000</span></div>
                            <div class="row"><span>1× Filtro de aire</span><span>$18.000</span></div>
                            <div class="row"><span>3× Bujía</span><span>$21.000</span></div>
                            <div class="row"><span>1× Mano de obra</span><span>$25.000</span></div>
                            <div class="total"><span>Total</span><b>$132.000</b></div>
                            <div class="charge">Cobrar y facturar</div>
                        </div>
                    </div>
                </div>
                <div class="float f1"><span class="ic">✓</span><div><b>Orden creada</b><small>Taller · hace 2 s</small></div></div>
                <div class="float f2"><span class="ic">✓</span><div><b>Factura DIAN emitida</b><small>FE-00482 · validada</small></div></div>
            </div>
        </div>
    </section>

    <!-- STATS -->
    <div class="stats">
        <div class="wrap stats-grid rv">
            <div class="stat"><b>1</b><span>sistema para toda la operación</span></div>
            <div class="stat"><b>En tiempo real</b><span>del mostrador a la bodega</span></div>
            <div class="stat"><b>PUC</b><span>contabilidad con plan de cuentas colombiano</span></div>
            <div class="stat"><b>DIAN</b><span>facturación electrónica integrada</span></div>
        </div>
    </div>

    <!-- PROBLEMA / SOLUCION -->
    <section class="s">
        <div class="wrap">
            <div class="head center rv">
                <p class="eyebrow">¿Qué es NussoraPos?</p>
                <h2>Un solo sistema donde todo está conectado</h2>
                <p class="sub">NussoraPos es una plataforma web, instalable en tu celular o computador, que une tus ventas y la operación diaria con el inventario y la contabilidad de tu negocio: restaurante, taller, tienda de repuestos, comercio o el que tengas.</p>
            </div>
            <div class="split">
                <div class="panel rv">
                    <h3>Sin NussoraPos</h3>
                    <ul>
                        <li>Un POS, un Excel de inventario y un software contable que no se comunican.</li>
                        <li>Pedidos y órdenes en papel que se pierden o se entienden mal.</li>
                        <li>Cierres de caja que no cuadran y nadie sabe por qué.</li>
                        <li>Contador recibiendo datos tarde, a mano y con errores.</li>
                    </ul>
                </div>
                <div class="panel dark rv">
                    <h3>Con NussoraPos</h3>
                    <ul>
                        <li>Cada venta descuenta inventario y genera su asiento contable sola.</li>
                        <li>Pedidos y órdenes enviados automáticamente al área correcta, en tiempo real.</li>
                        <li>Cierre de caja con arqueo y conteo físico, con trazabilidad.</li>
                        <li>Balance, IVA y retenciones listos sin digitar dos veces.</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- FUNCIONES -->
    <section class="s" id="funciones">
        <div class="wrap">
            <div class="head center rv">
                <p class="eyebrow">Funciones</p>
                <h2>Todo lo que tu negocio necesita</h2>
                <p class="sub">Desde tomar el pedido hasta cerrar el mes contable.</p>
            </div>
            <div class="feat-grid">
                <div class="feat rv"><div class="ficon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M6 3h12v18l-3-2-3 2-3-2-3 2V3Zm3 5h6M9 12h6"/></svg></div><h3>Punto de venta</h3><p>Ventas en mostrador, mesas o pedidos, división de cuentas, métodos de pago y comprobantes en segundos.</p><span class="chip">Básico</span></div>
                <div class="feat rv"><div class="ficon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="7" width="14" height="10" rx="2"/><path d="M8 3v4m8-4v4M8 17v4m8-4v4M2 10h3m14 0h3"/></svg></div><h3>Órdenes y mesas</h3><p>Mesas, órdenes de trabajo o pedidos en tiempo real: nuevos, en curso y por cobrar, de un solo vistazo.</p><span class="chip">Básico</span></div>
                <div class="feat rv"><div class="ficon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12a4 4 0 0 1 0-8 5 5 0 0 1 9-1 4 4 0 1 1 5 9v8H5v-8Zm0 4h14"/></svg></div><h3>Cocina, taller y despachos</h3><p>Envía pedidos y órdenes al área correcta (cocina, barra, taller o bodega), con cancelación de ítems y avisos.</p><span class="chip">Básico</span></div>
                <div class="feat rv"><div class="ficon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="m12 3 9 5v9l-9 5-9-5V8l9-5Zm-9 5 9 5 9-5m-9 5v9M7 5l9 5"/></svg></div><h3>Inventario y kardex</h3><p>Costeo promedio, existencias por bodega y reversión automática cuando se cancela un consumo.</p><span class="chip">Básico</span></div>
                <div class="feat rv"><div class="ficon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="5" width="18" height="14" rx="2"/><circle cx="12" cy="12" r="3"/><path d="M6 12h1m10 0h1"/></svg></div><h3>Cierre de caja</h3><p>Arqueo, conteo físico y control por turno y cajero. Sabrás siempre dónde está cada peso.</p><span class="chip">Básico</span></div>
                <div class="feat rv"><div class="ficon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="10" width="14" height="11" rx="2"/><path d="M8 10V7a4 4 0 0 1 8 0v3m-4 5v2"/></svg></div><h3>Usuarios y permisos</h3><p>Roles por cargo: el mesero ve lo suyo, el administrador ve todo.</p><span class="chip">Básico</span></div>
                <div class="feat rv"><div class="ficon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M12 5v16M3 4h5a4 4 0 0 1 4 2 4 4 0 0 1 4-2h5v15h-5a4 4 0 0 0-4 2 4 4 0 0 0-4-2H3V4Z"/></svg></div><h3>Contabilidad PUC</h3><p>Partida doble, balance general, estado de resultados y libros oficiales con plan de cuentas colombiano.</p><span class="chip pro">Pro</span></div>
                <div class="feat rv"><div class="ficon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M2 3h3l3 13h11l3-9H6"/><circle cx="9" cy="20" r="1"/><circle cx="18" cy="20" r="1"/></svg></div><h3>Compras y cartera</h3><p>Cuentas por pagar y por cobrar, IVA, retenciones y provisión de cartera automáticos.</p><span class="chip pro">Pro</span></div>
                <div class="feat rv"><div class="ficon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><circle cx="9" cy="7" r="4"/><path d="M2 21v-3a7 7 0 0 1 14 0v3m0-17a4 4 0 0 1 0 8m3 3a6 6 0 0 1 3 5"/></svg></div><h3>Nómina y activos fijos</h3><p>Liquida tu nómina y controla la depreciación de tus equipos y mobiliario.</p><span class="chip pro">Pro</span></div>
                <div class="feat rv"><div class="ficon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="m3 8 9-5 9 5H3Zm0 13h18M6 11v7m6-7v7m6-7v7"/></svg></div><h3>Tesorería</h3><p>Ingresos, egresos y conciliación bancaria integrados con tu contabilidad.</p><span class="chip pro">Pro</span></div>
                <div class="feat rv"><div class="ficon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M14 3H5v18h14V8l-5-5Zm0 0v5h5M8 14l3 3 5-6"/></svg></div><h3>Facturación DIAN</h3><p>Facturas, notas crédito/débito y documento soporte electrónicos vía Factus.</p><span class="chip pro">Pro</span></div>
                <div class="feat rv"><div class="ficon" aria-hidden="true"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="6" y="2" width="12" height="20" rx="3"/><path d="M10 18h4"/></svg></div><h3>App instalable</h3><p>Funciona en computador, tablet y celular. Instálala como app, sin tiendas ni descargas pesadas.</p><span class="chip">Incluido</span></div>
            </div>
        </div>
    </section>

    <!-- COMO FUNCIONA -->
    <section class="s" id="como-funciona">
        <div class="wrap">
            <div class="head center rv">
                <p class="eyebrow">Cómo funciona</p>
                <h2>De cero a vender en cuatro pasos</h2>
            </div>
            <div class="steps">
                <div class="step rv"><h3>Nos cuentas tu operación</h3><p>Sedes, áreas, cajas y equipo de trabajo.</p></div>
                <div class="step rv"><h3>Configuramos por ti</h3><p>Cargamos tus productos, servicios e inventario inicial.</p></div>
                <div class="step rv"><h3>Capacitamos a tu equipo</h3><p>Vendedores, mecánicos, cajeros y administración.</p></div>
                <div class="step rv"><h3>Vendes y controlas</h3><p>Empiezas a operar con soporte y actualizaciones continuas.</p></div>
            </div>
        </div>
    </section>

    <!-- PLANES -->
    <div class="plans-bg" id="planes">
        <div class="wrap">
            <div class="head center rv">
                <p class="eyebrow">Planes y precios</p>
                <h2>Dos formas de empezar con NussoraPos</h2>
                <p class="sub">Ambos incluyen actualizaciones continuas y una sede. Cambias de plan cuando el negocio lo pida, sin perder configuración ni historial.</p>
            </div>
            <div class="plans">
                <article class="plan rv">
                    <h3>NussoraPos Básico</h3>
                    <p class="for">Para modernizar la venta y la operación diaria de tu negocio.</p>
                    <div class="price"><b>$89.900</b><span>COP / mes</span></div>
                    <p class="pnote">Facturado mensualmente. Sin permanencia mínima.</p>
                    <ul>
                        <li>Punto de venta en mostrador, mesas o pedidos</li>
                        <li>Órdenes, mesas y pedidos en tiempo real</li>
                        <li>Envío automático de pedidos a cocina, taller o bodega</li>
                        <li>Inventario y kardex con costeo promedio</li>
                        <li>Cierre de caja con arqueo y conteo físico</li>
                        <li>Usuarios, roles y permisos por cargo</li>
                        <li>Reportes de ventas por producto y turno</li>
                    </ul>
                    <a href="https://wa.me/573157444356?text=Hola%2C%20me%20interesa%20el%20plan%20NussoraPos%20B%C3%A1sico" target="_blank" rel="noopener" class="btn btn-outline">Empezar con Básico</a>
                </article>

                <article class="plan pro rv">
                    <span class="ribbon">MÁS COMPLETO</span>
                    <h3>NussoraPos Pro</h3>
                    <p class="for">Para el negocio que además necesita su contabilidad y la DIAN al día.</p>
                    <div class="price"><b>$179.900</b><span>COP / mes</span></div>
                    <p class="pnote">Incluye todo lo de Básico.</p>
                    <ul>
                        <li class="inc">Todo lo del plan Básico</li>
                        <li>Contabilidad de partida doble con PUC colombiano</li>
                        <li>Balance general, estado de resultados y libros oficiales</li>
                        <li>IVA, retenciones y provisión de cartera automáticos</li>
                        <li>Compras, cuentas por pagar y por cobrar</li>
                        <li>Nómina y activos fijos</li>
                        <li>Tesorería: ingresos, egresos y conciliación bancaria</li>
                        <li>Facturación electrónica DIAN, vía Factus</li>
                    </ul>
                    <a href="https://wa.me/573157444356?text=Hola%2C%20me%20interesa%20el%20plan%20NussoraPos%20Pro" target="_blank" rel="noopener" class="btn btn-primary">Empezar con Pro</a>
                </article>
            </div>

            <div class="addons rv">
                <div class="ad"><div><b>Implementación, configuración y capacitación</b><small>Instalación, carga de productos e inventario inicial y entrenamiento del equipo. Pago único.</small></div><span class="amt">$900.000</span></div>
                <div class="ad"><div><b>Sede adicional</b><small>Por cada punto de venta extra bajo el mismo negocio.</small></div><span class="amt">+$59.900 /mes</span></div>
                <div class="ad"><div><b>Soporte prioritario o desarrollos a la medida</b><small>Cambios fuera del alcance estándar del plan.</small></div><span class="amt">Cotización aparte</span></div>
            </div>
        </div>
    </div>

    <!-- DIAN -->
    <section class="s" id="dian" style="padding-top:0">
        <div class="wrap">
            <div class="factus rv">
                <div>
                    <p class="eyebrow">Facturación electrónica</p>
                    <h2 style="font-size:1.9rem">Válida ante la DIAN, a tu nombre</h2>
                    <p>NussoraPos se integra con <b>Factus</b> para emitir facturas, notas crédito/débito y documentos soporte. La licencia de Factus se contrata directamente con ellos, a tu NIT; NussoraPos no la revende ni le agrega sobrecosto.</p>
                    <p style="font-size:.85rem">Requiere NIT y RUT vigentes ante la DIAN.</p>
                </div>
                <div>
                    <table>
                        <thead><tr><th>Documentos / año</th><th>Valor Factus (COP)</th></tr></thead>
                        <tbody>
                            <tr><td>150</td><td>$169.000</td></tr>
                            <tr><td>1.600</td><td>$220.000</td></tr>
                            <tr><td>5.000</td><td>$290.000</td></tr>
                            <tr><td>20.000</td><td>$490.000</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section class="s" id="faq">
        <div class="wrap">
            <div class="head center rv">
                <p class="eyebrow">Preguntas frecuentes</p>
                <h2>Lo que más nos preguntan</h2>
            </div>
            <div class="faq">
                <details class="rv"><summary>¿Hay contrato de permanencia?</summary><p>No. Los planes se facturan mensualmente y no tienen permanencia mínima.</p></details>
                <details class="rv"><summary>¿Puedo empezar con Básico y pasar a Pro después?</summary><p>Sí. El cambio se hace sin reinstalar nada y sin perder tu configuración ni tu historial.</p></details>
                <details class="rv"><summary>¿Qué equipos necesito?</summary><p>Un computador, tablet o celular con internet. Los equipos como impresora térmica, cajón de dinero o lector de código de barras no están incluidos.</p></details>
                <details class="rv"><summary>¿La facturación electrónica ya está incluida?</summary><p>La integración está incluida en el plan Pro, pero la licencia de Factus se contrata aparte, directamente por ti, según los documentos que emitas al año.</p></details>
                <details class="rv"><summary>¿Puedo manejar más de un local?</summary><p>Sí. Cada sede adicional tiene un costo de $59.900 al mes bajo el mismo negocio.</p></details>
                <details class="rv"><summary>¿Los precios incluyen IVA?</summary><p>Los precios están en pesos colombianos y no incluyen IVA si este aplica sobre el servicio.</p></details>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="cta" id="contacto">
        <div class="wrap rv">
            <h2>Lleva tu negocio al siguiente nivel</h2>
            <p>Escríbenos por WhatsApp y te ayudamos a elegir el plan ideal para tu negocio.</p>
            <div class="hero-cta">
                <a href="https://wa.me/573157444356?text=Hola%2C%20quiero%20m%C3%A1s%20informaci%C3%B3n%20de%20NussoraPos" target="_blank" rel="noopener" class="btn btn-primary">Escríbenos</a>
                @if (\Illuminate\Support\Facades\Route::has('login'))
                <a href="{{ route('login') }}" class="btn btn-ghost">Iniciar sesión</a>
                @endif
            </div>
        </div>
    </section>
</main>

<footer>
    <div class="wrap foot">
        <a href="#inicio" class="brand" aria-label="NussoraPos, inicio"><img class="mark" src="{{ asset('imgs/nussorapos-logo.png') }}" alt="" width="46" height="34"><span class="wordmark">NussoraPos</span></a>
        <span>© {{ date('Y') }} NussoraPos. Todos los derechos reservados.</span>
    </div>
</footer>

<script src="{{ asset('js/landing.js') }}?v=4" defer></script>
</body>
</html>
