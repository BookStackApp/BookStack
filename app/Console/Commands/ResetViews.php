<?php

namespace BookStack\Console\Commands;

use Illuminate\Console\Command;

class ResetViews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'views:reset';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset all view-counts for all entities.';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Views::resetAll();
    }
}
