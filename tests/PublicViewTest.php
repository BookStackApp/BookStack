<?php

class PublicViewTest extends TestCase
{

    public function testBooksViewable()
    {
        $this->setSettings(['app-public' => 'true']);
        $books = \BookStack\Book::orderBy('name', 'asc')->take(10)->get();
        $bookToVisit = $books[1];

        // Check books index page is showing
        $this->visit('/books')
            ->seeStatusCode(200)
            ->see($books[0]->name)
            // Check indavidual book page is showing and it's child contents are visible.
            ->click($bookToVisit->name)
            ->seePageIs($bookToVisit->getUrl())
            ->see($bookToVisit->name)
            ->see($bookToVisit->chapters()->first()->name);
    }

    public function testChaptersViewable()
    {
        $this->setSettings(['app-public' => 'true']);
        $chapterToVisit = \BookStack\Chapter::first();
        $pageToVisit = $chapterToVisit->pages()->first();

        // Check chapters index page is showing
        $this->visit($chapterToVisit->getUrl())
            ->seeStatusCode(200)
            ->see($chapterToVisit->name)
            // Check indavidual chapter page is showing and it's child contents are visible.
            ->see($pageToVisit->name)
            ->click($pageToVisit->name)
            ->see($chapterToVisit->book->name)
            ->see($chapterToVisit->name)
            ->seePageIs($pageToVisit->getUrl());
    }

}