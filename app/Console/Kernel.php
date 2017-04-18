<?php

namespace BookStack\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        \BookStack\Console\Commands\ClearViews::class,
        \BookStack\Console\Commands\ClearActivity::class,
        \BookStack\Console\Commands\ClearRevisions::class,
        \BookStack\Console\Commands\RegeneratePermissions::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }
}
