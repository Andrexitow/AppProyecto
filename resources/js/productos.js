window.buscarProducto = debounce(function () {
    const query = document.getElementById('buscarProducto')?.value.trim();
    const contenedor = document.getElementById('resultadosProducto');

    if (!query || query.length < 2 || !contenedor) return;

    fetch(`/productos/buscar?query=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
            contenedor.innerHTML = '';
            contenedor.classList.remove('hidden');

            data.forEach(p => {
                let descripcion = (p.descripcion || '').replace(/'/g, "\\'");
                let codigo = p.codigo ?? '-';

                contenedor.innerHTML += `
                    <div onclick="agregarProducto(${p.id}, '${descripcion}', ${p.precio})"
                        class="flex gap-3 p-3 hover:bg-gray-100 cursor-pointer border-b text-sm">
                        <div class="text-gray-500 w-24">${codigo}</div>
                        <div class="flex-1">${p.descripcion}</div>
                    </div>
                `;
            });
        });
}, 300);

window.agregarProducto = function (id, descripcion, precio) {
    const tabla = document.getElementById('tablaProductos');

    if (!tabla || document.getElementById('prod_' + id)) return;

    tabla.innerHTML += `
        <tr id="prod_${id}" class="hover:bg-gray-50">
            <td class="p-4">${descripcion}</td>
            <td class="p-4">
                <input type="number" value="1" class="cantidad w-20 mx-auto block border rounded text-center">
            </td>
            <td class="p-4">
                <input type="number" value="${precio}" class="precio w-24 border rounded text-center">
            </td>
            <td class="p-4">
                <select class="tipo border rounded">
                    <option value="entrada">+ Entrada</option>
                    <option value="salida">- Salida</option>
                </select>
            </td>
            <td class="p-4 text-center">
                <button onclick="eliminarProducto(${id})" class="text-red-500">Eliminar</button>
            </td>
        </tr>
    `;

    const input = document.getElementById('buscarProducto');
    const resultados = document.getElementById('resultadosProducto');

    if (input) input.value = '';
    if (resultados) resultados.classList.add('hidden');
};

window.eliminarProducto = function (id) {
    document.getElementById('prod_' + id)?.remove();
};