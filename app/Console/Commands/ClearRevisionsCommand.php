<?php

namespace BookStack\Console\Commands;

use BookStack\Entities\Models\PageRevision;
use BookStack\Entities\Models\PageUpdateDraft;
use Illuminate\Console\Command;

class ClearRevisionsCommand extends Command
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
        PageRevision::query()->delete();
        if ($this->option('all')) {
            PageUpdateDraft::query()->delete();
        }

        $this->comment('Revisions deleted');
        return 0;
    }
}
