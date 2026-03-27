<?php
// app/Http/Middleware/TwoFactorMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TwoFactorMiddleware
{
    /**
     * Verifica se o usuário completou a autenticação de dois fatores
     */
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Se 2FA estiver habilitado e não verificado, redirecionar
        if ($user && $user->two_factor_enabled && !session('2fa_verified')) {
            return redirect()->route('2fa.verify');
        }

        return $next($request);
    }
}
