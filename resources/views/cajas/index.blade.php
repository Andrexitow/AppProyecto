{{-- resources/views/cajas/index.blade.php --}}
<div class="p-6 space-y-8 animate-fadeIn">

    {{-- ===== HEADER ===== --}}
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-3xl font-black text-gray-800 tracking-tight">Cajas / Puntos de Venta</h1>
            <p class="text-gray-500 text-sm">Vinculación de puntos de venta, bodegas y cajeros asignados.</p>
        </div>
        <button onclick="openModalCaja()"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl shadow-lg shadow-blue-100 flex items-center gap-2 transition-all font-bold">
            + Nueva Caja
        </button>
    </div>

    {{-- ===== GRID DE CAJAS ===== --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach ($cajas as $caja)
            <div class="bg-white p-6 rounded-[2rem] border border-gray-100 shadow-sm hover:shadow-md transition-all group">

                {{-- Icono + estado --}}
                <div class="flex justify-between items-start mb-4">
                    <div class="w-12 h-12 bg-blue-50 rounded-2xl flex items-center justify-center text-2xl group-hover:scale-110 transition-transform">
                        🏧
                    </div>
                    <span class="px-3 py-1 text-[10px] font-black rounded-full uppercase tracking-tight
                        {{ $caja->activa ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }}">
                        {{ $caja->activa ? 'Activa' : 'Inactiva' }}
                    </span>
                </div>

                {{-- Nombre + prefijo --}}
                <h3 class="font-black text-xl text-gray-800 mb-1 uppercase">{{ $caja->nombre }}</h3>
                <div class="flex items-center gap-2 mb-5">
                    <span class="text-[10px] text-gray-400 font-bold uppercase">Prefijo:</span>
                    <span class="px-2 py-0.5 bg-blue-600 text-white text-[10px] font-black rounded-md">
                        {{ $caja->prefijo }}
                    </span>
                </div>

                {{-- Detalles --}}
                <div class="space-y-3 border-t border-gray-100 pt-5">
                    <div class="flex justify-between items-center">
                        <span class="text-[9px] text-gray-400 font-black uppercase tracking-widest">Bodega Origen</span>
                        <span class="text-[10px] text-gray-700 font-bold">{{ $caja->bodega->nombre ?? 'Sin Bodega' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[9px] text-gray-400 font-black uppercase tracking-widest">Cajero Asignado</span>
                        <span class="text-[10px] text-blue-600 font-bold italic">{{ $caja->cajero->name ?? 'No asignado' }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-[9px] text-gray-400 font-black uppercase tracking-widest">Consecutivo</span>
                        <span class="text-[10px] text-gray-800 font-black">#{{ str_pad($caja->proximo_numero, 5, '0', STR_PAD_LEFT) }}</span>
                    </div>
                </div>

                {{-- Acciones --}}
                <div class="flex justify-end gap-2 mt-5 pt-4 border-t border-gray-100">
                    <button onclick="editarCaja({{ $caja->id }})"
                        class="p-2 text-amber-600 hover:bg-amber-50 rounded-xl transition-all"
                        title="Editar">✏️</button>
                    <button onclick="eliminarCaja({{ $caja->id }})"
                        class="p-2 text-red-600 hover:bg-red-50 rounded-xl transition-all"
                        title="Eliminar">🗑️</button>
                </div>
            </div>
        @endforeach
    </div>
</div>

{{-- ===== MODAL CREAR / EDITAR CAJA ===== --}}
<div id="modalCaja"
    class="fixed inset-0 bg-gray-900/60 hidden backdrop-blur-sm items-center justify-center z-50 p-4 transition-all">
    <div class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden animate-popIn">

        {{-- Header del modal --}}
        <div class="bg-gradient-to-r from-blue-600 to-blue-500 px-8 py-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <h2 id="modalCajaTitulo" class="text-2xl font-black italic tracking-tighter">NUEVA CAJA</h2>
                    <p class="text-blue-100 text-xs font-bold uppercase tracking-widest">Configuración de punto de venta</p>
                </div>
                <button onclick="closeModalCaja()"
                    class="bg-white/20 hover:bg-white/30 p-2 rounded-full transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Formulario --}}
        <form id="formCaja" class="p-8 space-y-5">
            @csrf

            {{-- Nombre --}}
            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Nombre de la Caja</label>
                <div class="relative">
                    <span class="absolute left-4 top-3.5 text-gray-400">🏧</span>
                    <input type="text" name="nombre" placeholder="Ej: Caja Principal"
                        class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border-transparent focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all font-medium text-gray-700">
                </div>
            </div>

            {{-- Prefijo + Consecutivo --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Prefijo</label>
                    <input type="text" name="prefijo" placeholder="Ej: FAC"
                        class="w-full px-4 py-3.5 bg-gray-50 border-transparent focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all font-bold text-blue-600 uppercase">
                </div>
                {{-- <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Próximo Número</label>
                    <input type="number" name="proximo_numero" placeholder="Ej: 1" min="1"
                        class="w-full px-4 py-3.5 bg-gray-50 border-transparent focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all font-bold text-gray-700">
                </div> --}}
            </div>

            {{-- Bodega --}}
            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Bodega de Origen</label>
                <select name="bodega_id"
                    class="w-full px-4 py-3.5 bg-gray-50 border-transparent focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all font-bold text-gray-600 appearance-none cursor-pointer">
                    <option value="">Sin bodega asignada</option>
                    @foreach ($bodegas as $bodega)
                        <option value="{{ $bodega->id }}">{{ $bodega->descripcion }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Cajero --}}
            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Cajero Asignado</label>
                <select name="user_id"
                    class="w-full px-4 py-3.5 bg-gray-50 border-transparent focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all font-bold text-gray-600 appearance-none cursor-pointer">
                    <option value="">Sin cajero asignado</option>
                    @foreach ($usuarios as $usuario)
                        <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Activa --}}
            <div class="flex items-center gap-3 bg-gray-50 px-4 py-3 rounded-2xl">
                <input type="checkbox" name="activa" id="checkActiva" value="1" checked
                    class="w-5 h-5 text-blue-600 rounded focus:ring-blue-500">
                <label for="checkActiva" class="text-sm font-bold text-gray-700 cursor-pointer">
                    Caja activa y disponible para facturar
                </label>
            </div>

            {{-- Botones --}}
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModalCaja()"
                    class="flex-1 py-4 font-black text-gray-400 hover:text-gray-600 transition-colors uppercase text-xs tracking-widest">
                    Cancelar
                </button>
                <button type="button" onclick="guardarCaja()" id="btnGuardarCaja"
                    class="flex-[2] bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-black shadow-xl shadow-blue-200 transition-all transform hover:-translate-y-1 active:scale-95 uppercase text-xs tracking-widest">
                    Crear Caja
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    @keyframes popIn {
        0% { opacity: 0; transform: scale(0.9); }
        100% { opacity: 1; transform: scale(1); }
    }
    .animate-popIn { animation: popIn 0.2s ease-out forwards; }
</style>