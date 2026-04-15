<div class="p-6 w-full">

    <!-- HEADER -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
        <h1 class="text-3xl font-bold text-gray-800">Ajustes de Inventario</h1>

        <button onclick="openModalAjuste()"
            class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-3 rounded-xl shadow-md flex items-center gap-2 transition-colors">
            <span class="text-xl leading-none">+</span>
            <span>Añadir Ajuste</span>
        </button>
    </div>

    <!-- BUSCADOR GENERAL -->
    <div class="mb-6">
        <input type="text" id="buscarAjuste" placeholder="Buscar por número, observaciones o usuario..."
            class="w-full md:w-96 px-4 py-3 border border-gray-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
    </div>

    <!-- TABLA -->
    <div class="bg-white rounded-2xl shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-gray-700">

                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-6 py-4 text-left font-medium">Prefijo</th>
                        <th class="px-6 py-4 text-left font-medium">Número</th>
                        <th class="px-6 py-4 text-left font-medium">Observaciones</th>
                        <th class="px-6 py-4 text-right font-medium">Total</th>
                        <th class="px-6 py-4 text-center font-medium">Registrado</th>
                        <th class="px-6 py-4 text-left font-medium">Usuario</th>
                        <th class="px-6 py-4 text-left font-medium">Fecha</th>
                        <th class="px-6 py-4 text-center font-medium">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">

                    @forelse ($ajustes as $a)
                        <tr class="hover:bg-gray-50">

                            <td class="px-6 py-4">{{ $a->prefijo }}</td>
                            <td class="px-6 py-4">{{ $a->numero }}</td>
                            <td class="px-6 py-4">{{ $a->observaciones }}</td>

                            <td class="px-6 py-4 text-right font-medium">
                                ${{ number_format($a->total, 0, ',', '.') }}
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if ($a->registrado)
                                    <span class="text-green-600">✔</span>
                                @else
                                    <span class="text-red-500">✖</span>
                                @endif
                            </td>

                            <td class="px-6 py-4">
                                {{ $a->user->name ?? '-' }}
                            </td>

                            <td class="px-6 py-4">
                                {{ $a->fecha }}
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if (!$a->registrado)
                                    <button onclick="retomarAjuste({{ $a->id }})"
                                        class="text-blue-600 hover:underline text-sm">
                                        Completar
                                    </button>

                                    <button onclick="eliminarAjuste({{ $a->id }})"
                                        class="text-red-600 hover:underline text-sm ml-2">
                                        Eliminar
                                    </button>
                                @else
                                    <button onclick="verAjuste({{ $a->id }})"
                                        class="text-gray-600 hover:underline text-sm">
                                        Ver
                                    </button>

                                    <button onclick="revertirAjuste({{ $a->id }})"
                                        class="text-red-600 hover:underline text-sm ml-2">
                                        Revertir
                                    </button>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-6 text-gray-500">
                                No hay ajustes registrados
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>
        </div>
    </div>
</div>

<!-- ====================== MODAL NUEVO AJUSTE ====================== -->
<div id="modalAjuste" class="fixed inset-0 bg-black/60 hidden flex items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-4xl rounded-3xl shadow-2xl overflow-hidden">

        <!-- Header Modal -->
        <div class="flex items-center justify-between border-b px-6 py-5">
            <h2 class="text-2xl font-bold text-gray-800">Nuevo Ajuste de Inventario</h2>
            <button onclick="closeModalAjuste()"
                class="text-3xl text-gray-400 hover:text-gray-600 transition-colors leading-none">
                ✕
            </button>
        </div>

        <div class="p-6">

            <!-- PASO 1 -->
            <div id="paso1">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                    <!-- Documento (Prefijo + Número) -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Documento</label>
                        <div class="flex gap-3">
                            <select id="prefijo"
                                class="border border-gray-300 rounded-xl px-4 py-3 w-32 focus:ring-2 focus:ring-blue-500">
                                <option value="AJ">AJ</option>
                                <option value="EF">EF</option>
                                <option value="BR">BR</option>
                            </select>
                            <input type="text" id="numero" value="0001" readonly
                                class="flex-1 border border-gray-300 rounded-xl px-4 py-3 bg-gray-100 font-medium">
                        </div>
                    </div>

                    <!-- Fecha -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                        <input type="date" id="fecha"
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Bodega -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Bodega</label>
                        <select id="bodega_id"
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500">
                            <option value="">Seleccione una bodega</option>
                            @foreach ($bodegas as $b)
                                <option value="{{ $b->id }}">{{ $b->descripcion }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Tercero - Búsqueda por Documento -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tercero</label>
                        <div class="flex gap-3">
                            <input type="text" id="inputDocumento" placeholder="Cédula o NIT"
                                class="flex-1 border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500"
                                onkeyup="buscarTerceroPorDocumento()">

                            <button onclick="abrirModalTerceros()"
                                class="bg-gray-100 hover:bg-gray-200 px-6 rounded-xl transition-colors flex items-center">
                                🔍 Buscar
                            </button>
                        </div>

                        <!-- Resultados en vivo (dropdown) -->
                        <div id="resultadosTercero"
                            class="mt-1 border border-gray-300 rounded-2xl bg-white shadow-lg max-h-60 overflow-y-auto hidden">
                        </div>

                        <!-- Nombre del tercero (solo lectura) -->
                        <input type="text" id="inputNombre" placeholder="Nombre del tercero aparecerá aquí" readonly
                            class="mt-3 w-full border border-gray-300 rounded-xl px-4 py-3 bg-gray-100">
                        <input type="hidden" id="tercero_id">
                    </div>

                    <!-- Contrapartida -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Contrapartida</label>
                        <input type="text" id="contraparte" placeholder="Cuenta contable"
                            class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Observaciones -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">Observaciones</label>
                        <textarea id="observaciones" rows="3"
                            class="w-full border border-gray-300 rounded-2xl px-4 py-3 focus:ring-2 focus:ring-blue-500 resize-y"
                            placeholder="Motivo del ajuste..."></textarea>
                    </div>

                </div>

                <!-- Botón Siguiente -->
                <div class="flex justify-end mt-8">
                    <button onclick="irPaso2()"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-2xl font-medium flex items-center gap-2 transition-all">
                        Siguiente <span class="text-lg">→</span>
                    </button>
                </div>
            </div>

            <!-- PASO 2 -->
            <div id="paso2" class="hidden">

                <!-- BUSCADOR PRODUCTO -->
                <div class="mb-5">
                    <input type="text" id="buscarProducto" onkeyup="buscarProducto()"
                        placeholder="Buscar producto..."
                        class="w-full border border-gray-300 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500">

                    <div id="resultadosProducto"
                        class="border rounded-lg bg-white shadow mt-1 hidden max-h-60 overflow-y-auto">
                    </div>
                </div>

                <!-- TABLA PRODUCTOS -->
                <div class="overflow-x-auto rounded-2xl border border-gray-200 mb-6">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="p-4 text-left">Producto</th>
                                <th class="p-4 text-center w-32">Cantidad</th>
                                <th class="p-4 text-center w-40">Tipo de Ajuste</th>
                                <th class="p-4 text-center w-24">Acción</th>
                            </tr>
                        </thead>
                        <tbody id="tablaProductos" class="divide-y">
                            <!-- Las filas se agregan dinámicamente con JavaScript -->
                        </tbody>
                    </table>
                </div>

                <!-- BOTONES FINALES -->
                <div class="flex justify-between items-center">
                    <!-- PASO 2 — botón Volver con aviso -->
                    <button onclick="volverPaso1()"
                        class="text-gray-600 hover:text-gray-800 font-medium flex items-center gap-2">
                        ← Volver
                        <span class="text-xs text-gray-400">(el ajuste ya fue guardado)</span>
                    </button>

                    <button onclick="guardarAjuste()"
                        class="bg-green-600 hover:bg-green-700 text-white px-8 py-3 rounded-2xl font-medium shadow-md transition-colors">
                        Guardar Ajuste
                    </button>
                </div>

            </div>

        </div>
    </div>
</div>

<!-- ====================== MODAL LISTA DE TERCEROS ====================== -->
<div id="modalTerceros" class="fixed inset-0 bg-black/60 hidden flex items-center justify-center z-[60] p-4">
    <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl overflow-hidden">
        <div class="p-6">
            <input type="text" id="buscarTerceroModal" placeholder="Buscar por nombre, cédula o NIT..."
                class="w-full border border-gray-300 rounded-2xl px-5 py-3 focus:ring-2 focus:ring-blue-500 mb-4"
                onkeyup="buscarTerceroModal(this.value)">

            <div id="listaTerceros" class="max-h-96 overflow-y-auto border rounded-2xl divide-y bg-white"></div>

            <div class="flex justify-end mt-6">
                <button onclick="cerrarModalTerceros()"
                    class="px-6 py-3 text-red-500 hover:bg-red-50 rounded-2xl transition-colors">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<div id="modalVerAjuste" class="fixed inset-0 bg-black/60 hidden flex items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-3xl rounded-3xl shadow-2xl">

        <div class="flex justify-between items-center border-b p-5">
            <h2 class="text-xl font-bold">Detalle del Ajuste</h2>
            <button onclick="closeModalVerAjuste()" class="text-2xl">✕</button>
        </div>

        <div class="p-6 space-y-4 text-sm">

            <div><b>Documento:</b> <span id="ver_doc"></span></div>
            <div><b>Fecha:</b> <span id="ver_fecha"></span></div>
            <div><b>Tercero:</b> <span id="ver_tercero"></span></div>
            <div><b>Observaciones:</b> <span id="ver_obs"></span></div>
            <div><b>Total:</b> <span id="ver_total"></span></div>

            <!-- FUTURO: TABLA PRODUCTOS -->
            <div>
                <h3 class="font-semibold mt-4">Productos</h3>
                <table class="w-full text-sm mt-2 border">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="p-2 text-left">Producto</th>
                            <th class="p-2 text-center">Cantidad</th>
                        </tr>
                    </thead>
                    <tbody id="ver_detalles">
                        <!-- luego lo llenamos -->
                    </tbody>
                </table>
            </div>

        </div>

    </div>
</div>
