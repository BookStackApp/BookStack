<?php

namespace Tests\Settings;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use Tests\TestCase;

class PageListLimitsTest extends TestCase
{
    public function test_saving_setting_and_loading()
    {
        $resp = $this->asAdmin()->post('/settings/sorting', [
            'setting-lists-page-count-shelves' => '3',
            'setting-lists-page-count-books' => '6',
            'setting-lists-page-count-search' => '9',
        ]);
        $resp->assertRedirect('/settings/sorting');

        $this->assertEquals(3, setting()->getInteger('lists-page-count-shelves', 18));
        $this->assertEquals(6, setting()->getInteger('lists-page-count-books', 18));
        $this->assertEquals(9, setting()->getInteger('lists-page-count-search', 18));

        $resp = $this->get('/settings/sorting');
        $html = $this->withHtml($resp);

        $html->assertFieldHasValue('setting-lists-page-count-shelves', '3');
        $html->assertFieldHasValue('setting-lists-page-count-books', '6');
        $html->assertFieldHasValue('setting-lists-page-count-search', '9');
    }

    public function test_invalid_counts_will_use_default_when_fetched_as_an_integer()
    {
        $this->asAdmin()->post('/settings/sorting', [
            'setting-lists-page-count-shelves' => 'cat',
        ]);

        $this->assertEquals(18, setting()->getInteger('lists-page-count-shelves', 18));
    }

    public function test_shelf_count_is_used_on_shelves_view()
    {
        $resp = $this->asAdmin()->get('/shelves');
        $defaultCount = min(Bookshelf::query()->count(), 18);
        $this->withHtml($resp)->assertElementCount('main [data-entity-type="bookshelf"]', $defaultCount);

        $this->post('/settings/sorting', [
            'setting-lists-page-count-shelves' => '1',
        ]);

        $resp = $this->get('/shelves');
        $this->withHtml($resp)->assertElementCount('main [data-entity-type="bookshelf"]', 1);
    }

    public function test_book_count_is_used_on_books_view()
    {
        $resp = $this->asAdmin()->get('/books');
        $defaultCount = min(Book::query()->count(), 18);
        $this->withHtml($resp)->assertElementCount('main [data-entity-type="book"]', $defaultCount);

        $this->post('/settings/sorting', [
            'setting-lists-page-count-books' => '1',
        ]);

        $resp = $this->get('/books');
        $this->withHtml($resp)->assertElementCount('main [data-entity-type="book"]', 1);
    }

    public function test_search_count_is_used_on_search_view()
    {
        $resp = $this->asAdmin()->get('/search');
        $this->withHtml($resp)->assertElementCount('.entity-list [data-entity-id]', 18);

        $this->post('/settings/sorting', [
            'setting-lists-page-count-search' => '1',
        ]);

        $resp = $this->get('/search');
        $this->withHtml($resp)->assertElementCount('.entity-list [data-entity-id]', 1);
    }
}
