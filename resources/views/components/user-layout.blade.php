<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AppSystem</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #F5F6FA;
            display: flex;
            height: 100vh;
            overflow: hidden;
        }

        /* ── SIDEBAR ── */
        .sidebar {
            width: 210px;
            background: #fff;
            border-right: 1px solid #EAECF0;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            height: 100vh;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        /* Brand */
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 16px 14px 14px;
            border-bottom: 1px solid #EAECF0;
            text-decoration: none;
        }

        .brand-icon {
            width: 28px;
            height: 28px;
            background: #1D4ED8;
            border-radius: 7px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .brand-icon svg {
            width: 13px;
            height: 13px;
            fill: white;
        }

        .brand-name {
            font-size: 14px;
            font-weight: 600;
            color: #111827;
            letter-spacing: -0.3px;
        }

        /* Nav */
        .sidebar-nav {
            flex: 1;
            padding: 10px 8px;
            display: flex;
            flex-direction: column;
            gap: 2px;
            overflow-y: auto;
        }

        .nav-section-label {
            font-size: 10px;
            font-weight: 500;
            color: #9CA3AF;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding: 8px 8px 4px;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 9px;
            padding: 7px 10px;
            border-radius: 7px;
            border: none;
            background: transparent;
            cursor: pointer;
            width: 100%;
            text-align: left;
            transition: background 0.15s;
        }

        .nav-item:hover {
            background: #F3F4F6;
        }

        .nav-item.active {
            background: #EFF6FF;
        }

        .nav-item-icon {
            font-size: 15px;
            flex-shrink: 0;
        }

        .nav-item-label {
            font-size: 13px;
            font-weight: 500;
            color: #4B5563;
        }

        .nav-item.active .nav-item-label {
            color: #1D4ED8;
        }

        .nav-sep {
            height: 1px;
            background: #F3F4F6;
            margin: 6px 8px;
        }

        /* Footer usuario */
        .sidebar-footer {
            padding: 12px 10px;
            border-top: 1px solid #EAECF0;
            display: flex;
            align-items: center;
            gap: 9px;
        }

        .avatar {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: #1D4ED8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 12px;
            font-weight: 600;
            color: white;
            flex-shrink: 0;
        }

        .user-info {
            flex: 1;
            min-width: 0;
        }

        .user-name {
            font-size: 12px;
            font-weight: 600;
            color: #111827;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .user-role {
            font-size: 11px;
            color: #1D4ED8;
            margin-top: 1px;
        }

        .btn-logout {
            font-size: 11px;
            font-weight: 500;
            color: #DC2626;
            background: #FEF2F2;
            border: 1px solid #FECACA;
            padding: 4px 8px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.15s;
            white-space: nowrap;
        }

        .btn-logout:hover {
            background: #FEE2E2;
            border-color: #FCA5A5;
        }

        /* ── ÁREA PRINCIPAL ── */
        .main-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
            min-width: 0;
            height: 100vh;
            overflow: hidden;
        }

        .topbar {
            height: 46px;
            background: #fff;
            border-bottom: 1px solid #EAECF0;
            display: flex;
            align-items: center;
            padding: 0 20px;
            flex-shrink: 0;
        }

        .topbar-breadcrumb {
            font-size: 13px;
            color: #6B7280;
        }

        .topbar-breadcrumb span {
            color: #111827;
            font-weight: 500;
        }

        #main-content {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
        }

        /* ── MODALES Y NOTIF ── */
        #modalConfirm {
            position: fixed;
            inset: 0;
            background: rgba(17, 24, 39, 0.5);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        #modalConfirm.show {
            display: flex;
        }

        .modal-box {
            background: #fff;
            border-radius: 16px;
            padding: 28px 24px;
            width: 100%;
            max-width: 340px;
            text-align: center;
        }

        #notificaciones {
            position: fixed;
            top: 16px;
            right: 16px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 8px;
            pointer-events: none;
        }
    </style>
</head>

<body>

    {{-- ── SIDEBAR ── --}}
    <aside class="sidebar">

        <a class="sidebar-brand" href="#">
            <div class="brand-icon">
                <svg viewBox="0 0 16 16">
                    <rect x="2" y="2" width="5" height="5" rx="1" />
                    <rect x="9" y="2" width="5" height="5" rx="1" />
                    <rect x="2" y="9" width="5" height="5" rx="1" />
                    <rect x="9" y="9" width="5" height="5" rx="1" />
                </svg>
            </div>
            <span class="brand-name">AppSystem</span>
        </a>

        <nav class="sidebar-nav">

            {{-- ARCHIVO --}}
            <div class="nav-section-label">Archivo</div>
            <button class="nav-item" onclick="loadView('usuarios')">
                <span class="nav-item-icon">👤</span>
                <span class="nav-item-label">Cuentas</span>
            </button>
            <button class="nav-item" onclick="loadView('terceros')">
                <span class="nav-item-icon">👥</span>
                <span class="nav-item-label">Terceros</span>
            </button>
            <button class="nav-item" onclick="loadView('impresoras')">
                <span class="nav-item-icon">🖨️</span>
                <span class="nav-item-label">Impresoras</span>
            </button>

            <div class="nav-sep"></div>

            {{-- OPERACIONES --}}
            <div class="nav-section-label">Operaciones</div>
            <button class="nav-item" onclick="loadView('productos')">
                <span class="nav-item-icon">📦</span>
                <span class="nav-item-label">Productos</span>
            </button>
            <button class="nav-item" onclick="loadView('cajas')">
                <span class="nav-item-icon">💰</span>
                <span class="nav-item-label">Cajas</span>
            </button>
            <button class="nav-item" onclick="loadView('grupos')">
                <span class="nav-item-icon">🏷️</span>
                <span class="nav-item-label">Grupos menú</span>
            </button>
            <button class="nav-item" onclick="loadView('bodegas')">
                <span class="nav-item-icon">🏭</span>
                <span class="nav-item-label">Bodegas</span>
            </button>
            <button class="rbtn-lg" onclick="loadView('categorias_pos')">
                <span class="rbtn-icon">🏷️</span>
                <span class="rbtn-label">Categorías POS</span>
            </button>
            <button class="nav-item" onclick="toggleMenu('menuList')">
                <span class="nav-item-icon">📁</span>
                <span class="nav-item-label">Clasificación</span>
            </button>

            <div class="nav-sep"></div>

            {{-- REPORTES --}}
            <div class="nav-section-label">Reportes</div>
            <button class="nav-item" onclick="loadView('existencias')">
                <span class="nav-item-icon">📋</span>
                <span class="nav-item-label">Stock</span>
            </button>
            <button class="nav-item" onclick="loadView('ajustes')">
                <span class="nav-item-icon">⚙️</span>
                <span class="nav-item-label">Ajustes</span>
            </button>

        </nav>

        <div class="sidebar-footer">
            <div class="avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
            <div class="user-info">
                <div class="user-name">{{ auth()->user()->name }}</div>
                <div class="user-role">{{ auth()->user()->rol->nombre ?? 'Usuario' }}</div>
            </div>
            <form method="POST" action="/logout">
                @csrf
                <button type="submit" class="btn-logout">Salir</button>
            </form>
        </div>

    </aside>

    {{-- ── ÁREA PRINCIPAL ── --}}
    <div class="main-wrapper">

        <div class="topbar">
            <span class="topbar-breadcrumb" id="topbar-breadcrumb">
                Selecciona una opción
            </span>
        </div>

        <main id="main-content"></main>

    </div>

    {{-- ── NOTIFICACIONES ── --}}
    <div id="notificaciones"></div>

    {{-- ── MODAL CONFIRMAR ── --}}
    <div id="modalConfirm">
        <div class="modal-box">
            <div class="flex justify-center mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
            <p id="confirmMensaje" class="font-semibold text-gray-900 mb-6"></p>
            <div class="flex flex-col gap-2">
                <button id="btnConfirmarAccion"
                    class="w-full py-2.5 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-colors">Confirmar</button>
                <button
                    class="w-full py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors"
                    onclick="cerrarConfirm()">Cancelar</button>
            </div>
        </div>
    </div>

</body>

{{-- Scripts --}}
<script src="{{ asset('js/utils.js') }}?v={{ filemtime(public_path('js/utils.js')) }}"></script>
<script src="{{ asset('js/modales.js') }}?v={{ filemtime(public_path('js/modales.js')) }}"></script>
<script src="{{ asset('js/terceros.js') }}?v={{ filemtime(public_path('js/terceros.js')) }}"></script>
<script src="{{ asset('js/productos.js') }}?v={{ filemtime(public_path('js/productos.js')) }}"></script>
<script src="{{ asset('js/ajustes.js') }}?v={{ filemtime(public_path('js/ajustes.js')) }}"></script>
<script src="{{ asset('js/existencia.js') }}?v={{ filemtime(public_path('js/existencia.js')) }}"></script>
<script src="{{ asset('js/usuarios.js') }}?v={{ filemtime(public_path('js/usuarios.js')) }}"></script>
<script src="{{ asset('js/cajas.js') }}?v={{ filemtime(public_path('js/cajas.js')) }}"></script>
<script src="{{ asset('js/impresoras.js') }}?v={{ filemtime(public_path('js/impresoras.js')) }}"></script>
<script src="{{ asset('js/grupos.js') }}?v={{ filemtime(public_path('js/grupos.js')) }}"></script>
{{-- <script src="{{ asset('js/bodegas.js') }}?v={{ filemtime(public_path('js/bodegas.js')) }}"></script> --}}
<script src="{{ asset('js/categorias_pos.js') }}?v={{ filemtime(public_path('js/categorias_pos.js')) }}"></script>
<script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>

<script>
    // Marcar item activo y actualizar breadcrumb
    const sectionMap = {
        usuarios: 'Archivo',
        terceros: 'Archivo',
        impresoras: 'Archivo',
        productos: 'Operaciones',
        cajas: 'Operaciones',
        grupos: 'Operaciones',
        bodegas: 'Operaciones',
        existencias: 'Reportes',
        ajustes: 'Reportes'
    };

    const labelMap = {
        usuarios: 'Cuentas',
        terceros: 'Terceros',
        impresoras: 'Impresoras',
        productos: 'Productos',
        cajas: 'Cajas',
        grupos: 'Grupos menú',
        bodegas: 'Bodegas',
        existencias: 'Stock',
        ajustes: 'Ajustes'
    };

    // Interceptar loadView para marcar activo
    const _originalLoadView = typeof loadView === 'function' ? loadView : null;

    function setActiveNav(view) {
        document.querySelectorAll('.nav-item').forEach(b => b.classList.remove('active'));
        const btns = document.querySelectorAll('.nav-item');
        btns.forEach(b => {
            const onclick = b.getAttribute('onclick') || '';
            if (onclick.includes(`'${view}'`)) b.classList.add('active');
        });

        const section = sectionMap[view] || '';
        const label = labelMap[view] || view;
        const bc = document.getElementById('topbar-breadcrumb');
        if (bc) bc.innerHTML = section ?
            `${section} &rsaquo; <span>${label}</span>` :
            `<span>${label}</span>`;
    }

    // Parchar loadView para añadir lógica de sidebar
    document.addEventListener('DOMContentLoaded', () => {
        const originalLoadView = window.loadView;
        if (originalLoadView) {
            window.loadView = function(view) {
                setActiveNav(view);
                originalLoadView(view);
            };
        }
    });

    function cerrarConfirm() {
        document.getElementById('modalConfirm').classList.remove('show');
    }
</script>

</html>
{{-- 
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AppSystem</title>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: #F5F6FA;
        }

        /* ── NAVBAR ── */
        .navbar {
            background: #fff;
            border-bottom: 1px solid #EAECF0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 24px;
            height: 52px;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .nav-left {
            display: flex;
            align-items: center;
            gap: 24px;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }

        /* Brand */
        .brand {
            display: flex;
            align-items: center;
            gap: 9px;
            text-decoration: none;
        }

        .brand-icon {
            width: 30px;
            height: 30px;
            background: #1D4ED8;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .brand-icon svg {
            width: 15px;
            height: 15px;
            fill: white;
        }

        .brand-name {
            font-size: 15px;
            font-weight: 600;
            color: #111827;
            letter-spacing: -0.3px;
        }

        /* Nav tabs */
        .nav-tabs {
            display: flex;
            gap: 2px;
        }

        .nav-tab {
            font-size: 13px;
            font-weight: 500;
            color: #6B7280;
            padding: 5px 13px;
            border-radius: 6px;
            border: none;
            background: transparent;
            cursor: pointer;
            transition: background 0.15s, color 0.15s;
        }

        .nav-tab:hover {
            background: #F3F4F6;
            color: #111827;
        }

        .nav-tab.active {
            background: #EFF6FF;
            color: #1D4ED8;
            font-weight: 600;
        }

        /* User area */
        .user-area {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-meta {
            text-align: right;
        }

        .user-name {
            font-size: 13px;
            font-weight: 600;
            color: #111827;
            line-height: 1.2;
        }

        .user-role {
            font-size: 11px;
            color: #1D4ED8;
            margin-top: 1px;
        }

        .avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #1D4ED8;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 600;
            color: white;
            flex-shrink: 0;
        }

        .btn-logout {
            font-size: 12px;
            font-weight: 500;
            color: #DC2626;
            background: #FEF2F2;
            border: 1px solid #FECACA;
            padding: 5px 13px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.15s;
        }

        .btn-logout:hover {
            background: #FEE2E2;
            border-color: #FCA5A5;
        }

        /* ── RIBBON (Mejorado con Colapso) ── */
        .ribbon-container {
            background: #F9FAFB;
            border-bottom: 1px solid #EAECF0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
            max-height: 500px;
            /* Suficiente para mostrar el contenido */
        }

        .ribbon-container.collapsed {
            max-height: 0;
            border-bottom: none;
        }

        .ribbon-padding {
            padding: 10px 24px;
        }

        .ribbon-tab {
            display: none;
            animation: fadeIn 0.2s ease;
        }

        .ribbon-tab.active {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .ribbon-group {
            background: #fff;
            border: 1px solid #EAECF0;
            border-radius: 10px;
            padding: 10px 12px 8px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 106px;
        }

        .ribbon-body {
            display: flex;
            align-items: center;
            gap: 2px;
        }

        /* Botones Ribbon */
        .rbtn-lg {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 5px;
            padding: 7px 10px;
            border-radius: 8px;
            border: none;
            background: transparent;
            cursor: pointer;
            min-width: 62px;
            transition: background 0.15s;
        }

        .rbtn-lg:hover {
            background: #EFF6FF;
        }

        .rbtn-icon {
            font-size: 20px;
        }

        .rbtn-label {
            font-size: 11px;
            font-weight: 500;
            color: #374151;
            white-space: nowrap;
        }

        .rbtn-sm {
            display: flex;
            align-items: center;
            gap: 7px;
            padding: 5px 9px;
            border-radius: 6px;
            border: none;
            background: transparent;
            cursor: pointer;
            width: 100%;
            font-size: 11px;
            font-weight: 500;
            color: #374151;
            transition: background 0.15s;
        }

        .rbtn-sm:hover {
            background: #EFF6FF;
            color: #1D4ED8;
        }

        .rbtn-sm-icon {
            font-size: 14px;
        }

        .ribbon-sep {
            width: 1px;
            height: 56px;
            background: #EAECF0;
            margin: 0 6px;
            align-self: center;
            flex-shrink: 0;
        }

        .ribbon-sm-col {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .ribbon-footer {
            font-size: 10px;
            font-weight: 500;
            color: #9CA3AF;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-top: 1px solid #F3F4F6;
            padding-top: 6px;
            margin-top: 6px;
        }

        /* ── MODALES Y NOTIF ── */
        #modalConfirm {
            position: fixed;
            inset: 0;
            background: rgba(17, 24, 39, 0.5);
            backdrop-filter: blur(4px);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        }

        #modalConfirm.show {
            display: flex;
        }

        .modal-box {
            background: #fff;
            border-radius: 16px;
            padding: 28px 24px;
            width: 100%;
            max-width: 340px;
            text-align: center;
        }

        #notificaciones {
            position: fixed;
            top: 16px;
            right: 16px;
            z-index: 9999;
            display: flex;
            flex-direction: column;
            gap: 8px;
            pointer-events: none;
        }
    </style>
</head>

<body>


    <header class="navbar">
        <div class="nav-left">
            <a class="brand" href="#">
                <div class="brand-icon">
                    <svg viewBox="0 0 16 16">
                        <rect x="2" y="2" width="5" height="5" rx="1" />
                        <rect x="9" y="2" width="5" height="5" rx="1" />
                        <rect x="2" y="9" width="5" height="5" rx="1" />
                        <rect x="9" y="9" width="5" height="5" rx="1" />
                    </svg>
                </div>
                <span class="brand-name">AppSystem</span>
            </a>

            <nav class="nav-tabs">
                <button class="nav-tab active" onclick="handleTabClick('archivo', this)">Archivo</button>
                <button class="nav-tab" onclick="handleTabClick('operaciones', this)">Operaciones</button>
                <button class="nav-tab" onclick="handleTabClick('reportes', this)">Reportes</button>
            </nav>
        </div>

        <div class="nav-right">
            <div class="user-area">
                <div class="user-meta">
                    <p class="user-name">{{ auth()->user()->name }}</p>
                    <p class="user-role">{{ auth()->user()->rol->nombre ?? 'Usuario' }}</p>
                </div>
                <div class="avatar">{{ substr(auth()->user()->name, 0, 1) }}</div>
            </div>
            <form method="POST" action="/logout">
                @csrf
                <button type="submit" class="btn-logout">Cerrar sesión</button>
            </form>
        </div>
    </header>


    <div id="ribbon-container" class="ribbon-container">
        <div class="ribbon-padding">

            <div id="tab-archivo" class="ribbon-tab active">
                <div class="ribbon-group">
                    <div class="ribbon-body">
                        <button class="rbtn-lg" onclick="loadView('usuarios')">
                            <span class="rbtn-icon">👤</span>
                            <span class="rbtn-label">Cuentas</span>
                        </button>
                        <button class="rbtn-lg" onclick="loadView('terceros')">
                            <span class="rbtn-icon">👥</span>
                            <span class="rbtn-label">Terceros</span>
                        </button>
                        <div class="ribbon-sep"></div>
                        <div class="ribbon-sm-col">
                            <button class="rbtn-sm" onclick="loadView('impresoras')">
                                <span class="rbtn-sm-icon">🖨️</span> Impresoras
                            </button>
                        </div>
                    </div>
                    <div class="ribbon-footer">Configuración base</div>
                </div>
            </div>

            <div id="tab-operaciones" class="ribbon-tab">
                <div class="ribbon-group">
                    <div class="ribbon-body">
                        <button class="rbtn-lg" onclick="loadView('productos')">
                            <span class="rbtn-icon">📦</span>
                            <span class="rbtn-label">Productos</span>
                        </button>
                        <button class="rbtn-lg" onclick="loadView('cajas')">
                            <span class="rbtn-icon">💰</span>
                            <span class="rbtn-label">Cajas</span>
                        </button>
                        <button class="rbtn-lg" onclick="loadView('grupos')">
                            <span class="rbtn-icon">🏷️</span>
                            <span class="rbtn-label">Grupos menú</span>
                        </button>

                        <button class="rbtn-lg" onclick="loadView('bodegas')">
                            <span class="rbtn-icon">🏭</span>
                            <span class="rbtn-label">Bodegas</span>
                        </button>

                        <div class="ribbon-sep"></div>
                        <div class="ribbon-sm-col">
                            <button class="rbtn-sm" onclick="toggleMenu('menuList')">
                                <span class="rbtn-sm-icon">📁</span> Clasificación
                            </button>
                        </div>
                    </div>
                    <div class="ribbon-footer">Gestión diaria</div>
                </div>
            </div>

            <div id="tab-reportes" class="ribbon-tab">
                <div class="ribbon-group">
                    <div class="ribbon-body">
                        <button class="rbtn-lg" onclick="loadView('existencias')">
                            <span class="rbtn-icon">📋</span>
                            <span class="rbtn-label">Stock</span>
                        </button>
                        <button class="rbtn-lg" onclick="loadView('ajustes')">
                            <span class="rbtn-icon">⚙️</span>
                            <span class="rbtn-label">Ajustes</span>
                        </button>
                    </div>
                    <div class="ribbon-footer">Análisis y auditoría</div>
                </div>
            </div>

        </div>
    </div>

    <main id="main-content" class="p-6"></main>

    <div id="notificaciones"></div>

    <div id="modalConfirm">
        <div class="modal-box">
            <div class="flex justify-center mb-4">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
            </div>
            <p id="confirmMensaje" class="font-semibold text-gray-900 mb-6"></p>
            <div class="flex flex-col gap-2">
                <button id="btnConfirmarAccion"
                    class="w-full py-2.5 bg-red-600 text-white rounded-lg font-semibold hover:bg-red-700 transition-colors">Confirmar</button>
                <button
                    class="w-full py-2.5 bg-gray-100 text-gray-700 rounded-lg font-medium hover:bg-gray-200 transition-colors"
                    onclick="cerrarConfirm()">Cancelar</button>
            </div>
        </div>
    </div>

</body>

<script src="{{ asset('js/utils.js') }}?v={{ filemtime(public_path('js/utils.js')) }}"></script>
<script src="{{ asset('js/modales.js') }}?v={{ filemtime(public_path('js/modales.js')) }}"></script>
<script src="{{ asset('js/terceros.js') }}?v={{ filemtime(public_path('js/terceros.js')) }}"></script>
<script src="{{ asset('js/productos.js') }}?v={{ filemtime(public_path('js/productos.js')) }}"></script>
<script src="{{ asset('js/ajustes.js') }}?v={{ filemtime(public_path('js/ajustes.js')) }}"></script>
<script src="{{ asset('js/existencia.js') }}?v={{ filemtime(public_path('js/existencia.js')) }}"></script>
<script src="{{ asset('js/usuarios.js') }}?v={{ filemtime(public_path('js/usuarios.js')) }}"></script>
<script src="{{ asset('js/cajas.js') }}?v={{ filemtime(public_path('js/cajas.js')) }}"></script>
<script src="{{ asset('js/impresoras.js') }}?v={{ filemtime(public_path('js/impresoras.js')) }}"></script>
<script src="{{ asset('js/grupos.js') }}?v={{ filemtime(public_path('js/grupos.js')) }}"></script>
<script src="{{ asset('js/app.js') }}?v={{ filemtime(public_path('js/app.js')) }}"></script>

<script>
    let activeTabName = 'archivo';

    function handleTabClick(name, btnEl) {
        const ribbon = document.getElementById('ribbon-container');

        // Si el ribbon está colapsado -> Ábrelo y muestra la pestaña
        if (ribbon.classList.contains('collapsed')) {
            ribbon.classList.remove('collapsed');
            showTabContent(name, btnEl);
            return;
        }

        // Si ya está abierto y haces clic en la misma pestaña activa -> Colapsar
        if (activeTabName === name) {
            ribbon.classList.add('collapsed');
            document.querySelectorAll('.nav-tab').forEach(b => b.classList.remove('active'));
            activeTabName = null;
        } else {
            // Si haces clic en una pestaña diferente -> Cambia el contenido
            showTabContent(name, btnEl);
        }
    }

    function showTabContent(name, btnEl) {
        activeTabName = name;

        // Botones Navbar
        document.querySelectorAll('.nav-tab').forEach(b => b.classList.remove('active'));
        btnEl.classList.add('active');

        // Contenido Ribbon
        document.querySelectorAll('.ribbon-tab').forEach(t => t.classList.remove('active'));
        const tab = document.getElementById('tab-' + name);
        if (tab) tab.classList.add('active');
    }

    function cerrarConfirm() {
        document.getElementById('modalConfirm').classList.remove('show');
    }
</script>

</html> --}}
