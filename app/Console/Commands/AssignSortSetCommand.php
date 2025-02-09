<?php

namespace BookStack\Console\Commands;

use BookStack\Entities\Models\Book;
use BookStack\Sorting\BookSorter;
use BookStack\Sorting\SortSet;
use Illuminate\Console\Command;

class AssignSortSetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookstack:assign-sort-set
                            {sort-set=0: ID of the sort set to apply}
                            {--all-books : Apply to all books in the system}
                            {--books-without-sort : Apply to only books without a sort set already assigned}
                            {--books-with-sort= : Apply to only books with the sort of given id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign a sort set to content in the system';

    /**
     * Execute the console command.
     */
    public function handle(BookSorter $sorter): int
    {
        $sortSetId = intval($this->argument('sort-set')) ?? 0;
        if ($sortSetId === 0) {
            return $this->listSortSets();
        }

        $set = SortSet::query()->find($sortSetId);
        if ($this->option('all-books')) {
            $query = Book::query();
        } else if ($this->option('books-without-sort')) {
            $query = Book::query()->whereNull('sort_set_id');
        } else if ($this->option('books-with-sort')) {
            $sortId = intval($this->option('books-with-sort')) ?: 0;
            if (!$sortId) {
                $this->error("Provided --books-with-sort option value is invalid");
                return 1;
            }
            $query = Book::query()->where('sort_set_id', $sortId);
        } else {
            $this->error("Either the --all-books or --books-without-sort option must be provided!");
            return 1;
        }

        if (!$set) {
            $this->error("Sort set of provided id {$sortSetId} not found!");
            return 1;
        }

        $count = $query->clone()->count();
        $this->warn("This will apply sort set [{$set->id}: {$set->name}] to {$count} book(s) and run the sort on each.");
        $confirmed = $this->confirm("Are you sure you want to continue?");

        if (!$confirmed) {
            return 1;
        }

        $processed = 0;
        $query->chunkById(10, function ($books) use ($set, $sorter, $count, &$processed) {
            $max = min($count, ($processed + 10));
            $this->info("Applying to {$processed}-{$max} of {$count} books");
            foreach ($books as $book) {
                $book->sort_set_id = $set->id;
                $book->save();
                $sorter->runBookAutoSort($book);
            }
            $processed = $max;
        });

        $this->info("Sort applied to {$processed} books!");

        return 0;
    }

    protected function listSortSets(): int
    {

        $sets = SortSet::query()->orderBy('id', 'asc')->get();
        $this->error("Sort set ID required!");
        $this->warn("\nAvailable sort sets:");
        foreach ($sets as $set) {
            $this->info("{$set->id}: {$set->name}");
        }

        return 1;
    }
}
