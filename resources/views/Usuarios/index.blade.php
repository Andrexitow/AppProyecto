<div class="p-6 space-y-8 animate-fadeIn">
    <div>
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-3xl font-black text-gray-800 tracking-tight">Usuarios del Sistema</h1>
                <p class="text-gray-500 text-sm">Controla quién puede acceder y qué funciones tienen permitidas.</p>
            </div>
            <button onclick="openModalUsuario()"
                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl shadow-lg shadow-blue-100 flex items-center gap-2 transition-all font-bold">
                <span>+ Nuevo Usuario</span>
            </button>
        </div>

        <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-50/50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-left font-black text-gray-400 uppercase tracking-widest">Usuario</th>
                        <th class="px-6 py-4 text-left font-black text-gray-400 uppercase tracking-widest">Rol Asignado
                        </th>
                        <th class="px-6 py-4 text-center font-black text-gray-400 uppercase tracking-widest">Estado</th>
                        <th class="px-6 py-4 text-center font-black text-gray-400 uppercase tracking-widest">Acciones
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach ($usuarios as $u)
                        <tr class="hover:bg-blue-50/30 transition-colors">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 rounded-full bg-gradient-to-tr from-blue-500 to-blue-600 text-white flex items-center justify-center font-bold">
                                        {{ substr($u->name, 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-gray-800">{{ $u->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $u->username }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                {{-- Cambiamos $usuarios por $u y usamos la relación rol --}}
                                @if ($u->rol && $u->rol->nombre == 'Administrador')
                                    <span
                                        class="bg-purple-100 text-purple-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tight">
                                        {{ $u->rol->nombre }}
                                    </span>
                                @else
                                    <span
                                        class="bg-blue-100 text-blue-700 px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-tight">
                                        {{ $u->rol->nombre ?? 'Sin Rol' }}
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span
                                    class="w-2 h-2 inline-block rounded-full {{ $u->activo ? 'bg-emerald-500' : 'bg-gray-300' }} mr-2"></span>
                                <span
                                    class="font-bold text-[11px] uppercase {{ $u->activo ? 'text-emerald-600' : 'text-gray-400' }}">
                                    {{ $u->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <div class="flex justify-center gap-2">
                                    <button onclick="editarUsuario({{ $u->id }})"
                                        class="p-2 text-amber-600 hover:bg-amber-50 rounded-xl transition-all"
                                        title="Editar">
                                        ✏️
                                    </button>
                                    <button onclick="eliminarUsuario({{ $u->id }})"
                                        class="p-2 text-red-600 hover:bg-red-50 rounded-xl transition-all"
                                        title="Eliminar">
                                        🗑️
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="pt-8 border-t border-gray-100">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h2 class="text-2xl font-black text-gray-800">Roles y Permisos</h2>
                <p class="text-gray-500 text-sm">Define perfiles (Admin, Vendedor, etc.) y qué pantallas pueden ver.</p>
            </div>
            <button onclick="openModalRol()" class="text-blue-600 font-bold hover:underline">Crear Nuevo Rol</button>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach ($roles as $rol)
                <div
                    class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition-all">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="font-black text-lg text-gray-800">{{ $rol->nombre }}</h3>
                        <button onclick="editarRol({{ $rol->id }})"
                            class="text-xs font-bold text-blue-500 bg-blue-50 px-2 py-1 rounded-md">Configurar</button>
                    </div>
                    <div class="space-y-2">
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-widest">Permisos activos:</p>
                        <div class="flex flex-wrap gap-1">
                            @foreach ($rol->permisos->take(3) as $permiso)
                                <span
                                    class="text-[10px] bg-emerald-50 text-emerald-600 px-2 py-0.5 rounded-md font-bold">{{ $permiso->name }}</span>
                            @endforeach
                            @if ($rol->permisos->count() > 3)
                                <span
                                    class="text-[10px] bg-gray-50 text-gray-400 px-2 py-0.5 rounded-md font-bold">+{{ $rol->permisos->count() - 3 }}</span>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<div id="modalUsuario"
    class="fixed inset-0 bg-gray-900/60 hidden backdrop-blur-sm items-center justify-center z-50 p-4 transition-all">
    <div
        class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden transform transition-all animate-popIn">

        <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-8 py-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-black italic tracking-tighter">NUEVO ACCESO</h2>
                    <p class="text-blue-100 text-xs font-bold uppercase tracking-widest">Configuración de credenciales
                    </p>
                </div>
                <button onclick="closeModalUsuario()"
                    class="bg-white/20 hover:bg-white/30 p-2 rounded-full transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        <form id="formUsuario" class="p-8 space-y-5">
            @csrf
            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Nombre del Personal</label>
                <div class="relative">
                    <span class="absolute left-4 top-3.5 text-gray-400">👤</span>
                    <input type="text" name="name" placeholder="Ej: Juan Pérez"
                        class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border-transparent focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all font-medium text-gray-700">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Usuario (Login)</label>
                    <input type="text" name="username" placeholder="Ej: mesero_norte"
                        class="w-full px-4 py-3.5 bg-gray-50 border-transparent focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all font-bold text-blue-600">
                </div>

                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Contraseña</label>
                    <input type="password" name="password" placeholder="••••••"
                        class="w-full px-4 py-3.5 bg-gray-50 border-transparent focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all text-gray-700">
                </div>
            </div>

            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Rol Asignado</label>
                <select name="rol_id"
                    class="w-full px-4 py-3.5 bg-gray-50 border-transparent focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all font-bold text-gray-600 appearance-none cursor-pointer">
                    <option value="" disabled selected>Selecciona un perfil...</option>
                    @foreach ($roles as $rol)
                        <option value="{{ $rol->id }}">{{ $rol->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModalUsuario()"
                    class="flex-1 py-4 font-black text-gray-400 hover:text-gray-600 transition-colors uppercase text-xs tracking-widest">
                    Cancelar
                </button>
                <button type="button" onclick="guardarUsuario()"
                    class="flex-[2] bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-black shadow-xl shadow-blue-200 transition-all transform hover:-translate-y-1 active:scale-95 uppercase text-xs tracking-widest">
                    Crear Acceso Ahora
                </button>
            </div>
        </form>
    </div>
</div>
<div id="modalRol" class="fixed inset-0 bg-gray-900/60 hidden backdrop-blur-sm items-center justify-center z-50 p-4">
    <div class="bg-white w-full max-w-2xl rounded-[2.5rem] shadow-2xl overflow-hidden animate-popIn">

        <div
            class="bg-gradient-to-r from-purple-600 to-purple-500 px-8 py-6 text-white flex justify-between items-center">
            <div>
                <h2 class="text-2xl font-black italic tracking-tighter">GESTIÓN DE ROLES</h2>
                <p class="text-purple-100 text-xs font-bold uppercase tracking-widest">Definir jerarquías y accesos</p>
            </div>
            <button onclick="closeModalRol()"
                class="bg-white/20 hover:bg-white/30 p-2 rounded-full transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <form id="formRol" class="p-8 space-y-6">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Nombre del Rol</label>
                    <input type="text" name="nombre" placeholder="Ej: Mesero"
                        class="w-full px-4 py-3 bg-gray-50 border-transparent focus:border-purple-500 focus:bg-white focus:ring-4 focus:ring-purple-500/10 rounded-2xl outline-none transition-all font-bold">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Descripción</label>
                    <input type="text" name="descripcion" placeholder="Ej: Solo pedidos y ventas"
                        class="w-full px-4 py-3 bg-gray-50 border-transparent focus:border-purple-500 focus:bg-white focus:ring-4 focus:ring-purple-500/10 rounded-2xl outline-none transition-all">
                </div>
            </div>

            <div class="space-y-3">
                <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Permisos Disponibles</label>
                <div
                    class="grid grid-cols-2 gap-3 bg-gray-50 p-6 rounded-[2rem] border border-gray-100 max-h-60 overflow-y-auto">
                    @foreach ($permisos as $permiso)
                        <label
                            class="flex items-center p-3 bg-white rounded-xl border border-gray-100 cursor-pointer hover:border-purple-300 transition-all group">
                            <input type="checkbox" name="permisos[]" value="{{ $permiso->id }}"
                                class="w-5 h-5 text-purple-600 border-gray-300 rounded focus:ring-purple-500">
                            <div class="ml-3">
                                <p
                                    class="text-sm font-black text-gray-700 group-hover:text-purple-600 transition-colors">
                                    {{ $permiso->nombre }}</p>
                                <p class="text-[9px] text-gray-400 font-bold uppercase">{{ $permiso->slug }}</p>
                            </div>
                        </label>
                    @endforeach
                </div>
            </div>

            <div class="flex gap-3 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeModalRol()"
                    class="flex-1 py-4 font-black text-gray-400 hover:text-gray-600 uppercase text-xs">Cancelar</button>
                <button type="button" onclick="guardarRol()"
                    class="flex-[2] bg-purple-600 hover:bg-purple-700 text-white py-4 rounded-2xl font-black shadow-xl shadow-purple-200 transition-all uppercase text-xs">Guardar
                    Configuración</button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes popIn {
        0% {
            opacity: 0;
            transform: scale(0.9);
        }

        100% {
            opacity: 1;
            transform: scale(1);
        }
    }

    .animate-popIn {
        animation: popIn 0.2s ease-out forwards;
    }
</style>
