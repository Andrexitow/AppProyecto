<div class="p-6 w-full animate-fadeIn">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
        <div>
            <h1 class="text-3xl font-extrabold text-gray-800 tracking-tight">Directorio de Terceros</h1>
            <p class="text-gray-500 text-sm">Administra clientes, proveedores y colaboradores.</p>
        </div>
            {{-- openModalNuevoTercero() --}}
        <button onclick="openModalNuevoTercero()"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl shadow-lg shadow-blue-100 flex items-center gap-2 transition-all font-bold">
            <span class="text-2xl leading-none">+</span>
            <span>Nuevo Tercero</span>
        </button>
    </div>

    <div class="mb-6">
        <div class="relative w-full md:w-96">
            <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-gray-400">🔍</span>
            <input type="text" id="buscarTercero" onkeyup="filtrarTerceros()" placeholder="Buscar por nombre, documento o celular..."
                class="w-full pl-11 pr-4 py-3.5 border border-gray-200 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-500/10 focus:border-blue-500 transition-all bg-white shadow-sm">
        </div>
    </div>

    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-gray-700">
                <thead class="bg-gray-50/50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left font-black text-gray-400 uppercase tracking-widest">Nombre / Razón Social</th>
                        <th class="px-6 py-4 text-center font-black text-gray-400 uppercase tracking-widest">Tipo</th>
                        <th class="px-6 py-4 text-center font-black text-gray-400 uppercase tracking-widest">Documento</th>
                        <th class="px-6 py-4 text-center font-black text-gray-400 uppercase tracking-widest">Contacto</th>
                        <th class="px-6 py-4 text-center font-black text-gray-400 uppercase tracking-widest">Estado</th>
                        <th class="px-6 py-4 text-center font-black text-gray-400 uppercase tracking-widest">Acciones</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-50">
                    @forelse($terceros as $t)
                    <tr class="hover:bg-blue-50/30 transition-colors group">
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center font-bold text-xs uppercase">
                                    {{ substr($t->tipo == 'persona' ? $t->nombre : $t->razon_social, 0, 2) }}
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800 leading-tight">
                                        {{ $t->tipo == 'persona' ? $t->nombre . ' ' . $t->apellido : $t->razon_social }}
                                    </p>
                                    <p class="text-xs text-gray-400 font-medium">{{ $t->email ?? 'Sin correo' }}</p>
                                </div>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tighter {{ $t->tipo == 'persona' ? 'bg-purple-100 text-purple-700' : 'bg-indigo-100 text-indigo-700' }}">
                                {{ $t->tipo }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <span class="font-mono font-bold text-gray-600">
                                {{ $t->tipo == 'persona' ? $t->cedula : $t->nit }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <p class="font-bold text-gray-700">{{ $t->celular }}</p>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest
                                {{ $t->estado ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                {{ $t->estado ? 'Activo' : 'Inactivo' }}
                            </span>
                        </td>

                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-2">
                                <button onclick="verTercero({{ $t->id }})" 
                                    class="p-2 hover:bg-gray-100 text-gray-600 rounded-xl transition-all hover:scale-110" title="Ver Detalle">
                                    👁️
                                </button>
                                <button onclick="editarTercero({{ $t->id }})" 
                                    class="p-2 hover:bg-amber-100 text-amber-600 rounded-xl transition-all hover:scale-110" title="Editar">
                                    ✏️
                                </button>
                                <button onclick="eliminarTercero({{ $t->id }})" 
                                    class="p-2 hover:bg-red-100 text-red-600 rounded-xl transition-all hover:scale-110" title="Eliminar">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-16">
                            <div class="flex flex-col items-center text-gray-400">
                                <span class="text-5xl mb-4">👥</span>
                                <p class="font-bold text-lg">No hay terceros registrados</p>
                                <p class="text-sm">Comienza añadiendo uno nuevo.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modalNuevoTercero" class="fixed inset-0 bg-gray-900/60 hidden backdrop-blur-sm flex items-center justify-center z-50 p-4 animate-fadeIn">
    <div class="bg-white w-full max-w-2xl rounded-[2.5rem] shadow-2xl overflow-hidden border border-white/20">
        
        <div class="flex items-center justify-between border-b px-8 py-6 bg-gray-50/50">
            <div>
                <h2 class="text-2xl font-black text-gray-800" id="tituloModalTercero">Nuevo Tercero</h2>
                <p class="text-sm text-gray-500">Completa la información del contacto</p>
            </div>
            <button onclick="closeModalNuevoTercero()" class="w-10 h-10 flex items-center justify-center rounded-full hover:bg-gray-200 transition-colors text-gray-400 hover:text-gray-800 font-bold text-xl">✕</button>
        </div>

        <form id="formTercero" class="p-8 space-y-6" onsubmit="return false;">
            <div class="grid grid-cols-2 gap-4 p-1 bg-gray-100 rounded-2xl">
                <label class="cursor-pointer">
                    <input type="radio" name="tipo" value="persona" class="hidden peer" checked onchange="toggleTipoTercero('persona')">
                    <div class="text-center py-2 rounded-xl peer-checked:bg-white peer-checked:text-blue-600 peer-checked:shadow-sm font-bold text-gray-500 transition-all">
                        Persona Natural
                    </div>
                </label>
                <label class="cursor-pointer">
                    <input type="radio" name="tipo" value="empresa" class="hidden peer" onchange="toggleTipoTercero('empresa')">
                    <div class="text-center py-2 rounded-xl peer-checked:bg-white peer-checked:text-blue-600 peer-checked:shadow-sm font-bold text-gray-500 transition-all">
                        Empresa / NIT
                    </div>
                </label>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                
                <div class="md:col-span-1 campo-persona">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Nombre</label>
                    <input type="text" name="nombre" class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all">
                </div>
                <div class="md:col-span-1 campo-persona">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Apellido</label>
                    <input type="text" name="apellido" class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all">
                </div>
                <div class="md:col-span-2 campo-persona">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Cédula de Ciudadanía</label>
                    <input type="text" name="cedula" class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all">
                </div>

                <div class="md:col-span-2 campo-empresa hidden">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Razón Social</label>
                    <input type="text" name="razon_social" class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all">
                </div>
                <div class="md:col-span-2 campo-empresa hidden">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">NIT</label>
                    <input type="text" name="nit" class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all">
                </div>

                <div class="md:col-span-1">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Correo Electrónico</label>
                    <input type="email" name="email" placeholder="ejemplo@correo.com" class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Celular / Teléfono</label>
                    <input type="text" name="celular" class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-black text-gray-400 uppercase tracking-widest mb-2">Dirección</label>
                    <input type="text" name="direccion" class="w-full border border-gray-200 rounded-2xl px-4 py-3 focus:ring-4 focus:ring-blue-500/10 outline-none transition-all">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeModalNuevoTercero()" class="px-6 py-3 rounded-2xl font-bold text-gray-400 hover:bg-gray-100 transition-all">
                    Cancelar
                </button>
                <button type="button" onclick="guardarTercero()" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-2xl font-bold transition-all shadow-lg shadow-blue-100">
                    Guardar Tercero
                </button>
            </div>
        </form>
    </div>
</div>
