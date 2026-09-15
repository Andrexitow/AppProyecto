<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bloquea los módulos exclusivos del plan Pro (contabilidad, nómina,
 * activos fijos, tesorería, compras, cuentas por cobrar/pagar y
 * facturación electrónica DIAN) cuando la instalación está en plan Básico.
 *
 * A diferencia de RequireRole, esto no depende de quién sea el usuario:
 * es una propiedad de la INSTALACIÓN completa, definida en config('nexora.plan')
 * (variable NEXORA_PLAN del .env de cada cliente). Un Administrador en una
 * instalación Básica sigue sin poder entrar aquí hasta que el negocio
 * contrate Pro.
 */
class EnsurePlanPro
{
    public function handle(Request $request, Closure $next): Response
    {
        if (config('nexora.plan') === 'pro') {
            return $next($request);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'message' => 'Esta función requiere el plan Pro de Nexora.',
                'plan_requerido' => 'pro',
            ], 403);
        }

        // Se renderiza como fragmento (sin layout) para que encaje dentro del
        // contenedor de la SPA cuando la petición viene de loadView().
        return response(view('errors.plan-pro-requerido'), 403);
    }
}
