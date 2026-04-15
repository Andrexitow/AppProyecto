<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AppSystem</title>
    {{-- <script src="https://cdn.tailwindcss.com"></script> --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-100">

    <!-- HEADER -->
    <div class="bg-white border-b shadow-sm">
        <div class="flex items-center justify-between px-6 py-2">

            <div class="flex items-center space-x-6">
                <div class="flex items-center space-x-2">
                    <img src="{{ asset('imgs/LogoSystem.png') }}" class="w-10 h-10">
                    <span class="font-bold text-gray-800 text-lg">AppSystem</span>
                </div>

                <button onclick="toggleTab('archivo')" class="font-semibold text-gray-700 hover:text-blue-600">
                    Archivo
                </button>
            </div>

            <form method="POST" action="/logout">
                @csrf
                <button type="submit" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm">
                    Cerrar sesión
                </button>
            </form>

        </div>
    </div>

    <!-- RIBBON -->
    <div id="ribbon" class="bg-gray-50 border-b p-3 hidden">

        <div id="archivo" class="tab-content hidden flex gap-3 flex-wrap">

            <!-- ================= CONTABILIDAD ================= -->
            <div
                class="inline-flex flex-col justify-between border px-3 py-3 rounded-lg bg-white shadow-sm min-h-[140px]">

                <div class="flex">

                    <button class="flex flex-col items-center w-24 p-2 hover:bg-gray-100 rounded">
                        <span class="text-2xl">📊</span>
                        <span class="text-xs">Cuentas</span>
                    </button>

                    <button class="flex flex-col items-center w-24 p-2 hover:bg-gray-100 rounded">
                        <span class="text-2xl">👥</span>
                        <span class="text-xs">Terceros</span>
                    </button>

                    <div class="flex flex-col gap-1 w-44 ml-2">

                        <button class="flex items-center gap-2 px-2 py-1 text-xs hover:bg-gray-100 rounded w-full">
                            <span>📊</span>
                            <span>Centros de Costo</span>
                        </button>

                        <div class="relative w-full">
                            <button onclick="toggleMenu('menuTablas')"
                                class="flex justify-between px-2 py-1 text-xs bg-yellow-100 border border-yellow-300 rounded w-full hover:bg-yellow-200">
                                <span>📁 Tablas Generales</span>
                                <span>▼</span>
                            </button>

                            <div id="menuTablas"
                                class="hidden absolute left-0 mt-1 w-full bg-white border rounded shadow z-50">
                                <button
                                    class="block w-full text-left px-3 py-2 hover:bg-gray-100 text-sm">Clientes</button>
                                <button
                                    class="block w-full text-left px-3 py-2 hover:bg-gray-100 text-sm">Empleados</button>
                                <button
                                    class="block w-full text-left px-3 py-2 hover:bg-gray-100 text-sm">Productos</button>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="mt-3 pt-2 border-t text-center text-xs text-gray-500">
                    Contabilidad
                </div>

            </div>

            <!-- ================= FACTURACIÓN ================= -->
            <div
                class="inline-flex flex-col justify-between border px-3 py-3 rounded-lg bg-white shadow-sm min-h-[140px]">

                <div class="flex">

                    <button onclick="loadView('productos')"
                        class="flex flex-col items-center w-24 p-2 hover:bg-gray-100 rounded">
                        <span class="text-2xl">📦</span>
                        <span class="text-xs">Productos</span>
                    </button>

                    <button onclick="loadView('bodegas')"
                        class="flex flex-col items-center w-24 p-2 hover:bg-gray-100 rounded">
                        <span class="text-2xl">🏬</span>
                        <span class="text-xs">Bodegas</span>
                    </button>

                    <button class="flex flex-col items-center w-24 p-2 hover:bg-gray-100 rounded">
                        <span class="text-2xl">💲</span>
                        <span class="text-xs">Facturación</span>
                    </button>

                    <div class="border-l mx-2"></div>

                    <div class="flex flex-col gap-1 w-44">

                        <div class="relative w-full">
                            <button onclick="toggleMenu('menuList')"
                                class="flex justify-between px-2 py-1 text-xs bg-yellow-100 border border-yellow-300 rounded w-full hover:bg-yellow-200">
                                <span>📁 Listas</span>
                                <span>▼</span>
                            </button>

                            <div id="menuList"
                                class="hidden absolute left-0 mt-1 w-full bg-white border rounded shadow z-50">
                                <button class="block px-3 py-2 hover:bg-gray-100 text-sm">Categorías</button>
                                <button class="block px-3 py-2 hover:bg-gray-100 text-sm">Líneas</button>
                                <button class="block px-3 py-2 hover:bg-gray-100 text-sm">Grupos</button>
                                <button class="block px-3 py-2 hover:bg-gray-100 text-sm">Lista de precios</button>
                                <button class="block px-3 py-2 hover:bg-gray-100 text-sm">Cajas registradoras</button>
                            </div>
                        </div>

                        <div class="relative w-full">
                            <button onclick="toggleMenu('menuParametros')"
                                class="flex justify-between px-2 py-1 text-xs bg-yellow-100 border border-yellow-300 rounded w-full hover:bg-yellow-200">
                                <span>⚙️ Parametrización</span>
                                <span>▼</span>
                            </button>

                            <div id="menuParametros"
                                class="hidden absolute left-0 mt-1 w-full bg-white border rounded shadow z-50">
                                <button class="block px-3 py-2 hover:bg-gray-100 text-sm">Tallas</button>
                                <button class="block px-3 py-2 hover:bg-gray-100 text-sm">Colores</button>
                                <button class="block px-3 py-2 hover:bg-gray-100 text-sm">Acompañamientos</button>
                            </div>
                        </div>

                    </div>

                </div>

                <div class="mt-3 pt-2 border-t text-center text-xs text-gray-500">
                    Facturación e Inventario
                </div>

            </div>

            <!-- ================= TABLAS COMUNES ================= -->
            <div
                class="inline-flex flex-col justify-between border px-3 py-3 rounded-lg bg-white shadow-sm min-h-[140px]">

                <div class="flex">

                    <button onclick="loadView('ajustes')"
                        class="flex flex-col items-center w-24 p-2 hover:bg-gray-100 rounded">
                        <span class="text-2xl">📊</span>
                        <span class="text-xs">Ajustes</span>
                    </button>

                    <button class="flex flex-col items-center w-24 p-2 hover:bg-gray-100 rounded">
                        <span class="text-2xl">💳</span>
                        <span class="text-xs">Formas de pago</span>
                    </button>

                </div>

                <div class="mt-3 pt-2 border-t text-center text-xs text-gray-500">
                    Tablas Comunes
                </div>

            </div>

        </div>

    </div>

    <!-- CONTENIDO -->
    <div id="main-content" class="p-6">
        <!-- Aquí se cargan las vistas -->
    </div>
    {{-- <script src="{{ asset('js/app.js') }}"></script> --}}
    @vite('resources/js/app.js')
</body>

</html>
