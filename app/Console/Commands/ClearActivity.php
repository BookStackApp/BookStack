<?php

namespace BookStack\Console\Commands;

use BookStack\Actions\Activity;
use Illuminate\Console\Command;

class ClearActivity extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookstack:clear-activity';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear user activity from the system';

    protected $activity;

    /**
     * Create a new command instance.
     *
     * @param Activity $activity
     */
    public function __construct(Activity $activity)
    {
        $this->activity = $activity;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->activity->newQuery()->truncate();
        $this->comment('System activity cleared');
    }
}
