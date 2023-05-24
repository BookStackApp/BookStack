<?php

namespace BookStack\Console\Commands;

use BookStack\Activity\Models\Activity;
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

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        Activity::query()->truncate();
        $this->comment('System activity cleared');
        return 0;
    }
}
