<?php

namespace BookStack\Console\Commands;

use BookStack\Activity\Models\View;
use Illuminate\Console\Command;

class ClearViewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookstack:clear-views';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear all view-counts for all entities';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        View::query()->truncate();
        $this->comment('Views cleared');
        return 0;
    }
}
