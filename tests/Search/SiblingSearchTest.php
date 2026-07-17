<?php

namespace Tests\Search;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use Tests\TestCase;

class SiblingSearchTest extends TestCase
{
    public function test_sibling_search_for_pages()
    {
        $chapter = $this->entities->chapterHasPages();
        $this->assertGreaterThan(2, count($chapter->pages), 'Ensure we\'re testing with at least 1 sibling');
        $page = $chapter->pages->first();

        $search = $this->actingAs($this->users->viewer())->get("/search/entity/siblings?entity_id={$page->id}&entity_type=page");
        $search->assertSuccessful();
        foreach ($chapter->pages as $page) {
            $search->assertSee($page->name);
        }

        $search->assertDontSee($chapter->name);
    }

    public function test_sibling_search_for_pages_without_chapter()
    {
        $page = $this->entities->pageNotWithinChapter();
        $bookChildren = $page->book->getDirectVisibleChildren();
        $this->assertGreaterThan(2, count($bookChildren), 'Ensure we\'re testing with at least 1 sibling');

        $search = $this->actingAs($this->users->viewer())->get("/search/entity/siblings?entity_id={$page->id}&entity_type=page");
        $search->assertSuccessful();
        foreach ($bookChildren as $child) {
            $search->assertSee($child->name);
        }

        $search->assertDontSee($page->book->name);
    }

    public function test_sibling_search_for_chapters()
    {
        $chapter = $this->entities->chapter();
        $bookChildren = $chapter->book->getDirectVisibleChildren();
        $this->assertGreaterThan(2, count($bookChildren), 'Ensure we\'re testing with at least 1 sibling');

        $search = $this->actingAs($this->users->viewer())->get("/search/entity/siblings?entity_id={$chapter->id}&entity_type=chapter");
        $search->assertSuccessful();
        foreach ($bookChildren as $child) {
            $search->assertSee($child->name);
        }

        $search->assertDontSee($chapter->book->name);
    }

    public function test_sibling_search_for_books()
    {
        $books = Book::query()->take(10)->get();
        $book = $books->first();
        $this->assertGreaterThan(2, count($books), 'Ensure we\'re testing with at least 1 sibling');

        $search = $this->actingAs($this->users->viewer())->get("/search/entity/siblings?entity_id={$book->id}&entity_type=book");
        $search->assertSuccessful();
        foreach ($books as $expectedBook) {
            $search->assertSee($expectedBook->name);
        }
    }

    public function test_sibling_search_for_shelves()
    {
        $shelves = Bookshelf::query()->take(10)->get();
        $shelf = $shelves->first();
        $this->assertGreaterThan(2, count($shelves), 'Ensure we\'re testing with at least 1 sibling');

        $search = $this->actingAs($this->users->viewer())->get("/search/entity/siblings?entity_id={$shelf->id}&entity_type=bookshelf");
        $search->assertSuccessful();
        foreach ($shelves as $expectedShelf) {
            $search->assertSee($expectedShelf->name);
        }
    }

    public function test_sibling_search_for_books_provides_results_in_alphabetical_order()
    {
        $contextBook = $this->entities->book();
        $searchBook = $this->entities->book();

        $searchBook->name = 'Zebras';
        $searchBook->save();

        $search = $this->actingAs($this->users->viewer())->get("/search/entity/siblings?entity_id={$contextBook->id}&entity_type=book");
        $this->withHtml($search)->assertElementNotContains('a:first-child', 'Zebras');

        $searchBook->name = '1AAAAAAArdvarks';
        $searchBook->save();

        $search = $this->actingAs($this->users->viewer())->get("/search/entity/siblings?entity_id={$contextBook->id}&entity_type=book");
        $this->withHtml($search)->assertElementContains('a:first-child', '1AAAAAAArdvarks');
    }

    public function test_sibling_search_for_shelves_provides_results_in_alphabetical_order()
    {
        $contextShelf = $this->entities->shelf();
        $searchShelf = $this->entities->shelf();

        $searchShelf->name = 'Zebras';
        $searchShelf->save();

        $search = $this->actingAs($this->users->viewer())->get("/search/entity/siblings?entity_id={$contextShelf->id}&entity_type=bookshelf");
        $this->withHtml($search)->assertElementNotContains('a:first-child', 'Zebras');

        $searchShelf->name = '1AAAAAAArdvarks';
        $searchShelf->save();

        $search = $this->actingAs($this->users->viewer())->get("/search/entity/siblings?entity_id={$contextShelf->id}&entity_type=bookshelf");
        $this->withHtml($search)->assertElementContains('a:first-child', '1AAAAAAArdvarks');
    }
}
