<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\LogAuditoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Mostrar o formulário de login
     */
    public function showLoginForm(): View
    {
        return view('auth.login');
    }

    /**
     * Processar a tentativa de login
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();
            $user->ultimo_acesso = now();
            $user->save();

            // Registrar log de acesso
            LogAuditoria::registrar(
                $user->id,
                'LOGIN',
                'users',
                $user->id,
                null,
                ['ip' => $request->ip(), 'user_agent' => $request->userAgent()]
            );

            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'As credenciais fornecidas não correspondem aos nossos registos.',
        ])->onlyInput('email');
    }

    /**
     * Fazer logout do usuário
     */
    public function logout(Request $request)
    {
        $user = Auth::user();

        if ($user) {
            LogAuditoria::registrar(
                $user->id,
                'LOGOUT',
                'users',
                $user->id,
                null,
                ['ip' => $request->ip()]
            );
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
