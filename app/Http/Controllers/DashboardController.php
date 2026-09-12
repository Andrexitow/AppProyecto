<?php

namespace App\Http\Controllers;

use App\Models\ConfiguracionSistema;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $dashboard = $this->datos();

        return view('home', [
            'estadisticas' => $dashboard['estadisticas'],
            'ultimasFacturas' => $dashboard['ultimas_facturas'],
            'horaCorteOperativo' => $dashboard['hora_corte_operativo'],
            'inicioOperativo' => $dashboard['inicio_operativo'],
        ]);
    }

    public function resumen()
    {
        return response()
            ->json($this->datos())
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    public function actualizarHoraCorte(Request $request)
    {
        $datos = $request->validate([
            'hora_corte_operativo' => ['required', 'date_format:H:i'],
        ]);

        ConfiguracionSistema::updateOrCreate(
            ['clave' => 'hora_corte_operativo'],
            ['valor' => $datos['hora_corte_operativo']]
        );

        $dashboard = $this->datos();

        return response()->json([
            'ok' => true,
            'message' => 'La hora de corte operativo fue actualizada.',
            'hora_corte_operativo' => $dashboard['hora_corte_operativo'],
            'inicio_operativo' => $dashboard['inicio_operativo'],
        ]);
    }

    private function datos(): array
    {
        $horaCorteOperativo = ConfiguracionSistema::where('clave', 'hora_corte_operativo')
            ->value('valor') ?? '00:00';
        $ahora = now();
        [$hora, $minuto] = array_map('intval', explode(':', $horaCorteOperativo));
        $inicioOperativo = $ahora->copy()->setTime($hora, $minuto, 0);

        if ($ahora->lt($inicioOperativo)) {
            $inicioOperativo->subDay();
        }

        $finOperativo = $inicioOperativo->copy()->addDay();
        $mes = now()->startOfMonth();

        $estadisticas = [
            'ventas_hoy' => (float) DB::table('facturas')
                ->where('estado', 'pagada')
                ->where('created_at', '>=', $inicioOperativo)
                ->where('created_at', '<', $finOperativo)
                ->sum('total'),
            'facturas_hoy' => DB::table('facturas')
                ->where('estado', 'pagada')
                ->where('created_at', '>=', $inicioOperativo)
                ->where('created_at', '<', $finOperativo)
                ->count(),
            'ventas_mes' => (float) DB::table('facturas')->where('estado', 'pagada')->where('created_at', '>=', $mes)->sum('total'),
            'pedidos_activos' => DB::table('pedidos')->where('estado', 'pendiente')->count(),
            // Debe coincidir con la cola de Cocina: las comandas de barra no son pedidos de cocina.
            'comandas_pendientes' => DB::table('comandas_pendientes')
                ->join('impresoras', 'impresoras.id', '=', 'comandas_pendientes.impresora_id')
                ->where('comandas_pendientes.tipo', 'comanda')
                ->whereIn('comandas_pendientes.estado', ['pendiente', 'impreso'])
                ->whereRaw('LOWER(impresoras.nombre) LIKE ?', ['%cocina%'])
                ->count(),
            'comandas_barra' => DB::table('comandas_pendientes')
                ->join('impresoras', 'impresoras.id', '=', 'comandas_pendientes.impresora_id')
                ->where('comandas_pendientes.tipo', 'comanda')
                ->whereIn('comandas_pendientes.estado', ['pendiente', 'impreso'])
                ->whereRaw('LOWER(impresoras.nombre) LIKE ?', ['%barra%'])
                ->count(),
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
            'hora_corte_operativo' => $horaCorteOperativo,
            'inicio_operativo' => $inicioOperativo->format('d/m/Y, h:i a'),
            'actualizado_en' => now()->toIso8601String(),
        ];
    }
}
