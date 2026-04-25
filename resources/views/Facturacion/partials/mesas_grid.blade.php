@foreach ($mesas as $mesa)
    @php
        $esMia = false;
        if ($mesa->estado == 'ocupada') {
            $esMia = $mesa->pedidos->where('estado', 'pendiente')->where('user_id', Auth::id())->isNotEmpty();
        }
    @endphp

    <div {{-- Atributos para el filtrado en JS --}} data-zona="{{ $mesa->zona_id }}" data-numero="{{ $mesa->numero }}"
        @if ($mesa->estado == 'disponible') onclick="seleccionarMesa({{ $mesa->id }}, '{{ $mesa->numero }}')"
        @elseif($esMia)
            onclick="cargarPedidoExistente({{ $mesa->id }}, '{{ $mesa->numero }}')"
        @else
            onclick="notificar('Esta mesa está siendo atendida por otro compañero', 'info')" @endif
        class="mesa-item relative p-6 rounded-[2.5rem] border-2 cursor-pointer transition-all
        {{ $mesa->estado == 'disponible' ? 'bg-slate-800/40 border-slate-700' : '' }}
        {{ $esMia ? 'bg-emerald-600 border-emerald-400 shadow-[0_0_20px_rgba(16,185,129,0.4)]' : '' }}
        {{ $mesa->estado == 'ocupada' && !$esMia ? 'bg-slate-700 border-slate-600 opacity-50' : '' }}
        {{ $mesa->estado == 'seleccionada' ? 'bg-amber-500/20 border-amber-500 animate-pulse' : '' }}">

        {{-- INDICADOR DE MESA DISPONIBLE (PUNTICO VERDE) --}}
        @if ($mesa->estado == 'disponible')
            <div class="absolute top-4 right-5 flex items-center gap-2">
                <span class="text-[8px] font-black text-emerald-500 uppercase tracking-tighter">Libre</span>
                <span class="dot-online dot-pulse"></span>
            </div>
        @endif

        {{-- ETIQUETA DE MESA PROPIA --}}
        @if ($esMia)
            <div
                class="absolute -top-2 -right-2 bg-white text-emerald-600 text-[10px] font-black px-3 py-1 rounded-full shadow-lg z-10">
                TU MESA
            </div>
        @endif

        <h3 class="text-2xl font-black italic text-white">{{ $mesa->numero }}</h3>
        <p class="text-[9px] font-black uppercase text-white/60">{{ $mesa->zona->nombre }}</p>
    </div>
@endforeach
