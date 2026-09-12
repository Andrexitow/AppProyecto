<?php

namespace App\Http\Middleware;

use App\Services\AuditoriaService;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RegistrarActividad
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Solo registra operaciones que realmente modificaron datos.
        if (!in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)
            || $response->getStatusCode() >= 400
            || $request->attributes->get('auditoria_detallada')
            || !$request->user()) {
            return $response;
        }

        [$modulo, $accion] = $this->clasificar($request);
        $parametros = collect($request->route()?->parameters() ?? [])
            ->map(function ($valor) {
                if (is_object($valor) && method_exists($valor, 'getKey')) {
                    return $valor->getKey();
                }

                return is_scalar($valor) ? $valor : null;
            })
            ->filter()
            ->values()
            ->implode(', ');

        AuditoriaService::registrar(
            $request->user(),
            $modulo,
            $accion,
            $accion . ' realizado en ' . $modulo . '.',
            $request,
            $parametros ?: null,
            ['ruta' => $request->route()?->uri()]
        );

        return $response;
    }

    private function clasificar(Request $request): array
    {
        $ruta = '/' . ltrim($request->route()?->uri() ?? $request->path(), '/');

        $modulo = match (true) {
            str_contains($ruta, 'facturas') => 'Facturas',
            str_contains($ruta, 'comprobantes') => 'Comprobantes',
            str_contains($ruta, 'compras') => 'Compras',
            str_contains($ruta, 'traslados-bodega') => 'Traslados entre bodegas',
            str_contains($ruta, 'productos') => 'Productos',
            str_contains($ruta, 'bodegas') => 'Bodegas',
            str_contains($ruta, 'usuarios'), str_contains($ruta, 'roles') => 'Usuarios y roles',
            str_contains($ruta, 'cajas') => 'Cajas',
            str_contains($ruta, 'ajustes') => 'Ajustes de inventario',
            str_contains($ruta, 'pedidos'), str_contains($ruta, 'mesas') => 'Pedidos',
            str_contains($ruta, 'cocina') => 'Cocina',
            str_contains($ruta, 'impresoras') => 'Impresoras',
            str_contains($ruta, 'kardex') => 'Kardex',
            str_contains($ruta, 'tesoreria') => 'Tesorería',
            str_contains($ruta, 'cuentas-por-cobrar') => 'Cuentas por cobrar',
            str_contains($ruta, 'periodos-contables') => 'Períodos contables',
            str_contains($ruta, 'informes-contables') => 'Informes contables',
            str_contains($ruta, 'cuentas-contables') => 'Plan de cuentas',
            str_contains($ruta, 'metodos-pago-contables') => 'Medios de pago',
            default => 'Sistema',
        };

        $accion = match (true) {
            str_contains($ruta, 'anular') => 'Anulación',
            str_contains($ruta, 'revertir') => 'Reversión',
            str_contains($ruta, 'registrar') => 'Registro',
            $request->isMethod('DELETE') => 'Eliminación',
            in_array($request->method(), ['PUT', 'PATCH'], true) || str_contains($ruta, 'update') => 'Actualización',
            default => 'Creación',
        };

        return [$modulo, $accion];
    }
}
