<?php

namespace App\Http\Controllers;

use App\Models\GrupoMenu;
use App\Models\Impresora;
use Illuminate\Http\Request;

class GrupomenuController extends Controller
{
    public function index()
    {
        // 'impresoras' tiene que coincidir con el método del modelo
        $grupos = GrupoMenu::with('impresoras')->get();
        $impresoras = Impresora::all();

        return view('grupos.index', compact('grupos', 'impresoras'));
    }

    public function store(Request $request)
    {
        try {
            // 1. Validamos el nombre del grupo y la estructura del arreglo de impresoras
            $request->validate([
                'nombre' => 'required|string|max:255',
                'impresoras' => 'required|array|min:1',
                'impresoras.*.id' => 'required|exists:impresoras,id',
                'impresoras.*.punto' => 'required|string|max:255' // Ejemplo: 'RESTAURANTE', 'DISCOTECA'
            ]);

            // 2. Creamos el Grupo de Menú (Nota: Si removiste 'impresora_id' de la tabla, esto solo guardará el nombre)
            $grupoMenu = GrupoMenu::create([
                'nombre' => $request->nombre
                // Agrega aquí otros campos si tu tabla grupo_menus tiene más columnas (ej. 'estado', 'color', etc.)
            ]);

            // 3. Formateamos los datos para la tabla pivote asociando cada ID con su punto
            $impresorasData = [];
            foreach ($request->impresoras as $item) {
                $impresorasData[$item['id']] = [
                    'punto' => strtoupper($item['punto']) // Lo guardamos en mayúsculas para mantener orden
                ];
            }

            // 4. Sincronizamos la tabla intermedia 'grupo_menu_impresora'
            $grupoMenu->impresoras()->sync($impresorasData);

            return response()->json([
                'status' => 'success',
                'message' => 'Grupo creado correctamente con sus impresoras asignadas'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            // 1. Validamos los datos que vienen del formulario
            $request->validate([
                'nombre' => 'required|string|max:255',
                'impresoras' => 'required|array',
                'impresoras.*.id' => 'required|exists:impresoras,id',
                'impresoras.*.punto' => 'required|string|max:255'
            ]);

            // 2. Buscamos el grupo a editar
            $grupo = GrupoMenu::findOrFail($id);

            // 3. Actualizamos solo los datos propios del grupo (el nombre)
            $grupo->update([
                'nombre' => $request->nombre
            ]);

            // 4. Formateamos el arreglo para la tabla pivote asociando cada ID con su punto
            $impresorasData = [];
            foreach ($request->impresoras as $item) {
                $impresorasData[$item['id']] = [
                    'punto' => strtoupper($item['punto']) // Guardamos en mayúsculas para mantener consistencia
                ];
            }

            // 5. Sincronizamos (Borra los registros viejos de este grupo en 'grupo_menu_impresora' y mete los nuevos)
            $grupo->impresoras()->sync($impresorasData);

            return response()->json([
                'status' => 'success',
                'message' => 'Grupo actualizado correctamente con sus destinos'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
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
