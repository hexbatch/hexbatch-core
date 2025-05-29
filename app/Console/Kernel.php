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
        // $schedule->command('inspire')->hourly();
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

//todo make new command to give ownership to someone of abandoned namespace

//todo get token for the system user so that can be updated

//todo write command to trim pending things if they are done and past a time

//todo command to trim  paths not in the todo, not a constraint, not in the rules,and without a handle element
