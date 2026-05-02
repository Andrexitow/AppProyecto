<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect('/');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            /** @var \App\Models\User $user */
            $user = Auth::user();
            $rolNombre = $user->rol->nombre ?? null;

            // --- VALIDACIÓN DE CAJA PARA MESEROS ---
            if ($rolNombre === 'Mesero' && is_null($user->caja_id)) {
                Auth::logout(); // Cerramos la sesión que se acaba de abrir

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'username' => 'Acceso denegado: El usuario mesero debe tener una caja asignada.'
                ]);
            }

            if ($rolNombre === 'Cajero' && is_null($user->caja_id)) {
                Auth::logout(); // Cerramos la sesión que se acaba de abrir

                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'username' => 'Acceso denegado: El usuario cajero debe tener una caja asignada.'
                ]);
            }
            // ---------------------------------------

            $request->session()->regenerate();
            $request->session()->forget('url.intended');

            return match ($rolNombre) {
                'Mesero'   => redirect('/facturacion'),
                'Cajero'   => redirect('/facturacion'),
                'Cocina'   => redirect('/cocina'),
                default    => redirect('/'),
            };
        }

        return back()->withErrors([
            'username' => 'Las credenciales no coinciden con nuestros registros.'
        ]);
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
