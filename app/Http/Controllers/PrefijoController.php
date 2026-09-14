<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Prefijo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class PrefijoController extends Controller
{
    public function index()
    {
        return view('prefijos.index');
    }

    public function data()
    {
        return response()->json(['data' => Prefijo::orderBy('codigo')->get()]);
    }

    /** Solo los activos — para usar como selector en Cajas y en cualquier otro documento. */
    public function opciones()
    {
        return response()->json(['data' => Prefijo::where('activo', true)->orderBy('codigo')->get(['id', 'codigo', 'nombre'])]);
    }

    public function store(Request $request)
    {
        $datos = $this->validar($request);
        $datos['codigo'] = strtoupper($datos['codigo']);
        $prefijo = Prefijo::create($datos);

        return response()->json(['success' => true, 'data' => $prefijo, 'message' => 'Prefijo creado correctamente.'], 201);
    }

    public function update(Request $request, Prefijo $prefijo)
    {
        $datos = $this->validar($request, $prefijo);
        $datos['codigo'] = strtoupper($datos['codigo']);
        $prefijo->update($datos);

        return response()->json(['success' => true, 'data' => $prefijo->fresh(), 'message' => 'Prefijo actualizado correctamente.']);
    }

    public function cambiarEstado(Prefijo $prefijo)
    {
        $prefijo->update(['activo' => !$prefijo->activo]);

        return response()->json([
            'success' => true,
            'message' => $prefijo->activo ? 'Prefijo activado correctamente.' : 'Prefijo desactivado correctamente.',
            'data' => $prefijo->fresh(),
        ]);
    }

    public function destroy(Prefijo $prefijo)
    {
        $enUso = Caja::where('prefijo', $prefijo->codigo)->exists()
            || DB::table('documentos')->where('prefijo', $prefijo->codigo)->exists()
            || DB::table('facturas')->where('numero_factura', 'like', $prefijo->codigo . '-%')->exists();

        if ($enUso) {
            throw ValidationException::withMessages(['prefijo' => 'No se puede eliminar: hay cajas o documentos usando este prefijo. Desactívelo en su lugar.']);
        }

        $prefijo->delete();

        return response()->json(['success' => true, 'message' => 'Prefijo eliminado correctamente.']);
    }

    private function validar(Request $request, ?Prefijo $prefijo = null): array
    {
        return $request->validate([
            'codigo' => ['required', 'string', 'max:10', Rule::unique('prefijos', 'codigo')->ignore($prefijo?->id)],
            'nombre' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:255'],
            // Resolución de facturación DIAN para este prefijo — opcional
            // mientras no se facture electrónicamente, necesaria el día que sí.
            'resolucion_numero' => ['nullable', 'string', 'max:30'],
            'resolucion_fecha' => ['nullable', 'date'],
            'rango_desde' => ['nullable', 'integer', 'min:1'],
            'rango_hasta' => ['nullable', 'integer', 'gte:rango_desde'],
            'vigencia_desde' => ['nullable', 'date'],
            'vigencia_hasta' => ['nullable', 'date', 'after_or_equal:vigencia_desde'],
            'clave_tecnica' => ['nullable', 'string', 'max:100'],
            // Solo si el proveedor (p. ej. Factus) tiene más de un rango
            // activo — si se deja vacío, el proveedor usa su único rango.
            'numbering_range_id_factus' => ['nullable', 'integer', 'min:1'],
        ]);
    }
}
