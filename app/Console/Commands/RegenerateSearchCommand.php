<?php

namespace BookStack\Console\Commands;

use BookStack\Entities\Models\Entity;
use BookStack\Search\SearchIndex;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RegenerateSearchCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookstack:regenerate-search 
                            {--database= : The database connection to use}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-index all content for searching';

    /**
     * Execute the console command.
     */
    public function handle(SearchIndex $searchIndex): int
    {
        $connection = DB::getDefaultConnection();
        if ($this->option('database') !== null) {
            DB::setDefaultConnection($this->option('database'));
        }

        $searchIndex->indexAllEntities(function (Entity $model, int $processed, int $total): void {
            $this->info('Indexed ' . class_basename($model) . ' entries (' . $processed . '/' . $total . ')');
        });

        DB::setDefaultConnection($connection);
        $this->line('Search index regenerated!');

        return static::SUCCESS;
    }
}
