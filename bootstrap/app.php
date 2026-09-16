<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

$app = Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->alias([
            'auth.agente' => \App\Http\Middleware\AutenticarAgenteImpresion::class,
            'auditar' => \App\Http\Middleware\RegistrarActividad::class,
            'permission' => \App\Http\Middleware\RequirePermission::class,
            'role' => \App\Http\Middleware\RequireRole::class,
            'plan' => \App\Http\Middleware\EnsurePlanPro::class,
            'sesion.inactividad' => \App\Http\Middleware\CerrarSesionPorInactividad::class,
        ]);
        $middleware->validateCsrfTokens(except: [
            'agente/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();

// En este servidor (Hostinger) la carpeta se llama "public_html" en vez
// de "public" — Laravel busca "public" por defecto en public_path(), así
// que sin esto cualquier cosa que lea el archivo real en vez de solo
// generar su URL (filemtime() para el cache-busting de assets, storage:link,
// el manifest de Vite) apunta a una carpeta que no existe en este server.
// En local sigue existiendo "public", así que ahí no se activa.
if (! is_dir($app->basePath('public')) && is_dir($app->basePath('public_html'))) {
    $app->usePublicPath($app->basePath('public_html'));
}

return $app;
