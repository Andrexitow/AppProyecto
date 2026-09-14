<?php

namespace App\Http\Controllers;

use App\Models\ConceptoCaja;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ConceptoCajaController extends Controller
{
    public function index()
    {
        return view('conceptos-caja.index');
    }

    public function data()
    {
        return response()->json(['data' => ConceptoCaja::orderBy('nombre')->get()]);
    }

    /**
     * Solo los activos — para el selector de conceptos al registrar un
     * movimiento de caja (ingreso/salida). Accesible a cualquier usuario
     * autenticado (cajero incluido), no solo a Administrador.
     */
    public function opciones(Request $request)
    {
        $query = ConceptoCaja::where('activo', true);

        if ($request->filled('tipo')) {
            $query->where(function ($q) use ($request) {
                $q->where('tipo', $request->tipo)->orWhere('tipo', 'ambos');
            });
        }

        return response()->json(['data' => $query->orderBy('nombre')->get(['id', 'nombre', 'tipo'])]);
    }

    public function store(Request $request)
    {
        $concepto = ConceptoCaja::create($this->validar($request));

        return response()->json(['success' => true, 'data' => $concepto, 'message' => 'Concepto creado correctamente.'], 201);
    }

    public function update(Request $request, ConceptoCaja $conceptoCaja)
    {
        $conceptoCaja->update($this->validar($request, $conceptoCaja));

        return response()->json(['success' => true, 'data' => $conceptoCaja->fresh(), 'message' => 'Concepto actualizado correctamente.']);
    }

    public function cambiarEstado(ConceptoCaja $conceptoCaja)
    {
        $conceptoCaja->update(['activo' => !$conceptoCaja->activo]);

        return response()->json([
            'success' => true,
            'message' => $conceptoCaja->activo ? 'Concepto activado correctamente.' : 'Concepto desactivado correctamente.',
            'data' => $conceptoCaja->fresh(),
        ]);
    }

    public function destroy(ConceptoCaja $conceptoCaja)
    {
        if ($conceptoCaja->movimientos()->exists()) {
            throw ValidationException::withMessages(['concepto' => 'No se puede eliminar: hay movimientos de caja usando este concepto. Desactívelo en su lugar.']);
        }

        $conceptoCaja->delete();

        return response()->json(['success' => true, 'message' => 'Concepto eliminado correctamente.']);
    }

    private function validar(Request $request, ?ConceptoCaja $conceptoCaja = null): array
    {
        return $request->validate([
            'nombre' => ['required', 'string', 'max:150', Rule::unique('conceptos_caja', 'nombre')->ignore($conceptoCaja?->id)],
            'tipo' => ['required', Rule::in(array_keys(ConceptoCaja::TIPOS))],
            'descripcion' => ['nullable', 'string', 'max:255'],
        ]);
    }
}
