<style>
    #view-impresoras{padding:26px;max-width:1440px;margin:0 auto;color:#172033}.imp-top{display:flex;justify-content:space-between;align-items:flex-start;gap:18px;margin-bottom:24px}.imp-kicker{font-size:11px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:#2563eb;margin:0 0 7px}.imp-title{margin:0;color:#172033;font-size:26px;font-weight:800;letter-spacing:-.5px}.imp-sub{margin:6px 0 0;color:#667085;font-size:13px}.imp-primary{border:0;border-radius:8px;background:#1d4ed8;color:#fff;padding:11px 16px;font-weight:700;font-size:13px;cursor:pointer;box-shadow:0 6px 14px #1d4ed82e}.imp-primary:hover{background:#1e40af}.imp-metrics{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:14px;margin-bottom:20px}.imp-metric{background:#fff;border:1px solid #e6eaf0;border-radius:10px;padding:16px 18px}.imp-metric p{margin:0}.imp-metric-label{color:#667085;font-size:12px;font-weight:700}.imp-metric-value{color:#172033;font-size:26px;font-weight:800;margin-top:7px!important}.imp-metric-sub{color:#98a2b3;font-size:11px;margin-top:3px!important}.imp-table-card{background:#fff;border:1px solid #e6eaf0;border-radius:10px;overflow:hidden}.imp-table-head{padding:16px 18px;border-bottom:1px solid #edf0f4;display:flex;justify-content:space-between;align-items:center}.imp-table-head h3{margin:0;font-size:14px;color:#344054}.imp-table-head span{font-size:12px;color:#667085}#view-impresoras table{width:100%;border-collapse:collapse;font-size:13px}#view-impresoras thead{background:#f8fafc;border-bottom:1px solid #e7ebf0}#view-impresoras th{padding:12px 16px;text-align:left;font-size:11px;color:#667085;text-transform:uppercase;letter-spacing:.05em}#view-impresoras td{padding:14px 16px;border-bottom:1px solid #eff2f5}#view-impresoras tbody tr:last-child td{border-bottom:0}#view-impresoras tbody tr:hover{background:#f8fbff}.imp-name{display:flex;align-items:center;gap:11px}.imp-icon{width:35px;height:35px;border-radius:8px;display:grid;place-items:center;background:#eaf2ff;color:#1d4ed8;font-size:17px}.imp-name strong{display:block;color:#344054}.imp-name small{color:#98a2b3;font-size:11px}.imp-chip{display:inline-block;border-radius:5px;background:#f1f5f9;color:#334155;font:600 12px ui-monospace,monospace;padding:5px 8px}.imp-status{display:inline-flex;align-items:center;gap:6px;border-radius:999px;padding:5px 9px;font-size:11px;font-weight:700}.imp-status:before{content:'';width:6px;height:6px;border-radius:50%;background:currentColor}.imp-on{background:#ecfdf3;color:#027a48}.imp-off{background:#fef3f2;color:#b42318}.imp-action{border:0;background:transparent;border-radius:6px;padding:7px;cursor:pointer;font-size:14px}.imp-action:hover{background:#eef4ff}.imp-delete:hover{background:#fff1f2}@media(max-width:700px){#view-impresoras{padding:16px}.imp-top{flex-direction:column}.imp-metrics{grid-template-columns:1fr}.imp-table-card{overflow:auto}#view-impresoras table{min-width:620px}}
</style>
<div id="view-impresoras">
    <div class="imp-top">
        <div><p class="imp-kicker">Administración</p><h1 class="imp-title">Impresoras de red</h1><p class="imp-sub">Configure los destinos térmicos para caja, cocina y barra.</p></div>
        <button onclick="openModalImpresora()" class="imp-primary">+ Nueva impresora</button>
    </div>
    <div class="imp-metrics">
        <div class="imp-metric"><p class="imp-metric-label">Total impresoras</p><p class="imp-metric-value">{{ $impresoras->count() }}</p><p class="imp-metric-sub">Registradas en el sistema</p></div>
        <div class="imp-metric"><p class="imp-metric-label">Impresoras activas</p><p class="imp-metric-value">{{ $impresoras->where('activa', true)->count() }}</p><p class="imp-metric-sub">Disponibles para impresión</p></div>
        <div class="imp-metric"><p class="imp-metric-label">Impresoras inactivas</p><p class="imp-metric-value">{{ $impresoras->where('activa', false)->count() }}</p><p class="imp-metric-sub">Revisar conexión o configuración</p></div>
    </div>
    <div class="imp-table-card">
        <div class="imp-table-head"><h3>Listado de impresoras</h3><span>{{ $impresoras->count() }} registro(s)</span></div>
        <table>
            <thead>
                <tr>
                    <th>Destino</th><th>Dirección IP</th><th>Puerto</th><th>Estado</th><th style="text-align:center">Acciones</th>
                </tr>
            </thead>
            <tbody id="listaImpresoras">
                @forelse ($impresoras as $imp)
                    <tr>

                        {{-- Nombre / Destino --}}
                        <td><div class="imp-name"><div class="imp-icon">🖨️</div><div><strong>{{ $imp->nombre }}</strong><small>Impresora térmica ESC/POS</small></div></div>
                        </td>

                        {{-- IP --}}
                        <td><span class="imp-chip">{{ $imp->ip }}</span></td>

                        {{-- Puerto --}}
                        <td><span class="imp-chip">:{{ $imp->puerto }}</span></td>
                        <td><span class="imp-status {{ $imp->activa ? 'imp-on' : 'imp-off' }}">{{ $imp->activa ? 'Activa' : 'Inactiva' }}</span></td>

                        {{-- Acciones --}}
                        <td style="text-align:center"><div>
                                <button onclick='editarImpresora({{ json_encode($imp) }})'
                                    class="imp-action"
                                    title="Editar">
                                    ✏️
                                </button>
                                <button onclick="eliminarImpresora({{ $imp->id }})"
                                    class="imp-action imp-delete"
                                    title="Eliminar">
                                    🗑️
                                </button>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="padding:52px;text-align:center">
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
    </div></div>

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
                    <input autocomplete="off" type="text" name="nombre" id="imp_nombre"
                        placeholder="Ej: COCINA, BARRA, CAJA"
                        class="w-full pl-11 pr-4 py-3.5 bg-gray-50 border-transparent focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all font-bold text-gray-700">
                </div>
            </div>

            {{-- IP y Puerto en grid --}}
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Dirección IP</label>
                    <input autocomplete="off" type="text" name="ip" id="imp_ip"
                        placeholder="192.168.110.100"
                        class="w-full px-4 py-3.5 bg-gray-50 border-transparent focus:border-blue-500 focus:bg-white focus:ring-4 focus:ring-blue-500/10 rounded-2xl outline-none transition-all font-bold text-blue-600 font-mono">
                </div>
                <div class="space-y-1">
                    <label class="text-[10px] font-black text-gray-400 uppercase ml-2">Puerto</label>
                    <input autocomplete="off" type="number" name="puerto" id="imp_puerto"
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
        document.getElementById('imp_id').value = '';
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
        document.getElementById('imp_id').value = imp.id;
        document.getElementById('imp_nombre').value   = imp.nombre;
        document.getElementById('imp_ip').value       = imp.ip;
        document.getElementById('imp_puerto').value   = imp.puerto;

        const modal = document.getElementById('modalImpresora');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    // ── Guardar (crear o editar) vía AJAX ────────────────────
    function guardarImpresora() {
        const id     = document.getElementById('imp_id').value;
        const nombre = document.getElementById('imp_nombre').value.trim();
        const ip     = document.getElementById('imp_ip').value.trim();
        const puerto = document.getElementById('imp_puerto').value.trim();

        if (!nombre || !ip || !puerto) {
            Swal.fire('Campos vacíos', 'Completa todos los campos antes de guardar.', 'warning');
            return;
        }

        fetch('/api/impresoras/guardar', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            },
            body: JSON.stringify({ id, nombre, ip, puerto }),
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
