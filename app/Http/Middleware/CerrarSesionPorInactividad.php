<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Cierra la sesión si el usuario no hizo ninguna petición en más de X
 * minutos (config/nexora.php: inactividad_operativos_minutos /
 * inactividad_admin_minutos). No depende de SESSION_LIFETIME porque ese
 * valor solo controla cuándo Laravel purga la sesión del storage, no
 * cuándo debe forzarse el logout con aviso al usuario.
 */
class CerrarSesionPorInactividad
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $usuario = Auth::user();
            $rolNombre = $usuario->rol->nombre ?? null;

            $limiteMinutos = in_array($rolNombre, ['Mesero', 'Cajero', 'Cocina'], true)
                ? (int) config('nexora.inactividad_operativos_minutos')
                : (int) config('nexora.inactividad_admin_minutos');

            $ultimaActividad = $request->session()->get('ultima_actividad');

            // abs(): en Carbon 3, diffInMinutes() ya no devuelve siempre un
            // valor absoluto por defecto — con una fecha pasada devolvía
            // negativo y la comparación ">=" nunca se cumplía.
            if ($ultimaActividad && abs(now()->diffInMinutes($ultimaActividad)) >= $limiteMinutos) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->withErrors([
                    'username' => 'Tu sesión se cerró por inactividad. Vuelve a iniciar sesión.',
                ]);
            }

            $request->session()->put('ultima_actividad', now());
        }

        return $next($request);
    }
}
