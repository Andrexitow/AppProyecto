<?php

namespace App\Http\Controllers;

use App\Models\CuentaTesoreria;
use App\Services\ReporteContableService;
use App\Services\TesoreriaService;
use Illuminate\Http\Request;

class TesoreriaController extends Controller
{
    public function __construct(private TesoreriaService $tesoreria, private ReporteContableService $reportes)
    {
    }

    public function index()
    {
        return view('tesoreria.index');
    }

    public function cuentas()
    {
        $cuentas = CuentaTesoreria::where('activa', true)->with('cuentaContable:id,codigo,nombre')->orderBy('tipo')->orderBy('nombre')->get();

        $data = $cuentas->map(function (CuentaTesoreria $cuenta) {
            return [
                'id' => $cuenta->id,
                'nombre' => $cuenta->nombre,
                'tipo' => $cuenta->tipo,
                'numero_cuenta' => $cuenta->numero_cuenta,
                'cuenta_contable' => $cuenta->cuentaContable?->codigo . ' - ' . $cuenta->cuentaContable?->nombre,
                'saldo' => $this->tesoreria->saldo($cuenta),
            ];
        });

        return response()->json(['data' => $data]);
    }

    public function storeCuenta(Request $request)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:120'],
            'tipo' => ['required', 'in:CAJA,BANCO'],
            'cuenta_contable_id' => ['required', 'exists:cuentas_contables,id'],
            'numero_cuenta' => ['nullable', 'string', 'max:60'],
        ]);

        $cuenta = CuentaTesoreria::create($datos + ['activa' => true]);

        return response()->json(['success' => true, 'data' => $cuenta], 201);
    }

    public function movimientos(Request $request)
    {
        $datos = $request->validate([
            'cuenta_tesoreria_id' => ['required', 'exists:cuentas_tesoreria,id'],
            'desde' => ['required', 'date'],
            'hasta' => ['required', 'date', 'after_or_equal:desde'],
        ]);

        $cuenta = CuentaTesoreria::findOrFail($datos['cuenta_tesoreria_id']);

        // El libro de la cuenta de tesorería es, literalmente, el Libro Mayor de
        // SU cuenta contable: así incluye TODO lo que la mueve (ventas, compras,
        // abonos, y también los ingresos/egresos/transferencias manuales de este
        // módulo), y el saldo final siempre coincide con el saldo real de la
        // cuenta — no solo con lo registrado a través de esta pantalla.
        $mayor = $this->reportes->libroMayor($datos['desde'], $datos['hasta'], $cuenta->cuenta_contable_id);

        return response()->json([
            'cuenta' => $cuenta,
            'saldo_inicial' => $mayor['saldo_inicial'],
            'movimientos' => $mayor['movimientos'],
            'saldo_final' => $mayor['saldo_final'],
        ]);
    }

    public function registrarIngreso(Request $request)
    {
        $datos = $request->validate([
            'cuenta_tesoreria_id' => ['required', 'exists:cuentas_tesoreria,id'],
            'cuenta_contrapartida_id' => ['required', 'exists:cuentas_contables,id'],
            'fecha' => ['required', 'date'],
            'valor' => ['required', 'numeric', 'gt:0'],
            'descripcion' => ['required', 'string', 'max:255'],
            'tercero_id' => ['nullable', 'exists:terceros,id'],
        ]);

        $movimiento = $this->tesoreria->registrarIngreso($datos, $request->user()->id);

        return response()->json(['success' => true, 'data' => $movimiento], 201);
    }

    public function registrarEgreso(Request $request)
    {
        $datos = $request->validate([
            'cuenta_tesoreria_id' => ['required', 'exists:cuentas_tesoreria,id'],
            'cuenta_contrapartida_id' => ['required', 'exists:cuentas_contables,id'],
            'fecha' => ['required', 'date'],
            'valor' => ['required', 'numeric', 'gt:0'],
            'descripcion' => ['required', 'string', 'max:255'],
            'tercero_id' => ['nullable', 'exists:terceros,id'],
        ]);

        $movimiento = $this->tesoreria->registrarEgreso($datos, $request->user()->id);

        return response()->json(['success' => true, 'data' => $movimiento], 201);
    }

    public function registrarTransferencia(Request $request)
    {
        $datos = $request->validate([
            'cuenta_origen_id' => ['required', 'exists:cuentas_tesoreria,id'],
            'cuenta_destino_id' => ['required', 'exists:cuentas_tesoreria,id', 'different:cuenta_origen_id'],
            'fecha' => ['required', 'date'],
            'valor' => ['required', 'numeric', 'gt:0'],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ]);

        [$salida, $entrada] = $this->tesoreria->registrarTransferencia($datos, $request->user()->id);

        return response()->json(['success' => true, 'data' => ['salida' => $salida, 'entrada' => $entrada]], 201);
    }
}
