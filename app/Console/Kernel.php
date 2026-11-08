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
       $schedule->command('exchange-rates:sync --from=USD --to=IDR')->dailyAt('17:00');
    $schedule->command('exchange-rates:sync --from=SGD --to=IDR')->dailyAt('17:00');
    $schedule->command('exchange-rates:sync --from=EUR --to=IDR')->dailyAt('17:00');
    $schedule->command('exchange-rates:sync --from=JPY --to=IDR')->dailyAt('17:00');
    $schedule->command('exchange-rates:sync --from=GBP --to=IDR')->dailyAt('17:00');
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
