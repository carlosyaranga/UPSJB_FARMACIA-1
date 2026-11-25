<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    // Muestra el formulario de acceso al sistema.
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    // Verifica las credenciales y conserva la ruta protegida que el usuario intentaba abrir.
    public function login(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($validated, $request->boolean('remember'))) {
            return back()
                ->withErrors(['email' => 'El correo o la contraseña no son correctos.'])
                ->onlyInput('email');
        }

        $request->session()->regenerate();

        return redirect()->intended(route('dashboard.index'));
    }

    // Cierra la sesión e invalida el identificador anterior para proteger la cuenta.
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return to_route('login')->with('ok', 'Cerraste sesión correctamente.');
    }
}
