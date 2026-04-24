<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AppSystem</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100 font-sans antialiased">

    <div class="bg-white border-b border-gray-200 shadow-sm sticky top-0 z-50">
        <div class="flex items-center justify-between px-6 py-2.5">

            <div class="flex items-center space-x-10">
                <div class="flex items-center space-x-3">
                    <img src="{{ asset('imgs/Logo.png') }}" class="w-9 h-9 object-contain">
                    <span class="font-extrabold text-gray-800 text-xl tracking-tight">AppSystem</span>
                </div>

                <button onclick="toggleTab('archivo')"
                    class="font-bold text-gray-600 hover:text-blue-600 transition-colors py-2 border-b-2 border-transparent hover:border-blue-600">
                    Archivo
                </button>
            </div>

            <form method="POST" action="/logout">
                @csrf
                <button type="submit"
                    class="bg-red-50 text-red-600 hover:bg-red-600 hover:text-white px-4 py-2 rounded-lg text-sm font-bold transition-all border border-red-100 shadow-sm">
                    Cerrar sesión
                </button>
            </form>

        </div>
    </div>

    <div id="ribbon" class="bg-white border-b border-gray-200 p-4 hidden shadow-inner animate-fadeIn">

        <div id="archivo" class="tab-content hidden flex gap-4 flex-wrap">

            <div
                class="inline-flex flex-col justify-between border border-gray-200 px-4 py-4 rounded-xl bg-gray-50/50 shadow-sm min-h-[150px]">

                <div class="flex items-start">
                    <button
                        class="flex flex-col items-center w-24 p-2 hover:bg-white hover:shadow-md hover:text-blue-600 rounded-xl transition-all group">
                        <span class="text-3xl mb-1 group-hover:scale-110 transition-transform">📊</span>
                        <span class="text-[11px] font-bold text-gray-700">Cuentas</span>
                    </button>

                    <button onclick="loadView('terceros')"
                        class="flex flex-col items-center w-24 p-2 hover:bg-white hover:shadow-md hover:text-blue-600 rounded-xl transition-all group">
                        <span class="text-3xl mb-1 group-hover:scale-110 transition-transform">👥</span>
                        <span class="text-[11px] font-bold text-gray-700">Terceros</span>
                    </button>

                    <div class="flex flex-col gap-2 w-48 ml-3 border-l border-gray-200 pl-3">
                        <button
                            class="flex items-center gap-2 px-3 py-1.5 text-[11px] font-semibold text-gray-600 hover:bg-white hover:shadow-sm rounded-lg transition-all border border-transparent hover:border-gray-100">
                            <span>📈</span>
                            <span>Centros de Costo</span>
                        </button>

                        <div class="relative w-full">
                            <button onclick="toggleMenu('menuTablas')"
                                class="flex justify-between items-center px-3 py-1.5 text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200 rounded-lg w-full hover:bg-amber-100 transition-colors">
                                <span class="flex items-center gap-2">📁 Tablas Grales.</span>
                                <span class="text-[8px]">▼</span>
                            </button>

                            <div id="menuTablas"
                                class="hidden absolute left-0 mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden ring-4 ring-black/5">
                                <button
                                    class="block w-full text-left px-4 py-2.5 hover:bg-blue-50 text-xs font-medium text-gray-700 transition-colors">Clientes</button>
                                <button
                                    class="block w-full text-left px-4 py-2.5 hover:bg-blue-50 text-xs font-medium text-gray-700 transition-colors border-t border-gray-50">Empleados</button>
                                <button
                                    class="block w-full text-left px-4 py-2.5 hover:bg-blue-50 text-xs font-medium text-gray-700 transition-colors border-t border-gray-50">Productos</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="mt-4 pt-2 border-t border-gray-200 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                    Contabilidad
                </div>
            </div>

            <div
                class="inline-flex flex-col justify-between border border-gray-200 px-4 py-4 rounded-xl bg-gray-50/50 shadow-sm min-h-[150px]">

                <div class="flex items-start">
                    <button onclick="loadView('productos')"
                        class="flex flex-col items-center w-24 p-2 hover:bg-white hover:shadow-md hover:text-blue-600 rounded-xl transition-all group">
                        <span class="text-3xl mb-1 group-hover:scale-110 transition-transform">📦</span>
                        <span class="text-[11px] font-bold text-gray-700">Productos</span>
                    </button>

                    <button onclick="loadView('bodegas')"
                        class="flex flex-col items-center w-24 p-2 hover:bg-white hover:shadow-md hover:text-blue-600 rounded-xl transition-all group">
                        <span class="text-3xl mb-1 group-hover:scale-110 transition-transform">🏬</span>
                        <span class="text-[11px] font-bold text-gray-700">Bodegas</span>
                    </button>

                    <button
                        class="flex flex-col items-center w-24 p-2 hover:bg-white hover:shadow-md hover:text-blue-600 rounded-xl transition-all group">
                        <span class="text-3xl mb-1 group-hover:scale-110 transition-transform">💲</span>
                        <span class="text-[11px] font-bold text-gray-700">Facturación</span>
                    </button>

                    <div class="flex flex-col gap-2 w-48 ml-3 border-l border-gray-200 pl-3">
                        <div class="relative w-full">
                            <button onclick="toggleMenu('menuList')"
                                class="flex justify-between items-center px-3 py-1.5 text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200 rounded-lg w-full hover:bg-amber-100 transition-colors">
                                <span class="flex items-center gap-2">📁 Listas</span>
                                <span class="text-[8px]">▼</span>
                            </button>
                            <div id="menuList"
                                class="hidden absolute left-0 mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden ring-4 ring-black/5">
                                <button
                                    class="block w-full text-left px-4 py-2.5 hover:bg-blue-50 text-xs font-medium text-gray-700 transition-colors">Categorías</button>
                                <button
                                    class="block w-full text-left px-4 py-2.5 hover:bg-blue-50 text-xs font-medium text-gray-700 transition-colors border-t border-gray-50">Líneas</button>
                            </div>
                        </div>

                        <div class="relative w-full">
                            <button onclick="toggleMenu('menuParametros')"
                                class="flex justify-between items-center px-3 py-1.5 text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200 rounded-lg w-full hover:bg-amber-100 transition-colors">
                                <span class="flex items-center gap-2">⚙️ Parámetros</span>
                                <span class="text-[8px]">▼</span>
                            </button>
                            <div id="menuParametros"
                                class="hidden absolute left-0 mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-xl z-50 overflow-hidden ring-4 ring-black/5">
                                <button
                                    class="block w-full text-left px-4 py-2.5 hover:bg-blue-50 text-xs font-medium text-gray-700 transition-colors">Tallas</button>
                                <button
                                    class="block w-full text-left px-4 py-2.5 hover:bg-blue-50 text-xs font-medium text-gray-700 transition-colors border-t border-gray-50">Colores</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div
                    class="mt-4 pt-2 border-t border-gray-200 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                    Facturación e Inventario
                </div>
            </div>

            <div
                class="inline-flex flex-col justify-between border border-gray-200 px-4 py-4 rounded-xl bg-gray-50/50 shadow-sm min-h-[150px]">
                <div class="flex">
                    <button onclick="loadView('ajustes')"
                        class="flex flex-col items-center w-24 p-2 hover:bg-white hover:shadow-md hover:text-blue-600 rounded-xl transition-all group">
                        <span class="text-3xl mb-1 group-hover:scale-110 transition-transform">📊</span>
                        <span class="text-[11px] font-bold text-gray-700">Ajustes</span>
                    </button>

                    <button onclick="loadView('existencias')"
                        class="flex flex-col items-center w-24 p-2 hover:bg-white hover:shadow-md hover:text-blue-600 rounded-xl transition-all group">
                        <span class="text-3xl mb-1 group-hover:scale-110 transition-transform">💳</span>
                        <span class="text-[11px] font-bold text-gray-700">Existencia</span>
                    </button>
                </div>
                <div
                    class="mt-4 pt-2 border-t border-gray-200 text-center text-[10px] font-black text-gray-400 uppercase tracking-widest">
                    Tablas Comunes
                </div>
            </div>

        </div>
    </div>

    <div id="main-content" class="p-6 transition-all duration-300">
    </div>
    <div id="notificaciones" class="fixed top-4 right-4 z-[9999] flex flex-col gap-2 pointer-events-none"></div>
</body>

@vite('resources/js/app.js')
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

</html>
