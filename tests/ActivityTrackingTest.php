<?php


class ActivityTrackingTest extends BrowserKitTest
{

    public function test_recently_viewed_books()
    {
        $books = \BookStack\Book::all()->take(10);

        $this->asAdmin()->visit('/books')
            ->dontSeeInElement('#recents', $books[0]->name)
            ->dontSeeInElement('#recents', $books[1]->name)
            ->visit($books[0]->getUrl())
            ->visit($books[1]->getUrl())
            ->visit('/books')
            ->seeInElement('#recents', $books[0]->name)
            ->seeInElement('#recents', $books[1]->name);
    }

    public function test_popular_books()
    {
        $books = \BookStack\Book::all()->take(10);

        $this->asAdmin()->visit('/books')
            ->dontSeeInElement('#popular', $books[0]->name)
            ->dontSeeInElement('#popular', $books[1]->name)
            ->visit($books[0]->getUrl())
            ->visit($books[1]->getUrl())
            ->visit($books[0]->getUrl())
            ->visit('/books')
            ->seeInNthElement('#popular .book', 0, $books[0]->name)
            ->seeInNthElement('#popular .book', 1, $books[1]->name);
    }
}
