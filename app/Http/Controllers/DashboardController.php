<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $dashboard = $this->datos();

        return view('home', [
            'estadisticas' => $dashboard['estadisticas'],
            'ultimasFacturas' => $dashboard['ultimas_facturas'],
        ]);
    }

    public function resumen()
    {
        return response()
            ->json($this->datos())
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    private function datos(): array
    {
        $hoy = now()->startOfDay();
        $mes = now()->startOfMonth();

        $estadisticas = [
            'ventas_hoy' => (float) DB::table('facturas')->where('estado', 'pagada')->where('created_at', '>=', $hoy)->sum('total'),
            'facturas_hoy' => DB::table('facturas')->where('estado', 'pagada')->where('created_at', '>=', $hoy)->count(),
            'ventas_mes' => (float) DB::table('facturas')->where('estado', 'pagada')->where('created_at', '>=', $mes)->sum('total'),
            'pedidos_activos' => DB::table('pedidos')->where('estado', 'pendiente')->count(),
            'comandas_pendientes' => DB::table('comandas_pendientes')->where('tipo', 'comanda')->whereIn('estado', ['pendiente', 'impreso'])->count(),
            'mesas_ocupadas' => DB::table('mesas')->whereIn('estado', ['ocupada', 'cuenta_pedida', 'seleccionada'])->count(),
        ];

        $ultimasFacturas = DB::table('facturas')
            ->leftJoin('mesas', 'mesas.id', '=', 'facturas.mesa_id')
            ->leftJoin('users', 'users.id', '=', 'facturas.user_id')
            ->select('facturas.numero_factura', 'facturas.total', 'facturas.estado', 'facturas.created_at', 'mesas.numero as mesa', 'users.name as usuario')
            ->orderByDesc('facturas.id')
            ->limit(5)
            ->get();

        return [
            'estadisticas' => $estadisticas,
            'ultimas_facturas' => $ultimasFacturas,
            'actualizado_en' => now()->toIso8601String(),
        ];
    }
}
