<?php

namespace Illuminate\Foundation\Console;

use Illuminate\Console\Command;

class ClearCompiledCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'clear-compiled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove the compiled class file';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function fire()
    {
        $servicesPath = $this->laravel->getCachedServicesPath();

        if (file_exists($servicesPath)) {
            @unlink($servicesPath);
        }

        $this->info('The compiled services file has been removed.');
    }
}
