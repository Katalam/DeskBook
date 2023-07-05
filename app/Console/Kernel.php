<?php

namespace App\Console;

use App\Console\Commands\SyncPersonioUsersCommand;
use App\Jobs\SyncPersonioTimeOffTypesJob;
use App\Services\NotificationService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(SyncPersonioUsersCommand::class)->daily();
        $schedule->command(SyncPersonioTimeOffTypesJob::class)->daily();
        $schedule->call(function (NotificationService $notificationService) {
            $notificationService->handle();
        })->dailyAt('11:00');
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
