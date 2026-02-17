<?php

namespace App\Console;

use App\Jobs\FinetuneOptimizationParameters;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Fine-tune forecast parameters nightly at 2 AM
        // This job performs 729-iteration grid search for optimal parameters
        // on products that haven't been optimized in the last 90 days
        $schedule->job(new FinetuneOptimizationParameters)
            ->dailyAt('02:00')
            ->name('finetune-forecast-parameters');
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
