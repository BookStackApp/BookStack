<?php

namespace Commands;

use BookStack\Entities\Models\Book;
use BookStack\Sorting\SortSet;
use Tests\TestCase;

class AssignSortSetCommandTest extends TestCase
{
    public function test_no_given_sort_set_lists_options()
    {
        $sortSets = SortSet::factory()->createMany(10);

        $commandRun = $this->artisan('bookstack:assign-sort-set')
            ->expectsOutputToContain('Sort set ID required!')
            ->assertExitCode(1);

        foreach ($sortSets as $sortSet) {
            $commandRun->expectsOutputToContain("{$sortSet->id}: {$sortSet->name}");
        }
    }

    public function test_run_without_options_advises_help()
    {
        $this->artisan("bookstack:assign-sort-set 100")
            ->expectsOutput("No option provided to specify target. Run with the -h option to see all available options.")
            ->assertExitCode(1);
    }

    public function test_run_without_valid_sort_advises_help()
    {
        $this->artisan("bookstack:assign-sort-set 100342 --all-books")
            ->expectsOutput("Sort set of provided id 100342 not found!")
            ->assertExitCode(1);
    }

    public function test_confirmation_required()
    {
        $sortSet = SortSet::factory()->create();

        $this->artisan("bookstack:assign-sort-set {$sortSet->id} --all-books")
            ->expectsConfirmation('Are you sure you want to continue?', 'no')
            ->assertExitCode(1);

        $booksWithSort = Book::query()->whereNotNull('sort_set_id')->count();
        $this->assertEquals(0, $booksWithSort);
    }

    public function test_assign_to_all_books()
    {
        $sortSet = SortSet::factory()->create();
        $booksWithoutSort = Book::query()->whereNull('sort_set_id')->count();
        $this->assertGreaterThan(0, $booksWithoutSort);

        $this->artisan("bookstack:assign-sort-set {$sortSet->id} --all-books")
            ->expectsOutputToContain("This will apply sort set [{$sortSet->id}: {$sortSet->name}] to {$booksWithoutSort} book(s)")
            ->expectsConfirmation('Are you sure you want to continue?', 'yes')
            ->expectsOutputToContain("Sort applied to {$booksWithoutSort} book(s)")
            ->assertExitCode(0);

        $booksWithoutSort = Book::query()->whereNull('sort_set_id')->count();
        $this->assertEquals(0, $booksWithoutSort);
    }

    public function test_assign_to_all_books_without_sort()
    {
        $totalBooks = Book::query()->count();
        $book = $this->entities->book();
        $sortSetA = SortSet::factory()->create();
        $sortSetB = SortSet::factory()->create();
        $book->sort_set_id = $sortSetA->id;
        $book->save();

        $booksWithoutSort = Book::query()->whereNull('sort_set_id')->count();
        $this->assertEquals($totalBooks, $booksWithoutSort + 1);

        $this->artisan("bookstack:assign-sort-set {$sortSetB->id} --books-without-sort")
            ->expectsConfirmation('Are you sure you want to continue?', 'yes')
            ->expectsOutputToContain("Sort applied to {$booksWithoutSort} book(s)")
            ->assertExitCode(0);

        $booksWithoutSort = Book::query()->whereNull('sort_set_id')->count();
        $this->assertEquals(0, $booksWithoutSort);
        $this->assertEquals($totalBooks, $sortSetB->books()->count() + 1);
    }

    public function test_assign_to_all_books_with_sort()
    {
        $book = $this->entities->book();
        $sortSetA = SortSet::factory()->create();
        $sortSetB = SortSet::factory()->create();
        $book->sort_set_id = $sortSetA->id;
        $book->save();

        $this->artisan("bookstack:assign-sort-set {$sortSetB->id} --books-with-sort={$sortSetA->id}")
            ->expectsConfirmation('Are you sure you want to continue?', 'yes')
            ->expectsOutputToContain("Sort applied to 1 book(s)")
            ->assertExitCode(0);

        $book->refresh();
        $this->assertEquals($sortSetB->id, $book->sort_set_id);
        $this->assertEquals(1, $sortSetB->books()->count());
    }

    public function test_assign_to_all_books_with_sort_id_is_validated()
    {
        $this->artisan("bookstack:assign-sort-set 50 --books-with-sort=beans")
            ->expectsOutputToContain("Provided --books-with-sort option value is invalid")
            ->assertExitCode(1);
    }
}
