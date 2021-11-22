<?php

namespace BookStack\Console;

use BookStack\Facades\Theme;
use BookStack\Theming\ThemeService;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Symfony\Component\Console\Command\Command;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        //
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        // Default framework command loading from 'Commands' directory
        $this->load(__DIR__ . '/Commands');

        // Load any user commands that have been registered via the theme system.
        $themeService = $this->app->make(ThemeService::class);
        foreach ($themeService->getRegisteredCommands() as $command) {
            $this->registerCommand($command);
        }
    }
}
