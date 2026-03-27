<?php
namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;
use App\Models\Permissao;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Admin tem acesso a tudo
        Gate::before(function ($user, $ability) {
            if ($user->tipo_usuario === 'ADMIN') {
                return true;
            }
            return null;
        });

        // Registrar todos os Gates baseados nas permissões do banco
        try {
            $permissoes = \App\Models\Permissao::all();
            foreach ($permissoes as $permissao) {
                Gate::define($permissao->nome, function ($user) use ($permissao) {
                    // Verificar se o usuário tem perfil de acesso
                    if (!$user->perfilAcesso) {
                        return false;
                    }
                    return $user->perfilAcesso->hasPermissao($permissao->nome);
                });
            }
        } catch (\Exception $e) {
            // Tabela pode não existir durante as migrations
        }

        // Gates específicos para acesso a módulos (usando os mesmos nomes das views)
        Gate::define('gerir_frota', function ($user) {
            return $user->tipo_usuario === 'ADMIN' || 
                   ($user->perfilAcesso && $user->perfilAcesso->hasPermissao('gerir_frota'));
        });

        Gate::define('ver_frota', function ($user) {
            return $user->tipo_usuario === 'ADMIN' || 
                   ($user->perfilAcesso && $user->perfilAcesso->hasPermissao('ver_frota'));
        });

        Gate::define('gerir_colaboradores', function ($user) {
            return $user->tipo_usuario === 'ADMIN' || 
                   ($user->perfilAcesso && $user->perfilAcesso->hasPermissao('gerir_colaboradores'));
        });

        Gate::define('ver_colaboradores', function ($user) {
            return $user->tipo_usuario === 'ADMIN' || 
                   ($user->perfilAcesso && $user->perfilAcesso->hasPermissao('ver_colaboradores'));
        });

        Gate::define('gerir_operacoes', function ($user) {
            return $user->tipo_usuario === 'ADMIN' || 
                   ($user->perfilAcesso && $user->perfilAcesso->hasPermissao('gerir_operacoes'));
        });

        Gate::define('gerir_rotas', function ($user) {
            return $user->tipo_usuario === 'ADMIN' || 
                   ($user->perfilAcesso && $user->perfilAcesso->hasPermissao('gerir_rotas'));
        });

        Gate::define('gerir_horarios', function ($user) {
            return $user->tipo_usuario === 'ADMIN' || 
                   ($user->perfilAcesso && $user->perfilAcesso->hasPermissao('gerir_horarios'));
        });

        Gate::define('gerir_escalas', function ($user) {
            return $user->tipo_usuario === 'ADMIN' || 
                   ($user->perfilAcesso && $user->perfilAcesso->hasPermissao('gerir_escalas'));
        });

        Gate::define('gerir_bilhetica', function ($user) {
            return $user->tipo_usuario === 'ADMIN' || 
                   ($user->perfilAcesso && $user->perfilAcesso->hasPermissao('gerir_bilhetica'));
        });

        Gate::define('gerir_tarifas', function ($user) {
            return $user->tipo_usuario === 'ADMIN' || 
                   ($user->perfilAcesso && $user->perfilAcesso->hasPermissao('gerir_tarifas'));
        });

        Gate::define('vender_bilhetes', function ($user) {
            return $user->tipo_usuario === 'ADMIN' || 
                   ($user->perfilAcesso && $user->perfilAcesso->hasPermissao('vender_bilhetes'));
        });

        Gate::define('validar_bilhetes', function ($user) {
            return $user->tipo_usuario === 'ADMIN' || 
                   ($user->perfilAcesso && $user->perfilAcesso->hasPermissao('validar_bilhetes'));
        });

        Gate::define('gerir_ocorrencias', function ($user) {
            return $user->tipo_usuario === 'ADMIN' || 
                   ($user->perfilAcesso && $user->perfilAcesso->hasPermissao('gerir_ocorrencias'));
        });

        Gate::define('registrar_ocorrencias', function ($user) {
            return $user->tipo_usuario === 'ADMIN' || 
                   ($user->perfilAcesso && $user->perfilAcesso->hasPermissao('registrar_ocorrencias'));
        });

        Gate::define('gerir_manutencoes', function ($user) {
            return $user->tipo_usuario === 'ADMIN' || 
                   ($user->perfilAcesso && $user->perfilAcesso->hasPermissao('gerir_manutencoes'));
        });

        Gate::define('gerir_financeiro', function ($user) {
            return $user->tipo_usuario === 'ADMIN' || 
                   ($user->perfilAcesso && $user->perfilAcesso->hasPermissao('gerir_financeiro'));
        });

        Gate::define('gerir_receitas', function ($user) {
            return $user->tipo_usuario === 'ADMIN' || 
                   ($user->perfilAcesso && $user->perfilAcesso->hasPermissao('gerir_receitas'));
        });

        Gate::define('gerir_despesas', function ($user) {
            return $user->tipo_usuario === 'ADMIN' || 
                   ($user->perfilAcesso && $user->perfilAcesso->hasPermissao('gerir_despesas'));
        });

        Gate::define('fechar_caixa', function ($user) {
            return $user->tipo_usuario === 'ADMIN' || 
                   ($user->perfilAcesso && $user->perfilAcesso->hasPermissao('fechar_caixa'));
        });

        Gate::define('gerar_relatorios', function ($user) {
            return $user->tipo_usuario === 'ADMIN' || 
                   ($user->perfilAcesso && $user->perfilAcesso->hasPermissao('gerar_relatorios'));
        });

        Gate::define('gerir_usuarios', function ($user) {
            return $user->tipo_usuario === 'ADMIN';
        });

        Gate::define('gerir_perfis', function ($user) {
            return $user->tipo_usuario === 'ADMIN';
        });

        Gate::define('ver_auditoria', function ($user) {
            return $user->tipo_usuario === 'ADMIN';
        });

        Gate::define('configurar_sistema', function ($user) {
            return $user->tipo_usuario === 'ADMIN';
        });
    }
}