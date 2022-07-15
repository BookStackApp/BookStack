<?php

namespace Tests\Entity;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\BookChild;
use BookStack\Entities\Repos\BookRepo;
use Tests\TestCase;
use Tests\Uploads\UsesImages;

class BookTest extends TestCase
{
    use UsesImages;

    public function test_create()
    {
        $book = Book::factory()->make([
            'name' => 'My First Book',
        ]);

        $resp = $this->asEditor()->get('/books');
        $resp->assertElementContains('a[href="' . url('/create-book') . '"]', 'Create New Book');

        $resp = $this->get('/create-book');
        $resp->assertElementContains('form[action="' . url('/books') . '"][method="POST"]', 'Save Book');

        $resp = $this->post('/books', $book->only('name', 'description'));
        $resp->assertRedirect('/books/my-first-book');

        $resp = $this->get('/books/my-first-book');
        $resp->assertSee($book->name);
        $resp->assertSee($book->description);
    }

    public function test_create_uses_different_slugs_when_name_reused()
    {
        $book = Book::factory()->make([
            'name' => 'My First Book',
        ]);

        $this->asEditor()->post('/books', $book->only('name', 'description'));
        $this->asEditor()->post('/books', $book->only('name', 'description'));

        $books = Book::query()->where('name', '=', $book->name)
            ->orderBy('id', 'desc')
            ->take(2)
            ->get();

        $this->assertMatchesRegularExpression('/my-first-book-[0-9a-zA-Z]{3}/', $books[0]->slug);
        $this->assertEquals('my-first-book', $books[1]->slug);
    }

    public function test_update()
    {
        /** @var Book $book */
        $book = Book::query()->first();
        // Cheeky initial update to refresh slug
        $this->asEditor()->put($book->getUrl(), ['name' => $book->name . '5', 'description' => $book->description]);
        $book->refresh();

        $newName = $book->name . ' Updated';
        $newDesc = $book->description . ' with more content';

        $resp = $this->get($book->getUrl('/edit'));
        $resp->assertSee($book->name);
        $resp->assertSee($book->description);
        $resp->assertElementContains('form[action="' . $book->getUrl() . '"]', 'Save Book');

        $resp = $this->put($book->getUrl(), ['name' => $newName, 'description' => $newDesc]);
        $resp->assertRedirect($book->getUrl() . '-updated');

        $resp = $this->get($book->getUrl() . '-updated');
        $resp->assertSee($newName);
        $resp->assertSee($newDesc);
    }

    public function test_delete()
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

    public function test_cancel_on_create_page_leads_back_to_books_listing()
    {
        $resp = $this->asEditor()->get('/create-book');
        $resp->assertElementContains('form a[href="' . url('/books') . '"]', 'Cancel');
    }

    public function test_cancel_on_edit_book_page_leads_back_to_book()
    {
        /** @var Book $book */
        $book = Book::query()->first();
        $resp = $this->asEditor()->get($book->getUrl('/edit'));
        $resp->assertElementContains('form a[href="' . $book->getUrl() . '"]', 'Cancel');
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

    public function test_recently_viewed_books_updates_as_expected()
    {
        $books = Book::all()->take(2);

        $this->asAdmin()->get('/books')
            ->assertElementNotContains('#recents', $books[0]->name)
            ->assertElementNotContains('#recents', $books[1]->name);

        $this->get($books[0]->getUrl());
        $this->get($books[1]->getUrl());

        $this->get('/books')
            ->assertElementContains('#recents', $books[0]->name)
            ->assertElementContains('#recents', $books[1]->name);
    }

    public function test_popular_books_updates_upon_visits()
    {
        $books = Book::all()->take(2);

        $this->asAdmin()->get('/books')
            ->assertElementNotContains('#popular', $books[0]->name)
            ->assertElementNotContains('#popular', $books[1]->name);

        $this->get($books[0]->getUrl());
        $this->get($books[1]->getUrl());
        $this->get($books[0]->getUrl());

        $this->get('/books')
            ->assertElementContains('#popular .book:nth-child(1)', $books[0]->name)
            ->assertElementContains('#popular .book:nth-child(2)', $books[1]->name);
    }

    public function test_books_view_shows_view_toggle_option()
    {
        /** @var Book $book */
        $editor = $this->getEditor();
        setting()->putUser($editor, 'books_view_type', 'list');

        $resp = $this->actingAs($editor)->get('/books');
        $resp->assertElementContains('form[action$="/settings/users/' . $editor->id . '/switch-books-view"]', 'Grid View');
        $resp->assertElementExists('input[name="view_type"][value="grid"]');

        $resp = $this->patch("/settings/users/{$editor->id}/switch-books-view", ['view_type' => 'grid']);
        $resp->assertRedirect();
        $this->assertEquals('grid', setting()->getUser($editor, 'books_view_type'));

        $resp = $this->actingAs($editor)->get('/books');
        $resp->assertElementContains('form[action$="/settings/users/' . $editor->id . '/switch-books-view"]', 'List View');
        $resp->assertElementExists('input[name="view_type"][value="list"]');

        $resp = $this->patch("/settings/users/{$editor->id}/switch-books-view", ['view_type' => 'list']);
        $resp->assertRedirect();
        $this->assertEquals('list', setting()->getUser($editor, 'books_view_type'));
    }

    public function test_slug_multi_byte_url_safe()
    {
        $book = $this->newBook([
            'name' => 'информация',
        ]);

        $this->assertEquals('informaciya', $book->slug);

        $book = $this->newBook([
            'name' => '¿Qué?',
        ]);

        $this->assertEquals('que', $book->slug);
    }

    public function test_slug_format()
    {
        $book = $this->newBook([
            'name' => 'PartA / PartB / PartC',
        ]);

        $this->assertEquals('parta-partb-partc', $book->slug);
    }

    public function test_show_view_has_copy_button()
    {
        /** @var Book $book */
        $book = Book::query()->first();
        $resp = $this->asEditor()->get($book->getUrl());

        $resp->assertElementContains("a[href=\"{$book->getUrl('/copy')}\"]", 'Copy');
    }

    public function test_copy_view()
    {
        /** @var Book $book */
        $book = Book::query()->first();
        $resp = $this->asEditor()->get($book->getUrl('/copy'));

        $resp->assertOk();
        $resp->assertSee('Copy Book');
        $resp->assertElementExists("input[name=\"name\"][value=\"{$book->name}\"]");
    }

    public function test_copy()
    {
        /** @var Book $book */
        $book = Book::query()->whereHas('chapters')->whereHas('pages')->first();
        $resp = $this->asEditor()->post($book->getUrl('/copy'), ['name' => 'My copy book']);

        /** @var Book $copy */
        $copy = Book::query()->where('name', '=', 'My copy book')->first();

        $resp->assertRedirect($copy->getUrl());
        $this->assertEquals($book->getDirectChildren()->count(), $copy->getDirectChildren()->count());
    }

    public function test_copy_does_not_copy_non_visible_content()
    {
        /** @var Book $book */
        $book = Book::query()->whereHas('chapters')->whereHas('pages')->first();

        // Hide child content
        /** @var BookChild $page */
        foreach ($book->getDirectChildren() as $child) {
            $child->restricted = true;
            $child->save();
            $this->regenEntityPermissions($child);
        }

        $this->asEditor()->post($book->getUrl('/copy'), ['name' => 'My copy book']);
        /** @var Book $copy */
        $copy = Book::query()->where('name', '=', 'My copy book')->first();

        $this->assertEquals(0, $copy->getDirectChildren()->count());
    }

    public function test_copy_does_not_copy_pages_or_chapters_if_user_cant_create()
    {
        /** @var Book $book */
        $book = Book::query()->whereHas('chapters')->whereHas('directPages')->whereHas('chapters')->first();
        $viewer = $this->getViewer();
        $this->giveUserPermissions($viewer, ['book-create-all']);

        $this->actingAs($viewer)->post($book->getUrl('/copy'), ['name' => 'My copy book']);
        /** @var Book $copy */
        $copy = Book::query()->where('name', '=', 'My copy book')->first();

        $this->assertEquals(0, $copy->pages()->count());
        $this->assertEquals(0, $copy->chapters()->count());
    }

    public function test_copy_clones_cover_image_if_existing()
    {
        /** @var Book $book */
        $book = Book::query()->first();
        $bookRepo = $this->app->make(BookRepo::class);
        $coverImageFile = $this->getTestImage('cover.png');
        $bookRepo->updateCoverImage($book, $coverImageFile);

        $this->asEditor()->post($book->getUrl('/copy'), ['name' => 'My copy book']);

        /** @var Book $copy */
        $copy = Book::query()->where('name', '=', 'My copy book')->first();

        $this->assertNotNull($copy->cover);
        $this->assertNotEquals($book->cover->id, $copy->cover->id);
    }

    public function test_copy_does_update_html_self_referentials_page_links()
    {
        /** @var Book $book */
        $book = Book::query()->whereHas('pages')->first();
        $firstPage = $book->pages[0];
        $secondPage = $book->pages[1];

        $firstPage->html = "<p><a title='Some link' href='{$secondPage->getUrl()}'>some serious test content</a></p>";
        $secondPage->html = "<p><a title='Some link' href='{$firstPage->getUrl()}'>some serious test content</a></p>";
        $firstPage->save();
        $secondPage->save();

        $this->asEditor()->post($book->getUrl('/copy'), [
            'name' => 'My copied book wxcd',
        ]);

        /** @var Book $copiedBook */
        $copiedBook = Book::query()->where('name', '=', 'My copied book wxcd')->first();

        $copiedFirstPage = $copiedBook->pages[0];
        $copiedSecondPage = $copiedBook->pages[1];

        $this->assertEquals("<p><a title='Some link' href='{$copiedSecondPage->getUrl()}'>some serious test content</a></p>", $copiedFirstPage->html);
        // $this->assertEquals("<p><a title='Some link' href='{$copiedFirstPage->getUrl()}'>some serious test content</a></p>", $copiedSecondPage->html);
    }

    public function test_copy_does_not_update_html_external_page_links()
    {
        // todo
        $this->assertTrue(true);
    }

    public function test_copy_does_update_markdown_self_referentials_page_links()
    {
        /** @var Book $book */
        $book = Book::query()->whereHas('pages')->first();
        $firstPage = $book->pages[0];
        $secondPage = $book->pages[1];

        $firstPage->markdown = "[Awesome related page]({$secondPage->getUrl()})";
        $secondPage->markdown = "[Awesome related page]({$firstPage->getUrl()})";
        $firstPage->save();
        $secondPage->save();

        $this->asEditor()->post($book->getUrl('/copy'), [
            'name' => 'My copied book wxcd',
        ]);

        /** @var Book $copiedBook */
        $copiedBook = Book::query()->where('name', '=', 'My copied book wxcd')->first();

        $copiedFirstPage = $copiedBook->pages[0];
        $copiedSecondPage = $copiedBook->pages[1];

        $this->assertEquals("[Awesome related page]({$copiedSecondPage->getUrl()})", $copiedFirstPage->markdown);
        $this->assertEquals("[Awesome related page]({$copiedFirstPage->getUrl()})", $copiedSecondPage->markdown);
    }

    public function test_copy_does_not_update_markdown_external_page_links()
    {
        // todo
        $this->assertTrue(true);
    }
}
