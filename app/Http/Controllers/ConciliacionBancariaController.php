<?php

namespace App\Http\Controllers;

use App\Models\CuentaTesoreria;
use App\Models\MovimientoExtractoBancario;
use App\Services\ConciliacionBancariaService;
use Illuminate\Http\Request;

class ConciliacionBancariaController extends Controller
{
    public function __construct(private ConciliacionBancariaService $conciliacion)
    {
    }

    public function resumen(Request $request)
    {
        $datos = $request->validate([
            'cuenta_tesoreria_id' => ['required', 'exists:cuentas_tesoreria,id'],
            'desde' => ['required', 'date'],
            'hasta' => ['required', 'date', 'after_or_equal:desde'],
        ]);

        $cuenta = CuentaTesoreria::findOrFail($datos['cuenta_tesoreria_id']);

        return response()->json(
            $this->conciliacion->resumen($cuenta, $datos['desde'], $datos['hasta']) + [
                'movimientos_sistema' => $this->conciliacion->movimientosSistema($cuenta, $datos['desde'], $datos['hasta']),
                'lineas_extracto' => $this->conciliacion->lineasExtracto($cuenta, $datos['desde'], $datos['hasta']),
                'sugerencias' => $this->conciliacion->sugerirCoincidencias($cuenta, $datos['desde'], $datos['hasta']),
            ]
        );
    }

    public function storeLinea(Request $request)
    {
        $datos = $request->validate([
            'cuenta_tesoreria_id' => ['required', 'exists:cuentas_tesoreria,id'],
            'fecha' => ['required', 'date'],
            'descripcion' => ['required', 'string', 'max:255'],
            'valor' => ['required', 'numeric', 'not_in:0'],
        ]);

        $linea = $this->conciliacion->agregarLinea($datos, $request->user()->id);

        return response()->json(['success' => true, 'data' => $linea], 201);
    }

    public function destroyLinea(MovimientoExtractoBancario $linea)
    {
        $this->conciliacion->eliminarLinea($linea);

        return response()->json(['success' => true, 'message' => 'Línea del extracto eliminada.']);
    }

    public function conciliar(Request $request, MovimientoExtractoBancario $linea)
    {
        $datos = $request->validate(['movimiento_contable_id' => ['required', 'exists:movimientos_contables,id']]);
        $this->conciliacion->conciliar($linea, $datos['movimiento_contable_id']);

        return response()->json(['success' => true, 'message' => 'Partida conciliada.']);
    }

    public function desconciliar(MovimientoExtractoBancario $linea)
    {
        $this->conciliacion->desconciliar($linea);

        return response()->json(['success' => true, 'message' => 'Conciliación revertida.']);
    }
}
