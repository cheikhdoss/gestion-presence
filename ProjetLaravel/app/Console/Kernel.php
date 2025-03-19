<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Envoie des rappels de cours 24h avant
        $schedule->command('notifications:cours-rappels')
            ->hourly()
            ->withoutOverlapping();

        // Vérifie les émargements manquants à la fin de la journée
        $schedule->command('notifications:emargements-manquants')
            ->dailyAt('20:00')
            ->withoutOverlapping();

        // Nettoie les anciennes notifications (plus de 30 jours)
        $schedule->command('notifications:clean')
            ->daily()
            ->withoutOverlapping();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
