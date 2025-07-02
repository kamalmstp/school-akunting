<?php

namespace App\Console;

use App\Console\Commands\MonthlyGenerate;
use App\Console\Commands\MonthlyGenerateTeacher;
use App\Console\Commands\MonthlyGenerateEmployee;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->command(MonthlyGenerate::class)
            // ->monthlyOn(1, '00:00')
            ->everyFiveMinutes()
            ->timezone('Asia/Jakarta')
            ->withoutOverlapping()
            ->onOneServer()
            ->appendOutputTo(storage_path('logs/monthly-generate.log'))
            ->then(function () {
                Artisan::call(MonthlyGenerateTeacher::class);
            });

        $schedule->command(MonthlyGenerateTeacher::class)
            ->withoutOverlapping()
            ->onOneServer()
            ->appendOutputTo(storage_path('logs/monthly-generate.log'))
            ->then(function () {
                Artisan::call(MonthlyGenerateEmployee::class);
            });
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
