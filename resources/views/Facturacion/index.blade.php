<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS Terminal | AppSystem</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;800;900&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        .custom-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: #4b5563;
            border-radius: 10px;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade {
            animation: fadeIn 0.3s ease-out forwards;
        }

        .btn-disabled {
            opacity: 0.2;
            cursor: not-allowed;
            pointer-events: none;
        }

        /* El puntito verde */
        .dot-online {
            height: 8px;
            width: 8px;
            background-color: #22c55e;
            /* Verde esmeralda de Tailwind */
            border-radius: 50%;
            display: inline-block;
        }

        /* La animación de parpadeo (pulso) */
        .dot-pulse {
            animation: pulse-green 2s infinite;
        }

        @keyframes pulse-green {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0.7);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 10px rgba(34, 197, 94, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(34, 197, 94, 0);
            }
        }
    </style>
    {{-- FIX 1: @vite movido al final del head, después de todos los estilos --}}
    @vite(['resources/js/facturacion.js'])
</head>

<body class="bg-[#0f172a] text-slate-200 h-screen overflow-hidden flex flex-col">

    <header class="h-16 border-b border-slate-800 bg-[#0f172a] flex items-center justify-between px-6 shrink-0">
        <div class="flex items-center gap-4">
            <div class="bg-indigo-600 p-2 rounded-xl">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>
            <div>
                <h1 class="text-lg font-black italic tracking-tighter leading-none">POS_TERMINAL v2.0</h1>
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Unidad de Facturación Rápida
                </p>
            </div>
        </div>

        <div class="flex items-center gap-8">
            <div class="flex items-center gap-4 border-l border-slate-800 pl-6 ml-6">
                <button onclick="abrirSelectorMesas()"
                    class="group flex items-center gap-3 bg-slate-900 hover:bg-indigo-600 px-4 py-2 rounded-2xl transition-all border border-slate-700">
                    <div class="text-left">
                        <p
                            class="text-[9px] text-slate-500 group-hover:text-indigo-200 font-black uppercase tracking-widest">
                            Mesa Actual</p>
                        <p class="text-sm font-black italic text-white" id="mesa-activa-label">SELECCIONAR MESA</p>
                    </div>
                    <svg class="w-4 h-4 text-slate-500 group-hover:text-white" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
            </div>
            <div class="flex items-center gap-3">
                <div class="text-right">
                    <p class="text-sm font-bold text-white leading-none">{{ Auth::user()->name }}</p>
                    <p class="text-[9px] text-emerald-500 font-black uppercase tracking-widest">En Turno</p>
                </div>
                <form action="/logout" method="POST">
                    @csrf
                    <button type="submit" class="p-2 hover:bg-red-500/10 hover:text-red-500 rounded-lg transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </header>

    <div class="flex flex-1 overflow-hidden">

        <nav class="w-28 border-r border-slate-800 bg-[#1e293b]/30 flex flex-col items-center py-6 gap-6 shrink-0">
            <button class="flex flex-col items-center gap-2 group">
                <div
                    class="w-16 h-16 bg-indigo-600 rounded-[2rem] flex items-center justify-center text-white shadow-lg shadow-indigo-500/20 group-hover:scale-110 transition-all">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16m-7 6h7" />
                    </svg>
                </div>
                <span class="text-[10px] font-black uppercase text-indigo-400">Todo</span>
            </button>
            <button class="flex flex-col items-center gap-2 group opacity-40 hover:opacity-100 transition-opacity">
                <div
                    class="w-16 h-16 bg-slate-700 rounded-[2rem] flex items-center justify-center text-white group-hover:bg-slate-600 transition-all">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                    </svg>
                </div>
                <span class="text-[10px] font-black uppercase">Comidas</span>
            </button>
            <button class="flex flex-col items-center gap-2 group opacity-40 hover:opacity-100 transition-opacity">
                <div
                    class="w-16 h-16 bg-slate-700 rounded-[2rem] flex items-center justify-center text-white group-hover:bg-slate-600 transition-all">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <span class="text-[10px] font-black uppercase">Bebidas</span>
            </button>
        </nav>

        <section class="flex-1 flex flex-col bg-[#0f172a] overflow-hidden">

            <div class="px-6 py-4 bg-[#0f172a] border-b border-slate-800/50">
                <div class="relative group">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500">
                        <svg class="w-5 h-5 group-focus-within:text-indigo-500 transition-colors" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input type="text" id="buscarProducto"
                        placeholder="BUSCAR PRODUCTO (EJ: CERVEZA, HAMBURGUESA...)"
                        class="w-full bg-slate-900/50 border border-slate-800 text-white text-xs font-black tracking-widest py-4 pl-12 pr-4 rounded-2xl outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-500/10 transition-all placeholder:text-slate-600 uppercase">
                </div>
            </div>

            <div id="gridProductos"
                class="flex-1 p-6 overflow-y-auto custom-scroll grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
                @foreach ($productos as $p)
                    <div class="item-producto animate-fade group relative bg-slate-800/40 border border-slate-700/50 p-4 rounded-[2.5rem] hover:bg-slate-800 hover:border-indigo-500/50 transition-all cursor-pointer shadow-sm"
                        data-nombre="{{ strtolower($p->descripcion) }}"
                        data-categoria="{{ strtolower($p->categoria) }}"
                        onclick="agregarAlTicket({{ $p->id }}, '{{ $p->descripcion }}', {{ $p->precio }})">

                        <div
                            class="aspect-square bg-slate-900 rounded-[2rem] mb-4 flex items-center justify-center overflow-hidden border border-slate-700">
                            @if ($p->categoria == 'Cervezas')
                                <span class="text-amber-500 text-4xl">🍺</span>
                            @else
                                <span
                                    class="text-slate-700 font-black text-4xl uppercase">{{ substr($p->descripcion, 0, 2) }}</span>
                            @endif
                        </div>

                        <div>
                            <h3
                                class="text-[11px] font-black text-white uppercase leading-tight group-hover:text-indigo-400 transition-colors h-8">
                                {{ $p->descripcion }}
                            </h3>
                            <div class="flex justify-between items-end mt-2">
                                <span
                                    class="text-[9px] font-bold text-slate-500 uppercase tracking-widest">{{ $p->und_detal }}</span>
                                <span
                                    class="text-lg font-black text-white italic">${{ number_format($p->precio, 0) }}</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        <aside class="w-[400px] border-l border-slate-800 bg-[#1e293b]/50 flex flex-col shrink-0">
            <div class="p-6 border-b border-slate-800">
                <div class="flex justify-between items-center mb-1">
                    <h2 class="text-xl font-black italic tracking-tighter text-white">ORDEN ACTUAL</h2>
                    {{-- FIX 3: id="mesa-label" se actualiza desde seleccionarMesa() --}}
                    <span class="bg-indigo-500/20 text-indigo-400 text-[9px] font-black px-2 py-1 rounded-md uppercase"
                        id="mesa-label">
                        Mesa: --
                    </span>
                </div>
                <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest">Atiende:
                    {{ Auth::user()->name }}</p>
            </div>

            <div id="ticket-items" class="flex-1 overflow-y-auto p-6 space-y-4 custom-scroll">
                <div class="text-center py-20">
                    <p class="text-slate-600 text-xs font-bold uppercase tracking-tighter">Selecciona productos</p>
                </div>
            </div>

            <div class="p-8 bg-[#0f172a] border-t border-slate-800 rounded-t-[3rem] shadow-2xl">
                <div class="space-y-2 mb-6">
                    <div class="flex justify-between text-slate-500 font-bold text-[10px] uppercase tracking-widest">
                        <span>Subtotal</span>
                        <span id="subtotal-val">$0</span>
                    </div>
                    <div class="flex justify-between text-slate-500 font-bold text-[10px] uppercase tracking-widest">
                        <span>Servicio (10%)</span>
                        <span id="servicio-val">$0</span>
                    </div>
                    <div class="flex justify-between items-center pt-2 border-t border-slate-800">
                        <span class="text-sm font-black text-white uppercase italic tracking-tighter">Total a
                            Pagar</span>
                        <span class="text-3xl font-black text-indigo-500 italic" id="total-val">$0</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <button onclick="vaciarTicket()"
                        class="py-4 bg-slate-800 hover:bg-slate-700 rounded-2xl text-[10px] font-black uppercase tracking-widest transition-all text-slate-400">
                        Cancelar
                    </button>
                    <button onclick="enviarPedido()"
                        class="py-4 bg-indigo-600 hover:bg-indigo-700 text-white rounded-2xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-indigo-600/20 transition-all">
                        Enviar Pedido
                    </button>
                </div>
            </div>
        </aside>
    </div>

    {{-- Modal dentro del body, antes del cierre --}}
    <div id="modalMesas" class="fixed inset-0 bg-[#0f172a]/95 backdrop-blur-md z-[100] hidden flex-col animate-fade">

        <div class="h-20 flex items-center justify-between px-10 border-b border-slate-800 shrink-0">
            <h2 class="text-2xl font-black italic tracking-tighter text-white">MAPA DE SALA / MESAS</h2>
            <button onclick="cerrarSelectorMesas()"
                class="bg-slate-800 p-3 rounded-full text-white hover:bg-red-500 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="bg-slate-900/50 border-b border-slate-800/50 py-6 shrink-0">
            <div class="flex flex-col md:flex-row gap-4 px-10">
                <div class="w-full md:w-1/3 relative group">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500">
                        <svg class="w-5 h-5 group-focus-within:text-indigo-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        </svg>
                    </span>
                    <select id="filtroZona" onchange="filtrarMesasPorZona()"
                        class="w-full bg-slate-950 border border-slate-800 text-white text-[10px] font-black tracking-widest py-4 pl-12 pr-10 rounded-2xl outline-none focus:border-indigo-500 appearance-none cursor-pointer uppercase">
                        <option value="todas">TODAS LAS ZONAS</option>
                        @foreach ($zonas as $zona)
                            <option value="{{ $zona->id }}">{{ $zona->nombre }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="w-full md:flex-1 relative group">
                    <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-slate-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="3" />
                        </svg>
                    </span>
                    <input type="text" id="buscarMesa" onkeyup="filtrarMesasPorZona()"
                        placeholder="BUSCAR MESA POR NÚMERO..."
                        class="w-full bg-slate-950 border border-slate-800 text-white text-[10px] font-black tracking-widest py-4 pl-12 pr-4 rounded-2xl outline-none focus:border-indigo-500 placeholder:text-slate-700 uppercase">
                </div>
            </div>
        </div>

        <div id="contenedorMesas"
            class="flex-1 p-10 grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-6 overflow-y-auto custom-scroll">
            @include('facturacion.partials.mesas_grid')
        </div>
    </div>

    <script>
        function abrirSelectorMesas() {
            const modal = document.getElementById('modalMesas');
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }

        function cerrarSelectorMesas() {
            const modal = document.getElementById('modalMesas');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }

        // FIX 2: guarda el ID en window.mesaActivaId para que facturacion.js lo consuma en enviarPedido()
        // FIX 3: actualiza el mesa-label del aside además del header
        function seleccionarMesa(id, nombre) {
            window.mesaActivaId = id;
            document.getElementById('mesa-activa-label').textContent = nombre;
            document.getElementById('mesa-label').textContent = 'Mesa: ' + nombre;
            cerrarSelectorMesas();
        }

        // FIX 4: filtrarMesasPorZona definida aquí para que los eventos onchange/onkeyup del blade funcionen
        // Si la tienes duplicada en facturacion.js, elimínala de allá y deja solo esta
        function filtrarMesasPorZona() {
            const zonaSeleccionada = document.getElementById('filtroZona').value;
            const busqueda = document.getElementById('buscarMesa').value.toLowerCase().trim();
            const mesas = document.querySelectorAll('.mesa-item');

            mesas.forEach(mesa => {
                const zonaCoincide = zonaSeleccionada === 'todas' || mesa.dataset.zona === zonaSeleccionada;
                const numeroCoincide = mesa.dataset.numero.toLowerCase().includes(busqueda);
                mesa.style.display = (zonaCoincide && numeroCoincide) ? '' : 'none';
            });
        }
    </script>

</body>
<div id="modalConfirm"
    class="fixed inset-0 bg-gray-900/60 hidden backdrop-blur-sm items-center justify-center z-[9999] p-4">
    <div
        class="bg-white rounded-3xl p-8 w-full max-w-sm shadow-2xl border border-gray-100 transform transition-all scale-100">
        <div class="flex justify-center mb-4">
            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
            </div>
        </div>
        <p id="confirmMensaje" class="text-gray-800 font-bold text-center text-lg mb-6 leading-tight"></p>

        <div class="flex flex-col gap-3">
            <button id="btnConfirmarAccion"
                class="w-full py-3 rounded-xl bg-red-600 text-white font-bold hover:bg-red-700 transition-all shadow-lg shadow-red-200">
                Confirmar
            </button>
            <button onclick="cerrarConfirm()"
                class="w-full py-3 rounded-xl bg-gray-100 text-gray-600 font-bold hover:bg-gray-200 transition-all">
                Cancelar
            </button>
        </div>
    </div>
</div>
<div id="toast-container" class="fixed top-5 right-5 z-[9999] flex flex-col gap-3">
</div>

<div id="modalSuperClave"
    class="fixed inset-0 bg-gray-900/80 hidden backdrop-blur-sm items-center justify-center z-[10000] p-4">
    <div class="bg-slate-800 rounded-3xl p-8 w-full max-w-xs border border-slate-700 shadow-2xl">
        <h3 class="text-white font-black text-center mb-2 uppercase tracking-widest">Autorización</h3>
        <p class="text-slate-400 text-[10px] text-center mb-6">Se requiere SuperClave de Administrador para eliminar
            productos ya enviados.</p>

        <input type="password" id="inputSuperClave"
            class="w-full bg-slate-900 border border-slate-700 rounded-2xl px-4 py-3 text-white text-center text-xl mb-4 focus:border-indigo-500 outline-none"
            placeholder="****">

        <div class="flex flex-col gap-2">
            <button onclick="validarSuperClave()"
                class="w-full py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-700">Confirmar</button>
            <button onclick="cerrarSuperClave()" class="w-full py-3 text-slate-400 font-bold">Cancelar</button>
        </div>
    </div>
</div>

</html>
