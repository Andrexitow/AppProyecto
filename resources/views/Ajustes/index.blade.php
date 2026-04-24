<div class="p-6 w-full animate-fadeIn">
    <div id="notificaciones" class="fixed top-5 right-5 z-[9999] space-y-3"></div>

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">Ajustes de Inventario</h1>
            <p class="text-gray-500 text-sm">Control de entradas, salidas y movimientos de stock.</p>
        </div>

        <button onclick="openModalAjuste()"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl shadow-lg shadow-blue-100 flex items-center gap-2 transition-all font-bold">
            <span class="text-xl leading-none">+</span>
            <span>Añadir Ajuste</span>
        </button>
    </div>

    <div class="mb-6">
        <div class="relative w-full md:w-96">
            <input type="text" id="buscarAjuste" placeholder="Buscar por número, observaciones..."
                class="w-full px-5 py-3.5 border border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all shadow-sm bg-white">
        </div>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-gray-700">
                <thead class="bg-gray-50/50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left font-bold text-gray-400 uppercase tracking-wider">Documento</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-400 uppercase tracking-wider">Observaciones
                        </th>
                        <th class="px-6 py-4 text-right font-bold text-gray-400 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-4 text-center font-bold text-gray-400 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-400 uppercase tracking-wider">Usuario</th>
                        <th class="px-6 py-4 text-left font-bold text-gray-400 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-4 text-center font-bold text-gray-400 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-50">
                    @forelse ($ajustes as $a)
                        <tr class="hover:bg-blue-50/30 transition-colors group">
                            <td class="px-6 py-4">
                                <span class="font-bold text-gray-800">{{ $a->prefijo }}</span>
                                <span class="text-blue-600 font-bold">{{ $a->numero }}</span>
                            </td>
                            <td class="px-6 py-4 text-gray-500 italic">{{ $a->observaciones ?: 'Sin observaciones' }}
                            </td>
                            <td class="px-6 py-4 text-right font-bold text-gray-900">
                                ${{ number_format($a->total, 0, ',', '.') }}
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if (!$a->registrado)
                                    <span
                                        class="bg-amber-100 text-amber-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">Pendiente</span>
                                @else
                                    <span
                                        class="bg-emerald-100 text-emerald-700 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider">Registrado</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 font-medium">{{ $a->user->name ?? '-' }}</td>
                            <td class="px-6 py-4 text-gray-500">{{ $a->fecha }}</td>

                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-3">
                                    @if (!$a->registrado)
                                        <button onclick="retomarAjuste({{ $a->id }})"
                                            class="text-blue-600 hover:scale-120 transition-transform p-1"
                                            title="Completar">▶️</button>
                                        <button onclick="editarAjuste({{ $a->id }})"
                                            class="text-yellow-600 hover:scale-120 transition-transform p-1"
                                            title="Editar">✏️</button>
                                        <button onclick="eliminarAjuste({{ $a->id }})"
                                            class="text-red-600 hover:scale-120 transition-transform p-1"
                                            title="Eliminar">🗑️</button>
                                    @else
                                        <button onclick="verAjuste({{ $a->id }})"
                                            class="text-gray-600 hover:scale-120 transition-transform p-1"
                                            title="Ver Detalle">👁️</button>
                                        <button onclick="revertirAjuste({{ $a->id }})"
                                            class="text-red-600 hover:scale-120 transition-transform p-1"
                                            title="Revertir">🔄</button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-10 text-gray-400 font-medium">No hay ajustes
                                registrados</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modalAjuste"
    class="fixed inset-0 bg-gray-900/60 hidden backdrop-blur-sm flex items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-4xl rounded-[2.5rem] shadow-2xl overflow-hidden border border-white/20">

        <div class="flex items-center justify-between border-b px-8 py-6 bg-gray-50/50">
            <h2 class="text-2xl font-black text-gray-800">Ajuste de Inventario</h2>
            <button onclick="closeModalAjuste()"
                class="text-2xl text-gray-400 hover:text-gray-800 transition-colors">✕</button>
        </div>

        <div class="p-8">
            <div id="paso1" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="md:col-span-2">
                        <label
                            class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Documento</label>
                        <div class="flex gap-3">
                            <select id="prefijo"
                                class="border border-gray-200 rounded-2xl px-4 py-3.5 w-32 focus:ring-4 focus:ring-blue-500/10 font-bold bg-white outline-none">
                                <option value="AJ">AJ</option>
                                <option value="EF">EF</option>
                                <option value="BR">BR</option>
                            </select>
                            <input type="text" id="numero" value="0001" readonly
                                class="flex-1 border border-gray-200 rounded-2xl px-4 py-3.5 bg-gray-100 font-bold text-blue-600 outline-none">
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Fecha</label>
                        <input type="date" id="fecha"
                            class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:ring-4 focus:ring-blue-500/10 outline-none">
                    </div>

                    <div>
                        <label
                            class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Bodega</label>
                        <select id="bodega_id"
                            class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:ring-4 focus:ring-blue-500/10 outline-none bg-white">
                            <option value="">Seleccione una bodega</option>
                            @foreach ($bodegas as $b)
                                <option value="{{ $b->id }}">{{ $b->descripcion }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="md:col-span-2">
                        <label
                            class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Tercero</label>
                        <div class="flex gap-3">
                            <input type="text" id="inputDocumento" placeholder="Cédula o NIT"
                                onkeyup="buscarTerceroPorDocumento()"
                                class="flex-1 border border-gray-200 rounded-2xl px-4 py-3 focus:ring-4 focus:ring-blue-500/10 outline-none">
                            <button onclick="abrirModalTerceros()"
                                class="bg-gray-100 hover:bg-gray-200 px-6 rounded-2xl font-bold transition-all">🔍
                                Buscar</button>
                        </div>
                        <div id="resultadosTercero"
                            class="mt-2 border border-gray-100 rounded-2xl bg-white shadow-xl max-h-60 overflow-y-auto hidden z-[70]">
                        </div>
                        <input type="text" id="inputNombre" placeholder="Nombre del tercero" readonly
                            class="mt-3 w-full border-none rounded-2xl px-4 py-3 bg-blue-50/50 font-bold text-blue-700 outline-none">
                        <input type="hidden" id="tercero_id">
                    </div>

                    <div>
                        <label
                            class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Contrapartida</label>
                        <input type="text" id="contraparte" placeholder="Cuenta contable"
                            class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:ring-4 focus:ring-blue-500/10 outline-none">
                    </div>

                    <div class="md:col-span-2">
                        <label
                            class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Observaciones</label>
                        <textarea id="observaciones" rows="2"
                            class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:ring-4 focus:ring-blue-500/10 outline-none resize-none"
                            placeholder="Motivo..."></textarea>
                    </div>
                </div>

                <div class="flex justify-end mt-4">
                    <button onclick="guardarCabecera()"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-10 py-4 rounded-2xl font-bold transition-all shadow-lg shadow-blue-100">
                        Siguiente →
                    </button>
                </div>
            </div>

            <div id="paso2" class="hidden space-y-6">
                <div class="bg-gray-50 p-6 rounded-[2rem] border border-gray-100">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Buscar
                        Producto</label>
                    <input type="text" id="buscarProducto" onkeyup="buscarProducto()"
                        placeholder="Nombre del producto..."
                        class="w-full border-none rounded-2xl px-5 py-4 focus:ring-4 focus:ring-blue-500/10 shadow-sm outline-none">
                    <div id="resultadosProducto"
                        class="border border-gray-100 rounded-2xl bg-white shadow-2xl mt-2 hidden max-h-60 overflow-y-auto">
                    </div>
                </div>

                <div class="bg-white rounded-3xl border border-gray-100 overflow-hidden shadow-sm">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left font-black text-gray-400 uppercase tracking-widest">
                                    Producto</th>
                                <th
                                    class="px-6 py-4 text-center w-32 font-black text-gray-400 uppercase tracking-widest">
                                    Cantidad</th>
                                <th
                                    class="px-6 py-4 text-center w-40 font-black text-gray-400 uppercase tracking-widest">
                                    Tipo</th>
                                <th
                                    class="px-6 py-4 text-center w-24 font-black text-gray-400 uppercase tracking-widest">
                                </th>
                            </tr>
                        </thead>
                        <tbody id="tablaProductos" class="divide-y divide-gray-50"></tbody>
                    </table>
                </div>

                <div class="flex justify-end pt-4">
                    <button onclick="guardarAjuste()"
                        class="bg-green-600 hover:bg-green-700 text-white px-10 py-4 rounded-2xl font-bold transition-all shadow-lg shadow-green-100">
                        Guardar Ajuste
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ====================== MODAL LISTA DE TERCEROS ====================== -->
<div id="modalTerceros"
    class="fixed inset-0 bg-gray-900/60 hidden backdrop-blur-sm flex items-center justify-center z-[60] p-4">
    <div class="bg-white w-full max-w-2xl rounded-[2.5rem] shadow-2xl overflow-hidden border border-white/20">
        <div class="p-8">
            <h2 class="text-xl font-black text-gray-800 mb-4">Seleccionar Tercero</h2>
            <input type="text" id="buscarTerceroModal" placeholder="Buscar..."
                onkeyup="buscarTerceroModal(this.value)"
                class="w-full border border-gray-200 rounded-2xl px-5 py-4 focus:ring-4 focus:ring-blue-500/10 mb-4 outline-none">

            <div id="listaTerceros"
                class="max-h-96 overflow-y-auto border border-gray-100 rounded-2xl divide-y bg-white"></div>

            <div class="flex justify-end mt-6">
                <button onclick="cerrarModalTerceros()"
                    class="px-6 py-3 text-red-500 font-bold hover:bg-red-50 rounded-2xl transition-colors">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<div id="modalVerAjuste"
    class="fixed inset-0 bg-gray-900/60 hidden backdrop-blur-sm flex items-center justify-center z-50 p-4 animate-fadeIn">
    <div class="bg-white w-full max-w-3xl rounded-[2.5rem] shadow-2xl overflow-hidden border border-white/20">

        <div class="flex justify-between items-center bg-gray-50/50 border-b px-8 py-6">
            <div>
                <h2 class="text-2xl font-black text-gray-800">Detalle del Ajuste</h2>
                <p class="text-sm text-gray-500" id="ver_doc_subtitle">Consulta la información registrada</p>
            </div>
            <button onclick="closeModalVerAjuste()"
                class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-200 transition-colors text-gray-400 hover:text-gray-800 text-xl font-bold">
                ✕
            </button>
        </div>

        <div class="p-8">
            <div
                class="grid grid-cols-1 md:grid-cols-2 gap-y-4 gap-x-8 bg-blue-50/30 p-6 rounded-[2rem] border border-blue-100 mb-8">
                <div class="flex flex-col">
                    <span class="text-xs font-black text-blue-400 uppercase tracking-widest">Documento</span>
                    <span id="ver_doc" class="text-lg font-bold text-gray-800"></span>
                </div>
                <div class="flex flex-col">
                    <span class="text-xs font-black text-blue-400 uppercase tracking-widest">Fecha de Registro</span>
                    <span id="ver_fecha" class="text-lg font-bold text-gray-800"></span>
                </div>
                <div class="flex flex-col">
                    <span class="text-xs font-black text-blue-400 uppercase tracking-widest">Tercero /
                        Responsable</span>
                    <span id="ver_tercero" class="text-lg font-bold text-gray-800"></span>
                </div>
                <div class="flex flex-col">
                    <span class="text-xs font-black text-blue-400 uppercase tracking-widest">Bodega</span>
                    <span id="ver_bodega" class="text-lg font-bold text-gray-800"></span>
                </div>
                <div class="flex flex-col md:col-span-2 border-t border-blue-100 pt-4 mt-2">
                    <span class="text-xs font-black text-blue-400 uppercase tracking-widest">Observaciones</span>
                    <span id="ver_obs" class="text-gray-600 italic"></span>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex items-center justify-between px-2">
                    <h3 class="font-black text-gray-800 uppercase tracking-widest text-sm">Productos Ajustados</h3>
                    <div class="bg-gray-100 px-4 py-1 rounded-full">
                        <span class="text-xs font-bold text-gray-500">Total: </span>
                        <span id="ver_total" class="text-sm font-black text-blue-600"></span>
                    </div>
                </div>

                <div class="border border-gray-100 rounded-3xl overflow-hidden shadow-sm">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 font-black text-gray-400 uppercase tracking-tighter">Descripción
                                    del Producto</th>
                                <th
                                    class="px-6 py-4 text-center font-black text-gray-400 uppercase tracking-tighter w-32">
                                    Cantidad</th>
                            </tr>
                        </thead>
                        <tbody id="ver_detalles" class="divide-y divide-gray-50">
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button onclick="closeModalVerAjuste()"
                    class="bg-gray-800 hover:bg-black text-white px-8 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-gray-200">
                    Cerrar Detalle
                </button>
            </div>
        </div>

    </div>
</div>
