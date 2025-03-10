<?php

namespace App\Console;

use App\Console\Commands\CheckForecasts;
use Illuminate\Console\Scheduling\Schedule;
use Laravel\Lumen\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        CheckForecasts::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //tasks which only execute on production server
        if(app()->environment('production')){
            $schedule->command('surfscribe:check_forecasts')->dailyAt('07:00');
        }
    }
}
