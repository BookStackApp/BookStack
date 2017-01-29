<?php

class SortTest extends BrowserKitTest
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
        $entityRepo = app('\BookStack\Repos\EntityRepo');
        $draft = $entityRepo->getDraftPage($this->book);

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
            ->see('Move Page')
            ->type('book:' . $newBook->id, 'entity_selection')->press('Move Page');

        $page = \BookStack\Page::find($page->id);
        $this->seePageIs($page->getUrl());
        $this->assertTrue($page->book->id == $newBook->id, 'Page book is now the new book');

        $this->visit($newBook->getUrl())
            ->seeInNthElement('.activity-list-item', 0, 'moved page')
            ->seeInNthElement('.activity-list-item', 0, $page->name);
    }

    public function test_chapter_move()
    {
        $chapter = \BookStack\Chapter::first();
        $currentBook = $chapter->book;
        $pageToCheck = $chapter->pages->first();
        $newBook = \BookStack\Book::where('id', '!=', $currentBook->id)->first();

        $this->asAdmin()->visit($chapter->getUrl() . '/move')
            ->see('Move Chapter')
            ->type('book:' . $newBook->id, 'entity_selection')->press('Move Chapter');

        $chapter = \BookStack\Chapter::find($chapter->id);
        $this->seePageIs($chapter->getUrl());
        $this->assertTrue($chapter->book->id === $newBook->id, 'Chapter Book is now the new book');

        $this->visit($newBook->getUrl())
            ->seeInNthElement('.activity-list-item', 0, 'moved chapter')
            ->seeInNthElement('.activity-list-item', 0, $chapter->name);

        $pageToCheck = \BookStack\Page::find($pageToCheck->id);
        $this->assertTrue($pageToCheck->book_id === $newBook->id, 'Chapter child page\'s book id has changed to the new book');
        $this->visit($pageToCheck->getUrl())
            ->see($newBook->name);
    }

}