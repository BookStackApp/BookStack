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

}