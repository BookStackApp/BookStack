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
        $resp->assertJsonPath('data.0.id', $page->id);
        $resp->assertJsonPath('data.0.book.name', $book->name);
        $resp->assertJsonMissingPath('data.0.chapter');

        $this->permissions->disableEntityInheritedPermissions($book);

        $resp = $this->getJson($this->baseEndpoint . '?query=superextrauniquevalue');
        $resp->assertJsonPath('data.0.id', $page->id);
        $resp->assertJsonMissingPath('data.0.book.name');
    }
}
