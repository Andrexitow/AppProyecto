<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Si ya está logueado, lo mandamos a la raíz para que las rutas decidan
        if (Auth::check()) {
            return redirect('/');
        }
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            $request->session()->forget('url.intended'); // ← limpiar URL guardada

            /** @var \App\Models\User $user */
            $user = Auth::user();

            $rolNombre = $user->rol->nombre ?? null;

            return match ($rolNombre) {
                'Mesero'  => redirect('/facturacion'),
                'Cajero'  => redirect('/facturacion'),
                'Cocina'  => redirect('/cocina'),    // si tienes esa vista
                default   => redirect('/'),          // Admin y cualquier otro rol
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
