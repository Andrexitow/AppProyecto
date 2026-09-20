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
        // Toda petición AJAX/JSON que falle responde con un motivo legible
        // (nunca un "Server Error" pelado). Ver App\Support\MensajeError.
        $exceptions->render(function (\Throwable $e, \Illuminate\Http\Request $request) {
            return \App\Support\MensajeError::responder($e, $request);
        });
    })
    ->create();

// Solo en Hostinger: ahi la carpeta publica vive fuera del proyecto. En local
// esa ruta no existe y se usa el "public" normal.
$publicHostinger = '/home/u113350287/domains/papayawhip-porpoise-285252.hostingersite.com/public_html';
if (is_dir($publicHostinger)) {
    $app->usePublicPath($publicHostinger);
}

return $app;