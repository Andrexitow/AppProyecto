// facturacion.js — sin Vite, carga tradicional con defer

// ============================================================
// ESTADO GLOBAL — todo en window desde el inicio
// ============================================================
window.ticket = [];
window.mesaSeleccionadaId = null;
window.idProductoAEliminar = null;
window.accionConfirmar = null;
// true mientras el selector de mesas está abierto para decidir a dónde
// enviar un pedido ya armado (ver enviarPedido/abrirSelectorMesas) — en
// ese modo, elegir una mesa envía el pedido en vez de solo "entrar" a ella.
window.modoEnvioPedido = false;

console.log('%c✅ facturacion.js cargado', 'color: green; font-weight: bold;');

// ============================================================
// DOM READY — con defer el DOM ya existe aquí, pero lo
// envolvemos igual por seguridad
// ============================================================
document.addEventListener('DOMContentLoaded', function () {
    console.log('%c🧾 POS inicializado', 'color: #4f46e5; font-weight: bold;');

    // Buscador de productos
    var input = document.getElementById('buscarProducto');
    if (input) {
        input.addEventListener('input', function (e) {
            var q = e.target.value.toLowerCase();
            document.querySelectorAll('.item-producto').forEach(function (el) {
                var n = el.getAttribute('data-nombre') || '';
                el.style.display = n.includes(q) ? '' : 'none';
            });
        });
    }

    // Botón confirmar del modal de confirmación
    var btnConfirmar = document.getElementById('btnConfirmarAccion');
    if (btnConfirmar) {
        btnConfirmar.addEventListener('click', function () {
            if (window.accionConfirmar) {
                window.accionConfirmar();
                window.cerrarConfirm();
            }
        });
    }

    // Refresco automático de mesas cada 5 segundos
    setInterval(refrescarMesas, 5000);
});

// ============================================================
// HELPERS DE MODALES (llamados desde el blade con onclick)
// ============================================================
window.abrirSelectorMesas = function () {
    // Si ya hay productos nuevos armados y todavía no hay mesa, abrir el
    // selector significa "elegir a dónde enviar este pedido" — sin esto,
    // tomar/entrar a una mesa normal (seleccionarMesa/cargarPedidoExistente)
    // borraría el ticket que el mesero ya armó.
    var tieneNuevos = window.ticket.some(function (i) { return !i.existente; });
    if (tieneNuevos && !window.mesaSeleccionadaId) {
        window.modoEnvioPedido = true;
    }

    var m = document.getElementById('modalMesas');
    if (m) { m.classList.remove('hidden'); m.classList.add('flex'); }
};

window.cerrarSelectorMesas = function () {
    var m = document.getElementById('modalMesas');
    if (m) { m.classList.add('hidden'); m.classList.remove('flex'); }
    window.modoEnvioPedido = false;
};

// ============================================================
// MESAS — DISPATCH DE CLICK (llamado desde la cuadrícula de mesas)
// ============================================================
// Centraliza qué hacer al hacer clic en una mesa: en modo normal, "tomar"
// una mesa libre o entrar a una que ya tiene pedido; en modo envío
// (ver abrirSelectorMesas), en cambio, cualquier mesa elegida es el
// destino del pedido ya armado.
window.manejarClickMesa = function (id, numero, tipo) {
    if (window.modoEnvioPedido) {
        window.confirmarEnvioAMesa(id, numero, tipo);
        return;
    }

    if (tipo === 'disponible') {
        window.seleccionarMesa(id, numero);
    } else {
        window.cargarPedidoExistente(id, numero);
    }
};

// Envía el pedido ya armado (window.ticket) a la mesa elegida en el
// selector. Si la mesa está libre, primero la bloquea (para que quede
// asignada al mesero como cualquier mesa tomada normalmente); si ya
// tiene un pedido propio/de la misma caja, se agrega directo a ese
// pedido (el backend hace firstOrCreate por mesa_id).
window.confirmarEnvioAMesa = async function (id, numero, tipo) {
    if (tipo === 'disponible') {
        try {
            var res = await fetch('/mesas/' + id + '/bloquear', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
            var data = await res.json();
            if (!res.ok || data.status !== 'success') {
                window.notificar(data.message || 'No se pudo tomar la mesa', 'error');
                return;
            }
        } catch (e) {
            window.notificar(e.message || 'Error de conexión', 'error');
            return;
        }
    }

    window.mesaSeleccionadaId = id;
    window.cerrarSelectorMesas();

    var lA = document.getElementById('mesa-activa-label');
    var lM = document.getElementById('mesa-label');
    if (lA) lA.textContent = numero;
    if (lM) lM.textContent = 'Mesa: ' + numero;

    await enviarPedidoAMesa(id);
};

window.cerrarConfirm = function () {
    var m = document.getElementById('modalConfirm');
    if (m) { m.classList.add('hidden'); m.classList.remove('flex'); }
    window.accionConfirmar = null;
};

window.cerrarSuperClave = function () {
    var m = document.getElementById('modalSuperClave');
    if (m) { m.classList.add('hidden'); m.classList.remove('flex'); }
    var inp = document.getElementById('inputSuperClave');
    if (inp) inp.value = '';
    window.idProductoAEliminar = null;
};

window.abrirTicketMovil = function () {
    if (window.innerWidth < 768) {
        var p = document.getElementById('panel-ticket');
        var b = document.getElementById('ticket-backdrop');
        if (p) p.classList.add('ticket-open');
        if (b) b.classList.add('open');
    }
};

window.cerrarTicketMovil = function () {
    var p = document.getElementById('panel-ticket');
    var b = document.getElementById('ticket-backdrop');
    if (p) p.classList.remove('ticket-open');
    if (b) b.classList.remove('open');
};

// Menú "más acciones" del header móvil (Imprimir inventario / Arqueo y
// cierre) — en escritorio esas 2 acciones viven en el nav lateral, que se
// oculta entero en móvil (pos-nav es "hidden md:flex"), así que sin este
// menú el cajero no tenía cómo llegar a ellas desde el celular.
window.toggleMenuMovilPOS = function (evento) {
    if (evento) evento.stopPropagation();
    var menu = document.getElementById('menu-mas-acciones-pos');
    if (menu) menu.classList.toggle('hidden');
};

window.cerrarMenuMovilPOS = function () {
    var menu = document.getElementById('menu-mas-acciones-pos');
    if (menu) menu.classList.add('hidden');
};

document.addEventListener('click', function (evento) {
    var menu = document.getElementById('menu-mas-acciones-pos');
    var boton = document.getElementById('btn-mas-acciones-pos');
    if (!menu || menu.classList.contains('hidden')) return;
    if (evento.target === boton || boton?.contains(evento.target)) return;
    if (!menu.contains(evento.target)) menu.classList.add('hidden');
});

window.actualizarBadgeTicket = function (cantidad) {
    var badge = document.getElementById('ticket-badge');
    if (!badge) return;
    if (cantidad > 0) {
        badge.textContent = cantidad > 9 ? '9+' : cantidad;
        badge.classList.remove('hidden');
    } else {
        badge.classList.add('hidden');
    }
};

// ============================================================
// TICKET — AGREGAR
// ============================================================
window.agregarAlTicket = function (id, descripcion, precio, acompanamientoGrupoId) {
    // La mesa ya no se elige antes de comandar: se arma el ticket libremente
    // y solo al pulsar "Enviar pedido" se pide la mesa (ver enviarPedido).

    // Un producto con acompañamiento (ej. Cubetazo Mix) no se apila con "+1"
    // como los demás: cada uno necesita su propio reparto, así que primero
    // se abre el modal y solo al confirmar se agrega como línea nueva.
    if (acompanamientoGrupoId) {
        abrirModalAcompanamientoPos(id, descripcion, precio, acompanamientoGrupoId);
        return;
    }

    var pendiente = window.ticket.find(function (i) { return i.id === id && !i.existente; });

    if (pendiente) {
        pendiente.cantidad++;
    } else {
        window.ticket.push({ id: id, descripcion: descripcion, precio: precio, cantidad: 1, observacion: '', existente: false });
    }

    renderizarTicket();
    var total = window.ticket.reduce(function (a, i) { return a + i.cantidad; }, 0);
    window.actualizarBadgeTicket(total);
};

// ============================================================
// ACOMPAÑAMIENTO — modal de reparto libre (ej. Cubetazo Mix)
// ============================================================
var acompPosEstado = { id: null, descripcion: '', precio: 0, grupoId: null, maximo: 0, opciones: [] };

function abrirModalAcompanamientoPos(id, descripcion, precio, grupoId) {
    fetch('/acompanamientos/' + grupoId + '/opciones')
        .then(function (r) { return r.json(); })
        .then(function (data) {
            acompPosEstado = { id: id, descripcion: descripcion, precio: precio, grupoId: grupoId, maximo: data.grupo.cantidad_maxima, opciones: data.opciones };

            document.getElementById('acomp-pos-titulo').textContent = descripcion;
            document.getElementById('acomp-pos-max').textContent = data.grupo.cantidad_maxima;
            document.getElementById('acomp-pos-max-2').textContent = data.grupo.cantidad_maxima;

            var lista = document.getElementById('acomp-pos-lista');
            if (!data.opciones.length) {
                lista.innerHTML = '<p class="text-[11px] text-slate-500">Este grupo todavía no tiene productos configurados. Pide a un administrador que los agregue en "Acompañamientos".</p>';
            } else {
                lista.innerHTML = data.opciones.map(function (p) {
                    return '<div class="flex items-center justify-between p-2.5 rounded-lg" style="background:#1a2235;border:0.5px solid #283347;">' +
                        '<span class="text-[12px] text-white">' + p.descripcion + '</span>' +
                        '<div class="flex items-center gap-2">' +
                        '<button onclick="cambiarCantidadAcompPos(' + p.id + ', -1)" class="w-6 h-6 rounded flex items-center justify-center font-bold" style="background:#283347;color:#fff;">−</button>' +
                        '<span id="acomp-pos-cant-' + p.id + '" class="text-[13px] font-bold w-6 text-center" style="color:#fff;">0</span>' +
                        '<button onclick="cambiarCantidadAcompPos(' + p.id + ', 1)" class="w-6 h-6 rounded flex items-center justify-center font-bold" style="background:#283347;color:#fff;">+</button>' +
                        '</div></div>';
                }).join('');
            }

            actualizarTotalAcompPos();
            document.getElementById('modalAcompanamientoPos').classList.add('show');
        })
        .catch(function (e) { window.notificar(e.message || 'No se pudo cargar el acompañamiento', 'error'); });
}

window.cerrarModalAcompanamientoPos = function () {
    document.getElementById('modalAcompanamientoPos').classList.remove('show');
};

window.cambiarCantidadAcompPos = function (productoId, delta) {
    var span = document.getElementById('acomp-pos-cant-' + productoId);
    if (!span) return;
    var actual = parseInt(span.textContent, 10) || 0;
    var totalActual = sumaAcompPos();

    if (delta > 0 && totalActual >= acompPosEstado.maximo) return; // ya está en el máximo
    var nuevo = Math.max(0, actual + delta);
    span.textContent = nuevo;
    actualizarTotalAcompPos();
};

function sumaAcompPos() {
    return acompPosEstado.opciones.reduce(function (acc, p) {
        var span = document.getElementById('acomp-pos-cant-' + p.id);
        return acc + (span ? (parseInt(span.textContent, 10) || 0) : 0);
    }, 0);
}

function actualizarTotalAcompPos() {
    var total = sumaAcompPos();
    var elTotal = document.getElementById('acomp-pos-total');
    if (elTotal) elTotal.textContent = total;
}

window.confirmarAcompanamientoPos = function () {
    var reparto = acompPosEstado.opciones
        .map(function (p) {
            var span = document.getElementById('acomp-pos-cant-' + p.id);
            var cantidad = span ? (parseInt(span.textContent, 10) || 0) : 0;
            return { producto_id: p.id, cantidad: cantidad };
        })
        .filter(function (l) { return l.cantidad > 0; });

    var total = reparto.reduce(function (a, l) { return a + l.cantidad; }, 0);

    if (total < 1) {
        window.notificar('Elige al menos 1 unidad', 'warning');
        return;
    }
    if (total > acompPosEstado.maximo) {
        window.notificar('No puedes pasar de ' + acompPosEstado.maximo + ' unidades', 'error');
        return;
    }

    var resumen = reparto.map(function (l) {
        var opcion = acompPosEstado.opciones.find(function (p) { return p.id === l.producto_id; });
        return l.cantidad + ' ' + (opcion ? opcion.descripcion : '?');
    }).join(', ');

    window.ticket.push({
        id: acompPosEstado.id,
        descripcion: acompPosEstado.descripcion,
        precio: acompPosEstado.precio,
        cantidad: 1,
        observacion: '',
        existente: false,
        acompanamiento: reparto,
        acompanamientoResumen: resumen,
    });

    renderizarTicket();
    var totalTicket = window.ticket.reduce(function (a, i) { return a + i.cantidad; }, 0);
    window.actualizarBadgeTicket(totalTicket);
    cerrarModalAcompanamientoPos();
};

// ============================================================
// TICKET — ELIMINAR
// ============================================================
window.eliminarDelTicket = function (index) {
    var item = window.ticket[index];
    if (!item) return;

    if (item.existente) {
        window.idProductoAEliminar = item.id;
        window.indexProductoAEliminar = index;
        var m = document.getElementById('modalSuperClave');
        if (m) { m.classList.remove('hidden'); m.classList.add('flex'); }
        var inp = document.getElementById('inputSuperClave');
        if (inp) inp.focus();
    } else {
        window.ticket.splice(index, 1);
        renderizarTicket();
    }
};

// ============================================================
// TICKET — CANTIDAD Y OBSERVACIÓN
// ============================================================
window.cambiarCantidad = function (index, delta) {
    var item = window.ticket[index];
    if (!item) return;

    if (item.existente) {
        window.notificar('Ya fue enviado a cocina, no se puede cambiar la cantidad', 'warning');
        return;
    }

    if (item.cantidad + delta > 0) {
        item.cantidad += delta;
        renderizarTicket();
    }
};

window.actualizarObservacion = function (index, valor) {
    if (window.ticket[index]) window.ticket[index].observacion = valor;
};

// ============================================================
// TICKET — RENDER
// ============================================================
function renderizarTicket() {
    var contenedor = document.getElementById('ticket-items');
    if (!contenedor) return;

    if (window.ticket.length === 0) {
        contenedor.innerHTML = '<div class="text-center py-12 md:py-20"><p class="text-slate-600 text-xs font-bold uppercase tracking-tighter">Selecciona productos</p></div>';
        actualizarTotales();
        return;
    }

    contenedor.innerHTML = window.ticket.map(function (item, index) {
        // Un producto con acompañamiento va siempre en cantidad 1 (el
        // reparto ya define "cuánto hay" dentro de esa unidad) — no se
        // puede subir con "+", cada mezcla nueva es una línea aparte.
        var cantidadBloqueada = item.existente || !!item.acompanamiento;
        var bloqueado = cantidadBloqueada ? 'opacity-30 cursor-not-allowed pointer-events-none' : '';
        var disabledAttr = cantidadBloqueada ? 'disabled' : '';
        var readonly = item.existente ? 'readonly' : '';
        var borde = item.existente ? 'border-emerald-500/30' : 'border-slate-700/50';
        var tag = item.existente ? '<span class="text-[8px] text-emerald-400 border border-emerald-400 px-1 rounded ml-1">ENVIADO</span>' : '';
        var focusClass = item.existente ? 'opacity-50 cursor-not-allowed' : 'focus:border-indigo-500';
        var nombre = item.descripcion || item.nombre || '';
        var resumenAcomp = item.acompanamientoResumen
            ? '<p class="text-[9px] text-amber-400 font-bold mt-0.5">🍹 ' + item.acompanamientoResumen + '</p>'
            : '';

        return (
            '<div class="bg-slate-800/40 p-4 rounded-3xl border ' + borde + ' mb-3">' +
            '<div class="flex justify-between items-start mb-3">' +
            '<div class="flex-1">' +
            '<p class="text-xs font-black text-white uppercase leading-tight">' + nombre + ' ' + tag + '</p>' +
            resumenAcomp +
            '<p class="text-[10px] text-indigo-400 font-bold">$' + (item.precio * item.cantidad).toLocaleString() + '</p>' +
            '</div>' +
            '<button onclick="window.eliminarDelTicket(' + index + ')" class="text-slate-500 hover:text-red-500 transition-colors">' +
            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2"></path></svg>' +
            '</button>' +
            '</div>' +
            '<div class="flex items-center gap-4">' +
            '<div class="flex items-center bg-slate-900 rounded-xl p-1 border border-slate-700">' +
            '<button ' + disabledAttr + ' onclick="window.cambiarCantidad(' + index + ',-1)" class="w-7 h-7 flex items-center justify-center text-white rounded-lg ' + bloqueado + '">-</button>' +
            '<span class="w-8 text-center text-xs font-bold text-indigo-400">' + item.cantidad + '</span>' +
            '<button ' + disabledAttr + ' onclick="window.cambiarCantidad(' + index + ',1)" class="w-7 h-7 flex items-center justify-center text-white rounded-lg ' + bloqueado + '">+</button>' +
            '</div>' +
            '<div class="flex-1">' +
            '<input type="text" placeholder="Nota..." value="' + (item.observacion || '') + '" ' + readonly +
            ' onchange="window.actualizarObservacion(' + index + ', this.value)"' +
            ' class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-3 py-2 text-[10px] text-slate-300 focus:outline-none ' + focusClass + '">' +
            '</div>' +
            '</div>' +
            '</div>'
        );
    }).join('');

    actualizarTotales();
}

function actualizarTotales() {

    var subtotal = window.ticket.reduce(function (a, i) {
        return a + (i.precio * i.cantidad);
    }, 0);

    var total = subtotal;

    var s = document.getElementById('subtotal-val');
    var t = document.getElementById('total-val');

    if (s) s.innerText = '$' + subtotal.toLocaleString('es-CO');
    if (t) t.innerText = '$' + total.toLocaleString('es-CO');
}

// ============================================================
// MESAS — SELECCIONAR (única definición, no duplicar en blade)
// ============================================================
window.seleccionarMesa = async function (id, nombre) {
    console.log('Seleccionando mesa:', id, nombre);
    window.ticket = [];
    renderizarTicket();

    try {
        var res = await fetch('/mesas/' + id + '/bloquear', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });
        var data = await res.json();

        if (res.ok && data.status === 'success') {
            window.mesaSeleccionadaId = id;
            window.clienteSeleccionado = null;
            var lA = document.getElementById('mesa-activa-label');
            var lM = document.getElementById('mesa-label');
            var elCliente = document.getElementById('cliente-nombre-ticket');
            if (elCliente) elCliente.textContent = 'Consumidor final';
            if (lA) lA.textContent = nombre;
            if (lM) lM.textContent = 'Mesa: ' + nombre;
            window.cerrarSelectorMesas();
            window.notificar('Mesa ' + nombre + ' seleccionada', 'success');
        } else {
            window.notificar(data.message || 'Error al seleccionar la mesa', 'error');
        }
    } catch (e) {
        console.error(e);
        window.notificar(e.message || 'Error de conexión', 'error');
    }
};

// ============================================================
// MESAS — LIBERAR
// ============================================================
window.liberarMesaActual = async function (id) {
    var mesaId = id || window.mesaSeleccionadaId;
    if (!mesaId) { window.notificar('No hay mesa activa', 'warning'); return; }

    try {
        var res = await fetch('/mesas/' + mesaId + '/liberar', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        });
        var data = await res.json();

        if (data.status === 'success') {
            window.mesaSeleccionadaId = null;
            window.ticket = [];
            window.clienteSeleccionado = null;
            var elCliente = document.getElementById('cliente-nombre-ticket');
            var lA = document.getElementById('mesa-activa-label');
            var lM = document.getElementById('mesa-label');
            if (elCliente) elCliente.textContent = 'Consumidor final';
            if (lA) lA.textContent = 'MESA';
            if (lM) lM.textContent = 'Mesa: --';
            renderizarTicket();
            window.actualizarBadgeTicket(0);
            await refrescarMesas();
            window.notificar('Mesa liberada', 'success');
        }
    } catch (e) {
        window.notificar(e.message || 'Error al liberar mesa', 'error');
    }
};

// ============================================================
// MESAS — FILTRAR
// ============================================================
window.filtrarMesasPorZona = function () {
    var fZ = document.getElementById('filtroZona');
    var fB = document.getElementById('buscarMesa');
    if (!fZ || !fB) return;
    var zona = fZ.value;
    var texto = fB.value.toLowerCase();
    document.querySelectorAll('.mesa-item').forEach(function (m) {
        var zOk = zona === 'todas' || (m.getAttribute('data-zona') || '') === zona;
        var nOk = (m.getAttribute('data-numero') || '').toLowerCase().includes(texto);
        m.style.display = (zOk && nOk) ? '' : 'none';
    });
};

// ============================================================
// MESAS — REFRESCO AUTOMÁTICO
// ============================================================
async function refrescarMesas() {
    if (!window.mesaSeleccionadaId) { await ejecutarRefrescoVisual(); return; }

    try {
        var res = await fetch('/mesas/actualizar');
        // Este refresco corre solo cada 5s (ver setInterval más abajo). Si
        // la sesión expiró mientras la pantalla de mesas estaba abierta,
        // Laravel redirige esta petición a /login — sin este chequeo, la
        // página de login completa quedaba insertada dentro de la
        // cuadrícula de mesas cada 5 segundos, encimada sobre lo que ya
        // había en pantalla.
        if (res.redirected && res.url.indexOf('/login') !== -1) {
            window.location.href = res.url;
            return;
        }
        var html = await res.text();
        var tmp = document.createElement('div');
        tmp.innerHTML = html;

        // Si la mesa vuelve a aparecer como disponible, expiró en servidor
        // (el onclick de cada mesa ahora es manejarClickMesa(id, numero, tipo);
        // 'disponible' es el tipo que se usa solo para mesas libres).
        if (tmp.querySelector('[onclick*="manejarClickMesa(' + window.mesaSeleccionadaId + ',"][onclick*="\'disponible\'"]')) {
            window.mesaSeleccionadaId = null;
            window.clienteSeleccionado = null;
            var elCliente = document.getElementById('cliente-nombre-ticket');
            var lA = document.getElementById('mesa-activa-label');
            var lM = document.getElementById('mesa-label');
            if (elCliente) elCliente.textContent = 'Consumidor final';
            if (lA) lA.textContent = 'MESA';
            if (lM) lM.textContent = 'Mesa: --';
            window.notificar('La sesión de mesa expiró', 'warning');
        }

        var cont = document.getElementById('contenedorMesas');
        if (cont) cont.innerHTML = html;
        window.filtrarMesasPorZona();
    } catch (e) {
        console.error('Error refrescando mesas:', e);
    }
}

async function ejecutarRefrescoVisual() {
    try {
        var res = await fetch('/mesas/actualizar');
        if (res.redirected && res.url.indexOf('/login') !== -1) {
            window.location.href = res.url;
            return;
        }
        var html = await res.text();
        var cont = document.getElementById('contenedorMesas');
        if (cont) cont.innerHTML = html;
        window.filtrarMesasPorZona();
    } catch (e) {
        console.error('Error refresco visual:', e);
    }
}

// ============================================================
// ENVIAR PEDIDO
// ============================================================
// El pedido ya no requiere mesa seleccionada de antemano: primero se
// arma el ticket con los productos y, al pulsar "Enviar pedido", si
// todavía no hay mesa asignada se abre el selector (en modo envío) para
// elegir a dónde va — confirmarEnvioAMesa termina el envío desde ahí.
window.enviarPedido = async function () {
    var itemsNuevos = window.ticket.filter(function (item) { return !item.existente; });
    if (!itemsNuevos.length) { window.notificar('No hay productos nuevos para enviar', 'warning'); return; }

    if (!window.mesaSeleccionadaId) {
        window.notificar('Selecciona la mesa para enviar el pedido', 'info');
        window.abrirSelectorMesas();
        return;
    }

    await enviarPedidoAMesa(window.mesaSeleccionadaId);
};

async function enviarPedidoAMesa(mesaId) {
    var itemsNuevos = window.ticket.filter(function (item) { return !item.existente; });
    if (!itemsNuevos.length) { window.notificar('No hay productos nuevos para enviar', 'warning'); return; }

    try {
        var res = await fetch('/pedidos/guardar', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                mesa_id: mesaId,
                cliente_id: (window.clienteSeleccionado && window.clienteSeleccionado.id) || null, // ← AGREGAR ESTA LÍNEA
                items: itemsNuevos.map(function (item) {
                    return {
                        id: item.id,
                        nombre: item.descripcion || item.nombre || '',
                        descripcion: item.descripcion || item.nombre || '',
                        precio: item.precio,
                        cantidad: item.cantidad,
                        observacion: item.observacion || '',
                        acompanamiento: item.acompanamiento || undefined
                    };
                })
            })
        });

        var data = await res.json();

        if (res.ok && data.status === 'success') {

            // ✅ MENSAJE DINÁMICO: Si el PrintService devuelve los destinos, los mostramos.
            // Si no, dejamos el mensaje por defecto.
            var mensajeExito = '¡Pedido enviado correctamente!';
            if (data.impresion && data.impresion.destinos) {
                mensajeExito = '¡Enviado a: ' + data.impresion.destinos + '!';
            }

            window.notificar(mensajeExito, 'success');
            window.ticket = [];
            window.mesaSeleccionadaId = null;
            renderizarTicket();
            window.actualizarBadgeTicket(0);
            window.clienteSeleccionado = null;

            var lA = document.getElementById('mesa-activa-label');
            var elCliente = document.getElementById('cliente-nombre-ticket');
            var lM = document.getElementById('mesa-label');
            if (elCliente) elCliente.textContent = 'Consumidor final';
            if (lA) lA.textContent = 'MESA';
            if (lM) lM.textContent = 'Mesa: --';

            // Refrescar el grid de mesas para que refleje estado ocupada
            await refrescarMesas();

        } else {
            window.notificar(data.message || 'Error al enviar', 'error');
        }

    } catch (e) {
        console.error('Error al enviar pedido:', e);
        window.notificar(e.message || 'Error de conexión', 'error');
    }
}

// ============================================================
// CARGAR PEDIDO EXISTENTE
// ============================================================
window.cargarPedidoExistente = async function (mesaId, nombreMesa) {
    try {
        var res = await fetch('/pedidos/mesa/' + mesaId + '/pendiente');
        if (!res.ok) throw new Error('Sin pedido');
        var data = await res.json();

        if (data.status === 'success') {
            window.ticket = [];
            window.mesaSeleccionadaId = mesaId;
            window.clienteSeleccionado = null;
            var lA = document.getElementById('mesa-activa-label');
            var lM = document.getElementById('mesa-label');
            var elCliente = document.getElementById('cliente-nombre-ticket');
            if (elCliente) elCliente.textContent = 'Consumidor final';
            if (lA) lA.textContent = nombreMesa;
            if (lM) lM.textContent = 'Mesa: ' + nombreMesa;

            // El controlador devuelve nombre_producto, lo mapeamos a descripcion
            window.ticket = data.items.map(function (item) {
                return {
                    id: item.producto_id,
                    descripcion: item.nombre_producto || item.nombre || '',
                    precio: parseFloat(item.precio),
                    cantidad: parseInt(item.cantidad),
                    observacion: item.observacion || '',
                    existente: true
                };
            });

            // Restaurar cliente del pedido
            window.clienteSeleccionado = {
                id: data.cliente_id || 1,
                nombre: data.cliente_nombre || 'Consumidor Final'
            };
            var elCliente = document.getElementById('cliente-nombre-ticket');
            if (elCliente) elCliente.textContent = window.clienteSeleccionado.nombre;

            renderizarTicket();
            window.cerrarSelectorMesas();
            window.notificar('Pedido de ' + nombreMesa + ' cargado', 'success');
        } else {
            window.notificar(data.message || 'Sin pedidos pendientes', 'info');
        }
    } catch (e) {
        window.notificar(e.message || 'Error al cargar pedido', 'error');
    }
};

// ============================================================
// SUPER CLAVE
// ============================================================
window.validarSuperClave = async function () {
    var input = document.getElementById('inputSuperClave');
    var clave = input ? input.value : '';
    var boton = document.querySelector('#modalSuperClave .btn-send');

    if (!clave) {
        window.notificar('Ingresa la clave de autorización', 'warning');
        return;
    }

    if (boton) boton.disabled = true;

    try {
        var item = window.ticket.find(function (i) { return i.id === window.idProductoAEliminar; });

        if (item && item.existente) {
            var res = await fetch('/pedidos/eliminar-item', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    mesa_id: window.mesaSeleccionadaId,
                    producto_id: window.idProductoAEliminar,
                    clave: clave
                })
            });

            var data = await res.json().catch(function () { return {}; });
            if (!res.ok || data.status !== 'success') {
                throw new Error(data.message || 'No se pudo autorizar la eliminación');
            }

            // Si el servidor eliminó el pedido completo, limpiar todo en
            // lugar de intentar recargar un pedido que ya no existe.
            if (data.pedido_eliminado) {
                window.ticket = [];
                window.mesaSeleccionadaId = null;
                renderizarTicket();
                window.actualizarBadgeTicket(0);
                window.clienteSeleccionado = null;
                var lA = document.getElementById('mesa-activa-label');
                var lM = document.getElementById('mesa-label');
                var elCliente = document.getElementById('cliente-nombre-ticket');
                if (elCliente) elCliente.textContent = 'Consumidor final';
                if (lA) lA.textContent = 'MESA';
                if (lM) lM.textContent = 'Mesa: --';
                await refrescarMesas();
                window.cerrarSuperClave();
                window.notificar('Último producto eliminado, mesa liberada', 'success');
                return;
            }

        } else {
            // Item nuevo, solo filtrar local.
            window.ticket = window.ticket.filter(function (i) { return i.id !== window.idProductoAEliminar; });
            renderizarTicket();
            window.cerrarSuperClave();
            window.notificar('Producto eliminado', 'success');
            return;
        }

        // Quedan más items: recargar desde base de datos.
        window.clienteSeleccionado = null;
        var mesaId = window.mesaSeleccionadaId;
        var nombreMesa = document.getElementById('mesa-activa-label').textContent;
        var elCliente = document.getElementById('cliente-nombre-ticket');
        if (elCliente) elCliente.textContent = 'Consumidor final';
        window.cerrarSuperClave();
        await window.cargarPedidoExistente(mesaId, nombreMesa);
        window.notificar('Producto eliminado', 'success');

    } catch (e) {
        console.error(e);
        window.notificar('No se pudo eliminar: ' + e.message, 'error');
        if (input) input.value = '';
    } finally {
        if (boton) boton.disabled = false;
    }
};

// ============================================================
// MODAL CONFIRMAR
// ============================================================
window.mostrarConfirm = function (mensaje, callback) {
    var m = document.getElementById('modalConfirm');
    var txt = document.getElementById('confirmMensaje');
    if (txt) txt.innerText = mensaje;
    window.accionConfirmar = callback;
    if (m) { m.classList.remove('hidden'); m.classList.add('flex'); }
};

window.vaciarTicket = function () {
    var tieneNuevos = window.ticket.some(function (i) { return !i.existente; });
    var tieneExistentes = window.ticket.some(function (i) { return i.existente; });

    // Solo hay items existentes (solo estaba viendo el pedido)
    // → limpiar vista sin tocar servidor ni liberar mesa
    if (tieneExistentes && !tieneNuevos) {
        window.ticket = [];
        window.mesaSeleccionadaId = null;
        renderizarTicket();
        window.actualizarBadgeTicket(0);
        var lA = document.getElementById('mesa-activa-label');
        window.clienteSeleccionado = null;
        var elCliente = document.getElementById('cliente-nombre-ticket');
        var lM = document.getElementById('mesa-label');
        if (elCliente) elCliente.textContent = 'Consumidor final';
        if (lA) lA.textContent = 'MESA';
        if (lM) lM.textContent = 'Mesa: --';
        window.notificar('Saliste del pedido sin cambios', 'info');
        return;
    }

    // Ticket vacío → limpiar vista y liberar mesa en servidor
    if (window.ticket.length === 0) {
        var mesaId = window.mesaSeleccionadaId; // ✅ guardar antes de limpiar

        window.ticket = [];
        window.mesaSeleccionadaId = null;
        renderizarTicket();
        window.actualizarBadgeTicket(0);
        var lA2 = document.getElementById('mesa-activa-label');
        window.clienteSeleccionado = null;
        var elCliente = document.getElementById('cliente-nombre-ticket');
        var lM2 = document.getElementById('mesa-label');
        if (elCliente) elCliente.textContent = 'Consumidor final';
        if (lA2) lA2.textContent = 'MESA';
        if (lM2) lM2.textContent = 'Mesa: --';

        if (mesaId) window.liberarMesaActual(mesaId); // ✅ liberar en servidor
        return;
    }

    // Tiene items nuevos pero todavía no hay mesa asignada (se estaban
    // armando antes de "Enviar pedido"): no hay nada que liberar en el
    // servidor, solo se limpia el ticket local.
    if (!window.mesaSeleccionadaId) {
        window.ticket = [];
        window.cerrarSelectorMesas(); // por si estaba abierto esperando la mesa de destino
        renderizarTicket();
        window.actualizarBadgeTicket(0);
        window.notificar('Pedido cancelado', 'info');
        return;
    }

    // Tiene items nuevos y mesa asignada → confirmar antes de liberar
    window.mostrarConfirm(
        '¿Cancelar la orden? Los productos nuevos se perderán y la mesa se liberará.',
        window.liberarMesaActual
    );
};

// ============================================================
// TOASTS
// ============================================================
window.notificar = function (mensaje, tipo) {
    tipo = tipo || 'info';
    var container = document.getElementById('toast-container');
    if (!container) return;

    var colores = {
        info: 'bg-slate-800 border-indigo-500 text-white',
        warning: 'bg-amber-600 border-amber-400 text-white',
        error: 'bg-red-600 border-red-400 text-white',
        success: 'bg-emerald-600 border-emerald-400 text-white'
    };

    var toast = document.createElement('div');
    toast.className = (colores[tipo] || colores.info) +
        ' border-l-4 p-4 rounded-2xl shadow-2xl flex items-center gap-3 min-w-[260px] max-w-[90vw]';
    toast.innerHTML =
        '<div class="flex-1 font-bold text-sm uppercase tracking-wide">' + mensaje + '</div>' +
        '<button onclick="this.parentElement.remove()" class="opacity-50 hover:opacity-100 shrink-0">' +
        '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">' +
        '<path d="M6 18L18 6M6 6l12 12" stroke-width="2"></path></svg>' +
        '</button>';

    container.appendChild(toast);
    setTimeout(function () {
        toast.style.transition = 'opacity 0.3s, transform 0.3s';
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(-8px)';
        setTimeout(function () { toast.remove(); }, 300);
    }, 4000);
};

// Las alertas de cocina no se marcan como leídas hasta que el mesero las cierre.
window.notificacionesCocinaMostradas = new Set();
window.cerrarNotificacionCocina = function (id) {
    fetch('/notificaciones-pedidos/' + id + '/leer', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    }).then(function (response) {
        if (!response.ok) throw new Error();
        document.getElementById('notificacion-cocina-' + id)?.remove();
        window.notificacionesCocinaMostradas.delete(id);
    }).catch(function () {
        window.notificar('No fue posible cerrar la notificación.', 'error');
    });
};

window.mostrarNotificacionCocina = function (notificacion) {
    if (window.notificacionesCocinaMostradas.has(notificacion.id)) return;
    const container = document.getElementById('toast-container');
    if (!container) return;

    window.notificacionesCocinaMostradas.add(notificacion.id);
    const aviso = document.createElement('div');
    aviso.id = 'notificacion-cocina-' + notificacion.id;
    aviso.className = 'bg-emerald-600 border-l-4 border-emerald-300 p-4 rounded-2xl shadow-2xl flex items-center gap-3 min-w-[280px] max-w-[90vw]';
    aviso.innerHTML =
        '<div class="flex-1"><div class="text-[10px] font-black tracking-widest text-emerald-100 mb-1">COCINA</div>' +
        '<div class="font-bold text-sm text-white">' + notificacion.mensaje + '</div></div>' +
        '<button onclick="cerrarNotificacionCocina(' + notificacion.id + ')" class="shrink-0 rounded-lg bg-white/15 px-3 py-2 text-[10px] font-black uppercase text-white hover:bg-white/25">Cerrar</button>';
    container.appendChild(aviso);
};

// Consulta avisos persistentes generados por cocina para el mesero del pedido.
window.setInterval(function () {
    fetch('/notificaciones-pedidos/pendientes', {
        headers: { 'Accept': 'application/json' }
    })
        .then(function (response) {
            if (!response.ok) return null;
            return response.json();
        })
        .then(function (respuesta) {
            (respuesta?.data || []).forEach(function (notificacion) {
                window.mostrarNotificacionCocina(notificacion);
            });
        })
        .catch(function () {
            // Las notificaciones se consultan de nuevo en el siguiente ciclo.
        });
}, 8000);

// ============================================================
// MODAL DE PAGO — Lógica completa
// ============================================================
window.metodoSeleccionado = 'efectivo';
window._subtotalVenta = 0;
window._propinaValor = 0;
window._pagosMixtos = [];

// ============================================================
// PAGO MIXTO — desglose por forma de pago (efectivo/tarjeta/etc.)
// ============================================================
// Antes 'mixto' se enviaba al servidor sin este desglose y se contabilizaba
// todo como si hubiera sido efectivo. Ahora el cajero declara exactamente
// cuánto fue de cada forma, y el total debe cuadrar con la venta.
var METODOS_MIXTO = [
    { value: 'efectivo', label: 'Efectivo' },
    { value: 'tarjeta', label: 'Tarjeta' },
    { value: 'transferencia', label: 'Transferencia' },
    { value: 'nequi', label: 'Nequi' },
    { value: 'daviplata', label: 'Daviplata' },
];

window.agregarPagoMixto = function () {
    window._pagosMixtos.push({ metodo_pago: 'efectivo', valor: 0, referencia: '' });
    renderPagosMixtos();
};

window.eliminarPagoMixto = function (idx) {
    window._pagosMixtos.splice(idx, 1);
    renderPagosMixtos();
};

window.pagoMixtoChange = function (idx, campo, valor) {
    if (!window._pagosMixtos[idx]) return;
    window._pagosMixtos[idx][campo] = campo === 'valor' ? (Number(valor) || 0) : valor;
    renderPagosMixtos();
};

function totalVentaConPropina() {
    return (window._subtotalVenta || 0) + (window._propinaValor || 0);
}

function renderPagosMixtos() {
    var box = document.getElementById('filas-pago-mixto');
    var resumen = document.getElementById('mixto-resumen');
    if (!box) return;

    box.innerHTML = window._pagosMixtos.map(function (p, idx) {
        var opciones = METODOS_MIXTO.map(function (m) {
            return '<option value="' + m.value + '"' + (p.metodo_pago === m.value ? ' selected' : '') + '>' + m.label + '</option>';
        }).join('');
        return '<div class="flex gap-2 items-center">' +
            '<select onchange="pagoMixtoChange(' + idx + ',\'metodo_pago\',this.value)" class="modal-input-dark" style="flex:1;padding:8px 10px;font-size:10.5px;">' + opciones + '</select>' +
            '<input type="number" min="0" step="0.01" value="' + (p.valor || 0) + '" oninput="pagoMixtoChange(' + idx + ',\'valor\',this.value)" placeholder="Valor" class="modal-input-dark" style="flex:1;padding:8px 10px;font-size:10.5px;">' +
            '<button type="button" onclick="eliminarPagoMixto(' + idx + ')" style="border:0;background:transparent;color:#f87171;font-size:16px;cursor:pointer;">✕</button>' +
            '</div>';
    }).join('');

    if (resumen) {
        var suma = window._pagosMixtos.reduce(function (a, p) { return a + (Number(p.valor) || 0); }, 0);
        var total = totalVentaConPropina();
        var cuadra = Math.abs(suma - total) < 1;
        resumen.style.color = cuadra ? '#4ade80' : '#f87171';
        resumen.innerText = 'Formas de pago: $' + suma.toLocaleString('es-CO') + ' de $' + total.toLocaleString('es-CO');
    }
}

function limpiarCamposPagoAdicionales() {
    var referenciaTarjeta = document.getElementById('ref_tarjeta');
    var referenciaTransferencia = document.getElementById('ref_transferencia');
    var tipoTarjeta = document.getElementById('tipo_tarjeta');
    var bancoDestino = document.getElementById('banco_destino');

    if (referenciaTarjeta) referenciaTarjeta.value = '';
    if (referenciaTransferencia) referenciaTransferencia.value = '';
    if (tipoTarjeta) tipoTarjeta.selectedIndex = 0;
    if (bancoDestino) bancoDestino.selectedIndex = 0;

    window._pagosMixtos = [];
    var panelMixto = document.getElementById('campos-mixto');
    if (panelMixto) panelMixto.classList.add('hidden');
}

window.abrirModalPago = function () {

    // Validación 1: debe haber mesa seleccionada
    if (!window.mesaSeleccionadaId) {
        window.notificar('Debes seleccionar una mesa primero', 'error');
        return;
    }

    // Validación 2: debe haber productos en el ticket
    if (window.ticket.length === 0) {
        window.notificar('No hay productos en la orden', 'error');
        return;
    }

    // Validación 3: todos los productos deben estar enviados a cocina
    var tieneNuevos = window.ticket.some(function (i) { return !i.existente; });
    if (tieneNuevos) {
        window.notificar('Debes enviar el pedido a cocina antes de cobrar', 'warning');
        return;
    }

    var modal = document.getElementById('modalPago');
    if (!modal) { console.error('modalPago no encontrado'); return; }

    // ── Calcular subtotal puro (sin servicio) ──────────────────
    window._subtotalVenta = window.ticket.reduce(function (a, i) {
        return a + (i.precio * i.cantidad);
    }, 0);
    var servicio = Math.round(window._subtotalVenta * 0.10);
    var totalConServicio = window._subtotalVenta + servicio;

    // ── Mostrar total y mesa ───────────────────────────────────
    var totalPagar = document.getElementById('pago-total-val');
    var mesaLabel = document.getElementById('pago-mesa-label');
    var mesaNombre = document.getElementById('mesa-activa-label').textContent;

    if (totalPagar) {
        totalPagar.innerText =
            '$' + window._subtotalVenta.toLocaleString('es-CO');
    }
    if (mesaLabel) mesaLabel.innerText = 'Mesa: ' + mesaNombre;

    // ── Reset cliente ──────────────────────────────────────────
    // window.clienteSeleccionado = null;
    // var elCliente = document.getElementById('cliente-nombre-ticket');
    // if (elCliente) elCliente.textContent = 'Consumidor final';

    // ── Reset propina ──────────────────────────────────────────
    window._propinaValor = 0;

    document.querySelectorAll('.propina-btn').forEach(function (b, i) {
        if (i === 0) {
            b.style.background = '#1a2d50';
            b.style.borderColor = '#2d4faa';
            b.style.color = '#93c5fd';
        } else {
            b.style.background = '#1a2235';
            b.style.borderColor = '#283347';
            b.style.color = '#475569';
        }
    });

    var customWrap = document.getElementById('propina-custom-wrap');
    if (customWrap) customWrap.classList.add('hidden');

    actualizarDisplayPropina();

    // ── Reset efectivo recibido ────────────────────────────────
    var inputRecibido = document.getElementById('montoRecibido');
    if (inputRecibido) inputRecibido.value = '';
    var cambioEl = document.getElementById('pago-cambio-val');
    if (cambioEl) cambioEl.innerText = '$0';
    limpiarCamposPagoAdicionales();

    // ── Método por defecto ─────────────────────────────────────
    window.seleccionarMetodo('efectivo');

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    setTimeout(function () {
        if (inputRecibido) inputRecibido.focus();
    }, 100);
};

window.cerrarModalPago = function () {
    var modal = document.getElementById('modalPago');
    if (modal) {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
    limpiarCamposPagoAdicionales();
};

window.seleccionarMetodo = function (metodo) {
    window.metodoSeleccionado = metodo;

    document.querySelectorAll('.metodo-pago').forEach(function (btn) {
        btn.classList.remove('border-indigo-600', 'bg-indigo-600/10');
        btn.classList.add('border-slate-800', 'bg-slate-800/50');
    });

    var id = 'btn-pago-' + (metodo === 'transferencia' ? 'transfer' : metodo);
    var btn = document.getElementById(id);
    if (btn) {
        btn.classList.add('border-indigo-600', 'bg-indigo-600/10');
        btn.classList.remove('border-slate-800', 'bg-slate-800/50');
    }

    // Mostrar/ocultar campo efectivo recibido
    var wrapper = document.getElementById('wrapper-recibido');
    if (wrapper) wrapper.style.opacity = (metodo === 'efectivo') ? '1' : '0.3';

    // Limpiar cambio si no es efectivo
    if (metodo !== 'efectivo') {
        var inputRecibido = document.getElementById('montoRecibido');
        if (inputRecibido) inputRecibido.value = '';
        var cambioEl = document.getElementById('pago-cambio-val');
        if (cambioEl) cambioEl.innerText = '$0';
    }

    // Control de visibilidad de campos extra
    const panelTarjeta = document.getElementById('campos-tarjeta');
    const panelTransfer = document.getElementById('campos-transferencia');
    const panelMixto = document.getElementById('campos-mixto');
    const wrapperRecibido = document.getElementById('wrapper-recibido');
    const avisoCredito = document.getElementById('aviso-credito-cliente');

    // Resetear vistas
    panelTarjeta.classList.add('hidden');
    panelTransfer.classList.add('hidden');
    if (panelMixto) panelMixto.classList.add('hidden');
    if (avisoCredito) avisoCredito.classList.add('hidden');

    if (metodo === 'tarjeta') {
        panelTarjeta.classList.remove('hidden');
        wrapperRecibido.style.opacity = '0.3';
    } else if (metodo === 'transferencia') {
        panelTransfer.classList.remove('hidden');
        wrapperRecibido.style.opacity = '0.3';
    } else if (metodo === 'credito') {
        wrapperRecibido.style.opacity = '0.3';
        // Una venta a crédito no puede quedar a nombre del "Consumidor Final" (id 1).
        var esClienteReal = window.clienteSeleccionado && Number(window.clienteSeleccionado.id) > 1;
        if (avisoCredito) avisoCredito.classList.toggle('hidden', !!esClienteReal);
    } else if (metodo === 'mixto') {
        wrapperRecibido.style.opacity = '0.3';
        if (panelMixto) panelMixto.classList.remove('hidden');
        // Arranca con 2 filas (efectivo + tarjeta) para no obligar a agregar
        // manualmente la primera vez; el cajero ajusta método y valor.
        window._pagosMixtos = [
            { metodo_pago: 'efectivo', valor: 0, referencia: '' },
            { metodo_pago: 'tarjeta', valor: 0, referencia: '' },
        ];
        renderPagosMixtos();
    } else {
        wrapperRecibido.style.opacity = '1';
    }
};

window.procesarPagoFinal = async function () {

    var totalEl = document.getElementById('pago-gran-total'); // CORREGIDO: antes leía 'pago-total-val'
    var total = parseInt(totalEl.innerText.replace(/[^0-9]/g, '')) || 0;

    // 2. Validaciones específicas por método de pago
    var detallesPago = {
        tipo_tarjeta: null,
        banco_destino: null,
        referencia: null
    };

    if (window.metodoSeleccionado === 'efectivo') {
        var recibido = parseInt(document.getElementById('montoRecibido').value) || 0;

        if (recibido === 0) {
            window.notificar('Ingresa el efectivo recibido', 'warning');
            document.getElementById('montoRecibido').focus();
            return;
        }

        if (recibido < total) {
            var faltante = (total - recibido).toLocaleString('es-CO');
            window.notificar('Faltan $ ' + faltante + ' para completar el pago', 'error');
            document.getElementById('montoRecibido').focus();
            return;
        }
    }
    else if (window.metodoSeleccionado === 'tarjeta') {
        detallesPago.tipo_tarjeta = document.getElementById('tipo_tarjeta').value;
        detallesPago.referencia = document.getElementById('ref_tarjeta').value;

        // Opcional: Validar que pongan la referencia si es obligatorio para ti
        if (!detallesPago.referencia) {
            window.notificar('Por favor ingresa el número de voucher', 'warning');
            document.getElementById('ref_tarjeta').focus();
            return;
        }
    }
    else if (window.metodoSeleccionado === 'transferencia') {
        detallesPago.banco_destino = document.getElementById('banco_destino').value;
        detallesPago.referencia = document.getElementById('ref_transferencia').value;

        if (!detallesPago.referencia) {
            window.notificar('Ingresa el ID de la transacción', 'warning');
            document.getElementById('ref_transferencia').focus();
            return;
        }
    }
    else if (window.metodoSeleccionado === 'mixto') {
        var pagosValidos = (window._pagosMixtos || []).filter(function (p) { return (Number(p.valor) || 0) > 0; });

        if (pagosValidos.length < 2) {
            window.notificar('Agrega al menos 2 formas de pago con valor mayor a 0', 'warning');
            return;
        }

        var sumaPagos = pagosValidos.reduce(function (a, p) { return a + (Number(p.valor) || 0); }, 0);
        if (Math.abs(sumaPagos - total) >= 1) {
            window.notificar('Las formas de pago (' + sumaPagos.toLocaleString('es-CO') + ') deben sumar el total (' + total.toLocaleString('es-CO') + ')', 'error');
            return;
        }
    }
    // Nota: no se bloquea aquí si no hay window.clienteSeleccionado, porque ese
    // estado se resetea al "Enviar pedido" — el servidor valida contra el
    // cliente real que quedó guardado en el pedido y devuelve un error claro
    // si de verdad falta.

    // 3. Envío de datos al servidor
    try {
        var res = await fetch('/pedidos/cerrar-mesa', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                mesa_id: window.mesaSeleccionadaId,
                metodo_pago: window.metodoSeleccionado,
                total: total,
                propina: window._propinaValor || 0,
                tipo_tarjeta: detallesPago.tipo_tarjeta,
                banco_destino: detallesPago.banco_destino,
                referencia: detallesPago.referencia,
                cliente_id: (window.clienteSeleccionado && window.clienteSeleccionado.id) || 1,
                pagos: window.metodoSeleccionado === 'mixto'
                    ? (window._pagosMixtos || []).filter(function (p) { return (Number(p.valor) || 0) > 0; })
                    : undefined,
            })
        });


        var data = await res.json();

        if (res.ok && data.status === 'success') {
            window.notificar('¡Venta realizada! Mesa liberada', 'success');
            window.cerrarModalPago();

            // Limpiar estado de la App
            window.ticket = [];
            window.mesaSeleccionadaId = null;
            window.clienteSeleccionado = null;

            // Si tienes estas funciones definidas en tu facturacion.js
            if (typeof renderizarTicket === "function") renderizarTicket();
            if (typeof window.actualizarBadgeTicket === "function") window.actualizarBadgeTicket(0);

            var lA = document.getElementById('mesa-activa-label');
            var lM = document.getElementById('mesa-label');
            if (lA) lA.textContent = 'MESA';
            if (lM) lM.textContent = 'Mesa: --';
            var elCliente = document.getElementById('cliente-nombre-ticket');
            if (elCliente) elCliente.textContent = 'Consumidor final';

            // Actualizar la vista de mesas (para que cambie de color a disponible)
            if (typeof refrescarMesas === "function") await refrescarMesas();

        } else {
            window.notificar(data.message || 'Error al procesar el pago', 'error');
        }

    } catch (e) {
        console.error('Error procesando pago:', e);
        window.notificar(e.message || 'Error de conexión al procesar pago', 'error');
    }
}

function imprimirInventarioPOS() {
    if (!confirm("¿Deseas imprimir la plantilla de inventario actual en formato POS?")) return;

    fetch('/pedidos/imprimir-inventario-pos', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert("Plantilla de inventario enviada con éxito a la impresora térmica.");
            } else {
                alert("Error: " + data.message);
            }
        })
        .catch(error => {
            console.error("Error al procesar impresión de inventario:", error);
            alert(error.message || "Ocurrió un error de red al intentar mandar la impresión.");
        });
}

/**
 * 2. MODAL DE ARQUEO / CIERRE DE CAJA
 */
function abrirModalCierre() {
    const modal = document.getElementById('modalCierreCaja');

    // Bloqueo estricto del día actual
    const hoy = new Date().toISOString().split('T')[0];
    document.getElementById('cierre_fecha_fin').value = hoy;
    document.getElementById('cierre_fecha_inicio').value = hoy;

    // Autocompletar hora de cierre con la hora actual exacta del navegador
    const ahora = new Date();
    const horaStr = String(ahora.getHours()).padStart(2, '0') + ':' + String(ahora.getMinutes()).padStart(2, '0');
    document.getElementById('cierre_hora_fin').value = horaStr;

    // Default hora de inicio (puedes cambiarla o cargar la real del login si la guardas)
    document.getElementById('cierre_hora_inicio').value = "18:00";

    modal.classList.add('show');
}

function cerrarModalCierre() {
    document.getElementById('modalCierreCaja').classList.remove('show');
}

/**
 * CALCULADORA DINÁMICA DE ARQUEO
 */
function calcularArqueoTotal() {

    let totalEfectivoFisico = 0;

    const inputs = document.querySelectorAll('.input-denominacion');

    inputs.forEach(input => {

        const cantidad = parseInt(input.value) || 0;

        const valorDenominacion = parseInt(
            input.getAttribute('data-valor')
        );

        const subtotal = cantidad * valorDenominacion;

        totalEfectivoFisico += subtotal;

        /*
        |--------------------------------------------------------------------------
        | SUBTOTAL POR DENOMINACION
        |--------------------------------------------------------------------------
        */

        const subtotalElement = document.getElementById(
            `subtotal_den_${valorDenominacion}`
        );

        if (subtotalElement) {

            subtotalElement.innerText =
                "$" + subtotal.toLocaleString('es-CO');
        }
    });

    /*
    |--------------------------------------------------------------------------
    | TOTAL GENERAL FISICO
    |--------------------------------------------------------------------------
    */

    document.getElementById('total_efectivo_conteo').innerText =
        "$" + totalEfectivoFisico.toLocaleString('es-CO');
}

function procesarCierreFinal() {

    const textoConteo = document.getElementById('total_efectivo_conteo').innerText;
    const totalFisico = parseInt(textoConteo.replace('$', '').replace(/\./g, '')) || 0;

    const data = {
        fecha_inicio: document.getElementById('cierre_fecha_inicio').value,
        hora_inicio: document.getElementById('cierre_hora_inicio').value,
        fecha_fin: document.getElementById('cierre_fecha_fin').value,
        hora_fin: document.getElementById('cierre_hora_fin').value,
        base_caja: document.getElementById('base_caja').value,

        efectivo_fisico_conteo: totalFisico,

        // MONEDAS — IDs definidos en el Blade
        m100: parseInt(document.getElementById('m100')?.value) || 0,
        m200: parseInt(document.getElementById('m200')?.value) || 0,
        m500: parseInt(document.getElementById('m500')?.value) || 0,
        m1000: parseInt(document.getElementById('m1000')?.value) || 0,

        // BILLETES
        b2000: parseInt(document.getElementById('b2000')?.value) || 0,
        b5000: parseInt(document.getElementById('b5000')?.value) || 0,
        b10000: parseInt(document.getElementById('b10000')?.value) || 0,
        b20000: parseInt(document.getElementById('b20000')?.value) || 0,
        b50000: parseInt(document.getElementById('b50000')?.value) || 0,
        b100000: parseInt(document.getElementById('b100000')?.value) || 0,
    };

    fetch('/pedidos/procesar-cierre-caja', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify(data)
    })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                const icon = res.estado_cuadre === 'CUADRADO' ? '✅' : res.estado_cuadre === 'SOBRANTE' ? '📈' : '⚠️';
                const color = res.estado_cuadre === 'CUADRADO' ? '#22c55e' : res.estado_cuadre === 'SOBRANTE' ? '#3b82f6' : '#ef4444';
                mostrarToastCierre(icon, res.estado_cuadre, res.diferencia, color);
                setTimeout(() => location.reload(), 3500);
            } else {
                alert('Error: ' + (res.message || 'no se pudo procesar el cierre'));
            }
        })
        .catch(err => {
            console.error(err);
            alert(err.message || 'Error al procesar el cierre.');
        });
}

function mostrarToastCierre(icon, estado, diferencia, color) {
    const toast = document.createElement('div');
    toast.style.cssText = `
        position:fixed; top:20px; right:20px; z-index:99999;
        background:#0f172a; border:1px solid ${color}40;
        border-left:3px solid ${color};
        border-radius:12px; padding:16px 20px;
        min-width:260px; box-shadow:0 8px 32px rgba(0,0,0,0.5);
        animation:fadeIn .25s ease-out;
    `;
    toast.innerHTML = `
        <div style="display:flex; align-items:center; gap:10px; margin-bottom:6px;">
            <span style="font-size:20px;">${icon}</span>
            <span style="font-size:13px; font-weight:800; color:${color}; text-transform:uppercase; letter-spacing:.5px;">
                ${estado}
            </span>
        </div>
        <p style="font-size:11px; color:#94a3b8; font-weight:600; margin:0;">
            Diferencia: <strong style="color:#e2e8f0;">$${diferencia}</strong>
        </p>
        <p style="font-size:10px; color:#475569; margin:4px 0 0; font-weight:600;">
            Redirigiendo...
        </p>
    `;
    document.body.appendChild(toast);
}

// function calcularArqueoTotal() {
//     let totalFisico = 0;

//     document.querySelectorAll('.input-denominacion').forEach(input => {
//         const cantidad = parseInt(input.value) || 0;
//         const valor = parseInt(input.getAttribute('data-valor')) || 0;
//         const subtotal = cantidad * valor;
//         totalFisico += subtotal;

//         const span = document.getElementById('subtotal_den_' + valor);
//         if (span) span.innerText = '$' + subtotal.toLocaleString('es-CO');
//     });

//     document.getElementById('total_efectivo_conteo').innerText =
//         '$' + totalFisico.toLocaleString('es-CO');
// }

// ============================================================
// MOVIMIENTO CAJA — tipo único con selector, concepto de catálogo
// y tercero (para egresos, con impresión de comprobante para firma)
// ============================================================
window._tipoMovimiento = 'ingreso'; // default
window._terceroMovimiento = null; // {id, nombre, documento} — quien recibe el dinero en un egreso

window.abrirModalMovimiento = function () {
    seleccionarTipoMovimiento('ingreso');
    document.getElementById('mov_monto').value = '';
    document.getElementById('mov_nota').value = '';
    quitarTerceroMovimiento();
    document.getElementById('modalMovimientoCaja').classList.add('show');
};

window.seleccionarTipoMovimiento = function (tipo) {
    window._tipoMovimiento = tipo;

    const btnIngreso = document.getElementById('btn-tipo-ingreso');
    const btnEgreso = document.getElementById('btn-tipo-egreso');

    if (tipo === 'ingreso') {
        btnIngreso.style.background = '#0d2210';
        btnIngreso.style.borderColor = '#14532d';
        btnIngreso.style.color = '#4ade80';
        btnEgreso.style.background = '#1a2235';
        btnEgreso.style.borderColor = '#283347';
        btnEgreso.style.color = '#475569';
    } else {
        btnEgreso.style.background = '#2d1515';
        btnEgreso.style.borderColor = '#7f1d1d';
        btnEgreso.style.color = '#f87171';
        btnIngreso.style.background = '#1a2235';
        btnIngreso.style.borderColor = '#283347';
        btnIngreso.style.color = '#475569';
    }

    const wrapTercero = document.getElementById('mov-tercero-wrap');
    if (tipo === 'egreso') {
        wrapTercero.classList.remove('hidden');
    } else {
        wrapTercero.classList.add('hidden');
        quitarTerceroMovimiento();
    }

    cargarConceptosMovimiento(tipo);
};

function cargarConceptosMovimiento(tipoUi) {
    const backendTipo = tipoUi === 'egreso' ? 'salida' : 'ingreso';
    const select = document.getElementById('mov_concepto_id');
    const valorPrevio = select.value;
    select.innerHTML = '<option value="">Cargando conceptos...</option>';

    fetch('/conceptos-caja/opciones?tipo=' + backendTipo, { headers: { Accept: 'application/json' } })
        .then(r => r.json())
        .then(res => {
            const conceptos = res.data || [];
            if (!conceptos.length) {
                select.innerHTML = '<option value="">Sin conceptos configurados</option>';
                return;
            }
            select.innerHTML = '<option value="">Selecciona un concepto...</option>' +
                conceptos.map(c => '<option value="' + c.id + '">' + String(c.nombre).replace(/</g, '&lt;') + '</option>').join('');
            if (conceptos.some(c => String(c.id) === valorPrevio)) {
                select.value = valorPrevio;
            }
        })
        .catch(() => { select.innerHTML = '<option value="">Error al cargar conceptos</option>'; });
}

window.cerrarModalMovimiento = function () {
    document.getElementById('modalMovimientoCaja').classList.remove('show');
    document.getElementById('mov_monto').value = '';
    document.getElementById('mov_nota').value = '';
};

window.guardarMovimiento = function () {
    const tipoUi = window._tipoMovimiento;
    const tipo = tipoUi === 'egreso' ? 'salida' : 'ingreso';
    const monto = document.getElementById('mov_monto').value;
    const conceptoId = document.getElementById('mov_concepto_id').value;
    const nota = document.getElementById('mov_nota').value;

    if (!monto || parseFloat(monto) <= 0) {
        window.notificar('Ingresa un monto válido', 'warning');
        return;
    }
    if (!conceptoId) {
        window.notificar('Selecciona el concepto del movimiento', 'warning');
        return;
    }
    if (tipo === 'salida' && !window._terceroMovimiento) {
        window.notificar('Selecciona quién recibe el dinero', 'warning');
        return;
    }

    fetch('/pedidos/guardar-movimiento', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            tipo,
            monto,
            concepto_caja_id: conceptoId,
            nota: nota,
            tercero_id: window._terceroMovimiento ? window._terceroMovimiento.id : null
        })
    })
        .then(r => r.json().then(d => ({ ok: r.ok, d })))
        .then(({ ok, d }) => {
            if (!ok || !d.success) {
                throw new Error(d.errors ? Object.values(d.errors).flat().join(', ') : (d.message || 'No se pudo registrar el movimiento'));
            }
            let msg = (tipo === 'ingreso' ? 'Ingreso' : 'Egreso') + ' registrado: $' + parseFloat(monto).toLocaleString('es-CO');
            if (tipo === 'salida') {
                msg += d.comprobante_impreso ? ' — comprobante enviado a imprimir.' : ' — caja sin impresora, no se imprimió comprobante.';
            }
            window.notificar(msg, tipo === 'ingreso' ? 'success' : 'warning');
            cerrarModalMovimiento();
        })
        .catch(e => window.notificar('Error: ' + e.message, 'error'));
};

// ---- Tercero del movimiento (buscar / seleccionar / crear inline) --------

window.quitarTerceroMovimiento = function () {
    window._terceroMovimiento = null;
    const sel = document.getElementById('mov-tercero-seleccionado');
    sel.classList.add('hidden');
    sel.classList.remove('flex');
    document.getElementById('mov-btn-buscar-tercero').classList.remove('hidden');
};

window.abrirBuscadorTerceroMovimiento = function () {
    document.getElementById('mov-buscar-tercero-input').value = '';
    document.getElementById('mov-lista-terceros').innerHTML =
        '<p class="text-[10px] text-slate-600 text-center py-6 font-bold uppercase tracking-widest">Escribe al menos 2 caracteres...</p>';
    document.getElementById('modalTerceroMovimiento').classList.add('show');
    setTimeout(() => document.getElementById('mov-buscar-tercero-input').focus(), 100);
};

window.cerrarBuscadorTerceroMovimiento = function () {
    document.getElementById('modalTerceroMovimiento').classList.remove('show');
};

function buscarTercerosMovimiento(q) {
    fetch('/pedidos/terceros/buscar?query=' + encodeURIComponent(q), { headers: { Accept: 'application/json' } })
        .then(r => r.json())
        .then(data => {
            const lista = document.getElementById('mov-lista-terceros');
            if (!data.length) {
                lista.innerHTML = '<p class="text-[10px] text-slate-600 text-center py-6 font-bold uppercase">Sin resultados</p>';
                return;
            }
            lista.innerHTML = data.map(function (t) {
                const nombre = t.tipo === 'persona' ? (t.nombre + ' ' + (t.apellido || '')).trim() : (t.razon_social || '');
                const doc = t.tipo === 'persona' ? (t.cedula || '') : (t.nit || '');
                const iniciales = nombre.substring(0, 2).toUpperCase();
                return (
                    '<button onclick="seleccionarTerceroMovimiento(' + t.id + ', \'' +
                    nombre.replace(/'/g, "\\'") + '\', \'' + doc + '\')" ' +
                    'class="w-full flex items-center gap-3 p-3 rounded-lg transition-all text-left mb-1" ' +
                    'style="background:#1a2235; border:0.5px solid #283347;">' +
                    '<div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-[10px] font-black" ' +
                    'style="background:#2d4faa; color:#93c5fd;">' + iniciales + '</div>' +
                    '<div class="flex-1 min-w-0">' +
                    '<p class="text-[11px] font-bold text-white truncate">' + nombre + '</p>' +
                    '<p class="text-[9px] text-slate-500">' + (doc || 'Sin documento') +
                    (t.celular ? ' · ' + t.celular : '') + '</p>' +
                    '</div></button>'
                );
            }).join('');
        })
        .catch(() => {
            document.getElementById('mov-lista-terceros').innerHTML =
                '<p class="text-[10px] text-red-500 text-center py-4 font-bold uppercase">Error al buscar</p>';
        });
}

window.seleccionarTerceroMovimiento = function (id, nombre, documento) {
    window._terceroMovimiento = { id: id, nombre: nombre, documento: documento };
    document.getElementById('mov-tercero-nombre').textContent = nombre;
    document.getElementById('mov-tercero-doc').textContent = documento || 'Sin documento';
    const sel = document.getElementById('mov-tercero-seleccionado');
    sel.classList.remove('hidden');
    sel.classList.add('flex');
    document.getElementById('mov-btn-buscar-tercero').classList.add('hidden');
    cerrarCrearTerceroInline();
    cerrarBuscadorTerceroMovimiento();
};

document.addEventListener('DOMContentLoaded', function () {
    const inputBuscarTerceroMov = document.getElementById('mov-buscar-tercero-input');
    if (!inputBuscarTerceroMov) return;

    inputBuscarTerceroMov.addEventListener('input', function () {
        clearTimeout(window._terceroMovTimer);
        const q = this.value.trim();
        if (q.length < 2) {
            document.getElementById('mov-lista-terceros').innerHTML =
                '<p class="text-[10px] text-slate-600 text-center py-4 font-bold uppercase">Escribe al menos 2 caracteres...</p>';
            return;
        }
        document.getElementById('mov-lista-terceros').innerHTML =
            '<p class="text-[10px] text-slate-500 text-center py-4 font-bold uppercase">Buscando...</p>';
        window._terceroMovTimer = setTimeout(function () { buscarTercerosMovimiento(q); }, 350);
    });
});

// ---- Crear tercero inline (cuando no existe en la búsqueda) --------------

window._tipoTerceroInline = 'persona';

window.abrirCrearTerceroInline = function () {
    seleccionarTipoTerceroInline('persona');
    ['ct_nombre', 'ct_apellido', 'ct_cedula', 'ct_razon_social', 'ct_nit', 'ct_celular', 'ct_email', 'ct_direccion', 'ct_ciudad'].forEach(function (id) {
        const el = document.getElementById(id);
        if (el) el.value = '';
    });

    // Si ya escribió algo numérico en el buscador, lo sugerimos como documento
    const q = document.getElementById('mov-buscar-tercero-input').value.trim();
    if (q && /^\d+$/.test(q)) {
        document.getElementById('ct_cedula').value = q;
        document.getElementById('ct_nit').value = q;
    }

    document.getElementById('modalCrearTerceroMovimiento').classList.add('show');
};

window.cerrarCrearTerceroInline = function () {
    document.getElementById('modalCrearTerceroMovimiento').classList.remove('show');
};

window.seleccionarTipoTerceroInline = function (tipo) {
    window._tipoTerceroInline = tipo;
    const btnP = document.getElementById('ct-btn-persona');
    const btnE = document.getElementById('ct-btn-empresa');
    const camposP = document.getElementById('ct-campos-persona');
    const camposE = document.getElementById('ct-campos-empresa');

    if (tipo === 'persona') {
        btnP.style.background = '#1a2d50'; btnP.style.borderColor = '#2d4a7a'; btnP.style.color = '#93c5fd';
        btnE.style.background = '#1a2235'; btnE.style.borderColor = '#283347'; btnE.style.color = '#475569';
        camposP.classList.remove('hidden');
        camposE.classList.add('hidden');
    } else {
        btnE.style.background = '#1a2d50'; btnE.style.borderColor = '#2d4a7a'; btnE.style.color = '#93c5fd';
        btnP.style.background = '#1a2235'; btnP.style.borderColor = '#283347'; btnP.style.color = '#475569';
        camposE.classList.remove('hidden');
        camposP.classList.add('hidden');
    }
};

window.guardarTerceroInlineMovimiento = function () {
    const tipo = window._tipoTerceroInline;
    const payload = {
        tipo: tipo,
        celular: document.getElementById('ct_celular').value.trim(),
        email: document.getElementById('ct_email').value.trim(),
        direccion: document.getElementById('ct_direccion').value.trim(),
        ciudad: document.getElementById('ct_ciudad').value.trim()
    };

    if (tipo === 'persona') {
        payload.nombre = document.getElementById('ct_nombre').value.trim();
        payload.apellido = document.getElementById('ct_apellido').value.trim();
        payload.cedula = document.getElementById('ct_cedula').value.trim();
        if (!payload.nombre || !payload.apellido) {
            window.notificar('Nombres y apellidos son obligatorios', 'warning');
            return;
        }
    } else {
        payload.razon_social = document.getElementById('ct_razon_social').value.trim();
        payload.nit = document.getElementById('ct_nit').value.trim();
        if (!payload.razon_social) {
            window.notificar('La razón social es obligatoria', 'warning');
            return;
        }
    }

    if (!payload.celular) {
        window.notificar('El celular es obligatorio', 'warning');
        return;
    }

    fetch('/pedidos/terceros', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json'
        },
        body: JSON.stringify(payload)
    })
        .then(r => r.json().then(d => ({ ok: r.ok, d })))
        .then(({ ok, d }) => {
            if (!ok) {
                throw new Error(d.errors ? Object.values(d.errors).flat().join(', ') : (d.message || 'No se pudo crear el tercero'));
            }
            const t = d.data;
            const nombre = tipo === 'persona' ? (t.nombre + ' ' + (t.apellido || '')).trim() : t.razon_social;
            const doc = tipo === 'persona' ? t.cedula : t.nit;
            window.notificar('Tercero creado: ' + nombre, 'success');
            seleccionarTerceroMovimiento(t.id, nombre, doc || '');
        })
        .catch(e => window.notificar('Error: ' + e.message, 'error'));
};

// ============================================================
// PROPINA EN MODAL DE PAGO
// ============================================================
window._propinaValor = 0;

window.seleccionarPropina = function (opcion) {
    console.log('_subtotalVenta:', window._subtotalVenta);
    // El % se aplica sobre el SUBTOTAL puro, no sobre el total con servicio
    var base = window._subtotalVenta || 0;

    document.querySelectorAll('.propina-btn').forEach(function (b) {
        b.style.background = '#1a2235'; b.style.borderColor = '#283347'; b.style.color = '#475569';
    });

    var customWrap = document.getElementById('propina-custom-wrap');
    var btns = document.querySelectorAll('.propina-btn');

    if (opcion === 'custom') {
        customWrap.classList.remove('hidden');
        document.getElementById('propina_custom').value = '';
        document.getElementById('propina_custom').focus();
        window._propinaValor = 0;
        if (btns[3]) { btns[3].style.background = '#451a03'; btns[3].style.borderColor = '#92400e'; btns[3].style.color = '#fbbf24'; }
    } else {
        customWrap.classList.add('hidden');
        window._propinaValor = opcion === 0 ? 0 : Math.round(base * (opcion / 100));

        var idx = opcion === 0 ? 0 : opcion === 5 ? 1 : 2;
        if (btns[idx]) {
            btns[idx].style.background = '#1a2d50'; btns[idx].style.borderColor = '#2d4faa'; btns[idx].style.color = '#93c5fd';
        }
    }

    actualizarDisplayPropina();
};

window.aplicarPropinaCustom = function () {
    window._propinaValor = parseInt(document.getElementById('propina_custom').value) || 0;
    actualizarDisplayPropina();
};

function actualizarDisplayPropina() {

    var granTotal =
        (window._subtotalVenta || 0) +
        (window._propinaValor || 0);

    var granEl = document.getElementById('pago-gran-total');

    if (granEl) {
        granEl.innerText =
            '$' + granTotal.toLocaleString('es-CO');
    }

    var propinaTexto = '$' + (window._propinaValor || 0).toLocaleString('es-CO');
    var propinaResumen = document.getElementById('pago-propina-label');
    var propinaDetalle = document.getElementById('propina-val');
    if (propinaResumen) propinaResumen.innerText = propinaTexto;
    if (propinaDetalle) propinaDetalle.innerText = propinaTexto;

    window.calcularCambio();
}

window.calcularCambio = function () {

    var granTotal =
        (window._subtotalVenta || 0) +
        (window._propinaValor || 0);

    var recibido =
        parseInt(document.getElementById('montoRecibido')?.value) || 0;

    var cambio = recibido - granTotal;

    var cambioEl = document.getElementById('pago-cambio-val');

    if (!cambioEl) return;

    if (cambio > 0) {

        cambioEl.innerText =
            '$ ' + cambio.toLocaleString('es-CO');

        cambioEl.className =
            'text-xl font-black text-emerald-400';

    } else if (cambio < 0) {

        cambioEl.innerText =
            '- $ ' + Math.abs(cambio).toLocaleString('es-CO');

        cambioEl.className =
            'text-xl font-black text-red-400';

    } else {

        cambioEl.innerText = '$0';

        cambioEl.className =
            'text-xl font-black text-emerald-400';
    }
};
window.aplicarPropinaCustom = function () {
    const val = parseInt(document.getElementById('propina_custom').value) || 0;
    window._propinaValor = val;
    actualizarDisplayPropina();
};
