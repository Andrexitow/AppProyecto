<?php

namespace App\Http\Controllers;

use App\Models\AcompanamientoGrupo;
use App\Models\AcompanamientoOpcion;
use App\Models\Producto;
use Illuminate\Http\Request;

/**
 * Grupos de acompañamiento (ej. "Servicio de Cubetazo"): un máximo de
 * unidades a repartir libremente entre varias opciones de producto. Ver
 * AcompanamientoGrupo, AcompanamientoOpcion y el reparto real por venta en
 * DetallePedidoAcompanamiento / Consumo.
 */
class AcompanamientoController extends Controller
{
    public const POR_PAGINA = 50;

    public function index(Request $request)
    {
        $grupos = AcompanamientoGrupo::withCount('opciones')
            ->when($request->filled('buscar'), fn ($q) => $q->where(function ($q2) use ($request) {
                $q2->where('codigo', 'like', '%' . $request->buscar . '%')
                    ->orWhere('descripcion', 'like', '%' . $request->buscar . '%');
            }))
            ->orderBy('descripcion')
            ->paginate(self::POR_PAGINA)
            ->withQueryString();

        if ($request->ajax()) {
            return view('acompanamientos.partials.tabla', compact('grupos'));
        }

        return view('acompanamientos.index', compact('grupos'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'codigo' => 'required|string|max:30|unique:acompanamiento_grupos,codigo',
            'descripcion' => 'required|string|max:150',
            'cantidad_maxima' => 'required|integer|min:1|max:999',
        ]);

        $grupo = AcompanamientoGrupo::create($datos);

        return response()->json([
            'success' => true,
            'message' => 'Grupo de acompañamiento creado correctamente.',
            'data' => $grupo,
        ]);
    }

    public function update(Request $request, AcompanamientoGrupo $acompanamiento)
    {
        $datos = $request->validate([
            'codigo' => 'required|string|max:30|unique:acompanamiento_grupos,codigo,' . $acompanamiento->id,
            'descripcion' => 'required|string|max:150',
            'cantidad_maxima' => 'required|integer|min:1|max:999',
        ]);

        $acompanamiento->update($datos);

        return response()->json([
            'success' => true,
            'message' => 'Grupo actualizado correctamente.',
        ]);
    }

    public function destroy(AcompanamientoGrupo $acompanamiento)
    {
        $enUso = $acompanamiento->productos()->exists();
        if ($enUso) {
            return response()->json([
                'success' => false,
                'message' => 'No se puede eliminar: hay productos de venta usando este grupo (ej. "Cubetazo Mix"). Quítales el acompañamiento primero.',
            ], 422);
        }

        $acompanamiento->delete();

        return response()->json([
            'success' => true,
            'message' => 'Grupo eliminado correctamente.',
        ]);
    }

    /**
     * Opciones (productos elegibles) de un grupo — para el modal "Gestionar
     * productos" en la vista y para el modal de reparto en el POS.
     */
    public function opciones(AcompanamientoGrupo $acompanamiento)
    {
        $opciones = $acompanamiento->opciones()
            ->with('producto:id,codigo,descripcion,und_detal')
            ->get()
            ->pluck('producto');

        return response()->json([
            'grupo' => $acompanamiento->only(['id', 'codigo', 'descripcion', 'cantidad_maxima']),
            'opciones' => $opciones,
        ]);
    }

    public function agregarOpcion(Request $request, AcompanamientoGrupo $acompanamiento)
    {
        $request->validate([
            'producto_id' => 'required|exists:productos,id',
        ]);

        $yaExiste = $acompanamiento->opciones()->where('producto_id', $request->producto_id)->exists();
        if ($yaExiste) {
            return response()->json(['success' => false, 'message' => 'Ese producto ya está en el grupo.'], 422);
        }

        AcompanamientoOpcion::create([
            'acompanamiento_grupo_id' => $acompanamiento->id,
            'producto_id' => $request->producto_id,
        ]);

        return response()->json(['success' => true, 'message' => 'Producto agregado al grupo.']);
    }

    public function quitarOpcion(AcompanamientoGrupo $acompanamiento, Producto $producto)
    {
        $acompanamiento->opciones()->where('producto_id', $producto->id)->delete();

        return response()->json(['success' => true, 'message' => 'Producto quitado del grupo.']);
    }
}
