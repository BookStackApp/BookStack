<?php

namespace Tests\Api;

use BookStack\Activity\Models\Tag;
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

    public function test_all_endpoint_includes_book_and_chapter_titles_when_requested()
    {
        $this->actingAsApiEditor();

        $book = $this->entities->book();
        $chapter = $this->entities->chapter();
        $page = $this->entities->newPage();

        $book->name = 'My Test Book';
        $book->save();

        $chapter->name = 'My Test Chapter';
        $chapter->book_id = $book->id;
        $chapter->save();

        $page->name = 'My Test Page With UniqueSearchTerm';
        $page->book_id = $book->id;
        $page->chapter_id = $chapter->id;
        $page->save();

        $page->indexForSearch();

        // Test without include parameter
        $resp = $this->getJson($this->baseEndpoint . '?query=UniqueSearchTerm');
        $resp->assertOk();
        $resp->assertDontSee('book_title');
        $resp->assertDontSee('chapter_title');

        // Test with include parameter
        $resp = $this->getJson($this->baseEndpoint . '?query=UniqueSearchTerm&include=titles');
        $resp->assertOk();
        $resp->assertJsonFragment([
            'name' => 'My Test Page With UniqueSearchTerm',
            'book_title' => 'My Test Book',
            'chapter_title' => 'My Test Chapter',
            'type' => 'page'
        ]);
    }

    public function test_all_endpoint_validates_include_parameter()
    {
        $this->actingAsApiEditor();

        // Test invalid include value
        $resp = $this->getJson($this->baseEndpoint . '?query=test&include=invalid');
        $resp->assertOk();
        $resp->assertDontSee('book_title');

        // Test SQL injection attempt
        $resp = $this->getJson($this->baseEndpoint . '?query=test&include=titles;DROP TABLE users');
        $resp->assertStatus(422);

        // Test multiple includes
        $resp = $this->getJson($this->baseEndpoint . '?query=test&include=titles,tags');
        $resp->assertOk();
    }

    public function test_all_endpoint_includes_tags_when_requested()
    {
        $this->actingAsApiEditor();

        // Create a page and give it a unique name for search
        $page = $this->entities->page();
        $page->name = 'Page With UniqueSearchTerm';
        $page->save();

        // Save tags to the page using the existing saveTagsToEntity method
        $tags = [
            ['name' => 'SampleTag', 'value' => 'SampleValue']
        ];
        app(\BookStack\Activity\TagRepo::class)->saveTagsToEntity($page, $tags);

        // Ensure the page is indexed for search
        $page->indexForSearch();

        // Test without the "tags" include
        $resp = $this->getJson($this->baseEndpoint . '?query=UniqueSearchTerm');
        $resp->assertOk();
        $resp->assertDontSee('tags');

        // Test with the "tags" include
        $resp = $this->getJson($this->baseEndpoint . '?query=UniqueSearchTerm&include=tags');
        $resp->assertOk();
        
        // Assert that tags are included in the response
        $resp->assertJsonFragment([
            'name' => 'SampleTag',
            'value' => 'SampleValue',
        ]);

        // Optionally: check the structure to match the tag order as well
        $resp->assertJsonStructure([
            'data' => [
                '*' => [
                    'tags' => [
                        '*' => [
                            'name',
                            'value',
                            'order',
                        ],
                    ],
                ],
            ],
        ]);
    }


}
