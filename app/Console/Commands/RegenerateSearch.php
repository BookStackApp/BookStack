<?php

namespace BookStack\Console\Commands;

use BookStack\Entities\Models\Entity;
use BookStack\Entities\Tools\SearchIndex;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

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

    /**
     * @var SearchIndex
     */
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

        $this->searchIndex->indexAllEntities(function (Entity $model, int $processed, int $total) {
            $this->info('Indexed ' . class_basename($model) . ' entries (' . $processed . '/' . $total . ')');
        });

        DB::setDefaultConnection($connection);
        $this->line('Search index regenerated!');

        return static::SUCCESS;
    }
}
