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

            <input type="text" id="buscarTablaProducto" oninput="filtrarProducto()"
                placeholder="Buscar producto por nombre o código..."
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

    <div id="tablaProductos">
        @include('Productos.partials.tabla')
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
            <input type="hidden" id="producto_id">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-700">Código del Producto</label>
                    <input name="codigo" placeholder="Ej: PROD-001"
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
                    <input name="descripcion" placeholder="Nombre completo del producto"
                        class="w-full border-gray-200 focus:ring-2 focus:ring-blue-500 rounded-lg p-2.5 bg-gray-50 transition-all">
                </div>

                <div class="space-y-1">
                    <label class="text-sm font-semibold text-gray-700">Unidad de Medida</label>
                    <input name="und_detal" placeholder="Ej: Unidad, Kg, Paquete"
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
            <div class="space-y-1">
                <label class="text-sm font-semibold text-gray-700">Afecta Inventario</label>

                <select name="afecta_inventario"
                    class="w-full border-gray-200 focus:ring-2 focus:ring-blue-500 rounded-lg p-2.5 bg-gray-50">
                    <option value="1">Sí</option>
                    <option value="0">No</option>
                </select>
            </div>

            <div class="flex justify-end gap-3 mt-8 pt-6 border-t border-gray-100">
                <button type="button" onclick="closeModalProducto()"
                    class="px-5 py-2 text-gray-600 hover:bg-gray-100 rounded-lg font-medium transition-colors">
                    Cancelar
                </button>
                <button type="button" onclick="guardarProducto()"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2 rounded-lg font-bold shadow-md transition-all">
                    Guardar Producto
                </button>
            </div>
        </form>
    </div>
</div>
