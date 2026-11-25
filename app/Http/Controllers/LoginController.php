<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    // Muestra el formulario manual de inicio de sesión.
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Valida las credenciales y inicia la sesión del usuario.
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ], $remember)) {
            $request->session()->regenerate();

            return redirect()->intended(route('dashboard.index'));
        }

        return back()
            ->withErrors([
                'email' => 'El correo o la contraseña no son correctos.',
            ])
            ->onlyInput('email');
    }

    // Cierra la sesión actual de forma segura.
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('login')
            ->with('ok', 'Cerraste sesión correctamente.');
    }
}
