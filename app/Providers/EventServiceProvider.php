<?php
namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        // Eventos customizados do SGTP
        'App\Events\OcorrenciaCriada' => [
            'App\Listeners\NotificarOcorrencia',
            'App\Listeners\CriarAlertaOcorrencia',
        ],

        'App\Events\BilheteValidado' => [
            'App\Listeners\RegistrarPassageiro',
            'App\Listeners\AtualizarReceita',
        ],

        'App\Events\EscalaIniciada' => [
            'App\Listeners\RegistrarPonto',
            'App\Listeners\AtualizarStatusVeiculo',
        ],

        'App\Events\DocumentoVencendo' => [
            'App\Listeners\EnviarAlertaDocumento',
        ],
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
