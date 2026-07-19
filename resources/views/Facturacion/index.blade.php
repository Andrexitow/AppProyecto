<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>POS Terminal | AppSystem</title>
    <script src="https://cdn.tailwindcss.com"></script>
    
    <script src="{{ asset('js/facturacion.js') }}?v={{ filemtime(public_path('js/facturacion.js')) }}" defer></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
        }

        /* ── SCROLLBAR ── */
        .custom-scroll::-webkit-scrollbar {
            width: 3px;
        }

        .custom-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scroll::-webkit-scrollbar-thumb {
            background: #283347;
            border-radius: 4px;
        }

        /* ── ANIMACIONES ── */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade {
            animation: fadeIn 0.25s ease-out forwards;
        }

        /* ── NAV LATERAL ── */
        .pos-nav {
            width: 76px;
            background: #0a1120;
            border-right: 0.5px solid #1e293b;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0;
            /* ← quita el padding global */
            gap: 0;
            /* ← quita el gap global */
            flex-shrink: 0;
            height: 100%;
            overflow: hidden;
            /* ← el nav en sí no hace scroll */
        }

        .pos-nav::-webkit-scrollbar {
            width: 0;
        }

        .cat-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            width: 60px;
            padding: 9px 6px;
            border-radius: 12px;
            border: none;
            background: transparent;
            cursor: pointer;
            transition: background 0.15s;
            flex-shrink: 0;
        }

        .cat-btn:hover {
            background: #1e293b;
        }

        .cat-btn.active {
            background: #1a2d50;
        }

        .cat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 18px;
            transition: background 0.15s;
            flex-shrink: 0;
        }

        .cat-btn.active .cat-icon {
            background: #2d4faa;
        }

        .cat-btn:not(.active) .cat-icon {
            background: #1e293b;
        }

        .cat-label {
            font-size: 9px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            text-align: center;
            line-height: 1.2;
        }

        .cat-btn.active .cat-label {
            color: #93c5fd;
        }

        .nav-divider {
            width: 32px;
            height: 0.5px;
            background: #1e293b;
            margin: 6px 0;
            flex-shrink: 0;
        }

        .cash-btn {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
            width: 54px;
            padding: 8px 6px;
            border-radius: 10px;
            border: none;
            background: transparent;
            cursor: pointer;
            transition: background 0.15s;
        }

        .cash-btn:hover {
            background: #1e293b;
        }

        .cash-icon {
            width: 36px;
            height: 36px;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .cash-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        /* ── PRODUCTOS ── */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            gap: 10px;
            padding: 14px;
            overflow-y: auto;
            flex: 1;
        }

        .prod-card {
            background: #111827;
            border: 0.5px solid #1e293b;
            border-radius: 14px;
            padding: 12px;
            cursor: pointer;
            transition: border-color 0.15s, background 0.15s;
        }

        .prod-card:hover {
            background: #1a2235;
            border-color: rgba(59, 91, 219, 0.4);
        }

        .prod-thumb {
            aspect-ratio: 1;
            background: #0f172a;
            border-radius: 10px;
            border: 0.5px solid #1e293b;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 26px;
            margin-bottom: 10px;
        }

        .prod-name {
            font-size: 10px;
            font-weight: 700;
            color: #e2e8f0;
            text-transform: uppercase;
            line-height: 1.3;
            margin-bottom: 6px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            min-height: 26px;
        }

        .prod-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .prod-unit {
            font-size: 9px;
            color: #475569;
            font-weight: 600;
            text-transform: uppercase;
        }

        .prod-price {
            font-size: 14px;
            font-weight: 800;
            color: #e2e8f0;
            font-style: italic;
        }

        /* ── TICKET ── */
        .ticket-item {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #1a2235;
            border-radius: 10px;
            padding: 10px 12px;
            border: 0.5px solid #1e293b;
        }

        .item-emoji {
            font-size: 18px;
            flex-shrink: 0;
        }

        .item-info {
            flex: 1;
            min-width: 0;
        }

        .item-name {
            font-size: 10px;
            font-weight: 700;
            color: #e2e8f0;
            text-transform: uppercase;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .item-price {
            font-size: 11px;
            color: #3b82f6;
            font-weight: 700;
            margin-top: 2px;
        }

        .item-qty {
            display: flex;
            align-items: center;
            gap: 6px;
            flex-shrink: 0;
        }

        .qty-btn {
            width: 22px;
            height: 22px;
            border-radius: 6px;
            background: #283347;
            border: none;
            color: #94a3b8;
            font-size: 15px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.15s;
            line-height: 1;
            padding: 0;
        }

        .qty-btn:hover {
            background: #334155;
            color: #e2e8f0;
        }

        .qty-val {
            font-size: 11px;
            font-weight: 700;
            color: #e2e8f0;
            min-width: 14px;
            text-align: center;
        }

        /* ── SEARCH ── */
        .search-input {
            width: 100%;
            background: #1a2235;
            border: 0.5px solid #283347;
            color: #e2e8f0;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 0.5px;
            padding: 10px 14px 10px 38px;
            border-radius: 10px;
            outline: none;
            text-transform: uppercase;
            transition: border-color 0.15s;
        }

        .search-input:focus {
            border-color: #3b5bdb;
        }

        .search-input::placeholder {
            color: #334155;
        }

        /* ── BOTONES TICKET ── */
        .btn-cancel {
            background: #1e293b;
            border: 0.5px solid #283347;
            color: #64748b;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 11px;
            border-radius: 9px;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
            width: 100%;
        }

        .btn-cancel:hover {
            background: #283347;
            color: #94a3b8;
        }

        .btn-send {
            background: #1a2d50;
            border: 0.5px solid #2d4a7a;
            color: #93c5fd;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 11px;
            border-radius: 9px;
            cursor: pointer;
            transition: background 0.15s;
            width: 100%;
        }

        .btn-send:hover {
            background: #1e3660;
        }

        .btn-pay {
            width: 100%;
            background: #14532d;
            border: 0.5px solid #166534;
            color: #86efac;
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 14px;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: background 0.15s;
        }

        .btn-pay:hover {
            background: #166534;
        }

        .btn-pay-disabled {
            width: 100%;
            background: #1a2235;
            border: 0.5px solid #283347;
            color: #334155;
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 12px;
            border-radius: 10px;
            text-align: center;
        }

        /* ── CLIENT BTN ── */
        .client-btn {
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: #1a2235;
            border: 0.5px solid #283347;
            border-radius: 10px;
            padding: 9px 12px;
            cursor: pointer;
            transition: background 0.15s;
        }

        .client-btn:hover {
            background: #1e2d44;
        }

        /* ── MODALES ── */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(9, 14, 30, 0.85);
            backdrop-filter: blur(6px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            padding: 16px;
        }

        .modal-overlay.show {
            display: flex;
        }

        .modal-box-dark {
            background: #111827;
            border: 0.5px solid #1e293b;
            border-radius: 20px;
            overflow: hidden;
            width: 100%;
            box-shadow: 0 24px 60px rgba(0, 0, 0, 0.6);
        }

        .modal-header-dark {
            padding: 18px 20px;
            border-bottom: 0.5px solid #1e293b;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title-dark {
            font-size: 14px;
            font-weight: 800;
            color: #f1f5f9;
            font-style: italic;
            text-transform: uppercase;
            letter-spacing: -0.3px;
        }

        .modal-close {
            width: 28px;
            height: 28px;
            background: #1e293b;
            border: none;
            border-radius: 7px;
            color: #64748b;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s, color 0.15s;
        }

        .modal-close:hover {
            background: #7f1d1d;
            color: #fca5a5;
        }

        .modal-input-dark {
            width: 100%;
            background: #0f172a;
            border: 0.5px solid #283347;
            border-radius: 9px;
            padding: 11px 14px;
            color: #e2e8f0;
            font-size: 12px;
            font-weight: 600;
            outline: none;
            transition: border-color 0.15s;
        }

        .modal-input-dark:focus {
            border-color: #3b5bdb;
        }

        .modal-input-dark::placeholder {
            color: #334155;
        }

        /* ── MESAS MODAL ── */
        #modalMesas {
            position: fixed;
            inset: 0;
            background: rgba(9, 14, 30, 0.97);
            backdrop-filter: blur(8px);
            z-index: 100;
            flex-direction: column;
        }

        .dot-online {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #22c55e;
            display: inline-block;
            flex-shrink: 0;
        }

        /* #modalMesas.show {
            display: flex !important;
        } */

        /* ── MOBILE ── */
        @media (max-width: 767px) {
            #panel-ticket {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                z-index: 50;
                transform: translateY(100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                max-height: 85vh;
                border-radius: 20px 20px 0 0;
            }

            #panel-ticket.ticket-open {
                transform: translateY(0);
            }

            #ticket-backdrop {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0, 0, 0, 0.7);
                z-index: 49;
            }

            #ticket-backdrop.open {
                display: block;
            }

            .pos-nav {
                display: none !important;
            }
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

        .dot-pulse {
            animation: pulse-green 2s infinite;
        }
    </style>
</head>

<body class="bg-[#0b1120] text-slate-200 h-screen overflow-hidden flex flex-col">

    {{-- ===================== HEADER ===================== --}}
    <header
        class="h-13 border-b border-slate-800/80 bg-[#090e1e] flex items-center justify-between px-4 md:px-5 shrink-0"
        style="height:52px;">

        <div class="flex items-center gap-3">
            <div class="bg-[#2d4faa] p-1.5 rounded-xl">
                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>
            <div class="hidden sm:block">
                <h1 class="text-sm font-black italic tracking-tight leading-none text-white">POS Terminal</h1>
                <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest">Facturación rápida</p>
            </div>
            <h1 class="sm:hidden text-sm font-black italic text-white">POS</h1>
        </div>

        <div class="flex items-center gap-3">

            {{-- Mesa --}}
            <button onclick="abrirSelectorMesas()"
                class="flex items-center gap-2 bg-[#111827] hover:bg-[#1a2d50] border border-slate-800 hover:border-[#2d4a7a] px-3 py-1.5 rounded-xl transition-all">
                <div class="text-left">
                    <p class="text-[8px] text-slate-500 font-black uppercase tracking-widest hidden md:block">Mesa</p>
                    <p class="text-xs font-black italic text-white" id="mesa-activa-label">MESA</p>
                </div>
                <svg class="w-3 h-3 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            {{-- Usuario --}}
            <div class="hidden sm:flex items-center gap-2">
                <div class="text-right">
                    <p class="text-xs font-bold text-white leading-none">{{ Auth::user()->name }}</p>
                    <p class="text-[9px] text-emerald-500 font-black uppercase tracking-widest">En turno</p>
                </div>
            </div>

            {{-- Logout --}}
            <form action="/logout" method="POST">
                @csrf
                <button type="submit"
                    class="p-1.5 bg-[#111827] border border-slate-800 hover:bg-red-900/30 hover:border-red-800/50 hover:text-red-400 rounded-lg transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                    </svg>
                </button>
            </form>

            {{-- MÓVIL: botones rápidos --}}
            @if (auth()->user()->rol_id == 4 || auth()->user()->rol_id == 1)
                <button onclick="abrirModalMovimiento()" class="md:hidden p-2 rounded-xl border transition-all"
                    style="background:#1a1a2e; border-color:#4338ca;">
                    <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" stroke-width="2"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4" />
                    </svg>
                </button>
            @endif
            <button onclick="abrirTicketMovil()"
                class="md:hidden relative bg-[#1a2d50] border border-[#2d4a7a] p-2 rounded-xl">
                <svg class="w-4 h-4 text-[#93c5fd]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
                <span id="ticket-badge"
                    class="absolute -top-1 -right-1 bg-red-500 text-white text-[9px] font-black w-4 h-4 rounded-full flex items-center justify-center hidden">0</span>
            </button>
        </div>
    </header>

    {{-- ===================== BODY ===================== --}}
    <div class="flex flex-1 overflow-hidden">

        {{-- ── NAV LATERAL ── --}}
        <nav class="pos-nav hidden md:flex">

            {{-- ── ZONA SCROLL: Todo + Categorías dinámicas ── --}}
            <div
                style="flex:1; overflow-y:auto; overflow-x:hidden; display:flex; flex-direction:column; align-items:center; padding:14px 0 8px; gap:4px; scrollbar-width:none;">

                {{-- Todo --}}
                <button onclick="filtrarPOS(null, this)" class="cat-btn active">
                    <div class="cat-icon">
                        <svg width="18" height="18" fill="none" stroke="#93c5fd" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16m-7 6h7" />
                        </svg>
                    </div>
                    <span class="cat-label">Todo</span>
                </button>

                {{-- Categorías dinámicas --}}
                @foreach ($categorias_pos as $cat)
                    <button onclick="filtrarPOS('{{ strtolower($cat->nombre) }}', this)" class="cat-btn">
                        <div class="cat-icon">{{ $cat->icono }}</div>
                        <span class="cat-label">{{ $cat->nombre }}</span>
                    </button>
                @endforeach

            </div>

            {{-- ── ZONA FIJA: Acciones siempre visibles ── --}}
            @if (auth()->user()->rol_id == 4 || auth()->user()->rol_id == 1)
                <div
                    style="flex-shrink:0; display:flex; flex-direction:column; align-items:center; gap:6px; padding:10px 0 14px; border-top:0.5px solid #1e293b; width:100%;">

                    <button onclick="abrirModalMovimiento()" class="cash-btn">
                        <div class="cash-icon" style="background:#1a1a2e;">
                            <svg width="16" height="16" fill="none" stroke="#818cf8" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M7 16V4m0 0L3 8m4-4l4 4M17 8v12m0 0l4-4m-4 4l-4-4" />
                            </svg>
                        </div>
                        <span class="cash-label" style="color:#818cf8;">Caja</span>
                    </button>

                    <div class="nav-divider"></div>

                    <button onclick="imprimirInventarioPOS()" title="Imprimir Plantilla de Inventario"
                        class="w-12 h-12 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-500 flex items-center justify-center hover:bg-amber-500 hover:text-white transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </button>

                    <button onclick="abrirModalCierre()" title="Realizar Arqueo y Cierre"
                        class="w-12 h-12 rounded-xl bg-violet-500/10 border border-violet-500/30 text-violet-500 flex items-center justify-center hover:bg-violet-500 hover:text-white transition-all">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                    </button>

                </div>
            @endif

        </nav>

        {{-- ── SECCIÓN PRODUCTOS ── --}}
        <section class="flex-1 flex flex-col bg-[#0b1120] overflow-hidden">

            {{-- Buscador --}}
            <div class="px-3 py-2.5 border-b border-slate-800/60" style="background:#090e1e;">
                <div class="relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-600" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" id="buscarProducto" placeholder="Buscar producto..." class="search-input">
                </div>
            </div>

            {{-- Categorías móvil --}}
            <div class="md:hidden flex gap-2 px-3 pb-2 overflow-x-auto shrink-0" style="scrollbar-width:none;">
                <button onclick="filtrarPOS(null, this)"
                    class="cat-btn-mobile active-mobile shrink-0 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest border transition-all"
                    style="background:#1a2d50; border-color:#2d4a7a; color:#93c5fd; white-space:nowrap;">
                    Todo
                </button>
                @foreach ($categorias_pos as $cat)
                    <button onclick="filtrarPOS('{{ strtolower($cat->nombre) }}', this)"
                        class="cat-btn-mobile shrink-0 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest border transition-all"
                        style="background:#1a2235; border-color:#283347; color:#475569; white-space:nowrap;">
                        {{ $cat->icono }} {{ $cat->nombre }}
                    </button>
                @endforeach
            </div>

            {{-- Grid de productos --}}
            <div id="gridProductos" class="product-grid custom-scroll">
                @foreach ($productos as $p)
                    <div class="prod-card animate-fade item-producto" data-nombre="{{ strtolower($p->descripcion) }}"
                        data-catpos="{{ strtolower($p->categoria) }}"
                        onclick="agregarAlTicket({{ $p->id }}, '{{ addslashes($p->descripcion) }}', {{ $p->precio }})">

                        <div class="prod-thumb">
                            @if ($p->categoria == 'Cervezas')
                                🍺
                            @elseif ($p->categoria == 'Cocteles')
                                🍹
                            @elseif ($p->categoria == 'Comida')
                                🍔
                            @else
                                <span
                                    class="text-slate-600 font-black text-xl uppercase">{{ substr($p->descripcion, 0, 2) }}</span>
                            @endif
                        </div>

                        <div class="prod-name">{{ $p->descripcion }}</div>
                        <div class="prod-footer">
                            <span class="prod-unit">{{ $p->und_detal }}</span>
                            <span class="prod-price">${{ number_format($p->precio, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </section>

        {{-- ── ASIDE TICKET ── --}}
        <div id="ticket-backdrop" onclick="cerrarTicketMovil()"></div>

        <aside id="panel-ticket"
            class="w-full md:w-[300px] lg:w-[320px] border-l border-slate-800/60 flex flex-col shrink-0 overflow-hidden"
            style="background:#0f1729;">

            {{-- Handle móvil --}}
            <div class="md:hidden flex justify-center pt-3 pb-1 cursor-pointer" onclick="cerrarTicketMovil()">
                <div class="w-8 h-1 bg-slate-700 rounded-full"></div>
            </div>

            {{-- Header --}}
            <div class="px-4 py-3 border-b border-slate-800/60 shrink-0">
                <div class="flex justify-between items-center mb-1">
                    <h2 class="text-sm font-black italic tracking-tight text-white">Orden actual</h2>
                    <span class="text-[9px] font-bold px-2.5 py-1 rounded-md uppercase border"
                        style="background:#1a2d50; color:#93c5fd; border-color:#2d4a7a;" id="mesa-label">
                        Mesa: --
                    </span>
                </div>
                <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest mb-2.5">
                    Atiende: {{ Auth::user()->name }}
                </p>

                {{-- Cliente --}}
                @if (auth()->user()->rol_id == 4 || auth()->user()->rol_id == 1)
                    <button onclick="abrirModalCliente()" class="client-btn">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0"
                                style="background:#1a2d50;">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="#93c5fd" stroke-width="2"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                            </div>
                            <div>
                                <p
                                    class="text-[8px] text-slate-500 font-bold uppercase tracking-widest leading-none mb-0.5">
                                    Facturar a</p>
                                <p class="text-[10px] text-white font-bold leading-none" id="cliente-nombre-ticket">
                                    Consumidor final</p>
                            </div>
                        </div>
                        <svg class="w-3.5 h-3.5 text-slate-600" fill="none" stroke="currentColor"
                            stroke-width="2.5" viewBox="0 0 24 24">
                            <path d="M9 5l7 7-7 7" />
                        </svg>
                    </button>
                @else
                    <div class="flex items-center gap-2 px-1">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0"
                            style="background:#1a2235;">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="#475569" stroke-width="2"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                        </div>
                        <div>
                            <p
                                class="text-[8px] text-slate-500 font-bold uppercase tracking-widest leading-none mb-0.5">
                                Facturar a</p>
                            <p class="text-[10px] text-slate-500 font-bold leading-none">Consumidor final</p>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Items --}}
            <div id="ticket-items" class="flex-1 overflow-y-auto px-4 py-3 space-y-2 custom-scroll">
                <div class="flex items-center justify-center h-full py-16">
                    <p class="text-[10px] text-slate-700 font-bold uppercase tracking-widest">Selecciona productos</p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="px-4 py-4 border-t border-slate-800/60 shrink-0" style="background:#090e1e;">

                {{-- Totales --}}
                <div class="space-y-1.5 mb-4">
                    <div class="flex justify-between text-[10px] font-semibold text-slate-500 uppercase tracking-wide">
                        <span>Subtotal</span><span id="subtotal-val">$0</span>
                    </div>
                    <div class="flex justify-between text-[10px] font-semibold text-slate-500 uppercase tracking-wide">
                        <span>Servicio (10%)</span><span id="servicio-val">$0</span>
                    </div>
                    <div class="flex justify-between items-baseline pt-2 border-t border-slate-800/60">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wide">Total</span>
                        <span class="text-2xl font-black text-white italic" id="total-val">$0</span>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-2 mb-2">
                    <button onclick="vaciarTicket()" class="btn-cancel">Cancelar</button>
                    <button onclick="enviarPedido()" class="btn-send">Enviar pedido</button>
                </div>

                @if (auth()->user()->rol_id == 4 || auth()->user()->rol_id == 1)
                    <button onclick="abrirModalPago()" class="btn-pay">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Cobrar y cerrar mesa
                    </button>
                @else
                    <div class="btn-pay-disabled">Solo el cajero puede procesar pagos</div>
                @endif
            </div>
        </aside>
    </div>

    {{-- ===================== MODAL MESAS ===================== --}}
    <div id="modalMesas" class="fixed inset-0 z-[100] hidden flex-col animate-fade"
        style="background:rgba(9,14,30,0.97); backdrop-filter:blur(8px);">

        <div class="flex items-center justify-between px-5 md:px-8 border-b border-slate-800/60 shrink-0"
            style="height:52px; background:#090e1e;">
            <h2 class="text-sm font-black italic tracking-tight text-white uppercase">Mapa de sala / Mesas</h2>
            <button onclick="cerrarSelectorMesas()"
                class="w-8 h-8 bg-slate-800 hover:bg-red-900/40 border border-slate-700 hover:border-red-800/50 rounded-lg flex items-center justify-center text-slate-400 hover:text-red-400 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <div class="border-b border-slate-800/60 py-3 shrink-0" style="background:#0f172a;">
            <div class="flex flex-col sm:flex-row gap-3 px-5 md:px-8">
                <div class="sm:w-56 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-600" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                    </svg>
                    <select id="filtroZona" onchange="filtrarMesasPorZona()"
                        class="w-full pl-9 pr-4 py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-widest outline-none appearance-none cursor-pointer"
                        style="background:#111827; border:0.5px solid #283347; color:#e2e8f0;">
                        <option value="todas">Todas las zonas</option>
                        @foreach ($zonas as $zona)
                            <option value="{{ $zona->id }}">{{ $zona->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex-1 relative">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-600" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" stroke-width="3" />
                    </svg>
                    <input type="text" id="buscarMesa" onkeyup="filtrarMesasPorZona()"
                        placeholder="Buscar mesa..."
                        class="w-full pl-9 pr-4 py-2.5 rounded-lg text-[10px] font-bold uppercase tracking-widest outline-none"
                        style="background:#111827; border:0.5px solid #283347; color:#e2e8f0;">
                </div>
            </div>
        </div>

        <div id="contenedorMesas"
            class="flex-1 p-5 md:p-8 grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 lg:grid-cols-7 gap-3 md:gap-4 overflow-y-auto custom-scroll">
            @include('facturacion.partials.mesas_grid')
        </div>
    </div>

    {{-- ===================== MODAL CONFIRMAR ===================== --}}
    <div id="modalConfirm" class="modal-overlay">
        <div class="modal-box-dark" style="max-width:320px;">
            <div class="modal-header-dark">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0"
                        style="background:#2d1515;">
                        <svg class="w-4 h-4 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <span class="modal-title-dark" style="font-size:13px;">Confirmar acción</span>
                </div>
                <button onclick="cerrarConfirm()" class="modal-close">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-5">
                <p id="confirmMensaje" class="text-sm font-semibold text-slate-300 mb-5 text-center leading-snug"></p>
                <div class="flex flex-col gap-2">
                    <button id="btnConfirmarAccion" class="w-full py-3 rounded-lg font-bold text-sm transition-all"
                        style="background:#7f1d1d; border:0.5px solid #991b1b; color:#fca5a5;">
                        Confirmar
                    </button>
                    <button onclick="cerrarConfirm()" class="w-full py-3 rounded-lg font-bold text-sm transition-all"
                        style="background:#1e293b; border:0.5px solid #283347; color:#64748b;">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== TOAST ===================== --}}
    <div id="toast-container" class="fixed top-4 right-4 z-[99999] flex flex-col gap-2" style="max-width:340px;">
    </div>

    {{-- ===================== MODAL SUPER CLAVE ===================== --}}
    <div id="modalSuperClave" class="modal-overlay" style="z-index:10000;">
        <div class="modal-box-dark" style="max-width:300px;">
            <div class="modal-header-dark">
                <span class="modal-title-dark">Autorización</span>
                <button onclick="cerrarSuperClave()" class="modal-close">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-5">
                <p class="text-[10px] text-slate-500 text-center mb-4 leading-relaxed">
                    Se requiere superclave de administrador para eliminar productos ya enviados.
                </p>
                <input type="password" id="inputSuperClave" placeholder="••••"
                    class="modal-input-dark text-center text-xl tracking-widest mb-4">
                <div class="flex flex-col gap-2">
                    <button onclick="validarSuperClave()" class="btn-send"
                        style="border-radius:9px; padding:11px;">Confirmar</button>
                    <button onclick="cerrarSuperClave()"
                        class="w-full py-2.5 text-[10px] font-bold text-slate-500 hover:text-slate-300 transition-colors uppercase tracking-widest">
                        Cancelar
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== MODAL PAGO ===================== --}}
    <div id="modalPago" class="modal-overlay animate-fade" style="z-index:10001;">
        <div class="modal-box-dark" style="max-width:480px;">
            <div class="modal-header-dark">
                <div>
                    <span class="modal-title-dark">Finalizar venta</span>
                    <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest mt-0.5"
                        id="pago-mesa-label">Mesa: --</p>
                </div>
                <button onclick="cerrarModalPago()" class="modal-close">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <div class="p-6">
                <div class="text-center mb-6">
                    <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest mb-1">Total venta</p>
                    <h3 class="text-4xl font-black text-white italic" id="pago-total-val">$0</h3>
                    <div class="flex items-center justify-center gap-4 mt-2">
                        <span class="text-[9px] text-slate-500 font-bold uppercase">+ Propina: <span
                                id="pago-propina-label" class="text-amber-400">$0</span></span>
                        <span class="text-[9px] text-slate-500 font-bold uppercase">= Total: <span
                                id="pago-gran-total" class="text-emerald-400 font-black">$0</span></span>
                    </div>
                </div>

                {{-- Métodos --}}
                <div class="grid grid-cols-3 gap-2 mb-5">
                    <button onclick="seleccionarMetodo('efectivo')" id="btn-pago-efectivo"
                        class="metodo-pago p-3 rounded-xl text-center transition-all"
                        style="border:0.5px solid #2d4a7a; background:#1a2d50;">
                        <span class="block text-xl mb-1">💵</span>
                        <span class="text-[9px] font-bold text-white uppercase">Efectivo</span>
                    </button>
                    <button onclick="seleccionarMetodo('tarjeta')" id="btn-pago-tarjeta"
                        class="metodo-pago p-3 rounded-xl text-center transition-all"
                        style="border:0.5px solid #283347; background:#1a2235;">
                        <span class="block text-xl mb-1">💳</span>
                        <span class="text-[9px] font-bold text-slate-400 uppercase">Tarjeta</span>
                    </button>
                    <button onclick="seleccionarMetodo('transferencia')" id="btn-pago-transfer"
                        class="metodo-pago p-3 rounded-xl text-center transition-all"
                        style="border:0.5px solid #283347; background:#1a2235;">
                        <span class="block text-xl mb-1">📱</span>
                        <span class="text-[9px] font-bold text-slate-400 uppercase">Nequi/Davi</span>
                    </button>
                </div>

                <div id="detalles-pago-extra" class="mb-5">
                    <div id="campos-tarjeta" class="hidden space-y-3 animate-fade">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-[9px] text-slate-500 font-bold uppercase block mb-1.5">Tipo</label>
                                <select id="tipo_tarjeta" class="modal-input-dark"
                                    style="padding:9px 12px; font-size:11px;">
                                    <option value="Debito">Débito</option>
                                    <option value="Credito">Crédito</option>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="text-[9px] text-slate-500 font-bold uppercase block mb-1.5">Referencia</label>
                                <input type="text" id="ref_tarjeta" placeholder="Voucher #"
                                    class="modal-input-dark" style="padding:9px 12px; font-size:11px;">
                            </div>
                        </div>
                    </div>
                    <div id="campos-transferencia" class="hidden space-y-3 animate-fade">
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="text-[9px] text-slate-500 font-bold uppercase block mb-1.5">Banco</label>
                                <select id="banco_destino" class="modal-input-dark"
                                    style="padding:9px 12px; font-size:11px;">
                                    <option value="Bancolombia">Bancolombia</option>
                                    <option value="Nequi">Nequi</option>
                                    <option value="Daviplata">Daviplata</option>
                                    <option value="Caja Social">Caja Social</option>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="text-[9px] text-slate-500 font-bold uppercase block mb-1.5">Comprobante</label>
                                <input type="text" id="ref_transferencia" placeholder="ID Transacción"
                                    class="modal-input-dark" style="padding:9px 12px; font-size:11px;">
                            </div>
                        </div>
                    </div>
                </div>

                <div id="wrapper-recibido" class="mb-5">
                    <label class="text-[9px] text-slate-500 font-bold uppercase tracking-widest block mb-1.5">Efectivo
                        recibido</label>
                    <input type="number" id="montoRecibido" oninput="calcularCambio()" placeholder="0"
                        class="modal-input-dark text-xl font-black"
                        style="padding:12px 16px; color:#4ade80; border-width:1px;">
                </div>

                {{-- PROPINA --}}
                <div class="mb-5">
                    <label class="text-[9px] text-slate-500 font-bold uppercase tracking-widest block mb-2">
                        Propina del cliente
                    </label>
                    <div class="grid grid-cols-4 gap-2 mb-2" id="btns-propina">
                        <button onclick="seleccionarPropina(0)"
                            class="propina-btn py-2 rounded-xl text-[9px] font-black uppercase border transition-all"
                            style="background:#1a2235; border-color:#2d4faa; color:#93c5fd;">
                            Sin propina
                        </button>
                        <button onclick="seleccionarPropina(5)"
                            class="propina-btn py-2 rounded-xl text-[9px] font-black uppercase border transition-all"
                            style="background:#1a2235; border-color:#283347; color:#475569;">
                            5%
                        </button>
                        <button onclick="seleccionarPropina(10)"
                            class="propina-btn py-2 rounded-xl text-[9px] font-black uppercase border transition-all"
                            style="background:#1a2235; border-color:#283347; color:#475569;">
                            10%
                        </button>
                        <button onclick="seleccionarPropina('custom')"
                            class="propina-btn py-2 rounded-xl text-[9px] font-black uppercase border transition-all"
                            style="background:#1a2235; border-color:#283347; color:#475569;">
                            Otro
                        </button>
                    </div>
                    <div id="propina-custom-wrap" class="hidden mb-2">
                        <input type="number" id="propina_custom" placeholder="Monto propina"
                            oninput="aplicarPropinaCustom()" class="modal-input-dark font-bold"
                            style="color:#fbbf24;">
                    </div>
                    <div class="flex justify-between items-center px-1">
                        <span class="text-[9px] text-slate-500 font-bold uppercase">Propina:</span>
                        <span class="text-sm font-black text-amber-400" id="propina-val">$0</span>
                    </div>
                </div>

                <div class="flex justify-between items-center p-3.5 rounded-xl mb-5"
                    style="background:#0d2210; border:0.5px solid #166534;">
                    <span class="text-[10px] font-bold text-emerald-400 uppercase">Cambio:</span>
                    <span class="text-xl font-black text-emerald-400" id="pago-cambio-val">$0</span>
                </div>

                <button onclick="procesarPagoFinal()" class="btn-pay" style="padding:14px; border-radius:12px;">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                        viewBox="0 0 24 24">
                        <path d="M5 13l4 4L19 7" />
                    </svg>
                    Registrar pago y cerrar mesa
                </button>
            </div>
        </div>
    </div>

    {{-- ===================== MODAL DE ARQUEO Y CIERRE DE CAJA ===================== --}}
    <div id="modalCierreCaja" class="modal-overlay">
        <div
            class="bg-[#0f172a] border border-slate-800 rounded-3xl w-full max-w-4xl max-h-[90vh] flex flex-col overflow-hidden shadow-2xl animate-fade">

            <div class="p-5 border-b border-slate-800/80 flex justify-between items-center bg-[#020617]">
                <div>
                    <h2 class="text-md font-black italic text-white tracking-tight uppercase">Módulo de Arqueo y Cierre
                    </h2>
                    <p class="text-[9px] text-slate-500 font-bold uppercase tracking-widest mt-0.5">Control global de
                        valores y arqueo físico</p>
                </div>
                <button onclick="cerrarModalCierre()"
                    class="w-8 h-8 rounded-xl bg-slate-800 hover:bg-red-900/40 text-slate-400 hover:text-red-400 flex items-center justify-center transition-all font-bold">✕</button>
            </div>

            <div class="flex-1 overflow-y-auto p-6 grid grid-cols-1 lg:grid-cols-12 gap-6 custom-scroll">

                {{-- COLUMNA IZQUIERDA: PARÁMETROS --}}
                <div class="lg:col-span-5 space-y-4 border-r border-slate-800/60 pr-0 lg:pr-6">
                    <h3 class="text-xs font-extrabold text-blue-500 uppercase tracking-widest">1. Parámetros del Turno
                    </h3>

                    <div class="space-y-3 bg-[#020617] p-4 rounded-2xl border border-slate-800/80">
                        <div>
                            <label
                                class="text-[9px] text-slate-500 font-black uppercase tracking-wider block mb-1">Fecha
                                de Inicio</label>
                            <input type="date" id="cierre_fecha_inicio"
                                class="w-full bg-[#0f172a] border border-slate-800 text-xs font-semibold p-2.5 rounded-xl text-slate-300 outline-none">
                        </div>
                        <div>
                            <label
                                class="text-[9px] text-slate-500 font-black uppercase tracking-wider block mb-1">Hora
                                Apertura Turno</label>
                            <input type="time" id="cierre_hora_inicio"
                                class="w-full bg-[#0f172a] border border-slate-800 text-xs font-semibold p-2.5 rounded-xl text-slate-300 outline-none">
                        </div>
                        <hr class="border-slate-800/60 my-2">
                        <div>
                            <label
                                class="text-[9px] text-slate-400 font-black uppercase tracking-wider block mb-1">Fecha
                                Cierre (Bloqueada)</label>
                            <input type="date" id="cierre_fecha_fin" readonly
                                class="w-full bg-[#0a0f1d] border border-slate-900 text-xs font-bold p-2.5 rounded-xl text-slate-500 cursor-not-allowed outline-none">
                        </div>
                        <div>
                            <label class="text-[9px] text-blue-400 font-black uppercase tracking-wider block mb-1">Hora
                                de Cierre/Salida</label>
                            <input type="time" id="cierre_hora_fin"
                                class="w-full bg-[#0f172a] border border-blue-900/50 text-xs font-black p-2.5 rounded-xl text-white bg-blue-950/10 focus:border-blue-500 outline-none">
                        </div>
                    </div>

                    <div>
                        <label class="text-[9px] text-slate-400 font-black uppercase tracking-wider block mb-1">Base de
                            Caja Inicial ($)</label>
                        <input type="number" id="base_caja" value="0" oninput="calcularArqueoTotal()"
                            class="w-full bg-[#020617] border border-slate-800 text-sm font-black p-3 rounded-xl text-emerald-400 outline-none focus:border-emerald-500 transition-all">
                    </div>
                </div>

                {{-- COLUMNA DERECHA: CALCULADORA --}}
                <div class="lg:col-span-7 space-y-4">

                    <h3 class="text-xs font-extrabold text-emerald-500 uppercase tracking-widest">
                        2. Conteo de Efectivo Físico
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 max-h-[340px] overflow-y-auto pr-1 custom-scroll"
                        id="contenedorDenominaciones">

                        @php
                            $denominaciones = [
                                // BILLETES
                                ['id' => 'b100000', 'v' => 100000, 'l' => 'Billete $100.000'],
                                ['id' => 'b50000', 'v' => 50000, 'l' => 'Billete $50.000'],
                                ['id' => 'b20000', 'v' => 20000, 'l' => 'Billete $20.000'],
                                ['id' => 'b10000', 'v' => 10000, 'l' => 'Billete $10.000'],
                                ['id' => 'b5000', 'v' => 5000, 'l' => 'Billete $5.000'],
                                ['id' => 'b2000', 'v' => 2000, 'l' => 'Billete $2.000'],

                                // MONEDAS
                                ['id' => 'm1000', 'v' => 1000, 'l' => 'Moneda $1.000'],
                                ['id' => 'm500', 'v' => 500, 'l' => 'Moneda $500'],
                                ['id' => 'm200', 'v' => 200, 'l' => 'Moneda $200'],
                                ['id' => 'm100', 'v' => 100, 'l' => 'Moneda $100'],
                            ];
                        @endphp

                        @foreach ($denominaciones as $d)
                            <div class="bg-[#0a0f1d] border border-slate-800/60 rounded-xl p-3 space-y-2">

                                {{-- TITULO --}}
                                <div class="flex items-center justify-between">

                                    <div>
                                        <span
                                            class="text-[9px] font-black uppercase text-slate-400 tracking-wide block">
                                            {{ $d['l'] }}
                                        </span>

                                        <span class="text-[10px] font-black text-blue-400"
                                            id="subtotal_den_{{ $d['v'] }}">
                                            $0
                                        </span>
                                    </div>

                                    <div
                                        class="text-[9px] px-2 py-1 rounded-md bg-slate-900 border border-slate-700 text-slate-400 font-bold">
                                        x {{ number_format($d['v'], 0, ',', '.') }}
                                    </div>
                                </div>

                                {{-- INPUT --}}
                                <div class="flex items-center gap-2">

                                    <input id="{{ $d['id'] }}" type="number" min="0" value="0"
                                        data-valor="{{ $d['v'] }}" oninput="calcularArqueoTotal()"
                                        class="input-denominacion w-full bg-[#0f172a] border border-slate-700 text-right font-black text-sm p-2.5 rounded-xl text-white outline-none focus:border-blue-500">

                                </div>

                            </div>
                        @endforeach

                    </div>

                    {{-- TOTAL GENERAL --}}
                    <div
                        class="bg-[#020617] p-5 rounded-2xl border border-slate-800 flex justify-between items-center shadow-xl">

                        <div>

                            <p class="text-[9px] text-slate-500 font-black uppercase tracking-wider">
                                Total Efectivo Auditado
                            </p>

                            <p class="text-3xl font-black italic tracking-tight text-white mt-0.5"
                                id="total_efectivo_conteo">
                                $0
                            </p>

                        </div>

                        <div class="text-right">

                            <span
                                class="text-[8px] font-black uppercase px-3 py-1.5 rounded-md bg-emerald-500/10 border border-emerald-500/20 text-emerald-400 italic">
                                Listo para cierre
                            </span>

                        </div>

                    </div>

                </div>

            </div>

            {{-- FOOTER --}}
            <div class="p-4 border-t border-slate-800 bg-[#020617] flex justify-end gap-3">

                <button onclick="cerrarModalCierre()"
                    class="px-6 py-2.5 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-700 text-slate-300 transition-all">

                    Cancelar

                </button>

                <button onclick="procesarCierreFinal()"
                    class="px-8 py-2.5 rounded-xl text-xs font-black uppercase bg-violet-600 hover:bg-violet-500 text-white transition-all shadow-lg shadow-violet-950/50">

                    Ejecutar y Emitir Tiquete

                </button>

            </div>

        </div>

        {{-- <div class="p-4 border-t border-slate-800 bg-[#020617] flex justify-end gap-3">
            <button onclick="cerrarModalCierre()"
                class="px-6 py-2.5 rounded-xl text-xs font-bold bg-slate-800 hover:bg-slate-700 text-slate-300 transition-all">Cancelar</button>
            <button onclick="procesarCierreFinal()"
                class="px-8 py-2.5 rounded-xl text-xs font-black uppercase bg-violet-600 hover:bg-violet-500 text-white transition-all shadow-lg shadow-violet-950/50">Ejecutar
                y Emitir Tiquete</button>
        </div> --}}
    </div>
    </div>

    {{-- ===================== MODAL CLIENTE ===================== --}}
    <div id="modalCliente" class="modal-overlay" style="z-index:10002;">
        <div class="modal-box-dark" style="max-width:420px;">
            <div class="modal-header-dark">
                <span class="modal-title-dark">Buscar cliente</span>
                <button onclick="cerrarModalCliente()" class="modal-close">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-5">
                <div class="relative mb-3">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-600" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                    <input type="text" id="buscarTercero" placeholder="NIT, cédula o nombre..."
                        class="modal-input-dark" style="padding-left:38px;">
                </div>

                <button onclick="seleccionarCliente(1, 'Consumidor Final', '')"
                    class="w-full flex items-center gap-3 p-3 rounded-lg mb-2 transition-all text-left group"
                    style="background:#1a2235; border:0.5px solid #283347;">
                    <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-[10px] font-black group-hover:bg-blue-600 transition-colors"
                        style="background:#1e293b; color:#475569;">CF</div>
                    <div>
                        <p class="text-[11px] font-bold text-white leading-none">Consumidor Final</p>
                        <p class="text-[9px] text-slate-500 mt-1 uppercase tracking-tighter">Ventas de mostrador</p>
                    </div>
                </button>

                <div id="listaTerceros" class="custom-scroll space-y-1" style="max-height:240px; overflow-y:auto;">
                    <p class="text-[10px] text-slate-600 text-center py-6 font-bold uppercase tracking-widest">Escribe
                        para buscar...</p>
                </div>
            </div>
        </div>
    </div>

    {{-- ===================== MODAL MOVIMIENTO CAJA ===================== --}}
    <div id="modalMovimientoCaja" class="modal-overlay" style="z-index:10002;">
        <div class="modal-box-dark" style="max-width:360px;">
            <div class="modal-header-dark">
                <span class="modal-title-dark">Movimiento de caja</span>
                <button onclick="cerrarModalMovimiento()" class="modal-close">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <div class="p-5 space-y-4">

                {{-- SELECTOR TIPO --}}
                <div class="grid grid-cols-2 gap-2">
                    <button id="btn-tipo-ingreso" onclick="seleccionarTipoMovimiento('ingreso')"
                        class="tipo-mov-btn flex items-center justify-center gap-2 p-3 rounded-xl border transition-all"
                        style="background:#0d2210; border-color:#14532d; color:#4ade80;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-[10px] font-black uppercase tracking-widest">Ingreso</span>
                    </button>
                    <button id="btn-tipo-egreso" onclick="seleccionarTipoMovimiento('egreso')"
                        class="tipo-mov-btn flex items-center justify-center gap-2 p-3 rounded-xl border transition-all"
                        style="background:#1a2235; border-color:#283347; color:#475569;">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M15 12H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-[10px] font-black uppercase tracking-widest">Egreso</span>
                    </button>
                </div>

                <div>
                    <label class="text-[9px] text-slate-500 font-bold uppercase tracking-widest block mb-1.5">
                        Monto
                    </label>
                    <input type="number" id="mov_monto" placeholder="0" class="modal-input-dark text-lg font-bold"
                        style="color:#4ade80;">
                </div>

                <div>
                    <label class="text-[9px] text-slate-500 font-bold uppercase tracking-widest block mb-1.5">
                        Concepto / Motivo
                    </label>
                    <textarea id="mov_concepto" rows="3" class="modal-input-dark" style="resize:none;"
                        placeholder="Ej: Pago de hielo, transporte, etc..."></textarea>
                </div>

                <button onclick="guardarMovimiento()" class="btn-send w-full"
                    style="padding:14px; border-radius:12px;">
                    Registrar Movimiento
                </button>
                <button onclick="cerrarModalMovimiento()"
                    class="w-full py-2 text-[10px] font-bold text-slate-600 hover:text-slate-400 uppercase tracking-widest transition-colors">
                    Cancelar
                </button>
            </div>
        </div>
    </div>

    {{-- ===================== SCRIPTS ===================== --}}
    <script>
        function abrirSelectorMesas() {
            const m = document.getElementById('modalMesas');
            m.style.display = 'flex';
            m.classList.add('show');
        }

        function cerrarSelectorMesas() {
            const m = document.getElementById('modalMesas');
            m.style.display = 'none';
            m.classList.remove('show');
        }

        function filtrarPOS(catNombre, btn) {
            document.querySelectorAll('.cat-btn').forEach(b => b.classList.remove('active'));
            document.querySelectorAll('.cat-btn-mobile').forEach(b => {
                b.style.background = '#1a2235';
                b.style.borderColor = '#283347';
                b.style.color = '#475569';
            });
            if (btn.classList.contains('cat-btn')) btn.classList.add('active');
            if (btn.classList.contains('cat-btn-mobile')) {
                btn.style.background = '#1a2d50';
                btn.style.borderColor = '#2d4a7a';
                btn.style.color = '#93c5fd';
            }
            document.querySelectorAll('.item-producto').forEach(card => {
                const visible = !catNombre || card.dataset.catpos === catNombre;
                card.style.display = visible ? '' : 'none';
            });
        }

        document.getElementById('buscarProducto').addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            document.querySelectorAll('.item-producto').forEach(card => {
                card.style.display = !q || (card.dataset.nombre || '').includes(q) ? '' : 'none';
            });
        });

        function abrirModalCliente() {
            document.getElementById('modalCliente').classList.add('show');
            var inp = document.getElementById('buscarTercero');
            inp.value = '';
            document.getElementById('listaTerceros').innerHTML =
                '<p class="text-[10px] text-slate-600 text-center py-6 font-bold uppercase">Escribe para buscar...</p>';
            setTimeout(function() {
                inp.focus();
            }, 100);
        }

        function cerrarModalCliente() {
            document.getElementById('modalCliente').classList.remove('show');
        }

        function abrirModalMovimiento(tipo) {
            const titulo = document.getElementById('movimientoTitulo');
            titulo.textContent = tipo === 'egreso' ? 'Egreso de caja' : 'Ingreso de caja';
            titulo.dataset.tipo = tipo;
            document.getElementById('modalMovimientoCaja').classList.add('show');
        }

        function cerrarModalMovimiento() {
            document.getElementById('modalMovimientoCaja').classList.remove('show');
            document.getElementById('mov_monto').value = '';
            document.getElementById('mov_concepto').value = '';
        }

        function guardarMovimiento() {
            const tipo = document.getElementById('movimientoTitulo').dataset.tipo;
            const monto = document.getElementById('mov_monto').value;
            const concepto = document.getElementById('mov_concepto').value;
            if (!monto || !concepto) return;
            console.log('Movimiento:', {
                tipo,
                monto,
                concepto
            });
            cerrarModalMovimiento();
        }

        function cerrarConfirm() {
            document.getElementById('modalConfirm').classList.remove('show');
        }

        function buscarTerceros(q) {
            fetch('/terceros/buscar?query=' + encodeURIComponent(q), {
                    headers: {
                        'Accept': 'application/json'
                    }
                })
                .then(function(r) {
                    return r.json();
                })
                .then(function(data) {
                    var lista = document.getElementById('listaTerceros');
                    if (!data.length) {
                        lista.innerHTML =
                            '<p class="text-[10px] text-slate-600 text-center py-6 font-bold uppercase">Sin resultados</p>';
                        return;
                    }
                    lista.innerHTML = data.map(function(t) {
                        var nombre = t.tipo === 'persona' ?
                            (t.nombre + ' ' + (t.apellido || '')).trim() :
                            (t.razon_social || '');
                        var doc = t.tipo === 'persona' ? (t.cedula || '') : (t.nit || '');
                        var iniciales = nombre.substring(0, 2).toUpperCase();
                        return (
                            '<button onclick="seleccionarCliente(' + t.id + ', \'' +
                            nombre.replace(/'/g, "\\'") + '\', \'' + doc + '\')" ' +
                            'class="w-full flex items-center gap-3 p-3 rounded-lg transition-all text-left mb-1" ' +
                            'style="background:#1a2235; border:0.5px solid #283347;">' +
                            '<div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-[10px] font-black" ' +
                            'style="background:#2d4faa; color:#93c5fd;">' + iniciales + '</div>' +
                            '<div class="flex-1 min-w-0">' +
                            '<p class="text-[11px] font-bold text-white truncate">' + nombre + '</p>' +
                            '<p class="text-[9px] text-slate-500">' + (doc || 'Sin documento') +
                            (t.celular ? ' · ' + t.celular : '') + '</p>' +
                            '</div></button>'
                        );
                    }).join('');
                })
                .catch(function() {
                    document.getElementById('listaTerceros').innerHTML =
                        '<p class="text-[10px] text-red-500 text-center py-4 font-bold uppercase">Error al buscar</p>';
                });
        }

        function seleccionarCliente(id, nombre, documento) {
            window.clienteSeleccionado = {
                id: id,
                nombre: nombre,
                documento: documento
            };
            var el = document.getElementById('cliente-nombre-ticket');
            if (el) el.textContent = nombre;
            cerrarModalCliente();
            window.notificar('Cliente: ' + nombre, 'success');
        }

        // Debounce sobre el input
        document.getElementById('buscarTercero').addEventListener('input', function() {
            clearTimeout(window._terceroTimer);
            var q = this.value.trim();
            if (q.length < 2) {
                document.getElementById('listaTerceros').innerHTML =
                    '<p class="text-[10px] text-slate-600 text-center py-4 font-bold uppercase">Escribe al menos 2 caracteres...</p>';
                return;
            }
            document.getElementById('listaTerceros').innerHTML =
                '<p class="text-[10px] text-slate-500 text-center py-4 font-bold uppercase">Buscando...</p>';
            window._terceroTimer = setTimeout(function() {
                buscarTerceros(q);
            }, 350);
        });
    </script>

</body>

</html>
