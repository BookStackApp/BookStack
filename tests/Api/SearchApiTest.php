<?php

namespace Tests\Api;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use Tests\TestCase;

class SearchApiTest extends TestCase
{
    use TestsApi;

    protected string $baseEndpoint = '/api/search';

    public function test_all_endpoint_returns_search_filtered_results_with_query()
    {
        $this->actingAsApiEditor();
        $uniqueTerm = 'MySuperUniqueTermForSearching';

        /** @var Entity $entityClass */
        foreach ([Page::class, Chapter::class, Book::class, Bookshelf::class] as $entityClass) {
            /** @var Entity $first */
            $first = $entityClass::query()->first();
            $first->update(['name' => $uniqueTerm]);
            $first->indexForSearch();
        }

        $resp = $this->getJson($this->baseEndpoint . '?query=' . $uniqueTerm . '&count=5&page=1');
        $resp->assertJsonCount(4, 'data');
        $resp->assertJsonFragment(['name' => $uniqueTerm, 'type' => 'book']);
        $resp->assertJsonFragment(['name' => $uniqueTerm, 'type' => 'chapter']);
        $resp->assertJsonFragment(['name' => $uniqueTerm, 'type' => 'page']);
        $resp->assertJsonFragment(['name' => $uniqueTerm, 'type' => 'bookshelf']);
    }

    public function test_book_endpoint_limits_results_to_that_book()
    {
        $this->actingAsApiEditor();
        $uniqueTerm = 'MyUniqueBookScopedApiTerm';

        $bookA = $this->entities->book();
        $bookB = $this->entities->book();

        $pageA = $bookA->pages->first();
        $pageA->update(['name' => $uniqueTerm . ' in book a']);
        $pageA->indexForSearch();

        $pageB = $bookB->pages->first();
        $pageB->update(['name' => $uniqueTerm . ' in book b']);
        $pageB->indexForSearch();

        $resp = $this->getJson("/api/search/book/{$bookA->id}?query=" . urlencode($uniqueTerm));
        $resp->assertOk();
        $resp->assertJsonFragment(['name' => $pageA->name]);
        $resp->assertJsonMissing(['name' => $pageB->name]);
        $resp->assertJsonPath('total', 1);
    }

    public function test_book_endpoint_finds_chapters_as_well_as_pages()
    {
        $this->actingAsApiEditor();
        $uniqueTerm = 'MyUniqueBookChapterApiTerm';

        $book = $this->entities->bookHasChaptersAndPages();
        $chapter = $book->chapters->first();
        $chapter->update(['name' => $uniqueTerm . ' chapter']);
        $chapter->indexForSearch();
        $page = $book->pages->first();
        $page->update(['name' => $uniqueTerm . ' page']);
        $page->indexForSearch();

        $resp = $this->getJson("/api/search/book/{$book->id}?query=" . urlencode($uniqueTerm));
        $resp->assertJsonFragment(['name' => 'MyUniqueBookChapterApiTerm chapter', 'type' => 'chapter']);
        $resp->assertJsonFragment(['name' => 'MyUniqueBookChapterApiTerm page', 'type' => 'page']);
    }

    public function test_book_endpoint_cannot_be_widened_to_other_types()
    {
        $this->actingAsApiEditor();
        $uniqueTerm = 'MyUniqueBookTypeWideningTerm';

        $book = $this->entities->book();
        $book->update(['name' => $uniqueTerm . ' the book itself']);
        $book->indexForSearch();

        $page = $book->pages->first();
        $page->update(['name' => $uniqueTerm . ' a page inside']);
        $page->indexForSearch();

        $otherBook = $this->entities->book();
        $otherBook->update(['name' => $uniqueTerm . ' the other book']);
        $otherBook->indexForSearch();

        // A book cannot contain a book, so asking for one returns nothing rather than
        // quietly falling back to searching everything.
        $resp = $this->getJson("/api/search/book/{$book->id}?query=" . urlencode($uniqueTerm . ' {type:book}'));
        $resp->assertOk();
        $resp->assertJsonPath('total', 0);
        $resp->assertDontSee($uniqueTerm);
    }

    public function test_chapter_endpoint_limits_results_to_that_chapter()
    {
        $this->actingAsApiEditor();
        $uniqueTerm = 'MyUniqueChapterScopedApiTerm';

        $chapter = $this->entities->chapterHasPages();
        $inChapter = $chapter->pages->first();
        $inChapter->update(['name' => $uniqueTerm . ' in chapter']);
        $inChapter->indexForSearch();

        $elsewhere = $this->entities->pageNotWithinChapter();
        $elsewhere->update(['name' => $uniqueTerm . ' elsewhere']);
        $elsewhere->indexForSearch();

        $resp = $this->getJson("/api/search/chapter/{$chapter->id}?query=" . urlencode($uniqueTerm));
        $resp->assertJsonFragment(['name' => $inChapter->name]);
        $resp->assertJsonMissing(['name' => $elsewhere->name]);
    }

    public function test_scoped_endpoints_require_a_query()
    {
        $this->actingAsApiEditor();
        $book = $this->entities->book();
        $chapter = $this->entities->chapter();

        $this->getJson("/api/search/book/{$book->id}")->assertStatus(422);
        $this->getJson("/api/search/chapter/{$chapter->id}")->assertStatus(422);
    }

    public function test_all_endpoint_returns_entity_url()
    {
        $page = $this->entities->page();
        $page->update(['name' => 'name with superuniquevalue within']);
        $page->indexForSearch();

        $resp = $this->actingAsApiAdmin()->getJson($this->baseEndpoint . '?query=superuniquevalue');
        $resp->assertJsonFragment([
            'type' => 'page',
            'url' => $page->getUrl(),
        ]);
    }

    public function test_all_endpoint_returns_items_with_preview_html()
    {
        $book = $this->entities->book();
        $book->forceFill(['name' => 'name with superuniquevalue within', 'description' => 'Description with superuniquevalue within'])->save();
        $book->indexForSearch();

        $resp = $this->actingAsApiAdmin()->getJson($this->baseEndpoint . '?query=superuniquevalue');
        $resp->assertJsonFragment([
            'type' => 'book',
            'url' => $book->getUrl(),
            'preview_html' => [
                'name' => 'name with <strong>superuniquevalue</strong> within',
                'content' => 'Description with <strong>superuniquevalue</strong> within',
            ],
        ]);
    }

    public function test_all_endpoint_requires_query_parameter()
    {
        $resp = $this->actingAsApiEditor()->get($this->baseEndpoint);
        $resp->assertStatus(422);

        $resp = $this->actingAsApiEditor()->get($this->baseEndpoint . '?query=myqueryvalue');
        $resp->assertOk();
    }

    public function test_all_endpoint_includes_parent_details_where_visible()
    {
        $page = $this->entities->pageWithinChapter();
        $chapter = $page->chapter;
        $book = $page->book;

        $page->update(['name' => 'name with superextrauniquevalue within']);
        $page->indexForSearch();

        $editor = $this->users->editor();
        $this->actingAsApiEditor();
        $resp = $this->getJson($this->baseEndpoint . '?query=superextrauniquevalue');
        $resp->assertJsonFragment([
            'id' => $page->id,
            'type' => 'page',
            'book' => [
                'id' => $book->id,
                'name' => $book->name,
                'slug' => $book->slug,
            ],
            'chapter' => [
                'id' => $chapter->id,
                'name' => $chapter->name,
                'slug' => $chapter->slug,
            ],
        ]);

        $this->permissions->disableEntityInheritedPermissions($chapter);
        $this->permissions->setEntityPermissions($page, ['view'], [$editor->roles()->first()]);

        $resp = $this->getJson($this->baseEndpoint . '?query=superextrauniquevalue');
        $resp->assertOk();
        $resp->assertJsonPath('data.0.id', $page->id);
        $resp->assertJsonPath('data.0.book.name', $book->name);
        $resp->assertJsonMissingPath('data.0.chapter');

        $this->permissions->disableEntityInheritedPermissions($book);

        $resp = $this->getJson($this->baseEndpoint . '?query=superextrauniquevalue');
        $resp->assertOk();
        $resp->assertJsonPath('data.0.id', $page->id);
        $resp->assertJsonMissingPath('data.0.book.name');
    }
}
