
window.cargarExistencias = function () {
    const select = document.getElementById('bodega_id');
    const container = document.getElementById('contenidoExistencias');
    let bodega_id = select.value;

    if (!bodega_id) {
        alert('Por favor, selecciona una bodega primero.');
        return;
    }

    // Feedback visual de carga
    container.innerHTML = '<div class="p-10 text-center text-gray-500 italic">Cargando existencias...</div>';

    fetch(`/existencias/data?bodega_id=${bodega_id}`, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
        .then(async res => {
            if (!res.ok) {
                const errorText = await res.text();
                console.error('Error backend:', errorText);
                throw new Error(errorText);
            }
            return res.text();
        })
        .then(html => {
            container.innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            container.innerHTML = '<div class="p-10 text-center text-red-500">Error al cargar los datos. Intente de nuevo.</div>';
        });
}

window.imprimir = function () {
    let contenido = document.getElementById('contenidoExistencias').innerHTML;
    let bodegaNombre = document.getElementById('bodega_id').options[document.getElementById('bodega_id').selectedIndex].text;
    let ventana = window.open('', '', 'height=600,width=800');

    ventana.document.write(`
        <html>
            <head>
                <title>Reporte de Inventario</title>
                <style>
                    body { font-family: sans-serif; padding: 20px; }
                    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                    th, td { border: 1px solid #ddd; padding: 12px; text-align: left; }
                    th { background-color: #f2f2f2; }
                    .header { text-align: center; margin-bottom: 30px; }
                </style>
            </head>
            <body>
                <div class="header">
                    <h1>Reporte de Existencias</h1>
                    <p>Bodega: ${bodegaNombre}</p>
                    <p>Fecha: ${new Date().toLocaleDateString()}</p>
                </div>
                ${contenido}
            </body>
        </html>
    `);

    ventana.document.close();
    setTimeout(() => {
        ventana.print();
        ventana.close();
    }, 500);
}