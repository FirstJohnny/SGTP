<?php
// app/Http/Middleware/LogActivityMiddleware.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LogAuditoria;

class LogActivityMiddleware
{
    /**
     * Registra atividades importantes do usuário
     */
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);

        // Registrar apenas rotas específicas (POST, PUT, DELETE)
        if (Auth::check() && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            LogAuditoria::create([
                'usuario_id' => Auth::id(),
                'acao' => $request->method(),
                'entidade' => $this->getEntityName($request),
                'entidade_id' => $request->route('id') ?? null,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'timestamp' => now()
            ]);
        }

        return $response;
    }

    private function getEntityName(Request $request): string
    {
        $path = $request->path();
        $segments = explode('/', $path);

        // Retorna o nome da entidade baseado na URL
        if (count($segments) > 1) {
            return $segments[1] ?? 'desconhecido';
        }

        return 'desconhecido';
    }
}
