<?php

namespace Tests\Entity;

use BookStack\Entities\Models\Book;
use Tests\TestCase;

class BookTest extends TestCase
{
    public function test_book_delete()
    {
        $book = Book::query()->whereHas('pages')->whereHas('chapters')->first();
        $this->assertNull($book->deleted_at);
        $pageCount = $book->pages()->count();
        $chapterCount = $book->chapters()->count();

        $deleteViewReq = $this->asEditor()->get($book->getUrl('/delete'));
        $deleteViewReq->assertSeeText('Are you sure you want to delete this book?');

        $deleteReq = $this->delete($book->getUrl());
        $deleteReq->assertRedirect(url('/books'));
        $this->assertActivityExists('book_delete', $book);

        $book->refresh();
        $this->assertNotNull($book->deleted_at);

        $this->assertTrue($book->pages()->count() === 0);
        $this->assertTrue($book->chapters()->count() === 0);
        $this->assertTrue($book->pages()->withTrashed()->count() === $pageCount);
        $this->assertTrue($book->chapters()->withTrashed()->count() === $chapterCount);
        $this->assertTrue($book->deletions()->count() === 1);

        $redirectReq = $this->get($deleteReq->baseResponse->headers->get('location'));
        $redirectReq->assertNotificationContains('Book Successfully Deleted');
    }

    public function test_next_previous_navigation_controls_show_within_book_content()
    {
        $book = Book::query()->first();
        $chapter = $book->chapters->first();

        $resp = $this->asEditor()->get($chapter->getUrl());
        $resp->assertElementContains('#sibling-navigation', 'Next');
        $resp->assertElementContains('#sibling-navigation', substr($chapter->pages[0]->name, 0, 20));

        $resp = $this->get($chapter->pages[0]->getUrl());
        $resp->assertElementContains('#sibling-navigation', substr($chapter->pages[1]->name, 0, 20));
        $resp->assertElementContains('#sibling-navigation', 'Previous');
        $resp->assertElementContains('#sibling-navigation', substr($chapter->name, 0, 20));
    }
}
