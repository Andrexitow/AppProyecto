<?php

namespace App\Http\Controllers;

use App\Models\GrupoMenu;
use App\Models\Impresora;
use Illuminate\Http\Request;

class GrupomenuController extends Controller
{
    public function index()
    {
        $grupos = GrupoMenu::with('impresora')->get();
        $impresoras = Impresora::all();
        // Cambia 'vistas.grupos' por la ruta real de tu archivo blade
        return view('grupos.index', compact('grupos', 'impresoras'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'nombre' => 'required|string|max:255',
                'impresora_id' => 'required|exists:impresoras,id'
            ]);

            GrupoMenu::create($request->all());
            return response()->json(['status' => 'success', 'message' => 'Grupo creado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $grupo = GrupoMenu::findOrFail($id);
            $grupo->update($request->all());
            return response()->json(['status' => 'success', 'message' => 'Grupo actualizado']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    public function destroy($id)
    {
        try {
            // 1. Buscamos el grupo
            $grupo = GrupoMenu::find($id);

            // 2. Verificamos si existe
            if (!$grupo) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'El grupo no existe o ya fue eliminado.'
                ], 404);
            }

            // 3. Opcional: Validar si tiene productos asociados
            // Esto evita que dejes productos "huérfanos" sin grupo
            if ($grupo->productos()->count() > 0) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'No se puede eliminar: Este grupo tiene productos vinculados.'
                ], 422);
            }

            // 4. Ejecutamos la eliminación
            $grupo->delete();

            return response()->json([
                'status' => 'success',
                'message' => '<b>Eliminado:</b> El grupo se quitó correctamente.'
            ]);
        } catch (\Exception $e) {
            // Manejo de errores de base de datos (ej. restricciones de llave foránea)
            return response()->json([
                'status' => 'error',
                'message' => 'Error al eliminar: ' . $e->getMessage()
            ], 500);
        }
    }
}
