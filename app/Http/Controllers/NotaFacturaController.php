<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Services\FacturacionElectronicaService;
use App\Services\NotaFacturaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotaFacturaController extends Controller
{
    /** Notas ya emitidas sobre una factura, para mostrarlas en su detalle. */
    public function index(Factura $factura)
    {
        return response()->json(
            $factura->notas()->with('detalles.producto', 'usuario')->latest()->get()
        );
    }

    public function store(Request $request, Factura $factura, NotaFacturaService $notaFacturaService, FacturacionElectronicaService $facturacionElectronicaService)
    {
        $datos = $request->validate([
            'tipo' => 'required|in:credito,debito',
            'motivo' => 'required|string|min:5|max:500',
            'restaura_inventario' => 'boolean',
            'lineas' => 'required|array|min:1',
            'lineas.*.factura_detalle_id' => 'required|integer|exists:factura_detalles,id',
            'lineas.*.cantidad' => 'required|numeric|gt:0',
        ]);

        $nota = $notaFacturaService->emitir(
            $factura,
            $datos['tipo'],
            $datos['lineas'],
            $datos['motivo'],
            $datos['restaura_inventario'] ?? false,
            Auth::id()
        );

        // No hace nada mientras no haya proveedor configurado (ver
        // FacturacionElectronicaService).
        $facturacionElectronicaService->encolarNota($nota);

        return response()->json([
            'success' => true,
            'message' => ($datos['tipo'] === 'credito' ? 'Nota crédito' : 'Nota débito') . " {$nota->numero} emitida correctamente.",
            'data' => $nota->load('detalles.producto'),
        ], 201);
    }
}
