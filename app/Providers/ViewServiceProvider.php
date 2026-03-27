<?php
namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Compartilhar notificações com todas as views
        View::composer('*', function ($view) {
            if (Auth::check()) {
                $view->with('notificacoes', Auth::user()->unreadNotifications);
                $view->with('notificacoesCount', Auth::user()->unreadNotifications->count());
            }
        });

        // Compartilhar configurações do sistema
        View::share('configuracoes', cache('sgtp_configuracoes', []));
    }
}
