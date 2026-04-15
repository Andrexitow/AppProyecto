<!DOCTYPE html>
<html lang="es" class="dark">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="icon" href="{{ asset('imgs/Logo.png') }}" type="image/x-icon">
    <title> {{ $title ?? 'example' }} </title>
</head>

<body class="flex items-center justify-center min-h-screen bg-gray-100 dark:bg-gray-900">
    <main class="w-full max-w-md p-8 space-y-6 bg-white rounded-lg shadow-md dark:bg-gray-800">
        <div class="text-center">
            <img src="{{ asset('imgs/Simbolo.png') }}" alt="Logo de AppSystem" class="w-34 h-24 mx-auto mb-4">

            <h1 class="text-2xl font-bold text-gray-800 dark:text-white">Bienvenido a <span><img
                        src="{{ asset('imgs/LogoLetras.png') }}" alt="Logo de AppSystem" class="mb-2"></span> </h1>
            @if ($errors->any())
                <div
                    class="p-4 mb-4 text-sm rounded-lg 
        bg-red-100 text-red-700 dark:bg-red-900 dark:text-red-200">
                    <ul class="list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <p class="text-sm text-gray-600 dark:text-gray-400">Inicia sesión para continuar</p>
        </div>
        <form action="{{ url('/login') }}" method="POST" class="space-y-6">
            @csrf
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Correo
                    electrónico</label>
                <input type="email" name="email" id="email" required
                    class="block w-full px-4 py-2 mt-1 text-gray-900 bg-gray-100 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600">
            </div>
            <div>
                <label for="password"
                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Contraseña</label>
                <input type="password" name="password" id="password" required
                    class="block w-full px-4 py-2 mt-1 text-gray-900 bg-gray-100 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white dark:border-gray-600">
            </div>
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <input type="checkbox" id="remember" name="remember"
                        class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:bg-gray-700 dark:border-gray-600">
                    <label for="remember" class="ml-2 text-sm text-gray-600 dark:text-gray-300">Recuérdame</label>
                </div>
                {{-- <a href="#" class="text-sm text-blue-600 hover:underline dark:text-blue-400">¿Olvidaste tu
                    contraseña?</a> --}}
            </div>
            <button type="submit"
                class="w-full px-4 py-2 text-white bg-blue-600 rounded-lg hover:bg-blue-700 focus:ring-4 focus:ring-blue-300 dark:bg-blue-500 dark:hover:bg-blue-600 focus:outline-none dark:focus:ring-blue-700">Iniciar
                sesión</button>
        </form>
    </main>
</body>

</html>
