<?php

namespace Tests\Entity;

use BookStack\Actions\Tag;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Models\SearchTerm;
use Tests\TestCase;

class EntitySearchTest extends TestCase
{
    public function test_page_search()
    {
        $book = Book::all()->first();
        $page = $book->pages->first();

        $search = $this->asEditor()->get('/search?term=' . urlencode($page->name));
        $search->assertSee('Search Results');
        $search->assertSee($page->name);
    }

    public function test_bookshelf_search()
    {
        $shelf = Bookshelf::first();
        $search = $this->asEditor()->get('/search?term=' . urlencode(mb_substr($shelf->name, 0, 3)) . '  {type:bookshelf}');
        $search->assertStatus(200);
        $search->assertSee($shelf->name);
    }

    public function test_invalid_page_search()
    {
        $resp = $this->asEditor()->get('/search?term=' . urlencode('<p>test</p>'));
        $resp->assertSee('Search Results');
        $resp->assertStatus(200);
        $this->get('/search?term=cat+-')->assertStatus(200);
    }

    public function test_empty_search_shows_search_page()
    {
        $res = $this->asEditor()->get('/search');
        $res->assertStatus(200);
    }

    public function test_searching_accents_and_small_terms()
    {
        $page = $this->newPage(['name' => 'My new test quaffleachits', 'html' => 'some áéííúü¿¡ test content a2 orange dog']);
        $this->asEditor();

        $accentSearch = $this->get('/search?term=' . urlencode('áéíí'));
        $accentSearch->assertStatus(200)->assertSee($page->name);

        $smallSearch = $this->get('/search?term=' . urlencode('a2'));
        $smallSearch->assertStatus(200)->assertSee($page->name);
    }

    public function test_book_search()
    {
        $book = Book::first();
        $page = $book->pages->last();
        $chapter = $book->chapters->last();

        $pageTestResp = $this->asEditor()->get('/search/book/' . $book->id . '?term=' . urlencode($page->name));
        $pageTestResp->assertSee($page->name);

        $chapterTestResp = $this->asEditor()->get('/search/book/' . $book->id . '?term=' . urlencode($chapter->name));
        $chapterTestResp->assertSee($chapter->name);
    }

    public function test_chapter_search()
    {
        $chapter = Chapter::has('pages')->first();
        $page = $chapter->pages[0];

        $pageTestResp = $this->asEditor()->get('/search/chapter/' . $chapter->id . '?term=' . urlencode($page->name));
        $pageTestResp->assertSee($page->name);
    }

    public function test_tag_search()
    {
        $newTags = [
            new Tag([
                'name'  => 'animal',
                'value' => 'cat',
            ]),
            new Tag([
                'name'  => 'color',
                'value' => 'red',
            ]),
        ];

        $pageA = Page::first();
        $pageA->tags()->saveMany($newTags);

        $pageB = Page::all()->last();
        $pageB->tags()->create(['name' => 'animal', 'value' => 'dog']);

        $this->asEditor();
        $tNameSearch = $this->get('/search?term=%5Banimal%5D');
        $tNameSearch->assertSee($pageA->name)->assertSee($pageB->name);

        $tNameSearch2 = $this->get('/search?term=%5Bcolor%5D');
        $tNameSearch2->assertSee($pageA->name)->assertDontSee($pageB->name);

        $tNameValSearch = $this->get('/search?term=%5Banimal%3Dcat%5D');
        $tNameValSearch->assertSee($pageA->name)->assertDontSee($pageB->name);
    }

    public function test_exact_searches()
    {
        $page = $this->newPage(['name' => 'My new test page', 'html' => 'this is a story about an orange donkey']);

        $exactSearchA = $this->asEditor()->get('/search?term=' . urlencode('"story about an orange"'));
        $exactSearchA->assertStatus(200)->assertSee($page->name);

        $exactSearchB = $this->asEditor()->get('/search?term=' . urlencode('"story not about an orange"'));
        $exactSearchB->assertStatus(200)->assertDontSee($page->name);
    }

    public function test_search_filters()
    {
        $page = $this->newPage(['name' => 'My new test quaffleachits', 'html' => 'this is about an orange donkey danzorbhsing']);
        $this->asEditor();
        $editorId = $this->getEditor()->id;
        $editorSlug = $this->getEditor()->slug;

        // Viewed filter searches
        $this->get('/search?term=' . urlencode('danzorbhsing {not_viewed_by_me}'))->assertSee($page->name);
        $this->get('/search?term=' . urlencode('danzorbhsing {viewed_by_me}'))->assertDontSee($page->name);
        $this->get($page->getUrl());
        $this->get('/search?term=' . urlencode('danzorbhsing {not_viewed_by_me}'))->assertDontSee($page->name);
        $this->get('/search?term=' . urlencode('danzorbhsing {viewed_by_me}'))->assertSee($page->name);

        // User filters
        $this->get('/search?term=' . urlencode('danzorbhsing {created_by:me}'))->assertDontSee($page->name);
        $this->get('/search?term=' . urlencode('danzorbhsing {updated_by:me}'))->assertDontSee($page->name);
        $this->get('/search?term=' . urlencode('danzorbhsing {owned_by:me}'))->assertDontSee($page->name);
        $this->get('/search?term=' . urlencode('danzorbhsing {updated_by:' . $editorSlug . '}'))->assertDontSee($page->name);
        $page->created_by = $editorId;
        $page->save();
        $this->get('/search?term=' . urlencode('danzorbhsing {created_by:me}'))->assertSee($page->name);
        $this->get('/search?term=' . urlencode('danzorbhsing {created_by: ' . $editorSlug . '}'))->assertSee($page->name);
        $this->get('/search?term=' . urlencode('danzorbhsing {updated_by:me}'))->assertDontSee($page->name);
        $this->get('/search?term=' . urlencode('danzorbhsing {owned_by:me}'))->assertDontSee($page->name);
        $page->updated_by = $editorId;
        $page->save();
        $this->get('/search?term=' . urlencode('danzorbhsing {updated_by:me}'))->assertSee($page->name);
        $this->get('/search?term=' . urlencode('danzorbhsing {updated_by:' . $editorSlug . '}'))->assertSee($page->name);
        $this->get('/search?term=' . urlencode('danzorbhsing {owned_by:me}'))->assertDontSee($page->name);
        $page->owned_by = $editorId;
        $page->save();
        $this->get('/search?term=' . urlencode('danzorbhsing {owned_by:me}'))->assertSee($page->name);
        $this->get('/search?term=' . urlencode('danzorbhsing {owned_by:' . $editorSlug . '}'))->assertSee($page->name);

        // Content filters
        $this->get('/search?term=' . urlencode('{in_name:danzorbhsing}'))->assertDontSee($page->name);
        $this->get('/search?term=' . urlencode('{in_body:danzorbhsing}'))->assertSee($page->name);
        $this->get('/search?term=' . urlencode('{in_name:test quaffleachits}'))->assertSee($page->name);
        $this->get('/search?term=' . urlencode('{in_body:test quaffleachits}'))->assertDontSee($page->name);

        // Restricted filter
        $this->get('/search?term=' . urlencode('danzorbhsing {is_restricted}'))->assertDontSee($page->name);
        $page->restricted = true;
        $page->save();
        $this->get('/search?term=' . urlencode('danzorbhsing {is_restricted}'))->assertSee($page->name);

        // Date filters
        $this->get('/search?term=' . urlencode('danzorbhsing {updated_after:2037-01-01}'))->assertDontSee($page->name);
        $this->get('/search?term=' . urlencode('danzorbhsing {updated_before:2037-01-01}'))->assertSee($page->name);
        $page->updated_at = '2037-02-01';
        $page->save();
        $this->get('/search?term=' . urlencode('danzorbhsing {updated_after:2037-01-01}'))->assertSee($page->name);
        $this->get('/search?term=' . urlencode('danzorbhsing {updated_before:2037-01-01}'))->assertDontSee($page->name);

        $this->get('/search?term=' . urlencode('danzorbhsing {created_after:2037-01-01}'))->assertDontSee($page->name);
        $this->get('/search?term=' . urlencode('danzorbhsing {created_before:2037-01-01}'))->assertSee($page->name);
        $page->created_at = '2037-02-01';
        $page->save();
        $this->get('/search?term=' . urlencode('danzorbhsing {created_after:2037-01-01}'))->assertSee($page->name);
        $this->get('/search?term=' . urlencode('danzorbhsing {created_before:2037-01-01}'))->assertDontSee($page->name);
    }

    public function test_ajax_entity_search()
    {
        $page = $this->newPage(['name' => 'my ajax search test', 'html' => 'ajax test']);
        $notVisitedPage = Page::first();

        // Visit the page to make popular
        $this->asEditor()->get($page->getUrl());

        $normalSearch = $this->get('/ajax/search/entities?term=' . urlencode($page->name));
        $normalSearch->assertSee($page->name);

        $bookSearch = $this->get('/ajax/search/entities?types=book&term=' . urlencode($page->name));
        $bookSearch->assertDontSee($page->name);

        $defaultListTest = $this->get('/ajax/search/entities');
        $defaultListTest->assertSee($page->name);
        $defaultListTest->assertDontSee($notVisitedPage->name);
    }

    public function test_ajax_entity_serach_shows_breadcrumbs()
    {
        $chapter = Chapter::first();
        $page = $chapter->pages->first();
        $this->asEditor();

        $pageSearch = $this->get('/ajax/search/entities?term=' . urlencode($page->name));
        $pageSearch->assertSee($page->name);
        $pageSearch->assertSee($chapter->getShortName(42));
        $pageSearch->assertSee($page->book->getShortName(42));

        $chapterSearch = $this->get('/ajax/search/entities?term=' . urlencode($chapter->name));
        $chapterSearch->assertSee($chapter->name);
        $chapterSearch->assertSee($chapter->book->getShortName(42));
    }

    public function test_sibling_search_for_pages()
    {
        $chapter = Chapter::query()->with('pages')->first();
        $this->assertGreaterThan(2, count($chapter->pages), 'Ensure we\'re testing with at least 1 sibling');
        $page = $chapter->pages->first();

        $search = $this->actingAs($this->getViewer())->get("/search/entity/siblings?entity_id={$page->id}&entity_type=page");
        $search->assertSuccessful();
        foreach ($chapter->pages as $page) {
            $search->assertSee($page->name);
        }

        $search->assertDontSee($chapter->name);
    }

    public function test_sibling_search_for_pages_without_chapter()
    {
        $page = Page::query()->where('chapter_id', '=', 0)->firstOrFail();
        $bookChildren = $page->book->getDirectChildren();
        $this->assertGreaterThan(2, count($bookChildren), 'Ensure we\'re testing with at least 1 sibling');

        $search = $this->actingAs($this->getViewer())->get("/search/entity/siblings?entity_id={$page->id}&entity_type=page");
        $search->assertSuccessful();
        foreach ($bookChildren as $child) {
            $search->assertSee($child->name);
        }

        $search->assertDontSee($page->book->name);
    }

    public function test_sibling_search_for_chapters()
    {
        $chapter = Chapter::query()->firstOrFail();
        $bookChildren = $chapter->book->getDirectChildren();
        $this->assertGreaterThan(2, count($bookChildren), 'Ensure we\'re testing with at least 1 sibling');

        $search = $this->actingAs($this->getViewer())->get("/search/entity/siblings?entity_id={$chapter->id}&entity_type=chapter");
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

        $search = $this->actingAs($this->getViewer())->get("/search/entity/siblings?entity_id={$book->id}&entity_type=book");
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

        $search = $this->actingAs($this->getViewer())->get("/search/entity/siblings?entity_id={$shelf->id}&entity_type=bookshelf");
        $search->assertSuccessful();
        foreach ($shelves as $expectedShelf) {
            $search->assertSee($expectedShelf->name);
        }
    }

    public function test_search_works_on_updated_page_content()
    {
        $page = Page::query()->first();
        $this->asEditor();

        $update = $this->put($page->getUrl(), [
            'name' => $page->name,
            'html' => '<p>dog pandabearmonster spaghetti</p>',
        ]);

        $search = $this->asEditor()->get('/search?term=pandabearmonster');
        $search->assertStatus(200);
        $search->assertSeeText($page->name);
        $search->assertSee($page->getUrl());
    }

    public function test_search_ranks_common_words_lower()
    {
        $this->newPage(['name' => 'Test page A', 'html' => '<p>dog biscuit dog dog</p>']);
        $this->newPage(['name' => 'Test page B', 'html' => '<p>cat biscuit</p>']);

        $search = $this->asEditor()->get('/search?term=cat+dog+biscuit');
        $search->assertElementContains('.entity-list > .page', 'Test page A', 1);
        $search->assertElementContains('.entity-list > .page', 'Test page B', 2);

        for ($i = 0; $i < 2; $i++) {
            $this->newPage(['name' => 'Test page ' . $i, 'html' => '<p>dog</p>']);
        }

        $search = $this->asEditor()->get('/search?term=cat+dog+biscuit');
        $search->assertElementContains('.entity-list > .page', 'Test page B', 1);
        $search->assertElementContains('.entity-list > .page', 'Test page A', 2);
    }

    public function test_terms_in_headers_have_an_adjusted_index_score()
    {
        $page = $this->newPage(['name' => 'Test page A', 'html' => '
            <p>TermA</p>
            <h1>TermB <strong>TermNested</strong></h1>
            <h2>TermC</h2>
            <h3>TermD</h3>
            <h4>TermE</h4>
            <h5>TermF</h5>
            <h6>TermG</h6>
        ']);

        $scoreByTerm = $page->searchTerms()->pluck('score', 'term');

        $this->assertEquals(1, $scoreByTerm->get('TermA'));
        $this->assertEquals(10, $scoreByTerm->get('TermB'));
        $this->assertEquals(10, $scoreByTerm->get('TermNested'));
        $this->assertEquals(5, $scoreByTerm->get('TermC'));
        $this->assertEquals(4, $scoreByTerm->get('TermD'));
        $this->assertEquals(3, $scoreByTerm->get('TermE'));
        $this->assertEquals(2, $scoreByTerm->get('TermF'));
        // Is 1.5 but stored as integer, rounding up
        $this->assertEquals(2, $scoreByTerm->get('TermG'));
    }

    public function test_name_and_content_terms_are_merged_to_single_score()
    {
        $page = $this->newPage(['name' => 'TermA', 'html' => '
            <p>TermA</p>
        ']);

        $scoreByTerm = $page->searchTerms()->pluck('score', 'term');

        // Scores 40 for being in the name then 1 for being in the content
        $this->assertEquals(41, $scoreByTerm->get('TermA'));
    }

    public function test_tag_names_and_values_are_indexed_for_search()
    {
        $page = $this->newPage(['name' => 'PageA', 'html' => '<p>content</p>', 'tags' => [
            ['name' => 'Animal', 'value' => 'MeowieCat'],
            ['name' => 'SuperImportant'],
        ]]);

        $scoreByTerm = $page->searchTerms()->pluck('score', 'term');
        $this->assertEquals(5, $scoreByTerm->get('MeowieCat'));
        $this->assertEquals(3, $scoreByTerm->get('Animal'));
        $this->assertEquals(3, $scoreByTerm->get('SuperImportant'));
    }
}
