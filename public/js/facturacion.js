// facturacion.js — sin Vite, carga tradicional con defer

// ============================================================
// ESTADO GLOBAL — todo en window desde el inicio
// ============================================================
window.ticket = [];
window.mesaSeleccionadaId = null;
window.idProductoAEliminar = null;
window.accionConfirmar = null;

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
    var m = document.getElementById('modalMesas');
    if (m) { m.classList.remove('hidden'); m.classList.add('flex'); }
};

window.cerrarSelectorMesas = function () {
    var m = document.getElementById('modalMesas');
    if (m) { m.classList.add('hidden'); m.classList.remove('flex'); }
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
window.agregarAlTicket = function (id, descripcion, precio) {
    var existente = window.ticket.find(function (i) { return i.id === id; });
    if (existente) {
        existente.cantidad++;
    } else {
        window.ticket.push({ id: id, descripcion: descripcion, precio: precio, cantidad: 1, observacion: '' });
    }
    renderizarTicket();
    var total = window.ticket.reduce(function (a, i) { return a + i.cantidad; }, 0);
    window.actualizarBadgeTicket(total);
};

// ============================================================
// TICKET — ELIMINAR
// ============================================================
window.eliminarDelTicket = function (id) {
    var item = window.ticket.find(function (i) { return i.id === id; });
    if (!item) return;

    if (item.existente) {
        window.idProductoAEliminar = id;
        var m = document.getElementById('modalSuperClave');
        if (m) { m.classList.remove('hidden'); m.classList.add('flex'); }
        var inp = document.getElementById('inputSuperClave');
        if (inp) inp.focus();
    } else {
        window.ticket = window.ticket.filter(function (i) { return i.id !== id; });
        renderizarTicket();
    }
};

// ============================================================
// TICKET — CANTIDAD Y OBSERVACIÓN
// ============================================================
window.cambiarCantidad = function (index, delta) {
    var item = window.ticket[index];
    if (item && item.cantidad + delta > 0) {
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
        var bloqueado = item.existente ? 'btn-disabled' : '';
        var readonly = item.existente ? 'readonly' : '';
        var borde = item.existente ? 'border-emerald-500/30' : 'border-slate-700/50';
        var tag = item.existente ? '<span class="text-[8px] text-emerald-400 border border-emerald-400 px-1 rounded ml-1">ENVIADO</span>' : '';
        var focusClass = item.existente ? 'opacity-50 cursor-not-allowed' : 'focus:border-indigo-500';
        var nombre = item.descripcion || item.nombre || '';

        return (
            '<div class="bg-slate-800/40 p-4 rounded-3xl border ' + borde + ' mb-3">' +
            '<div class="flex justify-between items-start mb-3">' +
            '<div class="flex-1">' +
            '<p class="text-xs font-black text-white uppercase leading-tight">' + nombre + ' ' + tag + '</p>' +
            '<p class="text-[10px] text-indigo-400 font-bold">$' + (item.precio * item.cantidad).toLocaleString() + '</p>' +
            '</div>' +
            '<button onclick="window.eliminarDelTicket(' + item.id + ')" class="text-slate-500 hover:text-red-500 transition-colors">' +
            '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2"></path></svg>' +
            '</button>' +
            '</div>' +
            '<div class="flex items-center gap-4">' +
            '<div class="flex items-center bg-slate-900 rounded-xl p-1 border border-slate-700">' +
            '<button onclick="window.cambiarCantidad(' + index + ',-1)" class="w-7 h-7 flex items-center justify-center text-white rounded-lg ' + bloqueado + '">-</button>' +
            '<span class="w-8 text-center text-xs font-bold text-indigo-400">' + item.cantidad + '</span>' +
            '<button onclick="window.cambiarCantidad(' + index + ',1)" class="w-7 h-7 flex items-center justify-center text-white rounded-lg ' + bloqueado + '">+</button>' +
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
    var subtotal = window.ticket.reduce(function (a, i) { return a + (i.precio * i.cantidad); }, 0);
    var servicio = subtotal * 0.10;
    var total = subtotal + servicio;

    var s = document.getElementById('subtotal-val');
    var v = document.getElementById('servicio-val');
    var t = document.getElementById('total-val');
    if (s) s.innerText = '$' + subtotal.toLocaleString();
    if (v) v.innerText = '$' + servicio.toLocaleString();
    if (t) t.innerText = '$' + total.toLocaleString();
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
            var lA = document.getElementById('mesa-activa-label');
            var lM = document.getElementById('mesa-label');
            if (lA) lA.textContent = nombre;
            if (lM) lM.textContent = 'Mesa: ' + nombre;
            window.cerrarSelectorMesas();
            window.notificar('Mesa ' + nombre + ' seleccionada', 'success');
        } else {
            window.notificar(data.message || 'Error al seleccionar la mesa', 'error');
        }
    } catch (e) {
        console.error(e);
        window.notificar('Error de conexión', 'error');
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
            var lA = document.getElementById('mesa-activa-label');
            var lM = document.getElementById('mesa-label');
            if (lA) lA.textContent = 'SELECCIONAR MESA';
            if (lM) lM.textContent = 'Mesa: --';
            renderizarTicket();
            window.actualizarBadgeTicket(0);
            await refrescarMesas();
            window.notificar('Mesa liberada', 'success');
        }
    } catch (e) {
        window.notificar('Error al liberar mesa', 'error');
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
        var html = await res.text();
        var tmp = document.createElement('div');
        tmp.innerHTML = html;

        // Si la mesa vuelve a aparecer como disponible, expiró en servidor
        if (tmp.querySelector('[onclick*="seleccionarMesa(' + window.mesaSeleccionadaId + ',"]')) {
            window.mesaSeleccionadaId = null;
            var lA = document.getElementById('mesa-activa-label');
            var lM = document.getElementById('mesa-label');
            if (lA) lA.textContent = 'SELECCIONAR MESA';
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
window.enviarPedido = async function () {
    if (!window.mesaSeleccionadaId) { window.notificar('Selecciona una mesa primero', 'error'); return; }

    var itemsNuevos = window.ticket.filter(function (item) { return !item.existente; });
    if (!itemsNuevos.length) { window.notificar('No hay productos nuevos para enviar', 'warning'); return; }

    try {
        var mesaId = window.mesaSeleccionadaId;
        var nombreMesa = document.getElementById('mesa-activa-label').textContent;

        var res = await fetch('/pedidos/guardar', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                mesa_id: mesaId,
                items: itemsNuevos.map(function (item) {
                    return {
                        id: item.id,
                        nombre: item.descripcion || item.nombre || '',
                        descripcion: item.descripcion || item.nombre || '',
                        precio: item.precio,
                        cantidad: item.cantidad,
                        observacion: item.observacion || ''
                    };
                })
            })
        });

        var data = await res.json();

        if (res.ok && data.status === 'success') {
            window.notificar('¡Pedido enviado a cocina!', 'success');

            // ✅ Limpiar todo — el mesero ya entregó la orden
            window.ticket = [];
            window.mesaSeleccionadaId = null;
            renderizarTicket();
            window.actualizarBadgeTicket(0);

            var lA = document.getElementById('mesa-activa-label');
            var lM = document.getElementById('mesa-label');
            if (lA) lA.textContent = 'SELECCIONAR MESA';
            if (lM) lM.textContent = 'Mesa: --';

            // Refrescar el grid de mesas para que refleje estado ocupada
            await refrescarMesas();

        } else {
            window.notificar(data.message || 'Error al enviar', 'error');
        }

    } catch (e) {
        console.error('Error al enviar pedido:', e);
        window.notificar('Error de conexión', 'error');
    }
};

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
            var lA = document.getElementById('mesa-activa-label');
            var lM = document.getElementById('mesa-label');
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

            renderizarTicket();
            window.cerrarSelectorMesas();
            window.notificar('Pedido de ' + nombreMesa + ' cargado', 'success');
        } else {
            window.notificar(data.message || 'Sin pedidos pendientes', 'info');
        }
    } catch (e) {
        window.notificar('Error al cargar pedido', 'error');
    }
};

// ============================================================
// SUPER CLAVE
// ============================================================
window.validarSuperClave = async function () {
    var clave = document.getElementById('inputSuperClave').value;

    if (clave === '1234') {
        try {
            var item = window.ticket.find(function (i) { return i.id === window.idProductoAEliminar; });

            if (item && item.existente) {
                var res = await fetch('/pedidos/eliminar-item', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        mesa_id: window.mesaSeleccionadaId,
                        producto_id: window.idProductoAEliminar,
                        clave: clave
                    })
                });

                if (!res.ok) throw new Error('Error en servidor');

                var data = await res.json();

                // ✅ Si el servidor eliminó el pedido completo (era el último item)
                // limpiar todo en lugar de intentar recargar un pedido que ya no existe
                if (data.pedido_eliminado) {
                    window.ticket = [];
                    window.mesaSeleccionadaId = null;
                    renderizarTicket();
                    window.actualizarBadgeTicket(0);
                    var lA = document.getElementById('mesa-activa-label');
                    var lM = document.getElementById('mesa-label');
                    if (lA) lA.textContent = 'SELECCIONAR MESA';
                    if (lM) lM.textContent = 'Mesa: --';
                    await refrescarMesas();
                    window.cerrarSuperClave();
                    window.notificar('Último producto eliminado, mesa liberada', 'success');
                    return;
                }

            } else {
                // Item nuevo, solo filtrar local
                window.ticket = window.ticket.filter(function (i) { return i.id !== window.idProductoAEliminar; });
                renderizarTicket();
                window.cerrarSuperClave();
                window.notificar('Producto eliminado', 'success');
                return;
            }

            // Quedan más items → recargar desde DB
            var mesaId = window.mesaSeleccionadaId;
            var nombreMesa = document.getElementById('mesa-activa-label').textContent;
            window.cerrarSuperClave();
            await window.cargarPedidoExistente(mesaId, nombreMesa);
            window.notificar('Producto eliminado', 'success');

        } catch (e) {
            console.error(e);
            window.notificar('No se pudo eliminar: ' + e.message, 'error');
        }
    } else {
        window.notificar('SuperClave incorrecta', 'error');
        document.getElementById('inputSuperClave').value = '';
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
        var lM = document.getElementById('mesa-label');
        if (lA) lA.textContent = 'SELECCIONAR MESA';
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
        var lM2 = document.getElementById('mesa-label');
        if (lA2) lA2.textContent = 'SELECCIONAR MESA';
        if (lM2) lM2.textContent = 'Mesa: --';

        if (mesaId) window.liberarMesaActual(mesaId); // ✅ liberar en servidor
        return;
    }

    // Tiene items nuevos → confirmar antes de liberar
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

// ============================================================
// MODAL DE PAGO — Lógica completa
// ============================================================
window.metodoSeleccionado = 'efectivo';

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

    // Sincronizar total y mesa
    var totalStr = document.getElementById('total-val').innerText;
    var totalPagar = document.getElementById('pago-total-val');
    var mesaLabel = document.getElementById('pago-mesa-label');
    var mesaNombre = document.getElementById('mesa-activa-label').textContent;

    if (totalPagar) totalPagar.innerText = totalStr;
    if (mesaLabel) mesaLabel.innerText = 'Mesa: ' + mesaNombre;

    // Resetear estado del modal
    var inputRecibido = document.getElementById('montoRecibido');
    if (inputRecibido) inputRecibido.value = '';
    var cambioEl = document.getElementById('pago-cambio-val');
    if (cambioEl) cambioEl.innerText = '$0';

    // Resetear método a efectivo por defecto
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
    const wrapperRecibido = document.getElementById('wrapper-recibido');

    // Resetear vistas
    panelTarjeta.classList.add('hidden');
    panelTransfer.classList.add('hidden');

    if (metodo === 'tarjeta') {
        panelTarjeta.classList.remove('hidden');
        wrapperRecibido.style.opacity = '0.3';
    } else if (metodo === 'transferencia') {
        panelTransfer.classList.remove('hidden');
        wrapperRecibido.style.opacity = '0.3';
    } else {
        wrapperRecibido.style.opacity = '1';
    }
};

window.calcularCambio = function () {
    var totalEl = document.getElementById('pago-total-val');
    // Eliminar símbolo $ y separadores de miles para parsear correctamente COP
    var totalStr = totalEl ? totalEl.innerText.replace(/[^0-9]/g, '') : '0';
    var total = parseInt(totalStr) || 0;
    var recibido = parseInt(document.getElementById('montoRecibido').value) || 0;
    var cambio = recibido - total;

    var cambioEl = document.getElementById('pago-cambio-val');
    if (cambioEl) {
        if (cambio > 0) {
            cambioEl.innerText = '$ ' + cambio.toLocaleString('es-CO');
            cambioEl.classList.remove('text-red-400');
            cambioEl.classList.add('text-emerald-400');
        } else if (cambio < 0) {
            // Falta dinero
            cambioEl.innerText = '- $ ' + Math.abs(cambio).toLocaleString('es-CO');
            cambioEl.classList.remove('text-emerald-400');
            cambioEl.classList.add('text-red-400');
        } else {
            cambioEl.innerText = '$0';
            cambioEl.classList.remove('text-red-400');
            cambioEl.classList.add('text-emerald-400');
        }
    }
};

window.procesarPagoFinal = async function () {
    // 1. Obtener el total numérico una sola vez
    var totalEl = document.getElementById('pago-total-val');
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
                // Nuevos campos para la base de datos
                tipo_tarjeta: detallesPago.tipo_tarjeta,
                banco_destino: detallesPago.banco_destino,
                referencia: detallesPago.referencia
            })
        });

        var data = await res.json();

        if (res.ok && data.status === 'success') {
            window.notificar('¡Venta realizada! Mesa liberada', 'success');
            window.cerrarModalPago();

            // Limpiar estado de la App
            window.ticket = [];
            window.mesaSeleccionadaId = null;
            
            // Si tienes estas funciones definidas en tu facturacion.js
            if (typeof renderizarTicket === "function") renderizarTicket();
            if (typeof window.actualizarBadgeTicket === "function") window.actualizarBadgeTicket(0);

            var lA = document.getElementById('mesa-activa-label');
            var lM = document.getElementById('mesa-label');
            if (lA) lA.textContent = 'SELECCIONAR MESA';
            if (lM) lM.textContent = 'Mesa: --';

            // Actualizar la vista de mesas (para que cambie de color a disponible)
            if (typeof refrescarMesas === "function") await refrescarMesas();
            
        } else {
            window.notificar(data.message || 'Error al procesar el pago', 'error');
        }

    } catch (e) {
        console.error('Error procesando pago:', e);
        window.notificar('Error de conexión al procesar pago', 'error');
    }
};