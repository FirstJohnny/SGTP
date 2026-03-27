<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionMiddleware
{
    /**
     * Verifica se o usuário tem uma permissão específica
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Admin tem acesso total
        if ($user->tipo_usuario === 'ADMIN') {
            return $next($request);
        }

        // Verifica permissão via perfil de acesso
        if ($user->perfilAcesso && $user->perfilAcesso->hasPermissao($permission)) {
            return $next($request);
        }

        abort(403, 'Acesso negado. Permissão necessária: ' . $permission);
    }
}
