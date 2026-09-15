<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
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
        ]);
        $middleware->validateCsrfTokens(except: [
            'agente/*',
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
