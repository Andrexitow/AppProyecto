<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RequireRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $rol = $request->user()?->rol?->nombre;

        if (!$rol || !in_array($rol, $roles, true)) {
            abort(403, 'No tienes acceso a este módulo.');
        }

        return $next($request);
    }
}
