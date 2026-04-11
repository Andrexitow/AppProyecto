<div class="space-y-4">

    <!-- HEADER -->
    <div class="flex justify-between items-center">

        <!-- BUSCADOR -->
        <input type="text" placeholder="Buscar bodega..."
            class="w-1/3 px-4 py-2 border rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">

        <!-- BOTÓN -->
        <div class="flex gap-2">

            <button onclick="openModalBodega()"
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg shadow text-sm">
                + Nueva Bodega
            </button>

        </div>

    </div>

    <!-- TABLA -->
    <div class="bg-white rounded-lg shadow overflow-x-auto">

        <table class="min-w-full text-sm text-left">

            <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                <tr>
                    <th class="px-4 py-3">ID</th>
                    <th class="px-4 py-3">Descripción</th>
                    <th class="px-4 py-3 text-center">Acciones</th>
                </tr>
            </thead>

            <tbody class="divide-y">

                @forelse ($bodegas as $bodega)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">{{ $bodega->id }}</td>
                        <td class="px-4 py-2">{{ $bodega->descripcion }}</td>

                        <!-- ACCIONES -->
                        <td class="px-4 py-2 text-center flex justify-center gap-2">

                            <button class="bg-yellow-400 hover:bg-yellow-500 text-white px-2 py-1 rounded text-xs">
                                Editar
                            </button>

                            <button class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded text-xs">
                                Eliminar
                            </button>

                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3" class="text-center py-4 text-gray-400">
                            No hay bodegas registradas
                        </td>
                    </tr>
                @endforelse

            </tbody>

        </table>

    </div>

    <!-- MODAL -->
    <div id="modalBodega" class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">

        <div class="bg-white w-full max-w-md rounded-lg shadow-lg p-6">

            <!-- HEADER -->
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-bold">Nueva Bodega</h2>
            </div>

            <!-- FORM -->
            <form id="formBodega">

                <div class="grid grid-cols-1 gap-4">

                    <!-- DESCRIPCIÓN -->
                    <input name="descripcion" placeholder="Descripción *" required
                        class="border px-3 py-2 rounded w-full focus:outline-none focus:ring-2 focus:ring-blue-500">

                </div>

                <!-- BOTONES -->
                <div class="flex justify-end gap-2 mt-6">

                    <button type="button" onclick="closeModalBodega()"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">
                        Cancelar
                    </button>

                    <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded">
                        Guardar
                    </button>

                </div>

            </form>
        </div>

    </div>

</div>

