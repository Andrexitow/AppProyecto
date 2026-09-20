<?php

namespace App\Http\Controllers;

use App\Models\ComprobanteContable;
use App\Models\Compra;
use App\Models\CuentaTesoreria;
use App\Models\Factura;
use App\Models\PeriodoContable;
use App\Services\PeriodoContableService;
use App\Services\ReporteContableService;
use App\Services\TesoreriaService;

/**
 * Panel de inicio para el rol Contabilidad — antes caía en el mismo
 * Dashboard de ventas/cocina que Administrador, que no le sirve de nada a
 * quien hace la parte contable del negocio. Reutiliza los mismos cálculos
 * que ya existen en Cuentas por Cobrar/Pagar, Tesorería e Informes
 * Contables en vez de duplicarlos — ver cada método de datos() para la
 * fuente exacta.
 */
class DashboardContableController extends Controller
{
    public function __construct(
        private TesoreriaService $tesoreria,
        private ReporteContableService $reportes,
        private PeriodoContableService $periodos,
    ) {
    }

    public function index()
    {
        return view('home-contable', $this->datos());
    }

    public function resumen()
    {
        return response()
            ->json($this->datos())
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    private function datos(): array
    {
        $inicioMes = now()->startOfMonth()->toDateString();
        $hoy = now()->toDateString();

        $baseCartera = Factura::where('metodo_pago', 'credito');
        $basePorPagar = Compra::where('estado', 'confirmada')->where('saldo_pendiente', '>', 0);

        $saldoTesoreria = CuentaTesoreria::where('activa', true)->get()
            ->sum(fn (CuentaTesoreria $cuenta) => $this->tesoreria->saldo($cuenta));

        $periodoActual = PeriodoContable::whereDate('fecha_inicio', '<=', $hoy)
            ->whereDate('fecha_fin', '>=', $hoy)
            ->latest('id')
            ->first();

        $iva = $this->reportes->ivaPeriodo($inicioMes, $hoy);

        $ultimosComprobantes = ComprobanteContable::query()
            ->with(['tipoDocumento:id,nombre', 'usuario:id,name'])
            ->orderByDesc('id')
            ->limit(5)
            ->get()
            ->map(fn (ComprobanteContable $c) => [
                'numero' => $c->numero,
                'tipo' => $c->tipoDocumento?->nombre ?? $c->documento_origen,
                'estado' => $c->estado,
                'fecha' => optional($c->fecha)->format('d/m/Y'),
                'usuario' => $c->usuario?->name,
            ]);

        return [
            'cartera' => [
                'total' => (float) (clone $baseCartera)->sum('saldo_pendiente'),
                'facturas_pendientes' => (clone $baseCartera)->where('estado_pago', '!=', 'pagada')->count(),
                'vencida_30_dias' => (float) (clone $baseCartera)->where('estado_pago', '!=', 'pagada')->where('created_at', '<', now()->subDays(30))->sum('saldo_pendiente'),
            ],
            'por_pagar' => [
                'total' => (float) (clone $basePorPagar)->sum('saldo_pendiente'),
                'compras_pendientes' => (clone $basePorPagar)->count(),
                'vencida_90_dias' => (float) (clone $basePorPagar)->where('fecha', '<=', now()->subDays(90)->toDateString())->sum('saldo_pendiente'),
            ],
            'tesoreria' => [
                'saldo_total' => round($saldoTesoreria, 2),
            ],
            'periodo' => [
                'nombre' => $periodoActual?->nombre,
                'cerrado' => $this->periodos->estaCerrado($hoy),
            ],
            'comprobantes' => [
                'registrados_hoy' => ComprobanteContable::whereDate('fecha', $hoy)->where('estado', '!=', 'ANULADO')->count(),
                'borrador' => ComprobanteContable::where('estado', 'BORRADOR')->count(),
                'ultimos' => $ultimosComprobantes,
            ],
            'iva' => $iva,
            'actualizado_en' => now()->toIso8601String(),
        ];
    }
}
