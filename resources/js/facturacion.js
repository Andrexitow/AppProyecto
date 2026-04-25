// facturacion.js

// Estado global de la terminal
let ticket = [];
let mesaSeleccionada = null;

// MENSAJE DIRECTO AL CARGAR ARCHIVO
console.log('%c✅ facturacion.js cargado correctamente', 'color: green; font-weight: bold;');

document.addEventListener('DOMContentLoaded', () => {
    console.log('%c🧾 Terminal POS inicializada', 'color: #4f46e5; font-weight: bold;');

    // Inicializar buscador
    const inputBusqueda = document.getElementById('buscarProducto');

    if (inputBusqueda) {
        console.log('%c🔍 Input buscarProducto encontrado', 'color: #0ea5e9;');
        inputBusqueda.addEventListener('input', filtrarProductos);
    } else {
        console.warn('⚠️ No se encontró #buscarProducto');
    }
});

function filtrarProductos(e) {
    const busqueda = e.target.value.toLowerCase();
    const productos = document.querySelectorAll('.item-producto');

    productos.forEach(producto => {
        const nombre = producto.getAttribute('data-nombre');
        producto.style.display = nombre.includes(busqueda) ? 'block' : 'none';
    });
}

window.agregarAlTicket = function (id, nombre, precio) {
    const itemExistente = ticket.find(item => item.id === id);

    if (itemExistente) {
        itemExistente.cantidad++;
    } else {
        // Añadimos el campo 'observacion' vacío por defecto
        ticket.push({ id, nombre, precio, cantidad: 1, observacion: "" });
    }
    renderizarTicket();
}

function renderizarTicket() {
    const contenedor = document.getElementById('ticket-items');
    if (!contenedor) return;

    contenedor.innerHTML = ticket.map((item, index) => {
        // Determinamos si el item está bloqueado (si ya existe en la DB)
        const bloqueado = item.existente ? 'btn-disabled' : '';
        const soloLectura = item.existente ? 'readonly' : '';

        return `
        <div class="bg-slate-800/40 p-4 rounded-3xl border ${item.existente ? 'border-emerald-500/30' : 'border-slate-700/50'} mb-3 animate-fade-left">
            <div class="flex justify-between items-start mb-3">
                <div class="flex-1">
                    <p class="text-xs font-black text-white uppercase leading-tight">
                        ${item.nombre} ${item.existente ? '<span class="text-[8px] text-emerald-400 border border-emerald-400 px-1 rounded ml-1">ENVIADO</span>' : ''}
                    </p>
                    <p class="text-[10px] text-indigo-400 font-bold">$${(item.precio * item.cantidad).toLocaleString()}</p>
                </div>
                <button onclick="eliminarDelTicket(${item.id})" class="text-slate-500 hover:text-red-500 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2"></path></svg>
                </button>
            </div>

            <div class="flex items-center gap-4">
                <div class="flex items-center bg-slate-900 rounded-xl p-1 border border-slate-700">
                    <button onclick="cambiarCantidad(${index}, -1)" class="w-7 h-7 flex items-center justify-center text-white rounded-lg ${bloqueado}">-</button>
                    <span class="w-8 text-center text-xs font-bold text-indigo-400">${item.cantidad}</span>
                    <button onclick="cambiarCantidad(${index}, 1)" class="w-7 h-7 flex items-center justify-center text-white rounded-lg ${bloqueado}">+</button>
                </div>

                <div class="flex-1">
                    <input type="text" 
                        placeholder="Nota..." 
                        value="${item.observacion || ''}"
                        ${soloLectura}
                        onchange="actualizarObservacion(${index}, this.value)"
                        class="w-full bg-slate-900/50 border border-slate-700 rounded-xl px-3 py-2 text-[10px] text-slate-300 focus:outline-none ${item.existente ? 'opacity-50 cursor-not-allowed' : 'focus:border-indigo-500'}"
                    >
                </div>
            </div>
        </div>
        `;
    }).join('');

    actualizarTotales();
}

function actualizarTotales() {
    const subtotal = ticket.reduce((acc, item) => acc + (item.precio * item.cantidad), 0);
    const servicio = subtotal * 0.10;
    const total = subtotal + servicio;

    if (document.getElementById('subtotal-val')) document.getElementById('subtotal-val').innerText = `$${subtotal.toLocaleString()}`;
    if (document.getElementById('servicio-val')) document.getElementById('servicio-val').innerText = `$${servicio.toLocaleString()}`;
    if (document.getElementById('total-val')) document.getElementById('total-val').innerText = `$${total.toLocaleString()}`;
}

window.seleccionarMesa = async function (id, nombre) {
    console.log("--- INICIANDO SELECCIÓN DE MESA ---");
    console.log("ID de mesa:", id);
    console.log("Nombre de mesa:", nombre);

    // 1. LIMPIEZA PREVENTIVA: Vaciamos el ticket antes de bloquear la nueva mesa
    // Esto evita que productos de una mesa anterior se dupliquen en la nueva.
    ticket = [];
    renderizarTicket();

    try {
        console.log("Enviando petición al servidor...");

        const response = await fetch(`/mesas/${id}/bloquear`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        });

        console.log("Respuesta recibida del servidor. Status:", response.status);

        const data = await response.json();
        console.log("Datos recibidos:", data);

        if (response.ok && data.status === 'success') {
            console.log("✅ ÉXITO: Mesa bloqueada correctamente.");

            // 2. ACTUALIZACIÓN DE UI
            window.mesaSeleccionadaId = id;
            document.getElementById('mesa-activa-label').textContent = nombre;
            document.getElementById('mesa-label').textContent = 'Mesa: ' + nombre;

            // Cerramos el modal de selección
            cerrarSelectorMesas();

            // Notificación elegante en lugar de alert
            notificar(`Mesa ${nombre} seleccionada correctamente`, "success");

        } else {
            console.error("❌ ERROR DEL SERVIDOR:", data.message);
            // Usamos tu sistema de notificaciones
            notificar(data.message || "Error al seleccionar la mesa", "error");
        }

    } catch (error) {
        console.error("🚨 ERROR CRÍTICO EN JS:", error);
        notificar("Hubo un problema de conexión con el servidor", "error");
    }
}

let idProductoAEliminar = null;
const SUPER_CLAVE_MAESTRA = "1234"; // Aquí puedes poner la que quieras o validarla vía API

window.eliminarDelTicket = function (id) {
    const item = ticket.find(i => i.id === id);

    // Si el item tiene la marca 'existente', pedimos clave
    if (item.existente) {
        idProductoAEliminar = id;
        document.getElementById('modalSuperClave').classList.remove('hidden');
        document.getElementById('modalSuperClave').classList.add('flex');
        document.getElementById('inputSuperClave').focus();
    } else {
        // Si es nuevo, se borra normal
        ticket = ticket.filter(i => i.id !== id);
        renderizarTicket();
    }
};

window.validarSuperClave = function () {
    const clave = document.getElementById('inputSuperClave').value;

    if (clave === SUPER_CLAVE_MAESTRA) {
        // Si la clave es correcta, eliminamos y cerramos
        ticket = ticket.filter(i => i.id !== idProductoAEliminar);
        renderizarTicket();
        cerrarSuperClave();
        notificar("Producto eliminado por administrador", "success");
    } else {
        notificar("SuperClave Incorrecta", "error");
        document.getElementById('inputSuperClave').value = "";
    }
};

window.cerrarSuperClave = function () {
    document.getElementById('modalSuperClave').classList.add('hidden');
    document.getElementById('modalSuperClave').classList.remove('flex');
    document.getElementById('inputSuperClave').value = "";
    idProductoAEliminar = null;
};

window.cambiarCantidad = function (index, delta) {
    if (ticket[index].cantidad + delta > 0) {
        ticket[index].cantidad += delta;
        renderizarTicket();
    }
};

window.actualizarObservacion = function (index, valor) {
    ticket[index].observacion = valor;
    // No renderizamos de nuevo aquí para no perder el foco del teclado
    console.log(`Nota actualizada para ${ticket[index].nombre}: ${valor}`);
};

window.filtrarMesasPorZona = function () {
    const filtroZona = document.getElementById('filtroZona');
    const buscarMesa = document.getElementById('buscarMesa');

    // Si los inputs no existen en el DOM, no hacemos nada
    if (!filtroZona || !buscarMesa) return;

    const zonaSeleccionada = filtroZona.value;
    const textoBusqueda = buscarMesa.value.toLowerCase();
    const mesas = document.querySelectorAll('.mesa-item');

    mesas.forEach(mesa => {
        // Usamos || '' para que si el atributo no existe, sea un texto vacío y no 'null'
        const mesaZonaId = mesa.getAttribute('data-zona') || '';
        const mesaNombre = (mesa.getAttribute('data-numero') || '').toLowerCase();

        const coincideZona = (zonaSeleccionada === 'todas' || mesaZonaId === zonaSeleccionada);
        const coincideTexto = mesaNombre.includes(textoBusqueda);

        if (coincideZona && coincideTexto) {
            mesa.style.display = 'block';
        } else {
            mesa.style.display = 'none';
        }
    });
}

window.liberarMesaActual = async function (id) {
    // Si no hay ID, intentamos usar el global por si acaso
    const mesaId = id || window.mesaSeleccionadaId;

    if (!mesaId) {
        notificar("No hay una mesa activa para liberar", "warning");
        return;
    }

    console.log("🔓 Liberando mesa ID:", mesaId);

    try {
        const response = await fetch(`/mesas/${mesaId}/liberar`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json'
            }
        });

        const data = await response.json();

        if (data.status === 'success') {
            console.log("✅ Mesa liberada en servidor.");

            // 1. LIMPIEZA DE VARIABLES (CRÍTICO)
            window.mesaSeleccionadaId = null;
            ticket = [];

            // 2. LIMPIEZA DE INTERFAZ DEL TICKET
            const labelActiva = document.getElementById('mesa-activa-label');
            const labelMesa = document.getElementById('mesa-label');

            if (labelActiva) labelActiva.textContent = 'SELECCIONAR MESA';
            if (labelMesa) labelMesa.textContent = 'Mesa: --';

            // 3. VACIAR EL TICKET VISUALMENTE
            renderizarTicket();

            // 4. REFRESCO VISUAL INMEDIATO
            // Primero quitamos la clase de selección manualmente por si el refresco tarda
            const mesaElemento = document.querySelector(`.mesa-item[onclick*="seleccionarMesa(${mesaId}"]`) ||
                document.querySelector(`.mesa-item[onclick*="cargarPedidoExistente(${mesaId}"]`);

            if (mesaElemento) {
                // Le quitamos las clases de 'seleccionada' o 'ocupada' de golpe
                mesaElemento.classList.remove('bg-emerald-600', 'border-emerald-400', 'bg-amber-500/20', 'border-amber-500', 'animate-pulse');
                // Le ponemos la clase de disponible (opcional, el refresco lo hará después)
                mesaElemento.classList.add('bg-slate-800/40', 'border-slate-700');
            }

            // Llamamos al refresco general para sincronizar con el servidor
            if (typeof window.refrescarMesas === 'function') {
                await window.refrescarMesas();
            }

            notificar("Mesa liberada con éxito", "success");
        }
    } catch (error) {
        console.error("Error al liberar mesa:", error);
        notificar("Error de conexión al liberar mesa", "error");
    }
};

// Variable para guardar qué función ejecutar al confirmar
let accionConfirmar = null;

window.mostrarConfirm = function (mensaje, callback) {
    const modal = document.getElementById('modalConfirm');
    const mensajeTxt = document.getElementById('confirmMensaje');

    mensajeTxt.innerText = mensaje;
    accionConfirmar = callback; // Guardamos la función (liberarMesaActual)

    modal.classList.remove('hidden');
    modal.classList.add('flex');
};

window.cerrarConfirm = function () {
    const modal = document.getElementById('modalConfirm');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    accionConfirmar = null;
};

// Escuchador para el botón de "Confirmar" dentro del modal
document.getElementById('btnConfirmarAccion').addEventListener('click', () => {
    if (accionConfirmar) {
        accionConfirmar(); // Ejecutamos la liberación de la mesa
        cerrarConfirm();
    }
});

// Reemplazo de tu función vaciarTicket
window.vaciarTicket = function () {
    // En lugar de confirm(), llamamos a nuestro modal
    mostrarConfirm(
        "¿Estás seguro de cancelar la orden? La mesa se liberará y se borrará el pedido.",
        liberarMesaActual
    );
};

async function refrescarMesas() {
    // Si no hay mesa seleccionada localmente, solo refrescamos el grid y salimos
    if (!window.mesaSeleccionadaId) {
        ejecutarRefrescoVisual();
        return;
    }

    try {
        const response = await fetch('/mesas/actualizar');
        const html = await response.text();

        // Creamos un elemento temporal para analizar el HTML que llegó
        const tempDiv = document.createElement('div');
        tempDiv.innerHTML = html;

        // BUSCAMOS NUESTRA MESA en el nuevo HTML
        const mesaEnServidor = tempDiv.querySelector(`[onclick*="seleccionarMesa(${window.mesaSeleccionadaId},"]`);

        // LÓGICA DE EXPULSIÓN:
        // Si la mesa aparece como "disponible" (porque tiene el onclick habilitado) 
        // significa que el servidor ya la liberó por tiempo.
        if (mesaEnServidor) {
            console.warn("⚠️ La mesa expiró en el servidor.");

            window.mesaSeleccionadaId = null;
            document.getElementById('mesa-activa-label').textContent = 'SELECCIONAR MESA';
            document.getElementById('mesa-label').textContent = 'Mesa: --';

            // CAMBIO AQUÍ: Usamos notificar en lugar de alert
            notificar("La sesión de la mesa ha expirado por inactividad", "warning");
        }

        // Finalmente actualizamos el grid visual
        document.getElementById('contenedorMesas').innerHTML = html;

        if (window.filtrarMesasPorZona) window.filtrarMesasPorZona();

    } catch (error) {
        console.error("Error al refrescar mesas:", error);
    }
}

// Función auxiliar para no repetir código
async function ejecutarRefrescoVisual() {
    const response = await fetch('/mesas/actualizar');
    const html = await response.text();
    document.getElementById('contenedorMesas').innerHTML = html;
    if (window.filtrarMesasPorZona) window.filtrarMesasPorZona();
}
// Iniciar el temporizador (5000ms = 5 segundos)
setInterval(refrescarMesas, 5000);

window.notificar = function (mensaje, tipo = 'info') {
    const container = document.getElementById('toast-container');
    const toast = document.createElement('div');

    // Colores según el tipo (puedes añadir más)
    const estilos = {
        info: 'bg-slate-800 border-indigo-500 text-white',
        warning: 'bg-amber-600 border-amber-400 text-white',
        error: 'bg-red-600 border-red-400 text-white',
        success: 'bg-emerald-600 border-emerald-400 text-white'
    };

    toast.className = `${estilos[tipo]} border-l-4 p-4 rounded-2xl shadow-2xl flex items-center gap-3 min-w-[300px] animate-fade-left animate-duration-300`;

    toast.innerHTML = `
        <div class="flex-1 font-bold text-sm uppercase tracking-wide">
            ${mensaje}
        </div>
        <button onclick="this.parentElement.remove()" class="opacity-50 hover:opacity-100">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12" stroke-width="2"></path></svg>
        </button>
    `;

    container.appendChild(toast);

    // Auto eliminar después de 4 segundos
    setTimeout(() => {
        toast.classList.replace('animate-fade-left', 'animate-fade-out');
        setTimeout(() => toast.remove(), 300);
    }, 4000);
};

window.enviarPedido = async function () {
    // 1. Validaciones básicas
    if (!window.mesaSeleccionadaId) {
        notificar("Primero debes seleccionar una mesa", "error");
        return;
    }

    if (ticket.length === 0) {
        notificar("El ticket está vacío", "warning");
        return;
    }

    console.log("🚀 Enviando pedido para mesa:", window.mesaSeleccionadaId);

    try {
        // Bloqueamos el botón o mostramos un estado de carga si lo deseas
        const response = await fetch('/pedidos/guardar', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                mesa_id: window.mesaSeleccionadaId,
                items: ticket // Enviamos todo el array de productos
            })
        });

        const data = await response.json();

        if (response.ok && data.status === 'success') {
            notificar("¡Pedido enviado a cocina correctamente!", "success");

            // 2. Limpiar todo después del éxito
            ticket = [];
            window.mesaSeleccionadaId = null;
            renderizarTicket();

            // Actualizar etiquetas visuales
            document.getElementById('mesa-activa-label').textContent = 'SELECCIONAR MESA';
            document.getElementById('mesa-label').textContent = 'Mesa: --';

            // Opcional: Cerrar cualquier modal abierto
            if (typeof cerrarSelectorMesas === 'function') cerrarSelectorMesas();

        } else {
            notificar(data.message || "Error al procesar el pedido", "error");
        }

    } catch (error) {
        console.error("🚨 Error al enviar pedido:", error);
        notificar("Error de conexión con el servidor", "error");
    }
};

window.cargarPedidoExistente = async function (mesaId, nombreMesa) {
    console.log("📂 Cargando pedido pendiente de mesa:", mesaId);

    try {
        const response = await fetch(`/pedidos/mesa/${mesaId}/pendiente`);

        // Verificamos si la respuesta es correcta antes de intentar parsear el JSON
        if (!response.ok) {
            throw new Error("No se pudo obtener el pedido del servidor");
        }

        const data = await response.json();

        if (data.status === 'success') {
            // 1. LIMPIEZA TOTAL: Vaciamos cualquier rastro de tickets anteriores
            ticket = [];

            // 2. ACTUALIZACIÓN DE INTERFAZ: Seteamos la mesa activa
            window.mesaSeleccionadaId = mesaId;

            const labelActiva = document.getElementById('mesa-activa-label');
            const labelMesa = document.getElementById('mesa-label');

            if (labelActiva) labelActiva.textContent = nombreMesa;
            if (labelMesa) labelMesa.textContent = 'Mesa: ' + nombreMesa;

            // 3. MAPEADO DE ITEMS: Sincronizamos con los nombres de columna del controlador
            // Usamos item.id e item.nombre (asegúrate que tu controlador envíe estas llaves)
            ticket = data.items.map(item => ({
                id: item.id,
                nombre: item.nombre,
                precio: parseFloat(item.precio),
                cantidad: parseInt(item.cantidad),
                observacion: item.observacion || "",
                existente: true // Bloquea la edición de estos items en el ticket
            }));

            // 4. RENDERIZADO Y CIERRE
            renderizarTicket();
            cerrarSelectorMesas();

            notificar(`Pedido de ${nombreMesa} cargado correctamente`, "success");
        } else {
            notificar(data.message || "La mesa no tiene pedidos pendientes", "info");
        }
    } catch (error) {
        console.error("🚨 Error al cargar pedido:", error);
        notificar("Error crítico al recuperar el pedido", "error");
    }
};

window.validarSuperClave = async function () {
    const clave = document.getElementById('inputSuperClave').value;

    if (clave === "1234") {
        try {
            // SI EL PRODUCTO YA EXISTÍA EN LA DB, HAY QUE BORRARLO ALLÁ
            const item = ticket.find(i => i.id === idProductoAEliminar);

            if (item.existente) {
                const response = await fetch('/pedidos/eliminar-item', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        mesa_id: window.mesaSeleccionadaId,
                        producto_id: idProductoAEliminar,
                        clave: clave
                    })
                });

                if (!response.ok) throw new Error("Error al borrar en servidor");
            }

            // Si el servidor dio OK (o si era un producto nuevo), borramos de la pantalla
            ticket = ticket.filter(i => i.id !== idProductoAEliminar);
            renderizarTicket();
            cerrarSuperClave();
            notificar("Producto eliminado definitivamente", "success");

        } catch (error) {
            console.error(error);
            notificar("No se pudo eliminar de la base de datos", "error");
        }
    } else {
        notificar("SuperClave Incorrecta", "error");
    }
};