<?php

namespace BookStack\Console\Commands;

use BookStack\Entities\Models\PageRevision;
use Illuminate\Console\Command;

class ClearRevisions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookstack:clear-revisions
                            {--a|all : Include active update drafts in deletion}
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clear page revisions';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $deleteTypes = $this->option('all') ? ['version', 'update_draft'] : ['version'];
        PageRevision::query()->whereIn('type', $deleteTypes)->delete();
        $this->comment('Revisions deleted');
        return 0;
    }
}
