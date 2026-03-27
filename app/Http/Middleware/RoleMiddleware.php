<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     * Verifica se o usuário tem uma das permissões especificadas
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Admin tem acesso total
        if ($user->tipo_usuario === 'ADMIN') {
            return $next($request);
        }

        // Verifica se o usuário tem um dos papéis permitidos
        if (in_array($user->tipo_usuario, $roles)) {
            return $next($request);
        }

        abort(403, 'Acesso negado. Você não tem permissão para acessar esta área.');
    }
}
