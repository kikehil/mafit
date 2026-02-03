<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthenticatedSessionController extends Controller
{
    public function create()
    {
        return view('auth.login');
    }

    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);

        if (!Auth::attempt($request->only('email', 'password'), $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'email' => __('auth.failed'),
            ]);
        }

        // Regenerar sesión para prevenir fijación de sesión
        $request->session()->regenerate();

        // Verificar que la sesión se haya guardado correctamente
        if (!Auth::check()) {
            \Log::error('Error: Usuario autenticado pero sesión no persistida', [
                'email' => $request->email,
                'session_id' => $request->session()->getId(),
            ]);
            throw ValidationException::withMessages([
                'email' => 'Error al iniciar sesión. Por favor, intente nuevamente.',
            ]);
        }

        return redirect()->intended(route('dashboard'));
    }

    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}











