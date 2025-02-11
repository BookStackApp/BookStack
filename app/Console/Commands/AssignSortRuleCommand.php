<?php

namespace BookStack\Console\Commands;

use BookStack\Entities\Models\Book;
use BookStack\Sorting\BookSorter;
use BookStack\Sorting\SortRule;
use Illuminate\Console\Command;

class AssignSortRuleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookstack:assign-sort-rule
                            {sort-rule=0: ID of the sort rule to apply}
                            {--all-books : Apply to all books in the system}
                            {--books-without-sort : Apply to only books without a sort rule already assigned}
                            {--books-with-sort= : Apply to only books with the sort rule of given id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign a sort rule to content in the system';

    /**
     * Execute the console command.
     */
    public function handle(BookSorter $sorter): int
    {
        $sortRuleId = intval($this->argument('sort-rule')) ?? 0;
        if ($sortRuleId === 0) {
            return $this->listSortRules();
        }

        $rule = SortRule::query()->find($sortRuleId);
        if ($this->option('all-books')) {
            $query = Book::query();
        } else if ($this->option('books-without-sort')) {
            $query = Book::query()->whereNull('sort_rule_id');
        } else if ($this->option('books-with-sort')) {
            $sortId = intval($this->option('books-with-sort')) ?: 0;
            if (!$sortId) {
                $this->error("Provided --books-with-sort option value is invalid");
                return 1;
            }
            $query = Book::query()->where('sort_rule_id', $sortId);
        } else {
            $this->error("No option provided to specify target. Run with the -h option to see all available options.");
            return 1;
        }

        if (!$rule) {
            $this->error("Sort rule of provided id {$sortRuleId} not found!");
            return 1;
        }

        $count = $query->clone()->count();
        $this->warn("This will apply sort rule [{$rule->id}: {$rule->name}] to {$count} book(s) and run the sort on each.");
        $confirmed = $this->confirm("Are you sure you want to continue?");

        if (!$confirmed) {
            return 1;
        }

        $processed = 0;
        $query->chunkById(10, function ($books) use ($rule, $sorter, $count, &$processed) {
            $max = min($count, ($processed + 10));
            $this->info("Applying to {$processed}-{$max} of {$count} books");
            foreach ($books as $book) {
                $book->sort_rule_id = $rule->id;
                $book->save();
                $sorter->runBookAutoSort($book);
            }
            $processed = $max;
        });

        $this->info("Sort applied to {$processed} book(s)!");

        return 0;
    }

    protected function listSortRules(): int
    {

        $rules = SortRule::query()->orderBy('id', 'asc')->get();
        $this->error("Sort rule ID required!");
        $this->warn("\nAvailable sort rules:");
        foreach ($rules as $rule) {
            $this->info("{$rule->id}: {$rule->name}");
        }

        return 1;
    }
}
