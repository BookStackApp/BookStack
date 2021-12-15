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

    protected $baseEndpoint = '/api/search';

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
        /** @var Page $page */
        $page = Page::query()->first();
        $page->update(['name' => 'name with superuniquevalue within']);
        $page->indexForSearch();

        $resp = $this->actingAsApiAdmin()->getJson($this->baseEndpoint . '?query=superuniquevalue');
        $resp->assertJsonFragment([
            'type' => 'page',
            'url'  => $page->getUrl(),
        ]);
    }

    public function test_all_endpoint_returns_items_with_preview_html()
    {
        /** @var Book $book */
        $book = Book::query()->first();
        $book->update(['name' => 'name with superuniquevalue within', 'description' => 'Description with superuniquevalue within']);
        $book->indexForSearch();

        $resp = $this->actingAsApiAdmin()->getJson($this->baseEndpoint . '?query=superuniquevalue');
        $resp->assertJsonFragment([
            'type'         => 'book',
            'url'          => $book->getUrl(),
            'preview_html' => [
                'name'    => 'name with <strong>superuniquevalue</strong> within',
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
}
