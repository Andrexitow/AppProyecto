<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
        <div>
            <h2 class="text-xl font-black text-gray-800">Grupos de Menú</h2>
            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider">Categorización y Destino de Impresión Multi-Punto</p>
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
                    <th class="px-6 py-4 text-[11px] font-black text-gray-400 uppercase">Impresoras por Punto</th>
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
                        <div class="flex flex-wrap gap-2">
                            @if($grupo->impresoras->isEmpty())
                                <span class="px-3 py-1 rounded-full bg-gray-100 text-gray-500 text-[10px] font-black uppercase">
                                    Sin Impresoras
                                </span>
                            @else
                                @foreach($grupo->impresoras as $imp)
                                <span class="px-3 py-1 rounded-full bg-amber-100 text-amber-700 text-[10px] font-black uppercase flex items-center gap-1">
                                    🖨️ {{ $imp->nombre }} ➡️ <span class="text-blue-700">{{ $imp->pivot->punto }}</span>
                                </span>
                                @endforeach
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right space-x-2">
                        {{-- Pasamos las impresoras asociadas codificadas en JSON para el script de edición --}}
                        <button onclick="editarGrupo({{ $grupo->id }}, '{{ $grupo->nombre }}', {{ json_encode($grupo->impresoras) }})" 
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

<div id="modalGrupo" class="fixed inset-0 bg-gray-900/60 hidden backdrop-blur-sm items-center justify-center z-[9999] p-4">
    <div class="bg-white rounded-3xl p-8 w-full max-w-lg shadow-2xl transform transition-all">
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
                    <div class="flex justify-between items-center mb-2">
                        <label class="block text-[11px] font-black text-gray-400 uppercase">Destinos de Impresión</label>
                        <button type="button" onclick="agregarFilaImpresora()" class="text-xs text-blue-600 font-bold hover:underline">
                            + Añadir Punto
                        </button>
                    </div>
                    
                    <div id="contenedor-impresoras" class="space-y-2 max-h-48 overflow-y-auto pr-1">
                        </div>
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

<select id="plantilla-impresoras" class="hidden">
    <option value="">Seleccionar...</option>
    @foreach($impresoras as $imp)
        <option value="{{ $imp->id }}">{{ $imp->nombre }}</option>
    @endforeach
</select>