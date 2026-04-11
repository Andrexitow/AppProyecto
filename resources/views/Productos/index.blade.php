<div class="space-y-4">

    <!-- HEADER -->
    <div class="flex justify-between items-center">

        <!-- BUSCADOR -->
        <input type="text" placeholder="Buscar producto..."
            class="w-1/3 px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

        <!-- BOTONES -->
        <div class="flex gap-2">

            <button onclick="openModalProducto()"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow text-sm">
                + Nuevo Producto
            </button>


        </div>

    </div>

    <!-- TABLA -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">

        <table class="min-w-full text-sm text-left">

            <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">Código</th>
                    <th class="px-4 py-3">Descripción</th>
                    <th class="px-4 py-3">Und Detal</th>
                    <th class="px-4 py-3">Existencia</th>
                    <th class="px-4 py-3">Precio Detal</th>
                    <th class="px-4 py-3">Categoría</th>
                    <th class="px-4 py-3">Inactivo</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>


            <tbody class="divide-y">

                @forelse ($productos as $producto)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $producto->codigo }}</td>
                        <td class="px-4 py-2">{{ $producto->descripcion }}</td>
                        <td class="px-4 py-2">{{ $producto->und_detal }}</td>
                        <td class="px-4 py-2">0</td> <!-- luego inventario -->
                        <td class="px-4 py-2">
                            ${{ number_format($producto->precio, 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-2">{{ $producto->categoria }}</td>
                        <td class="px-4 py-2">
                            @if ($producto->inactivo)
                                <span class="text-red-600">Sí</span>
                            @else
                                <span class="text-green-600">No</span>
                            @endif
                        </td>

                        <!-- ACCIONES -->
                        <td class="px-4 py-2 text-center flex justify-center gap-2">

                            <button class="bg-yellow-400 hover:bg-yellow-500 text-white px-2 py-1 rounded text-xs">
                                Editar
                            </button>

                            <button class="bg-gray-400 hover:bg-gray-500 text-white px-2 py-1 rounded text-xs">
                                {{ $producto->inactivo ? 'Activar' : 'Desactivar' }}
                            </button>

                            <button class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">
                                Eliminar
                            </button>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-4 text-gray-400">
                            No hay productos registrados
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>
    <!-- MODAL -->
    <div id="modalProducto" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">

        <div class="bg-white w-full max-w-2xl rounded-lg shadow-lg p-6">

            <!-- HEADER -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold">Nuevo Producto</h2>
            </div>

            <form id="formProducto">

                <div class="grid grid-cols-2 gap-4">

                    <!-- CÓDIGO -->
                    <input name="codigo" placeholder="Código *" required class="border px-3 py-2 rounded w-full">

                    <!-- DESCRIPCIÓN -->
                    <input name="descripcion" placeholder="Descripción *" required
                        class="border px-3 py-2 rounded w-full">

                    <!-- UNIDAD DETAL -->
                    <input name="und_detal" placeholder="Unidad Detal *" required
                        class="border px-3 py-2 rounded w-full">

                    <!-- CATEGORÍA -->
                    <input name="categoria" placeholder="Categoría *" required class="border px-3 py-2 rounded w-full">

                    <!-- CARACTERÍSTICAS (más largo) -->
                    <textarea name="caracteristicas" placeholder="Características" class="border px-3 py-2 rounded w-full col-span-2"></textarea>

                </div>

                <!-- BOTONES -->
                <div class="flex justify-end gap-2 mt-6">
                    <button type="button" onclick="closeModalProducto()" class="px-4 py-2 bg-gray-300 rounded">
                        Cancelar
                    </button>

                    <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded">
                        Guardar
                    </button>
                </div>

            </form>
        </div>

    </div>

</div>
