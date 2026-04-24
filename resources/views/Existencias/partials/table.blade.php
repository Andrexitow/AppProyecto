<div class="overflow-x-auto">
    <table class="w-full text-sm text-left text-gray-500">
        <thead class="text-xs text-gray-700 uppercase bg-gray-100">
            <tr>
                <th class="px-6 py-4 font-bold">Producto</th>
                <th class="px-6 py-4 font-bold text-center">Cantidad</th>
                <th class="px-6 py-4 font-bold text-center">Estado</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @forelse($existencias as $item)
                <tr class="hover:bg-gray-50 transition-colors">
                    <td class="px-6 py-4 font-medium text-gray-900">
                        {{ $item->descripcion }}
                    </td>
                    <td class="px-6 py-4 text-center">
                        <span class="font-mono">{{ $item->stock }}</span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if($item->stock <= 0)
                            <span class="px-2 py-1 text-xs font-semibold text-red-700 bg-red-100 rounded-full">Agotado</span>
                        @elseif($item->stock < 10)
                            <span class="px-2 py-1 text-xs font-semibold text-yellow-700 bg-yellow-100 rounded-full">Bajo Stock</span>
                        @else
                            <span class="px-2 py-1 text-xs font-semibold text-green-700 bg-green-100 rounded-full">Disponible</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-6 py-10 text-center text-gray-400 italic">
                        No se encontraron productos en esta bodega.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>