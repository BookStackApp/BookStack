<?php

class SortTest extends TestCase
{
    protected $book;

    public function setUp()
    {
        parent::setUp();
        $this->book = \BookStack\Book::first();
    }

    public function test_drafts_do_not_show_up()
    {
        $this->asAdmin();
        $pageRepo = app('\BookStack\Repos\PageRepo');
        $draft = $pageRepo->getDraftPage($this->book);

        $this->visit($this->book->getUrl())
            ->see($draft->name)
            ->visit($this->book->getUrl() . '/sort')
            ->dontSee($draft->name);
    }

    public function test_page_move()
    {
        $page = \BookStack\Page::first();
        $currentBook = $page->book;
        $newBook = \BookStack\Book::where('id', '!=', $currentBook->id)->first();
        $this->asAdmin()->visit($page->getUrl() . '/move')
            ->see('Move Page')->see($page->name)
            ->type('book:' . $newBook->id, 'entity_selection')->press('Move Page');

        $page = \BookStack\Page::find($page->id);
        $this->seePageIs($page->getUrl());
        $this->assertTrue($page->book->id == $newBook->id, 'Page book is now the new book');

        $this->visit($newBook->getUrl())
            ->seeInNthElement('.activity-list-item', 0, 'moved page')
            ->seeInNthElement('.activity-list-item', 0, $page->name);
    }

}