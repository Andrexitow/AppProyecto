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

            /** @var \App\Models\User $user */
            $user = Auth::user();

            // REDIRECCIÓN INTELIGENTE
            // Si es mesero, va directo a facturación. Si no, a la raíz (Home).
            if ($user->rol && $user->rol->nombre === 'Mesero') {
                return redirect()->intended('/facturacion');
            }

            return redirect()->intended('/');
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
