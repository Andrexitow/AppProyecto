<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">

    <table class="min-w-full divide-y divide-gray-200 text-sm">

        <thead class="bg-gray-50">
            <tr>
                <th class="px-6 py-4 font-semibold text-gray-600 uppercase">Código</th>
                <th class="px-6 py-4 font-semibold text-gray-600 uppercase">Descripción</th>
                <th class="px-6 py-4 font-semibold text-gray-600 uppercase text-center">Existencia</th>
                <th class="px-6 py-4 font-semibold text-gray-600 uppercase">Precio</th>
                <th class="px-6 py-4 font-semibold text-gray-600 uppercase text-center">Estado</th>
                <th class="px-6 py-4 font-semibold text-gray-600 uppercase text-right">Acciones</th>
            </tr>
        </thead>

        <tbody class="bg-white divide-y divide-gray-100">

            @forelse ($productos as $producto)
                <tr class="hover:bg-blue-50/30 transition-colors">

                    <td class="px-6 py-4 font-mono text-blue-600">
                        {{ $producto->codigo }}
                    </td>

                    <td class="px-6 py-4">
                        <div class="font-medium text-gray-900">
                            {{ $producto->descripcion }}
                        </div>

                        <div class="text-xs text-gray-500">
                            {{ $producto->categoria }} | {{ $producto->und_detal }}
                        </div>
                    </td>

                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 bg-gray-100 rounded-full font-bold text-gray-700">
                            {{ $producto->inventarios->sum('stock') }}
                        </span>
                    </td>

                    <td class="px-6 py-4 font-semibold text-gray-800">
                        ${{ number_format($producto->precio, 0, ',', '.') }}
                    </td>

                    <td class="px-6 py-4 text-center">

                        @if ($producto->inactivo == 1)
                            <span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-700">
                                Inactivo
                            </span>
                        @else
                            <span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-700">
                                Activo
                            </span>
                        @endif

                    </td>

                    <td class="px-6 py-4 text-right space-x-2">

                        @if (auth()->user()->tienePermiso('productos.editar'))
                            <button onclick="editarProducto({{ $producto->id }})"
                                class="p-2 text-yellow-600 hover:bg-yellow-50 rounded-lg transition-colors"
                                title="Editar Producto">
                                ✏️
                            </button>
                        @endif

                        @if (auth()->user()->tienePermiso('productos.estado'))
                            <button onclick="cambiarEstadoProducto({{ $producto->id }})"
                                class="p-2 text-gray-500 hover:bg-gray-100 rounded-lg transition-colors"
                                title="Cambiar Estado">
                                🚫
                            </button>
                        @endif

                        @if (auth()->user()->tienePermiso('productos.eliminar'))
                            <button onclick="eliminarrProducto({{ $producto->id }})"
                                class="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                title="Eliminar Producto">
                                🗑️
                            </button>
                        @endif

                    </td>

                </tr>

            @empty

                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">
                        No hay productos registrados
                    </td>
                </tr>
            @endforelse

        </tbody>

    </table>

</div>
