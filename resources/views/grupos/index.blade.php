<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
        <div>
            <h2 class="text-xl font-black text-gray-800">Grupos de Menú</h2>
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Categorización y Destino de Impresión</p>
        </div>
        <button onclick="abrirModalGrupo()" 
            class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl text-sm font-bold transition-all shadow-lg shadow-blue-100 flex items-center gap-2">
            <span>+</span> Nuevo Grupo
        </button>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-gray-50">
                    <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase">Nombre del Grupo</th>
                    <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase">Impresora Asignada</th>
                    <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase text-right">Acciones</th>
                </tr>
            </thead>
            <tbody id="tabla-grupos" class="divide-y divide-gray-100">
                @foreach($grupos as $grupo)
                <tr class="hover:bg-blue-50/30 transition-colors">
                    <td class="px-6 py-4">
                        <span class="font-bold text-gray-700">{{ $grupo->nombre }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-[10px] font-black uppercase">
                            {{ $grupo->impresora->nombre ?? 'Sin Impresora' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        <button onclick="editarGrupo({{ $grupo->id }}, '{{ $grupo->nombre }}', {{ $grupo->impresora_id ?? 'null' }})" 
                            class="text-blue-600 hover:bg-blue-100 p-2 rounded-lg transition-colors">
                            ✏️
                        </button>
                        <button onclick="eliminarGrupo({{ $grupo->id }})" 
                            class="text-red-600 hover:bg-red-100 p-2 rounded-lg transition-colors">
                            🗑️
                        </button>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para Crear/Editar -->
<div id="modalGrupo" class="fixed inset-0 bg-gray-900/60 hidden backdrop-blur-sm items-center justify-center z-[9999] p-4">
    <div class="bg-white rounded-3xl p-8 w-full max-w-md shadow-2xl transform transition-all">
        <h3 id="modalTitulo" class="text-xl font-black text-gray-800 mb-6">Nuevo Grupo</h3>
        
        <form id="formGrupo" onsubmit="guardarGrupo(event)">
            <input type="hidden" id="grupo_id">
            <div class="space-y-4">
                <div>
                    <label class="block text-[11px] font-black text-gray-400 uppercase mb-2">Nombre del Grupo</label>
                    <input type="text" id="nombre_grupo" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all font-bold text-gray-700">
                </div>

                <div>
                    <label class="block text-[11px] font-black text-gray-400 uppercase mb-2">Destino de Comanda (Impresora)</label>
                    <select id="impresora_id" required
                        class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-blue-100 focus:border-blue-500 outline-none transition-all font-bold text-gray-700">
                        <option value="">Seleccionar Impresora...</option>
                        @foreach($impresoras as $imp)
                            <option value="{{ $imp->id }}">{{ $imp->nombre }} ({{ $imp->ip }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="flex gap-3 mt-8">
                <button type="button" onclick="cerrarModalGrupo()"
                    class="flex-1 py-3 rounded-xl bg-gray-100 text-gray-600 font-bold hover:bg-gray-200 transition-all">
                    Cancelar
                </button>
                <button type="submit"
                    class="flex-1 py-3 rounded-xl bg-blue-600 text-white font-bold hover:bg-blue-700 transition-all shadow-lg shadow-blue-200">
                    Guardar
                </button>
            </div>
        </form>
    </div>
</div>