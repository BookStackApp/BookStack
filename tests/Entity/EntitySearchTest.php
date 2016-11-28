<?php

use Illuminate\Support\Facades\DB;

class EntitySearchTest extends TestCase
{

    public function test_page_search()
    {
        $book = \BookStack\Book::all()->first();
        $page = $book->pages->first();

        $this->asAdmin()
            ->visit('/')
            ->type($page->name, 'term')
            ->press('header-search-box-button')
            ->see('Search Results')
            ->see($page->name)
            ->click($page->name)
            ->seePageIs($page->getUrl());
    }

    public function test_invalid_page_search()
    {
        $this->asAdmin()
            ->visit('/')
            ->type('<p>test</p>', 'term')
            ->press('header-search-box-button')
            ->see('Search Results')
            ->seeStatusCode(200);
    }

    public function test_empty_search_redirects_back()
    {
        $this->asAdmin()
            ->visit('/')
            ->visit('/search/all')
            ->seePageIs('/');
    }

    public function test_book_search()
    {
        $book = \BookStack\Book::all()->first();
        $page = $book->pages->last();
        $chapter = $book->chapters->last();

        $this->asAdmin()
            ->visit('/search/book/' . $book->id . '?term=' . urlencode($page->name))
            ->see($page->name)

            ->visit('/search/book/' . $book->id  . '?term=' . urlencode($chapter->name))
            ->see($chapter->name);
    }

    public function test_empty_book_search_redirects_back()
    {
        $book = \BookStack\Book::all()->first();
        $this->asAdmin()
            ->visit('/books')
            ->visit('/search/book/' . $book->id . '?term=')
            ->seePageIs('/books');
    }


    public function test_pages_search_listing()
    {
        $page = \BookStack\Page::all()->last();
        $this->asAdmin()->visit('/search/pages?term=' . $page->name)
            ->see('Page Search Results')->see('.entity-list', $page->name);
    }

    public function test_chapters_search_listing()
    {
        $chapter = \BookStack\Chapter::all()->last();
        $this->asAdmin()->visit('/search/chapters?term=' . $chapter->name)
            ->see('Chapter Search Results')->seeInElement('.entity-list', $chapter->name);
    }

    public function test_search_quote_term_preparation()
    {
        $termString = '"192" cat "dog hat"';
        $repo = $this->app[\BookStack\Repos\EntityRepo::class];
        $preparedTerms = $repo->prepareSearchTerms($termString);
        $this->assertTrue($preparedTerms === ['"192"','"dog hat"', 'cat']);
    }

    public function test_books_search_listing()
    {
        $book = \BookStack\Book::all()->last();
        $this->asAdmin()->visit('/search/books?term=' . $book->name)
            ->see('Book Search Results')->see('.entity-list', $book->name);
    }

    public function test_searching_hypen_doesnt_break()
    {
        $this->visit('/search/all?term=cat+-')
            ->seeStatusCode(200);
    }

    public function test_tag_search()
    {
        $newTags = [
            new \BookStack\Tag([
                'name' => 'animal',
                'value' => 'cat'
            ]),
            new \BookStack\Tag([
                'name' => 'color',
                'value' => 'red'
            ])
        ];

        $pageA = \BookStack\Page::first();
        $pageA->tags()->saveMany($newTags);

        $pageB = \BookStack\Page::all()->last();
        $pageB->tags()->create(['name' => 'animal', 'value' => 'dog']);

        $this->asAdmin()->visit('/search/all?term=%5Banimal%5D')
            ->seeLink($pageA->name)
            ->seeLink($pageB->name);

        $this->visit('/search/all?term=%5Bcolor%5D')
            ->seeLink($pageA->name)
            ->dontSeeLink($pageB->name);

        $this->visit('/search/all?term=%5Banimal%3Dcat%5D')
            ->seeLink($pageA->name)
            ->dontSeeLink($pageB->name);

    }

    public function test_ajax_entity_search()
    {
        $page = \BookStack\Page::all()->last();
        $notVisitedPage = \BookStack\Page::first();
        $this->visit($page->getUrl());
        $this->asAdmin()->visit('/ajax/search/entities?term=' . $page->name)->see('.entity-list', $page->name);
        $this->asAdmin()->visit('/ajax/search/entities?types=book&term=' . $page->name)->dontSee('.entity-list', $page->name);
        $this->asAdmin()->visit('/ajax/search/entities')->see('.entity-list', $page->name)->dontSee($notVisitedPage->name);
    }
}
