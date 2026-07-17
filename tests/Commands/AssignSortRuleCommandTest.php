<?php

namespace Tests\Commands;

use BookStack\Entities\Models\Book;
use BookStack\Sorting\SortRule;
use Tests\TestCase;

class AssignSortRuleCommandTest extends TestCase
{
    public function test_no_given_sort_rule_lists_options()
    {
        $sortRules = SortRule::factory()->createMany(10);

        $commandRun = $this->artisan('bookstack:assign-sort-rule')
            ->expectsOutputToContain('Sort rule ID required!')
            ->assertExitCode(1);

        foreach ($sortRules as $sortRule) {
            $commandRun->expectsOutputToContain("{$sortRule->id}: {$sortRule->name}");
        }
    }

    public function test_run_without_options_advises_help()
    {
        $this->artisan("bookstack:assign-sort-rule 100")
            ->expectsOutput("No option provided to specify target. Run with the -h option to see all available options.")
            ->assertExitCode(1);
    }

    public function test_run_without_valid_sort_advises_help()
    {
        $this->artisan("bookstack:assign-sort-rule 100342 --all-books")
            ->expectsOutput("Sort rule of provided id 100342 not found!")
            ->assertExitCode(1);
    }

    public function test_confirmation_required()
    {
        $sortRule = SortRule::factory()->create();

        $this->artisan("bookstack:assign-sort-rule {$sortRule->id} --all-books")
            ->expectsConfirmation('Are you sure you want to continue?', 'no')
            ->assertExitCode(1);

        $booksWithSort = Book::query()->whereNotNull('sort_rule_id')->count();
        $this->assertEquals(0, $booksWithSort);
    }

    public function test_assign_to_all_books()
    {
        $sortRule = SortRule::factory()->create();
        $booksWithoutSort = Book::query()->whereNull('sort_rule_id')->count();
        $this->assertGreaterThan(0, $booksWithoutSort);

        $this->artisan("bookstack:assign-sort-rule {$sortRule->id} --all-books")
            ->expectsOutputToContain("This will apply sort rule [{$sortRule->id}: {$sortRule->name}] to {$booksWithoutSort} book(s)")
            ->expectsConfirmation('Are you sure you want to continue?', 'yes')
            ->expectsOutputToContain("Sort applied to {$booksWithoutSort} book(s)")
            ->assertExitCode(0);

        $booksWithoutSort = Book::query()->whereNull('sort_rule_id')->count();
        $this->assertEquals(0, $booksWithoutSort);
    }

    public function test_assign_to_all_books_without_sort()
    {
        $totalBooks = Book::query()->count();
        $book = $this->entities->book();
        $sortRuleA = SortRule::factory()->create();
        $sortRuleB = SortRule::factory()->create();
        $book->sort_rule_id = $sortRuleA->id;
        $book->save();

        $booksWithoutSort = Book::query()->whereNull('sort_rule_id')->count();
        $this->assertEquals($totalBooks, $booksWithoutSort + 1);

        $this->artisan("bookstack:assign-sort-rule {$sortRuleB->id} --books-without-sort")
            ->expectsConfirmation('Are you sure you want to continue?', 'yes')
            ->expectsOutputToContain("Sort applied to {$booksWithoutSort} book(s)")
            ->assertExitCode(0);

        $booksWithoutSort = Book::query()->whereNull('sort_rule_id')->count();
        $this->assertEquals(0, $booksWithoutSort);
        $this->assertEquals($totalBooks, $sortRuleB->books()->count() + 1);
    }

    public function test_assign_to_all_books_with_sort()
    {
        $book = $this->entities->book();
        $sortRuleA = SortRule::factory()->create();
        $sortRuleB = SortRule::factory()->create();
        $book->sort_rule_id = $sortRuleA->id;
        $book->save();

        $this->artisan("bookstack:assign-sort-rule {$sortRuleB->id} --books-with-sort={$sortRuleA->id}")
            ->expectsConfirmation('Are you sure you want to continue?', 'yes')
            ->expectsOutputToContain("Sort applied to 1 book(s)")
            ->assertExitCode(0);

        $book->refresh();
        $this->assertEquals($sortRuleB->id, $book->sort_rule_id);
        $this->assertEquals(1, $sortRuleB->books()->count());
    }

    public function test_assign_to_all_books_with_sort_id_is_validated()
    {
        $this->artisan("bookstack:assign-sort-rule 50 --books-with-sort=beans")
            ->expectsOutputToContain("Provided --books-with-sort option value is invalid")
            ->assertExitCode(1);
    }
}
