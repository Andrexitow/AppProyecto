<?php

namespace App\Http\Controllers;

use App\Models\Bodega;
use App\Models\Mesa;
use App\Models\Zona;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

/**
 * CRUD de Zonas y Mesas — el piso del POS (pantalla "Comandar") se llena
 * con lo que exista aquí. Sin esto no había forma de crear mesas desde la
 * app; solo existían por seeders/scripts de datos.
 */
class MesaController extends Controller
{
    public function index()
    {
        $zonas = Zona::with(['bodega:id,descripcion', 'mesas' => function ($query) {
            $query->orderBy('numero');
        }])->orderBy('nombre')->get();

        $bodegas = Bodega::orderBy('descripcion')->get(['id', 'descripcion']);

        return view('mesas.index', compact('zonas', 'bodegas'));
    }

    public function storeZona(Request $request)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'bodega_id' => ['nullable', 'exists:bodegas,id'],
        ]);

        $zona = Zona::create($datos);

        return response()->json(['success' => true, 'data' => $zona->load('bodega:id,descripcion'), 'message' => 'Zona creada correctamente.'], 201);
    }

    public function updateZona(Request $request, Zona $zona)
    {
        $datos = $request->validate([
            'nombre' => ['required', 'string', 'max:100'],
            'bodega_id' => ['nullable', 'exists:bodegas,id'],
        ]);

        $zona->update($datos);

        return response()->json(['success' => true, 'data' => $zona->fresh('bodega:id,descripcion'), 'message' => 'Zona actualizada correctamente.']);
    }

    public function destroyZona(Zona $zona)
    {
        if ($zona->mesas()->exists()) {
            return response()->json([
                'message' => 'No se puede eliminar: la zona todavía tiene mesas. Elimina o mueve sus mesas primero.',
            ], 422);
        }

        $zona->delete();

        return response()->json(['success' => true, 'message' => 'Zona eliminada correctamente.']);
    }

    public function storeMesa(Request $request)
    {
        $datos = $this->validarMesa($request);
        $mesa = Mesa::create($datos + ['estado' => 'disponible']);

        return response()->json(['success' => true, 'data' => $mesa, 'message' => 'Mesa creada correctamente.'], 201);
    }

    public function updateMesa(Request $request, Mesa $mesa)
    {
        $datos = $this->validarMesa($request, $mesa);
        $mesa->update($datos);

        return response()->json(['success' => true, 'data' => $mesa->fresh(), 'message' => 'Mesa actualizada correctamente.']);
    }

    /**
     * Deja la mesa en 'disponible' de nuevo — para cuando quedó atascada
     * (ej. 'seleccionada'/'ocupada' por una prueba) sin un pedido real
     * detrás. Si tiene un pedido pendiente de verdad, usa el flujo normal
     * de "liberar mesa" del POS, no esto.
     */
    public function resetMesa(Mesa $mesa)
    {
        if ($mesa->pedidos()->where('estado', 'pendiente')->exists()) {
            return response()->json([
                'message' => 'Esta mesa tiene un pedido pendiente real. Libérala desde la pantalla de Comandar, no desde aquí.',
            ], 422);
        }

        $mesa->update(['estado' => 'disponible', 'bloqueada_por' => null, 'bloqueada_at' => null]);

        return response()->json(['success' => true, 'data' => $mesa->fresh(), 'message' => 'Mesa restablecida a disponible.']);
    }

    public function destroyMesa(Mesa $mesa)
    {
        $enUso = DB::table('pedidos')->where('mesa_id', $mesa->id)->exists()
            || DB::table('facturas')->where('mesa_id', $mesa->id)->exists();

        if ($enUso) {
            return response()->json([
                'message' => 'No se puede eliminar: esta mesa ya tiene pedidos o facturas asociadas.',
            ], 422);
        }

        $mesa->delete();

        return response()->json(['success' => true, 'message' => 'Mesa eliminada correctamente.']);
    }

    private function validarMesa(Request $request, ?Mesa $mesa = null): array
    {
        return $request->validate([
            'zona_id' => ['required', 'exists:zonas,id'],
            'numero' => [
                'required', 'string', 'max:20',
                Rule::unique('mesas', 'numero')->where('zona_id', $request->zona_id)->ignore($mesa?->id),
            ],
            'capacidad' => ['required', 'integer', 'min:1', 'max:50'],
        ]);
    }
}
