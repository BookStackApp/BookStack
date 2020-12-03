<?php

namespace BookStack\Console\Commands;

use BookStack\Entities\Tools\SearchIndex;
use DB;
use Illuminate\Console\Command;

class RegenerateSearch extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookstack:regenerate-search {--database= : The database connection to use.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-index all content for searching';

    protected $searchIndex;

    /**
     * Create a new command instance.
     */
    public function __construct(SearchIndex $searchIndex)
    {
        parent::__construct();
        $this->searchIndex = $searchIndex;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $connection = DB::getDefaultConnection();
        if ($this->option('database') !== null) {
            DB::setDefaultConnection($this->option('database'));
        }

        $this->searchIndex->indexAllEntities();
        DB::setDefaultConnection($connection);
        $this->comment('Search index regenerated');
    }
}
