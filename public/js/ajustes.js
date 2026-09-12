function escAj(s) {
    return String(s ?? '').replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

/* ════════════════════════════════════════════════
   BUSCADOR DE TERCERO (autocompletar en vivo)
════════════════════════════════════════════════ */
let ajTercerosActuales = [];

function buscarTerceroAj() {
    const texto = document.getElementById('aj-tercero-buscar')?.value.trim() || '';
    const contenedor = document.getElementById('aj-tercero-resultados');
    document.getElementById('tercero_id').value = '';

    if (texto.length < 2) {
        contenedor?.classList.add('hidden');
        return;
    }

    fetch('/terceros/buscar?query=' + encodeURIComponent(texto))
        .then(res => res.json())
        .then(data => {
            ajTercerosActuales = Array.isArray(data) ? data : (data.data || []);
            renderResultadosTerceroAj();
        })
        .catch(() => {});
}

function nombreTerceroAj(t) {
    return t.nombre_completo || (t.nombre ? `${t.nombre} ${t.apellido || ''}`.trim() : t.razon_social) || 'Sin nombre';
}

function renderResultadosTerceroAj() {
    const contenedor = document.getElementById('aj-tercero-resultados');
    if (!contenedor) return;

    if (!ajTercerosActuales.length) {
        contenedor.innerHTML = '<div class="aj-result-empty">Sin coincidencias</div>';
    } else {
        contenedor.innerHTML = ajTercerosActuales.map(t => {
            const doc = t.cedula || t.nit || '—';
            return `<button type="button" class="aj-result-item" onclick="seleccionarTerceroAj(${t.id})">` +
                `<span>${escAj(nombreTerceroAj(t))}</span><span class="aj-result-mono">${escAj(doc)}</span></button>`;
        }).join('');
    }
    contenedor.classList.remove('hidden');
}

window.seleccionarTerceroAj = function (id) {
    const t = ajTercerosActuales.find(x => Number(x.id) === Number(id));
    if (!t) return;
    const doc = t.cedula || t.nit || '';
    document.getElementById('tercero_id').value = t.id;
    document.getElementById('aj-tercero-buscar').value = nombreTerceroAj(t) + (doc ? ' — ' + doc : '');
    document.getElementById('aj-tercero-resultados').classList.add('hidden');
};

/* ════════════════════════════════════════════════
   BUSCADOR DE CUENTA DE CONTRAPARTIDA (PUC)
════════════════════════════════════════════════ */
const AJ_LIMITE_CUENTAS = 30;

function renderResultadosContraparteAj() {
    const contenedor = document.getElementById('aj-contraparte-resultados');
    if (!contenedor) return;

    const texto = (document.getElementById('aj-contraparte-buscar')?.value || '').toLowerCase().trim();
    const cuentas = Array.isArray(window.AJ_CUENTAS) ? window.AJ_CUENTAS : [];
    const coincidencias = cuentas.filter(c => !texto || (c.codigo + ' ' + c.nombre).toLowerCase().includes(texto));

    if (!coincidencias.length) {
        contenedor.innerHTML = '<div class="aj-result-empty">Sin coincidencias</div>';
    } else {
        const idSeleccionada = document.getElementById('contraparte_cuenta_id').value;
        contenedor.innerHTML = coincidencias.slice(0, AJ_LIMITE_CUENTAS).map(c => {
            const activa = String(c.id) === String(idSeleccionada) ? ' activo' : '';
            return `<button type="button" class="aj-result-item${activa}" onclick="seleccionarContraparteAj(${c.id})">` +
                `<span class="aj-result-mono">${escAj(c.codigo)}</span><span>${escAj(c.nombre)}</span></button>`;
        }).join('');
        if (coincidencias.length > AJ_LIMITE_CUENTAS) {
            contenedor.innerHTML += `<div class="aj-result-empty">Mostrando ${AJ_LIMITE_CUENTAS} de ${coincidencias.length} · siga escribiendo para refinar</div>`;
        }
    }
    contenedor.classList.remove('hidden');
}

window.seleccionarContraparteAj = function (id) {
    const cuentas = Array.isArray(window.AJ_CUENTAS) ? window.AJ_CUENTAS : [];
    const c = cuentas.find(x => Number(x.id) === Number(id));
    if (!c) return;
    document.getElementById('contraparte_cuenta_id').value = c.id;
    document.getElementById('aj-contraparte-buscar').value = c.codigo + ' - ' + c.nombre;
    document.getElementById('aj-contraparte-resultados').classList.add('hidden');
};

// Usado por retomarAjuste()/editarAjuste() para precargar los dos buscadores
// (tercero y contrapartida) con los datos de un ajuste ya existente.
function precargarTerceroYContraparteAj(a) {
    document.getElementById('tercero_id').value = a.tercero_id ?? '';
    const doc = a.tercero?.cedula || a.tercero?.nit || '';
    const nombre = a.tercero ? nombreTerceroAj(a.tercero) : '';
    document.getElementById('aj-tercero-buscar').value = nombre ? (nombre + (doc ? ' — ' + doc : '')) : '';

    document.getElementById('contraparte_cuenta_id').value = a.contraparte_cuenta_id ?? '';
    const cuentas = Array.isArray(window.AJ_CUENTAS) ? window.AJ_CUENTAS : [];
    const cuenta = cuentas.find(c => Number(c.id) === Number(a.contraparte_cuenta_id));
    document.getElementById('aj-contraparte-buscar').value = cuenta ? (cuenta.codigo + ' - ' + cuenta.nombre) : '';
}

const buscarTerceroAjDebounced = debounce(buscarTerceroAj, 300);

document.addEventListener('input', function (e) {
    if (e.target.id === 'aj-tercero-buscar') buscarTerceroAjDebounced();
    if (e.target.id === 'aj-contraparte-buscar') renderResultadosContraparteAj();
});

// "focus" no burbujea; "focusin" sí. Al enfocar la contrapartida mostramos
// todas las cuentas para no obligar a escribir si el catálogo es corto.
document.addEventListener('focusin', function (e) {
    if (e.target.id === 'aj-contraparte-buscar') renderResultadosContraparteAj();
});

document.addEventListener('click', function (e) {
    if (!e.target.closest('#aj-tercero-buscar') && !e.target.closest('#aj-tercero-resultados')) {
        document.getElementById('aj-tercero-resultados')?.classList.add('hidden');
    }
    if (!e.target.closest('#aj-contraparte-buscar') && !e.target.closest('#aj-contraparte-resultados')) {
        document.getElementById('aj-contraparte-resultados')?.classList.add('hidden');
    }
});

window.obtenerSiguienteNumero = function () {

    let prefijo = document.getElementById('prefijo').value;

    fetch(`/ajustes/siguiente-numero?prefijo=${prefijo}`)
        .then(res => res.json())
        .then(data => {
            let numeroFormateado = String(data.numero).padStart(4, '0');
            document.getElementById('numero').value = numeroFormateado;
        });

};

document.addEventListener('change', function (e) {
    if (e.target.id === 'prefijo') {
        obtenerSiguienteNumero();
    }
});

/* ════════════════════════════════════════════════
   GUARDAR / REGISTRAR AJUSTE (encabezado + productos
   visibles en una sola pantalla, sin paso "Siguiente")
════════════════════════════════════════════════ */

// Recolecta los valores actuales del encabezado del formulario.
function recolectarCabeceraAjuste() {
    return {
        prefijo: document.getElementById('prefijo')?.value,
        numero: document.getElementById('numero')?.value,
        fecha: document.getElementById('fecha')?.value,
        tercero_id: document.getElementById('tercero_id')?.value,
        bodega_id: document.getElementById('bodega_id')?.value,
        contraparte_cuenta_id: document.getElementById('contraparte_cuenta_id')?.value || null,
        observaciones: document.getElementById('observaciones')?.value || null,
    };
}

function validarCabeceraAjuste(datos) {
    if (!datos.tercero_id) return 'Debes seleccionar un tercero';
    if (!datos.bodega_id) return 'Debes seleccionar una bodega';
    if (!datos.fecha) return 'Debes ingresar una fecha';
    if (!datos.prefijo || !datos.numero) return 'Error con el documento';
    return null;
}

// Crea (POST) o actualiza (PUT) el encabezado del ajuste activo y devuelve
// una Promise con su id. La usan tanto "Guardar borrador" como "Registrar
// Ajuste", para que registrar sea una sola acción aunque el encabezado
// todavía no exista en la base de datos.
function guardarCabeceraAjuste() {
    const datos = recolectarCabeceraAjuste();
    const error = validarCabeceraAjuste(datos);
    if (error) return Promise.reject(new Error(error));

    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
    const url = window.ajusteActivoId ? `/ajustes/${window.ajusteActivoId}` : '/ajustes';
    const method = window.ajusteActivoId ? 'PUT' : 'POST';

    return fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        body: JSON.stringify(datos),
    }).then(async res => {
        const data = await res.json();
        if (!res.ok) throw new Error(data.error || 'No se pudo guardar el encabezado del ajuste');
        if (!window.ajusteActivoId) window.ajusteActivoId = data.id;
        return window.ajusteActivoId;
    });
}

// "Guardar borrador": deja el encabezado listo sin tocar el inventario.
// Útil cuando aún no se tienen todos los productos a la mano; el ajuste
// queda "Pendiente" en el listado y se retoma luego con "Completar".
window.guardarBorradorAjuste = function () {
    // El borrador solo guarda el encabezado; los productos agregados en pantalla
    // aún no están respaldados en el servidor, así que avisamos antes de perderlos.
    if (recolectarDetallesAjuste().length > 0) {
        const continuar = window.confirm('Los productos agregados no se guardarán como borrador (solo se aplican al registrar). ¿Deseas continuar?');
        if (!continuar) return;
    }

    const btn = document.getElementById('btnGuardarBorrador');
    if (btn) btn.disabled = true;

    guardarCabeceraAjuste()
        .then(() => {
            mostrarNotificacion('Borrador guardado. Puedes completarlo luego desde "Pendientes".', 'success');
            closeModalAjuste();
            loadView('ajustes');
        })
        .catch(err => mostrarNotificacion(err.message, 'error'))
        .finally(() => { if (btn) btn.disabled = false; });
};

function recolectarDetallesAjuste() {
    return Array.from(document.querySelectorAll('#tablaProductos tr')).map(fila => ({
        producto_id: fila.id.replace('prod_', ''),
        cantidad: parseFloat(fila.querySelector('.cantidad').value) || 0,
        precio: parseFloat(fila.querySelector('.precio').value) || 0,
        tipo: fila.querySelector('.tipo').value,
    }));
}

// "Registrar Ajuste": guarda (o actualiza) el encabezado y en la misma
// acción registra los productos, aplicando el movimiento al inventario.
// Antes hacía falta pulsar "Siguiente" (que ya creaba el ajuste en
// borrador) y luego, en una pantalla aparte, "Guardar Ajuste". Ahora
// encabezado y productos se ven juntos y esto es un único clic.
window.registrarAjusteCompleto = function () {
    const detalles = recolectarDetallesAjuste();

    if (detalles.length === 0) {
        mostrarNotificacion('Agrega al menos un producto antes de registrar', 'error');
        return;
    }

    const btn = document.getElementById('btnRegistrarAjuste');
    if (btn) { btn.disabled = true; btn.textContent = '⏳ Registrando…'; }

    guardarCabeceraAjuste()
        .then(id => fetch(`/ajustes/${id}/detalles`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ detalles }),
        }))
        .then(async res => {
            const response = await res.json();
            if (!res.ok) {
                const msg = response.error?.detalle || response.error?.mensaje || response.error || 'Error al registrar';
                throw new Error(msg);
            }
            return response;
        })
        .then(() => {
            mostrarNotificacion('Ajuste registrado correctamente', 'success');
            window.ajusteActivoId = null;
            closeModalAjuste();
            loadView('ajustes');
        })
        .catch(err => mostrarNotificacion(err.message, 'error'))
        .finally(() => {
            if (btn) { btn.disabled = false; btn.textContent = '✅ Registrar Ajuste'; }
        });
};

window.retomarAjuste = function (id) {

    fetch(`/ajustes/${id}`, {
        credentials: 'same-origin'
    })
        .then(res => res.json())
        .then(a => {

            window.ajusteActivoId = a.id;

            openModalAjuste();

            document.getElementById('prefijo').value = a.prefijo;
            document.getElementById('numero').value = String(a.numero).padStart(4, '0');
            document.getElementById('fecha').value = a.fecha;
            document.getElementById('bodega_id').value = a.bodega_id;

            precargarTerceroYContraparteAj(a);

            document.getElementById('observaciones').value = a.observaciones ?? '';
        })
        .catch(err => {
            console.error(err);
            alert('Error cargando ajuste');
        });
};

window.verAjuste = function (id) {

    fetch(`/ajustes/${id}`, {
        credentials: 'same-origin'
    })
        .then(res => res.json())
        .then(a => {

            openModalVerAjuste();

            // ================= CABECERA =================
            document.getElementById('ver_doc').innerText =
                a.prefijo + '-' + String(a.numero).padStart(4, '0');

            document.getElementById('ver_fecha').innerText = a.fecha;

            document.getElementById('ver_tercero').innerText =
                a.tercero?.nombre_completo || '';

            document.getElementById('ver_bodega').innerText =
                a.bodega?.descripcion || 'Sin bodega';

            document.getElementById('ver_obs').innerText =
                a.observaciones ?? '';

            document.getElementById('ver_total').innerText =
                '$' + Number(a.total).toLocaleString();

            // ================= DETALLES =================
            const tbody = document.getElementById('ver_detalles');
            tbody.innerHTML = '';

            if (!a.detalles || a.detalles.length === 0) {
                tbody.innerHTML = `
                <tr>
                    <td colspan="2" class="text-center p-3 text-gray-400">
                        Sin productos
                    </td>
                </tr>
            `;
                return;
            }

            a.detalles.forEach(d => {
                tbody.innerHTML += `
                <tr class="border-t">
                    <td class="p-2">
                        ${d.producto?.descripcion ?? 'Sin nombre'}
                    </td>
                    <td class="p-2 text-center">
                        ${d.cantidad}
                    </td>
                </tr>
            `;
            });

        })
        .catch(err => {
            console.error(err);
            mostrarNotificacion(err.message, 'error');
        });
};



window.editarAjuste = function (id) {

    fetch(`/ajustes/${id}`, {
        credentials: 'same-origin'
    })
        .then(res => res.json())
        .then(a => {

            window.ajusteActivoId = a.id;

            openModalAjuste();

            const titulo = document.querySelector('#modalAjuste .aj-modal-title');
            if (titulo) titulo.innerText = 'Editar Ajuste';

            document.getElementById('prefijo').value = a.prefijo;
            document.getElementById('numero').value = String(a.numero).padStart(4, '0');
            document.getElementById('fecha').value = a.fecha;
            document.getElementById('bodega_id').value = a.bodega_id;

            precargarTerceroYContraparteAj(a);

            document.getElementById('observaciones').value = a.observaciones ?? '';

        })
        .catch(err => {
            console.error(err);
            mostrarNotificacion(err.message, 'error');
        });
};

window.eliminarAjuste = function (id) {

    abrirConfirm('¿Seguro que deseas eliminar este ajuste?', () => {

        fetch(`/ajustes/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
            .then(async res => {
                const response = await res.json();
                if (!res.ok) throw new Error(response.error || 'Error al eliminar');
                return response;
            })
            .then(() => {

                const btn = document.querySelector(`button[onclick="eliminarAjuste(${id})"]`);
                const fila = btn.closest('tr');
                fila.remove();

                mostrarNotificacion('Ajuste eliminado correctamente', 'success');

            })
            .catch(err => {
                console.error(err);
                mostrarNotificacion(err.message, 'error');
            });

    });

};

let accionConfirmada = null;

window.abrirConfirm = function (mensaje, callback) {

    const modal = document.getElementById('modalConfirm');

    if (!modal) return console.error('NO EXISTE modalConfirm');

    document.getElementById('confirmMensaje').innerText = mensaje;

    modal.classList.remove('hidden');
    modal.classList.add('flex');

    accionConfirmada = callback;
};

window.cerrarConfirm = function () {
    const modal = document.getElementById('modalConfirm');

    modal.classList.add('hidden');
    modal.classList.remove('flex'); // 👈 importante

    accionConfirmada = null;
};

// botón confirmar (versión PRO)
document.addEventListener('click', function (e) {
    if (e.target.closest('#btnConfirmarAccion')) {
        if (accionConfirmada) accionConfirmada();
        cerrarConfirm();
    }
});

window.revertirAjuste = function (id) {

    abrirConfirm('¿Seguro que deseas revertir este ajuste?', () => {

        fetch(`/ajustes/${id}/revertir`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
            .then(async res => {
                const response = await res.json();
                if (!res.ok) throw new Error(response.error || 'Error al revertir');
                return response;
            })
            .then(() => {

                mostrarNotificacion('Ajuste revertido correctamente', 'success');

                // 🔥 recargar vista
                loadView('ajustes');

            })
            .catch(err => {
                console.error(err);
                mostrarNotificacion(err.message, 'error');
            });

    });
};
