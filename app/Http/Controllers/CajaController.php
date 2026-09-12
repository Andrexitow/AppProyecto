<?php

namespace App\Http\Controllers;

use App\Models\Bodega;
use App\Models\Caja;
use App\Models\Impresora;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CajaController extends Controller
{
    public function index()
    {
        // Traemos las cajas con sus relaciones para la tabla/cards
        $cajas = Caja::with(['bodega', 'cajero'])->get();
        $impresoras = Impresora::all();

        // Traemos bodegas y usuarios para los select del modal de creación
        $bodegas = Bodega::all();
        $usuarios = User::all();

        return view('cajas.index', compact('cajas', 'bodegas', 'usuarios', 'impresoras'));
    }

    /** Listado administrativo. GET /cajas se reserva para el catálogo POS activo. */
    public function data()
    {
        return response()->json(['data' => Caja::with(['bodega', 'impresora', 'cajero'])->orderBy('nombre')->get()]);
    }

    public function show(Caja $caja)
    {
        return response()->json($caja->load(['bodega', 'impresora', 'cajero']));
    }

    public function store(Request $request)
    {
        try {
            // 1. Validaciones incluyendo los nuevos campos del formulario
            $request->validate([
                'nombre'         => 'required|string|max:255',
                'prefijo'        => 'required|string|max:10',
                'proximo_numero' => 'required|integer|min:1', // Nuevo campo validado
                'bodega_id'      => 'required|exists:bodegas,id',
                'impresora_id'   => 'nullable|exists:impresoras,id', // Nuevo campo validado
                'user_id'        => 'nullable|exists:users,id',
            ]);

            // 2. Extraer los datos y estandarizar el prefijo a MAYÚSCULAS
            $datos = $request->all();
            $datos['prefijo'] = strtoupper($request->prefijo);

            // Manejo del checkbox 'activa' (si viene en el request toma 1, sino 0)
            $datos['activa'] = $request->boolean('activa');

            // 3. Crear la caja con todos los parámetros
            $caja = DB::transaction(function () use ($datos) {
                $caja = Caja::create($datos);
                $this->sincronizarCajero($caja, $datos['user_id'] ?? null, null);
                return $caja;
            });

            // Devolver JSON para la integración asíncrona (AJAX/Fetch)
            return response()->json([
                'status'  => 'success',
                'message' => 'Caja creada y configurada correctamente',
                'data'    => $caja
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 422);
        }
    }

    public function edit($id)
    {
        // Cargamos la caja junto con sus relaciones para tener la info completa en JS
        $caja = Caja::with(['bodega', 'impresora', 'cajero'])->findOrFail($id);
        return response()->json($caja);
    }

    public function update(Request $request, $id)
    {
        try {
            $caja = Caja::findOrFail($id);

            if ($request->has('activa') && count($request->all()) === 1) {
                return $this->cambiarEstado($request, $caja);
            }

            // 1. Validaciones incluyendo los nuevos campos del formulario
            $request->validate([
                'nombre'         => 'required|string|max:255',
                'prefijo'        => 'required|string|max:10',
                'proximo_numero' => 'required|integer|min:1', // Nuevo campo
                'bodega_id'      => 'required|exists:bodegas,id',
                'impresora_id'   => 'nullable|exists:impresoras,id', // Nuevo campo
                'user_id'        => 'nullable|exists:users,id',
            ]);

            $anteriorCajeroId = $caja->user_id;

            // 2. Actualizar el registro mapeando todo correctamente
            $caja->update([
                'nombre'         => $request->nombre,
                'prefijo'        => strtoupper($request->prefijo), // Estandariza a mayúsculas
                'proximo_numero' => $request->proximo_numero,      // Nuevo campo
                'bodega_id'      => $request->bodega_id,
                'impresora_id'   => $request->impresora_id ?: null, // Nuevo campo
                'user_id'        => $request->user_id ?: null,
                // Evaluamos correctamente el checkbox si viene marcado (1) o desmarcado (0)
                'activa'         => $request->boolean('activa'),
            ]);
            $this->sincronizarCajero($caja, $request->user_id ?: null, $anteriorCajeroId);

            return response()->json([
                'status'  => 'success',
                'message' => 'Caja actualizada y configurada correctamente',
                'data'    => $caja
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 422);
        }
    }

    public function cambiarEstado(Request $request, Caja $caja)
    {
        $datos = $request->validate(['activa' => ['required', 'boolean']]);
        $caja->update(['activa' => (bool) $datos['activa']]);

        return response()->json([
            'status' => 'success',
            'message' => $caja->activa ? 'Caja habilitada correctamente.' : 'Caja deshabilitada correctamente.',
            'data' => $caja->fresh(),
        ]);
    }

    private function sincronizarCajero(Caja $caja, ?int $nuevoCajeroId, ?int $anteriorCajeroId): void
    {
        if ($anteriorCajeroId && $anteriorCajeroId !== $nuevoCajeroId) {
            User::where('id', $anteriorCajeroId)->where('caja_id', $caja->id)->update(['caja_id' => null]);
        }
        if (!$nuevoCajeroId) return;

        Caja::where('user_id', $nuevoCajeroId)->where('id', '!=', $caja->id)->update(['user_id' => null]);
        User::whereKey($nuevoCajeroId)->update(['caja_id' => $caja->id]);
    }

    public function destroy($id)
    {
        try {
            $caja = Caja::findOrFail($id);
            $tieneHistorial = DB::table('facturas')->where('caja_id', $caja->id)->exists()
                || DB::table('cierres_caja')->where('caja_id', $caja->id)->exists()
                || DB::table('documentos')->where('caja_id', $caja->id)->exists();
            if ($tieneHistorial) {
                throw ValidationException::withMessages(['caja' => 'No se puede eliminar una caja con facturas, cierres o documentos. Deshabilítela para conservar su historial.']);
            }
            DB::transaction(function () use ($caja) {
                User::where('caja_id', $caja->id)->update(['caja_id' => null]);
                $caja->delete();
            });

            return response()->json([
                'status'  => 'success',
                'message' => 'Caja eliminada correctamente'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Error: ' . $e->getMessage()
            ], 422);
        }
    }
}
