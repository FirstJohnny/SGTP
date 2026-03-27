<?php
// app/Console/Kernel.php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     */
    protected $commands = [
        Commands\CheckDocumentExpiration::class,
        Commands\GenerateMaintenanceSchedule::class,
        Commands\SyncGPSData::class,
        Commands\SendDailyReports::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)
    {
        // Verificar documentos expirados diariamente às 8h
        $schedule->command('sgtp:check-documents')->dailyAt('08:00');

        // Gerar manutenções preventivas às segundas-feiras
        $schedule->command('sgtp:generate-maintenance')->weekly()->mondays()->at('06:00');

        // Sincronizar GPS a cada 30 segundos (RF03)
        $schedule->command('sgtp:sync-gps')->everyThirtySeconds();

        // Enviar relatórios diários às 7h
        $schedule->command('sgtp:send-reports')->dailyAt('07:00');

        // Consolidar receitas diariamente às 23:55
        $schedule->call(function () {
            \App\Models\Receita::consolidarDiaria();
        })->dailyAt('23:55');

        // Limpar logs antigos (retenção de 5 anos - RN13)
        $schedule->command('model:prune')->monthly();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
