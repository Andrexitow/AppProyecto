<div class="p-6 space-y-8 animate-fadeIn">

    {{-- ENCABEZADO + BOTÓN --}}
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-black text-gray-800 tracking-tight">Impresoras de Red</h1>
            <p class="text-gray-500 text-sm">Administra las impresoras térmicas conectadas por LAN al sistema.</p>
        </div>
        <button onclick="openModalImpresora()"
            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-2xl shadow-lg shadow-blue-100 flex items-center gap-2 transition-all font-bold">
            <span>+ Nueva Impresora</span>
        </button>
    </div>

    {{-- TABLA --}}
    <div class="bg-white rounded-[2.5rem] shadow-sm border border-gray-100 overflow-hidden">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-50/50 border-b border-gray-100">
                <tr>
                    <th class="px-6 py-4 text-left font-black text-gray-400 uppercase tracking-widest">Destino</th>
                    <th class="px-6 py-4 text-left font-black text-gray-400 uppercase tracking-widest">Dirección IP</th>
                    <th class="px-6 py-4 text-left font-black text-gray-400 uppercase tracking-widest">Puerto</th>
                    <th class="px-6 py-4 text-center font-black text-gray-400 uppercase tracking-widest">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50" id="listaImpresoras">
                @forelse ($impresoras as $imp)
                    <tr class="hover:bg-blue-50/30 transition-colors">

                        {{-- Nombre / Destino --}}
                        <td class="px-6 py-4">
                            <div class="flex items-center gap-3">
                                <div
                                    class="w-10 h-10 rounded-full bg-gradient-to-tr from-indigo-500 to-blue-500 text-white flex items-center justify-center font-bold text-lg">
                                    🖨️
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800">{{ $imp->nombre }}</p>
                                    <p class="text-xs text-gray-400">Impresora térmica ESC/POS</p>
                                </div>
                            </div>
                        </td>

                        {{-- IP --}}
                        <td class="px-6 py-4">
                            <span
                                class="bg-indigo-100 text-indigo-700 px-3 py-1 rounded-full text-[11px] font-black tracking-tight font-mono">
                                {{ $imp->ip }}
                            </span>
                        </td>

                        {{-- Puerto --}}
                        <td class="px-6 py-4">
                            <span
                                class="bg-gray-100 text-gray-600 px-3 py-1 rounded-full text-[11px] font-black tracking-tight font-mono">
                                :{{ $imp->puerto }}
                            </span>
                        </td>

                        {{-- Acciones --}}
                        <td class="px-6 py-4 text-center">
                            <div class="flex justify-center gap-2">
                                <button onclick='editarImpresora({{ json_encode($imp) }})'
                                    class="p-2 text-amber-600 hover:bg-amber-50 rounded-xl transition-all"
                                    title="Editar">
                                    ✏️
                                </button>
                                <button onclick="eliminarImpresora({{ $imp->id }})"
                                    class="p-2 text-red-600 hover:bg-red-50 rounded-xl transition-all"
                                    title="Eliminar">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center gap-3 text-gray-300">
                                <span class="text-5xl">🖨️</span>
                                <p class="font-black text-sm uppercase tracking-widest">Sin impresoras registradas</p>
                                <p class="text-xs text-gray-400">Agrega una impresora con el botón de arriba</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>

{{-- ══════════════════════════════════════════════════════
     MODAL — NUEVA / EDITAR IMPRESORA
══════════════════════════════════════════════════════ --}}
<div id="modalImpresora"
    class="fixed inset-0 bg-gray-900/60 hidden backdrop-blur-sm items-center justify-center z-50 p-4 transition-all">
    <div
        class="bg-white w-full max-w-lg rounded-[2.5rem] shadow-2xl overflow-hidden transform transition-all animate-popIn">

        {{-- Header del modal --}}
        <div class="bg-gradient-to-r from-blue-600 to-indigo-500 px-8 py-6 text-white">
            <div class="flex justify-between items-center">
                <div>
                    <h2 id="modalImpresoraTitle" class="text-2xl font-black italic tracking-tighter">NUEVA IMPRESORA
                    </h2>
                    <p class="text-blue-100 text-xs font-bold uppercase tracking-widest">Conexión ESC/POS por red LAN</p>
                </div>
                <button onclick="closeModalImpresora()"
                    class="bg-white/20 hover:bg-white/30 p-2 rounded-full transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>

        {{-- Formulario --}}
        <form id="formImpresora" class="p-8 space-y-5">
            @csrf
            <input type="hidden" name="id" id="imp_id">

            {{-- Nombre / Destino --}}
            <div class="space-y-1">
                <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Nombre / Destino</label>
                <div class="relative">
                    <span class="absolute left-4 top-3.5 text-gray-400">🖨️</span>
                    <input type="text" name="nombre" id="imp_nombre"
                        placeholder="Ej: COCINA, BARRA, CAJA"
                        class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border-transparent focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all font-bold text-gray-700">
                </div>
            </div>

            {{-- IP y Puerto en grid --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Dirección IP</label>
                    <input type="text" name="ip" id="imp_ip"
                        placeholder="192.168.110.100"
                        class="w-full px-4 py-3.5 bg-gray-50 border-transparent focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all font-bold text-blue-600 font-mono">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Puerto</label>
                    <input type="number" name="puerto" id="imp_puerto"
                        value="9100"
                        class="w-full px-4 py-3.5 bg-gray-50 border-transparent focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all font-bold text-gray-700 font-mono">
                </div>
            </div>

            {{-- Botones --}}
            <div class="flex gap-3 pt-4">
                <button type="button" onclick="closeModalImpresora()"
                    class="flex-1 py-4 font-black text-gray-400 hover:text-gray-600 transition-colors uppercase text-xs tracking-widest">
                    Cancelar
                </button>
                <button type="button" onclick="guardarImpresora()"
                    class="flex-[2] bg-blue-600 hover:bg-blue-700 text-white py-4 rounded-2xl font-black shadow-xl shadow-blue-200 transition-all transform hover:-translate-y-1 active:scale-95 uppercase text-xs tracking-widest">
                    Guardar Impresora
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     JAVASCRIPT
══════════════════════════════════════════════════════ --}}
<script>
    // ── Abrir modal vacío (nueva impresora) ──────────────────
    function openModalImpresora() {
        document.getElementById('modalImpresoraTitle').textContent = 'NUEVA IMPRESORA';
        document.getElementById('formImpresora').reset();
        document.getElementById('impresora_id').value = '';
        document.getElementById('imp_puerto').value = '9100';

        const modal = document.getElementById('modalImpresora');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    // ── Cerrar modal ─────────────────────────────────────────
    function closeModalImpresora() {
        const modal = document.getElementById('modalImpresora');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }

    // ── Cargar datos al modal para editar ────────────────────
    function editarImpresora(imp) {
        document.getElementById('modalImpresoraTitle').textContent = 'EDITAR IMPRESORA';
        document.getElementById('impresora_id').value = imp.id;
        document.getElementById('imp_nombre').value   = imp.nombre;
        document.getElementById('imp_ip').value       = imp.ip;
        document.getElementById('imp_puerto').value   = imp.puerto;

        const modal = document.getElementById('modalImpresora');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    // ── Guardar (crear o editar) vía AJAX ────────────────────
    function guardarImpresora() {
        const id     = document.getElementById('impresora_id').value;
        const nombre = document.getElementById('imp_nombre').value.trim();
        const ip     = document.getElementById('imp_ip').value.trim();
        const puerto = document.getElementById('imp_puerto').value.trim();

        if (!nombre || !ip || !puerto) {
            Swal.fire('Campos vacíos', 'Completa todos los campos antes de guardar.', 'warning');
            return;
        }

        const url    = id ? `/api/impresoras/${id}` : '/api/impresoras/guardar';
        const method = id ? 'PUT' : 'POST';

        fetch(url, {
            method,
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ nombre, ip, puerto }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({ icon: 'success', title: '¡Guardado!', text: data.message, timer: 1500, showConfirmButton: false });
                closeModalImpresora();
                loadView('impresoras'); // recarga la vista
            } else {
                Swal.fire('Error', data.message ?? 'Algo salió mal.', 'error');
            }
        })
        .catch(() => Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error'));
    }

    // ── Eliminar impresora ───────────────────────────────────
    function eliminarImpresora(id) {
        Swal.fire({
            title: '¿Eliminar impresora?',
            text: 'Esta acción no se puede deshacer.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc2626',
            cancelButtonColor: '#6b7280',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
        }).then(result => {
            if (!result.isConfirmed) return;

            fetch(`/api/impresoras/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
            })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({ icon: 'success', title: 'Eliminada', timer: 1200, showConfirmButton: false });
                    loadView('impresoras');
                } else {
                    Swal.fire('Error', data.message ?? 'No se pudo eliminar.', 'error');
                }
            })
            .catch(() => Swal.fire('Error', 'No se pudo conectar con el servidor.', 'error'));
        });
    }

    // Cerrar modal al hacer click fuera
    document.getElementById('modalImpresora').addEventListener('click', function(e) {
        if (e.target === this) closeModalImpresora();
    });
</script>

<style>
    @keyframes popIn {
        0%   { opacity: 0; transform: scale(0.9); }
        100% { opacity: 1; transform: scale(1); }
    }
    .animate-popIn { animation: popIn 0.2s ease-out forwards; }
</style>