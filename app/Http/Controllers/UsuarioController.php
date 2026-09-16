<?php

namespace App\Http\Controllers;

use App\Models\Caja;
use App\Models\Permisos;
use App\Models\User;
use App\Models\Roles;
use App\Services\CajeroAsignacionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class UsuarioController extends Controller
{
    public function __construct(private CajeroAsignacionService $cajeroAsignacion)
    {
    }

    public function index()
    {
        $usuarios = User::with(['rol', 'caja'])->get(); // ← agregar caja
        $roles    = Roles::all();
        $permisos = Permisos::all();
        $cajas    = Caja::all(); // ← agregar
        return view('usuarios.index', compact('usuarios', 'roles', 'permisos', 'cajas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:4',
            'clave_anulacion' => 'nullable|string|min:4|max:50',
            'rol_id'   => 'required|exists:roles,id',
            'caja_id'  => 'nullable|exists:cajas,id', // ← agregar
        ], [
            'name.required'   => 'El nombre del personal es obligatorio.',
            'username.unique' => 'Este nombre de usuario ya está en uso.',
            'password.min'    => 'La contraseña debe tener al menos 4 caracteres.',
            'rol_id.required' => 'Debes asignar un rol al usuario.',
        ]);

        $usuario = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'clave_anulacion' => $request->filled('clave_anulacion') ? Hash::make($request->clave_anulacion) : null,
            'rol_id'   => $request->rol_id,
            'role'     => strtolower(Roles::find($request->rol_id)->nombre),
            'caja_id'  => $request->caja_id ?: null, // ← agregar
            'activo'   => true,
        ]);
        $this->cajeroAsignacion->asignarCajaAUsuario($usuario, $request->caja_id ?: null, null);

        return response()->json(['success' => 'Usuario creado con éxito']);
    }

    public function edit($id)
    {
        $usuario = User::findOrFail($id);
        return response()->json($usuario);
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:50|unique:users,username,' . $id,
            'rol_id'   => 'required|exists:roles,id',
            'caja_id'  => 'nullable|exists:cajas,id', // ← agregar
            'clave_anulacion' => 'nullable|string|min:4|max:50',
        ], [
            'username.unique' => 'Este nombre de usuario ya está siendo usado por otra persona.',
            'rol_id.exists'   => 'El rol seleccionado no es válido.'
        ]);

        $cajaAnteriorId = $usuario->caja_id;

        $usuario->name     = $request->name;
        $usuario->username = $request->username;
        $usuario->rol_id   = $request->rol_id;
        $usuario->caja_id  = $request->caja_id ?: null; // ← agregar

        $rol = Roles::find($request->rol_id);
        $usuario->role = strtolower($rol->nombre);

        if ($request->filled('password')) {
            $usuario->password = Hash::make($request->password);
        }
        if ($request->filled('clave_anulacion')) {
            $usuario->clave_anulacion = Hash::make($request->clave_anulacion);
        }

        $usuario->save();
        $this->cajeroAsignacion->asignarCajaAUsuario($usuario, $request->caja_id ?: null, $cajaAnteriorId);

        return response()->json(['success' => '¡Usuario actualizado correctamente!']);
    }

    /**
     * Activar/desactivar sin borrar: un usuario inactivo no puede iniciar
     * sesión (ver AuthController::login) pero conserva su historial —
     * pensado para desactivar personal que no trabaja hoy en vez de
     * dejarle la cuenta habilitada permanentemente.
     */
    public function toggleActivo($id)
    {
        $usuario = User::findOrFail($id);

        if (Auth::id() == $id) {
            return response()->json(['error' => 'No puedes desactivar tu propia cuenta'], 422);
        }

        $usuario->activo = !$usuario->activo;
        $usuario->save();

        return response()->json([
            'success' => $usuario->activo ? 'Usuario activado' : 'Usuario desactivado',
            'activo' => $usuario->activo,
        ]);
    }

    public function destroy($id)
    {
        // Usamos la Facade Auth directamente para evitar el error "Undefined method user"
        $user = Auth::user();

        // 1. Verificar si existe sesión y tiene permiso
        if (!$user || !$user->tienePermiso('usuarios.eliminar')) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // 2. Evitar que el admin se borre a sí mismo usando Auth::id()
        if (Auth::id() == $id) {
            return response()->json(['error' => 'No puedes eliminar tu propia cuenta'], 422);
        }

        try {
            $usuario = User::findOrFail($id);
            Caja::where('user_id', $usuario->id)->update(['user_id' => null]);
            $usuario->delete();

            return response()->json(['success' => 'Usuario eliminado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'No se pudo eliminar el usuario'], 500);
        }
    }

    public function editRole($id)
    {
        $rol = Roles::with('permisos')->findOrFail($id);

        // Extraemos solo los IDs de los permisos para que el JS los marque fácilmente
        $permisosIds = $rol->permisos->pluck('id')->toArray();

        return response()->json([
            'rol' => $rol,
            'permisosIds' => $permisosIds
        ]);
    }

    public function updateRole(Request $request, $id)
    {
        try {
            $rol = Roles::findOrFail($id);

            // Actualizar datos básicos
            $rol->update([
                'nombre' => $request->nombre,
                'descripcion' => $request->descripcion,
            ]);

            // Sincronizar permisos (los checkboxes)
            if ($request->has('permisos')) {
                $rol->permisos()->sync($request->permisos);
            } else {
                // Si no enviaron ninguno, limpiamos los permisos del rol
                $rol->permisos()->detach();
            }

            return response()->json(['success' => 'Rol actualizado correctamente']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Error al actualizar: ' . $e->getMessage()], 500);
        }
    }
}
