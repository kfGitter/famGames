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
        // This runs our streak updater once per day
        $schedule->command('streaks:update')->daily();
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    protected function schedule(Schedule $schedule): void
{
    // Daily reminder
    $schedule->command('reminders:send daily')->dailyAt('09:00');

    // Weekly reminder (e.g., every Sunday morning)
    $schedule->command('reminders:send weekly')->sundays()->at('10:00');
}

}
