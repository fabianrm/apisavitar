<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use App\Console\Commands\DailyServiceCutsSummaryCommand;
use App\Console\Commands\MonitorMikrotikConnectivityCommand;
use App\Console\Commands\SincronizarMikrotikCommand;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     */
    protected $commands = [
        //  SincronizarMikrotikCommand::class,
    ];

    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('mk:sync')
        //     ->dailyAt('12:00')
        //     ->timezone('America/Lima')
        //     ->environments(['production']);

        $schedule->command(MonitorMikrotikConnectivityCommand::class)
            ->everyFiveMinutes()
            ->withoutOverlapping()
            ->environments(['production']);

        $schedule->command(DailyServiceCutsSummaryCommand::class)
            ->dailyAt('20:00')
            ->timezone('America/Lima')
            ->environments(['production']);
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
