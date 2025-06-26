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
        // Schedule the subscription reminder command to run daily
        $schedule->command('subscription:reminder')->daily();

        // Schedule the subscription deduction command to run daily at midnight
        $schedule->command('subscription:deduct')->dailyAt('00:00');
        
        $schedule->command('subscription:update-balance')->dailyAt('01:00');
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
