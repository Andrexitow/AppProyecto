<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Cierra la sesión automáticamente por dos motivos distintos — ver
 * config/nexora.php para el detalle de cada uno:
 *
 *  - Inactividad: no hizo ninguna petición en X minutos.
 *  - Duración máxima: lleva más de X horas con sesión abierta, sin
 *    importar si sigue activo (útil para turnos de Mesero/Cajero/Cocina,
 *    ya que la pantalla de facturación se autorefresca sola y nunca
 *    "parece" inactiva por sí misma).
 *
 * Ninguno de los dos toca la contraseña ni desactiva la cuenta — solo
 * termina la sesión actual. Si el turno sigue, se vuelve a iniciar sesión
 * normal.
 */
class CerrarSesionPorInactividad
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $usuario = Auth::user();
            $esOperativo = in_array($usuario->rol->nombre ?? null, ['Mesero', 'Cajero', 'Cocina'], true);

            $limiteInactividadMinutos = $esOperativo
                ? (int) config('nexora.inactividad_operativos_minutos')
                : (int) config('nexora.inactividad_admin_minutos');

            $limiteSesionHoras = $esOperativo
                ? (float) config('nexora.sesion_maxima_operativos_horas')
                : (float) config('nexora.sesion_maxima_admin_horas');

            $ultimaActividad = $request->session()->get('ultima_actividad');
            $inicioSesion = $request->session()->get('inicio_sesion');

            // abs(): en Carbon 3, diffInMinutes()/diffInHours() ya no
            // devuelven siempre un valor absoluto por defecto — con una
            // fecha pasada daban negativo y la comparación ">=" nunca se
            // cumplía.
            $porInactividad = $ultimaActividad
                && abs(now()->diffInMinutes($ultimaActividad)) >= $limiteInactividadMinutos;

            $porDuracionMaxima = $limiteSesionHoras > 0
                && $inicioSesion
                && abs(now()->diffInHours($inicioSesion)) >= $limiteSesionHoras;

            if ($porInactividad || $porDuracionMaxima) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                $mensaje = $porInactividad
                    ? 'Tu sesión se cerró por inactividad. Vuelve a iniciar sesión.'
                    : 'Tu sesión alcanzó la duración máxima permitida. Vuelve a iniciar sesión.';

                // La SPA hace fetch() a /views/*, /mesas/actualizar, etc. cada
                // pocos segundos para refrescar contenido dentro de un div —
                // no navegaciones de página completa. Si a esas peticiones
                // les devolvemos el HTML del login (una redirección normal),
                // ese HTML termina insertado DENTRO del panel de contenido en
                // vez de reemplazar la pantalla. Con 401 JSON, el propio
                // fetch puede detectar la sesión vencida sin quedar con un
                // login incrustado a la fuerza.
                if ($request->expectsJson()) {
                    return response()->json(['message' => $mensaje], 401);
                }

                return redirect()->route('login')->withErrors(['username' => $mensaje]);
            }

            $request->session()->put('ultima_actividad', now());

            // Se guarda solo una vez, al primer request de la sesión — a
            // diferencia de "ultima_actividad", este valor NO se refresca
            // en cada petición, porque necesita seguir marcando cuándo
            // empezó la sesión, no cuándo fue la última vez que se usó.
            if (!$inicioSesion) {
                $request->session()->put('inicio_sesion', now());
            }
        }

        return $next($request);
    }
}
