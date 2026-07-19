<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AutenticarAgenteImpresion
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken();
        $tokenValido = config('app.agente_impresion_token', env('AGENTE_IMPRESION_TOKEN'));

        if (!$token || !$tokenValido || !hash_equals($tokenValido, $token)) {
            return response()->json(['message' => 'No autorizado'], 401);
        }

        return $next($request);
    }
}