<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Services\ClienteContableService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CuentaPorCobrarController extends Controller
{
    public function __construct(private ClienteContableService $contable)
    {
    }

    public function index()
    {
        return view('cuentas-por-cobrar.index');
    }

    public function data(Request $request)
    {
        $facturas = Factura::query()
            ->with(['cliente:id,razon_social,nombre,apellido,cedula,nit', 'user:id,name'])
            ->where('metodo_pago', 'credito')
            ->when($request->filled('estado_pago'), fn ($q) => $q->where('estado_pago', $request->string('estado_pago')))
            ->when($request->filled('buscar'), function ($q) use ($request) {
                $buscar = trim((string) $request->string('buscar'));
                $q->where(function ($sub) use ($buscar) {
                    $sub->where('numero_factura', 'like', "%{$buscar}%")
                        ->orWhereHas('cliente', function ($c) use ($buscar) {
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

        return response()->json($facturas);
    }

    public function resumen()
    {
        $base = Factura::where('metodo_pago', 'credito');

        return response()->json([
            'total_cartera' => (float) (clone $base)->sum('saldo_pendiente'),
            'facturas_pendientes' => (clone $base)->where('estado_pago', '!=', 'pagada')->count(),
            'clientes_con_deuda' => (clone $base)->where('saldo_pendiente', '>', 0)->distinct('cliente_id')->count('cliente_id'),
            'vencida_30_dias' => (float) (clone $base)->where('estado_pago', '!=', 'pagada')->where('created_at', '<', now()->subDays(30))->sum('saldo_pendiente'),
        ]);
    }

    public function show(Factura $factura)
    {
        abort_unless($factura->metodo_pago === 'credito', 404);

        return response()->json($factura->load(['cliente', 'detalles.producto', 'pagosCliente.pago.metodoPago']));
    }

    public function registrarAbono(Request $request, Factura $factura)
    {
        abort_unless($factura->metodo_pago === 'credito', 404);

        $datos = $request->validate([
            'fecha' => ['required', 'date'],
            'valor' => ['required', 'numeric', 'gt:0'],
            'metodo_pago_contable_id' => ['required', 'exists:metodos_pago_contables,id'],
            'referencia' => ['nullable', 'string', 'max:120'],
        ]);

        return DB::transaction(function () use ($factura, $datos, $request) {
            $pago = $this->contable->registrarAbono($factura, $datos, $request->user()->id);

            return response()->json(['success' => true, 'data' => $pago]);
        });
    }
}
