<div class="p-8 bg-gray-50 min-h-screen">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 mb-6">
        <h2 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            Gestión de Existencias
        </h2>

        <div class="flex flex-col md:flex-row md:items-end gap-4">
            <div class="flex-grow max-w-sm">
                <label for="bodega_id" class="block text-sm font-medium text-gray-700 mb-1">Bodega de Destino</label>
                <select id="bodega_id"
                    class="w-full border-gray-300 focus:border-blue-500 focus:ring-blue-500 rounded-lg shadow-sm transition duration-150">
                    <option value="">Seleccione bodega...</option>
                    @foreach ($bodegas as $bodega)
                        <option value="{{ $bodega->id }}">{{ $bodega->descripcion }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-2">
                <button onclick="cargarExistencias()"
                    class="inline-flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-2.5 rounded-lg transition-all shadow-sm active:scale-95">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    Consultar
                </button>

                <button onclick="imprimir()"
                    class="inline-flex items-center justify-center bg-white border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold px-6 py-2.5 rounded-lg transition-all shadow-sm">
                    <svg class="w-4 h-4 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                    </svg>
                    Imprimir
                </button>
            </div>
        </div>
    </div>

    <div id="contenidoExistencias" class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="p-12 text-center text-gray-400">
            <p>Seleccione una bodega para visualizar los datos</p>
        </div>
    </div>
</div>
