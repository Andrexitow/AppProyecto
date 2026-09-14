<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Services\CompraContableService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Vista dedicada de Cuentas por Pagar (proveedores) — el equivalente de
 * Cuentas por Cobrar, del lado de las compras a crédito. Las compras ya se
 * pagan/abonan desde el módulo de Compras; esto agrega la vista consolidada
 * de antigüedad de saldos que una contadora necesita para planear pagos.
 */
class CuentaPorPagarController extends Controller
{
    public function __construct(private CompraContableService $contable)
    {
    }

    public function index()
    {
        return view('cuentas-por-pagar.index');
    }

    public function data(Request $request)
    {
        $compras = Compra::query()
            ->with(['proveedor:id,razon_social,nombre,apellido,cedula,nit', 'usuario:id,name'])
            ->where('estado', 'confirmada')
            ->where('saldo_pendiente', '>', 0)
            ->when($request->filled('estado_pago'), fn ($q) => $q->where('estado_pago', $request->string('estado_pago')))
            ->when($request->filled('buscar'), function ($q) use ($request) {
                $buscar = trim((string) $request->string('buscar'));
                $q->where(function ($sub) use ($buscar) {
                    $sub->where('numero_factura', 'like', "%{$buscar}%")
                        ->orWhereHas('proveedor', function ($c) use ($buscar) {
                            $c->where('razon_social', 'like', "%{$buscar}%")
                                ->orWhere('nombre', 'like', "%{$buscar}%")
                                ->orWhere('apellido', 'like', "%{$buscar}%")
                                ->orWhere('cedula', 'like', "%{$buscar}%")
                                ->orWhere('nit', 'like', "%{$buscar}%");
                        });
                });
            })
            ->latest('id')
            ->paginate(30);

        return response()->json($compras);
    }

    /** Resumen con antigüedad de saldos (0-30 / 31-60 / 61-90 / +90 días), clave para planear pagos. */
    public function resumen()
    {
        $base = Compra::where('estado', 'confirmada')->where('saldo_pendiente', '>', 0);

        $vencidas = fn (int $desde, ?int $hasta) => (clone $base)
            ->where('fecha', '<=', now()->subDays($desde)->toDateString())
            ->when($hasta, fn ($q) => $q->where('fecha', '>', now()->subDays($hasta)->toDateString()))
            ->sum('saldo_pendiente');

        return response()->json([
            'total_por_pagar' => (float) (clone $base)->sum('saldo_pendiente'),
            'compras_pendientes' => (clone $base)->count(),
            'proveedores_con_deuda' => (clone $base)->distinct('proveedor_id')->count('proveedor_id'),
            'antiguedad' => [
                'al_dia' => (float) (clone $base)->where('fecha', '>', now()->subDays(30)->toDateString())->sum('saldo_pendiente'),
                'dias_30_60' => (float) $vencidas(30, 60),
                'dias_60_90' => (float) $vencidas(60, 90),
                'mas_90' => (float) $vencidas(90, null),
            ],
        ]);
    }

    public function show(Compra $compra)
    {
        abort_unless($compra->saldo_pendiente > 0, 404);

        return response()->json($compra->load(['proveedor', 'detalles.producto', 'pagosProveedor.pago.metodoPago']));
    }

    public function registrarAbono(Request $request, Compra $compra)
    {
        $datos = $request->validate([
            'fecha' => ['required', 'date'],
            'valor' => ['required', 'numeric', 'gt:0'],
            'metodo_pago_contable_id' => ['required', 'exists:metodos_pago_contables,id'],
            'referencia' => ['nullable', 'string', 'max:120'],
        ]);

        return DB::transaction(function () use ($compra, $datos, $request) {
            $pago = $this->contable->registrarAbono($compra, $datos, $request->user()->id);

            return response()->json(['success' => true, 'data' => $pago]);
        });
    }
}
