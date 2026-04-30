@foreach ($mesas as $mesa)
    @php
        $pedidoActivo = $mesa->pedidos->where('estado', 'pendiente')->first();
        $esMia = $pedidoActivo && $pedidoActivo->user_id === Auth::id();
        $esAdmin = Auth::user()->rol->nombre === 'Administrador';
        $esCajero = Auth::user()->rol->nombre === 'Cajero';
        $esCajeroOAdmin = $esCajero || $esAdmin;
        $nombreMesero = $pedidoActivo?->user?->name ?? 'Mesero';

        // Verificar si el pedido pertenece a un mesero de la misma caja del cajero
        $esDeMiCaja = $pedidoActivo && $pedidoActivo->user &&
                      $pedidoActivo->user->caja_id === Auth::user()->caja_id;
    @endphp

    <div
        data-zona="{{ $mesa->zona_id }}"
        data-numero="{{ $mesa->numero }}"

        @if ($mesa->estado == 'disponible')
            onclick="seleccionarMesa({{ $mesa->id }}, '{{ $mesa->numero }}')"

        @elseif ($esMia)
            onclick="cargarPedidoExistente({{ $mesa->id }}, '{{ $mesa->numero }}')"

        @elseif ($mesa->estado == 'ocupada' && $esAdmin)
            {{-- Admin puede abrir cualquier mesa --}}
            onclick="cargarPedidoExistente({{ $mesa->id }}, '{{ $mesa->numero }}')"

        @elseif ($mesa->estado == 'ocupada' && $esCajero && $esDeMiCaja)
            {{-- Cajero solo puede abrir mesas de sus meseros --}}
            onclick="cargarPedidoExistente({{ $mesa->id }}, '{{ $mesa->numero }}')"

        @elseif ($mesa->estado == 'ocupada' && $esCajero && !$esDeMiCaja)
            {{-- Bloqueada para este cajero --}}
            onclick="notificar('Esta mesa pertenece a otra caja', 'warning')"

        @else
            onclick="notificar('Esta mesa está siendo atendida por otro compañero', 'info')"
        @endif

        class="mesa-item relative p-6 rounded-[2.5rem] border-2 cursor-pointer transition-all
            {{ $mesa->estado == 'disponible' ? 'bg-slate-800/40 border-slate-700' : '' }}
            {{ $esMia ? 'bg-emerald-600 border-emerald-400 shadow-[0_0_20px_rgba(16,185,129,0.4)]' : '' }}
            {{ $mesa->estado == 'ocupada' && ($esAdmin || $esDeMiCaja) && !$esMia ? 'bg-indigo-900/60 border-indigo-500/60 hover:border-indigo-400' : '' }}
            {{ $mesa->estado == 'ocupada' && $esCajero && !$esDeMiCaja ? 'bg-slate-900/80 border-slate-700 opacity-40 cursor-not-allowed' : '' }}
            {{ $mesa->estado == 'ocupada' && !$esMia && !$esCajeroOAdmin ? 'bg-slate-700 border-slate-600 opacity-50' : '' }}
            {{ $mesa->estado == 'seleccionada' ? 'bg-amber-500/20 border-amber-500 animate-pulse' : '' }}">

        {{-- PUNTICO VERDE: mesa libre --}}
        @if ($mesa->estado == 'disponible')
            <div class="absolute top-4 right-5 flex items-center gap-2">
                <span class="text-[8px] font-black text-emerald-500 uppercase tracking-tighter">Libre</span>
                <span class="dot-online dot-pulse"></span>
            </div>
        @endif

        {{-- ETIQUETA: mesa propia del mesero --}}
        @if ($esMia)
            <div class="absolute -top-2 -right-2 bg-white text-emerald-600 text-[10px] font-black px-3 py-1 rounded-full shadow-lg z-10">
                TU MESA
            </div>

        {{-- ETIQUETA: cajero/admin ve el nombre del mesero de su caja --}}
        @elseif ($mesa->estado == 'ocupada' && ($esAdmin || $esDeMiCaja))
            <div class="absolute -top-2 -right-2 bg-indigo-500 text-white text-[10px] font-black px-3 py-1 rounded-full shadow-lg z-10 max-w-[120px] truncate">
                🧑‍🍳 {{ $nombreMesero }}
            </div>

        {{-- ETIQUETA: bloqueada para este cajero --}}
        @elseif ($mesa->estado == 'ocupada' && $esCajero && !$esDeMiCaja)
            <div class="absolute -top-2 -right-2 bg-slate-600 text-slate-300 text-[10px] font-black px-3 py-1 rounded-full shadow-lg z-10">
                🔒 Otra caja
            </div>
        @endif

        <h3 class="text-2xl font-black italic text-white">{{ $mesa->numero }}</h3>
        <p class="text-[9px] font-black uppercase text-white/60">{{ $mesa->zona->nombre }}</p>
    </div>
@endforeach