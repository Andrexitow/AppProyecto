<div class="p-6 space-y-6 bg-gray-50 min-h-screen">
    <div
        class="flex flex-col md:flex-row justify-between items-center gap-4 bg-white p-4 rounded-xl shadow-sm border border-gray-100">
        <div class="relative w-full md:w-1/3">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3">
                <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
            </span>
            <input type="text" placeholder="Buscar producto por nombre o código..."
                class="w-full pl-10 pr-4 py-2 border-gray-200 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all">
        </div>

        <button onclick="openModalProducto()"
            class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg shadow-sm flex items-center justify-center font-medium transition-colors">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
            </svg>
            Nuevo Producto
        </button>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200 text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-4 font-semibold text-gray-600 uppercase tracking-wider">Código</th>
                    <th class="px-6 py-4 font-semibold text-gray-600 uppercase tracking-wider">Descripción</th>
                    <th class="px-6 py-4 font-semibold text-gray-600 uppercase tracking-wider text-center">Existencia
                    </th>
                    <th class="px-6 py-4 font-semibold text-gray-600 uppercase tracking-wider">Precio</th>
                    <th class="px-6 py-4 font-semibold text-gray-600 uppercase tracking-wider text-center">Estado</th>
                    <th class="px-6 py-4 font-semibold text-gray-600 uppercase tracking-wider text-right">Acciones</th>
                </tr>
            </thead>

            <tbody class="bg-white divide-y divide-gray-100">
                @forelse ($productos as $producto)
                    <tr class="hover:bg-blue-50/30 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap font-mono text-blue-600">{{ $producto->codigo }}</td>
                        <td class="px-6 py-4">
                            <div class="text-gray-900 font-medium">{{ $producto->descripcion }}</div>
                            <div class="text-gray-500 text-xs">{{ $producto->categoria }} | {{ $producto->und_detal }}
                            </div>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 bg-gray-100 rounded-full font-bold text-gray-700">
                                {{ $producto->inventarios->sum('stock') }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap font-semibold text-gray-800">
                            ${{ number_format($producto->precio, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 text-center">
                            @if ($producto->inactivo)
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                    Inactivo
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    Activo
                                </span>
                            @endif
                        </td>
                        <td class="px-6 py-4 text-right space-x-2">
                            <button title="Editar"
                                class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                </svg>
                            </button>
                            <button title="Cambiar estado"
                                class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                </svg>
                            </button>
                            <button title="Eliminar"
                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 mb-3 opacity-20" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path
                                        d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                </svg>
                                <span>No hay productos registrados</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
<!-- MODAL -->
<div id="modalProducto"
    class="fixed inset-0 bg-gray-900/60 hidden backdrop-blur-sm items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-2xl rounded-2xl shadow-2xl overflow-hidden transform transition-all">
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">Registrar Nuevo Producto</h2>
            <button onclick="closeModalProducto()" class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="formProducto" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-700">Código del Producto</label>
                    <input name="codigo" placeholder="Ej: PROD-001" required
                        class="w-full border-gray-200 focus:ring-2 focus:ring-blue-500 rounded-lg p-2.5 bg-gray-50 transition-all">
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-700">Categoría</label>
                    <select name="categoria"
                        class="w-full border-gray-200 focus:ring-2 focus:ring-blue-500 rounded-lg p-2.5 bg-gray-50 transition-all">
                        <option value="">Seleccione...</option>
                        <option value="General">General</option>
                    </select>
                </div>

                <div class="space-y-1 md:col-span-2">
                    <label class="text-sm font-semibold text-gray-700">Nombre / Descripción</label>
                    <input name="descripcion" placeholder="Nombre completo del producto" required
                        class="w-full border-gray-200 focus:ring-2 focus:ring-blue-500 rounded-lg p-2.5 bg-gray-50 transition-all">
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-700">Unidad de Medida</label>
                    <input name="und_detal" placeholder="Ej: Unidad, Kg, Paquete" required
                        class="w-full border-gray-200 focus:ring-2 focus:ring-blue-500 rounded-lg p-2.5 bg-gray-50 transition-all">
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-700">Precio de Venta</label>
                    <input type="number" name="precio" placeholder="0.00"
                        class="w-full border-gray-200 focus:ring-2 focus:ring-blue-500 rounded-lg p-2.5 bg-gray-50 transition-all">
                </div>

                <div class="space-y-1 md:col-span-2">
                    <label class="text-sm font-semibold text-gray-700">Características Adicionales</label>
                    <textarea name="caracteristicas" rows="3" placeholder="Detalles técnicos, colores, etc."
                        class="w-full border-gray-200 focus:ring-2 focus:ring-blue-500 rounded-lg p-2.5 bg-gray-50 transition-all"></textarea>
                </div>
            </div>

            <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-100">
                <button type="button" onclick="closeModalProducto()"
                    class="px-5 py-2 text-gray-600 hover:bg-gray-100 rounded-lg font-medium transition-colors">
                    Cancelar
                </button>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2 rounded-lg font-bold shadow-md transition-all">
                    Guardar Producto
                </button>
            </div>
        </form>
    </div>
</div>
