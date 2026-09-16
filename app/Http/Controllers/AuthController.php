<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Caja;
use App\Services\AuditoriaService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect('/');
        }

        // Evita que el navegador sirva esta página desde su caché (botón
        // "atrás" o bfcache) tras un logout: si eso pasara, el formulario
        // traería un _token de una sesión ya invalidada y el login
        // terminaría en "419 Page Expired" en vez de iniciar sesión.
        return response(view('auth.login'))->withHeaders([
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma' => 'no-cache',
        ]);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $rolNombre = $user->rol->nombre ?? null;

            // Un usuario desactivado (Usuarios → 🚫) no puede entrar aunque
            // recuerde su contraseña — pensado para poder "apagar" el acceso
            // de personal que no está trabajando hoy sin borrar su cuenta.
            if (!$user->activo) {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                AuditoriaService::registrar($user, 'Acceso', 'Inicio de sesión bloqueado', 'Intento de inicio de sesión con usuario desactivado.', $request);

                return back()->withErrors([
                    'username' => 'Este usuario está desactivado. Contacta al administrador.'
                ]);
            }

            // --- VALIDACIÓN DE CAJA PARA MESEROS ---
            if ($rolNombre === 'Mesero' && (is_null($user->caja_id) || !Caja::whereKey($user->caja_id)->where('activa', true)->exists())) {
                Auth::logout(); // Cerramos la sesión que se acaba de abrir

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'username' => 'Acceso denegado: El usuario mesero debe tener una caja activa asignada.'
                ]);
            }

            if ($rolNombre === 'Cajero' && (is_null($user->caja_id) || !Caja::whereKey($user->caja_id)->where('activa', true)->exists())) {
                Auth::logout(); // Cerramos la sesión que se acaba de abrir

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'username' => 'Acceso denegado: El usuario cajero debe tener una caja activa asignada.'
                ]);
            }
            // ---------------------------------------

            $request->session()->regenerate();
            $request->session()->forget('url.intended');
            AuditoriaService::registrar($user, 'Acceso', 'Inicio de sesión', 'Inicio de sesión exitoso.', $request);

            return match ($rolNombre) {
                'Mesero'   => redirect('/facturacion'),
                'Cajero'   => redirect('/facturacion'),
                'Cocina'   => redirect('/cocina'),
                default    => redirect('/'),
            };
        }

        AuditoriaService::registrar(null, 'Acceso', 'Inicio de sesión fallido', 'Intento de inicio de sesión con credenciales inválidas.', $request);

        return back()->withErrors([
            'username' => 'Las credenciales no coinciden con nuestros registros.'
        ]);
    }

    public function logout(Request $request)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();
        AuditoriaService::registrar($user, 'Acceso', 'Cierre de sesión', 'Cierre de sesión realizado.', $request);
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
