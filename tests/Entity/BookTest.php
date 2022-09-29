<?php

namespace Tests\Entity;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\BookChild;
use BookStack\Entities\Models\Bookshelf;
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
        $this->withHtml($resp)->assertElementContains('a[href="' . url('/create-book') . '"]', 'Create New Book');

        $resp = $this->get('/create-book');
        $this->withHtml($resp)->assertElementContains('form[action="' . url('/books') . '"][method="POST"]', 'Save Book');

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

    public function test_create_sets_tags()
    {
        // Cheeky initial update to refresh slug
        $this->asEditor()->post('books', [
            'name'        => 'My book with tags',
            'description' => 'A book with tags',
            'tags'        => [
                [
                    'name'  => 'Category',
                    'value' => 'Donkey Content',
                ],
                [
                    'name'  => 'Level',
                    'value' => '5',
                ],
            ],
        ]);

        /** @var Book $book */
        $book = Book::query()->where('name', '=', 'My book with tags')->firstOrFail();
        $tags = $book->tags()->get();

        $this->assertEquals(2, $tags->count());
        $this->assertEquals('Donkey Content', $tags[0]->value);
        $this->assertEquals('Level', $tags[1]->name);
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
        $this->withHtml($resp)->assertElementContains('form[action="' . $book->getUrl() . '"]', 'Save Book');

        $resp = $this->put($book->getUrl(), ['name' => $newName, 'description' => $newDesc]);
        $resp->assertRedirect($book->getUrl() . '-updated');

        $resp = $this->get($book->getUrl() . '-updated');
        $resp->assertSee($newName);
        $resp->assertSee($newDesc);
    }

    public function test_update_sets_tags()
    {
        /** @var Book $book */
        $book = Book::query()->first();

        $this->assertEquals(0, $book->tags()->count());

        // Cheeky initial update to refresh slug
        $this->asEditor()->put($book->getUrl(), [
            'name' => $book->name,
            'tags' => [
                [
                    'name'  => 'Category',
                    'value' => 'Dolphin Content',
                ],
                [
                    'name'  => 'Level',
                    'value' => '5',
                ],
            ],
        ]);

        $book->refresh();
        $tags = $book->tags()->get();

        $this->assertEquals(2, $tags->count());
        $this->assertEquals('Dolphin Content', $tags[0]->value);
        $this->assertEquals('Level', $tags[1]->name);
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
        $this->assertNotificationContains($redirectReq, 'Book Successfully Deleted');
    }

    public function test_cancel_on_create_page_leads_back_to_books_listing()
    {
        $resp = $this->asEditor()->get('/create-book');
        $this->withHtml($resp)->assertElementContains('form a[href="' . url('/books') . '"]', 'Cancel');
    }

    public function test_cancel_on_edit_book_page_leads_back_to_book()
    {
        /** @var Book $book */
        $book = Book::query()->first();
        $resp = $this->asEditor()->get($book->getUrl('/edit'));
        $this->withHtml($resp)->assertElementContains('form a[href="' . $book->getUrl() . '"]', 'Cancel');
    }

    public function test_next_previous_navigation_controls_show_within_book_content()
    {
        $book = Book::query()->first();
        $chapter = $book->chapters->first();

        $resp = $this->asEditor()->get($chapter->getUrl());
        $this->withHtml($resp)->assertElementContains('#sibling-navigation', 'Next');
        $this->withHtml($resp)->assertElementContains('#sibling-navigation', substr($chapter->pages[0]->name, 0, 20));

        $resp = $this->get($chapter->pages[0]->getUrl());
        $this->withHtml($resp)->assertElementContains('#sibling-navigation', substr($chapter->pages[1]->name, 0, 20));
        $this->withHtml($resp)->assertElementContains('#sibling-navigation', 'Previous');
        $this->withHtml($resp)->assertElementContains('#sibling-navigation', substr($chapter->name, 0, 20));
    }

    public function test_recently_viewed_books_updates_as_expected()
    {
        $books = Book::all()->take(2);

        $resp = $this->asAdmin()->get('/books');
        $this->withHtml($resp)->assertElementNotContains('#recents', $books[0]->name)
            ->assertElementNotContains('#recents', $books[1]->name);

        $this->get($books[0]->getUrl());
        $this->get($books[1]->getUrl());

        $resp = $this->get('/books');
        $this->withHtml($resp)->assertElementContains('#recents', $books[0]->name)
            ->assertElementContains('#recents', $books[1]->name);
    }

    public function test_popular_books_updates_upon_visits()
    {
        $books = Book::all()->take(2);

        $resp = $this->asAdmin()->get('/books');
        $this->withHtml($resp)->assertElementNotContains('#popular', $books[0]->name)
            ->assertElementNotContains('#popular', $books[1]->name);

        $this->get($books[0]->getUrl());
        $this->get($books[1]->getUrl());
        $this->get($books[0]->getUrl());

        $resp = $this->get('/books');
        $this->withHtml($resp)->assertElementContains('#popular .book:nth-child(1)', $books[0]->name)
            ->assertElementContains('#popular .book:nth-child(2)', $books[1]->name);
    }

    public function test_books_view_shows_view_toggle_option()
    {
        /** @var Book $book */
        $editor = $this->getEditor();
        setting()->putUser($editor, 'books_view_type', 'list');

        $resp = $this->actingAs($editor)->get('/books');
        $this->withHtml($resp)->assertElementContains('form[action$="/settings/users/' . $editor->id . '/switch-books-view"]', 'Grid View');
        $this->withHtml($resp)->assertElementExists('input[name="view_type"][value="grid"]');

        $resp = $this->patch("/settings/users/{$editor->id}/switch-books-view", ['view_type' => 'grid']);
        $resp->assertRedirect();
        $this->assertEquals('grid', setting()->getUser($editor, 'books_view_type'));

        $resp = $this->actingAs($editor)->get('/books');
        $this->withHtml($resp)->assertElementContains('form[action$="/settings/users/' . $editor->id . '/switch-books-view"]', 'List View');
        $this->withHtml($resp)->assertElementExists('input[name="view_type"][value="list"]');

        $resp = $this->patch("/settings/users/{$editor->id}/switch-books-view", ['view_type' => 'list']);
        $resp->assertRedirect();
        $this->assertEquals('list', setting()->getUser($editor, 'books_view_type'));
    }

    public function test_slug_multi_byte_url_safe()
    {
        $book = $this->entities->newBook([
            'name' => 'информация',
        ]);

        $this->assertEquals('informaciya', $book->slug);

        $book = $this->entities->newBook([
            'name' => '¿Qué?',
        ]);

        $this->assertEquals('que', $book->slug);
    }

    public function test_slug_format()
    {
        $book = $this->entities->newBook([
            'name' => 'PartA / PartB / PartC',
        ]);

        $this->assertEquals('parta-partb-partc', $book->slug);
    }

    public function test_show_view_has_copy_button()
    {
        /** @var Book $book */
        $book = Book::query()->first();
        $resp = $this->asEditor()->get($book->getUrl());

        $this->withHtml($resp)->assertElementContains("a[href=\"{$book->getUrl('/copy')}\"]", 'Copy');
    }

    public function test_copy_view()
    {
        /** @var Book $book */
        $book = Book::query()->first();
        $resp = $this->asEditor()->get($book->getUrl('/copy'));

        $resp->assertOk();
        $resp->assertSee('Copy Book');
        $this->withHtml($resp)->assertElementExists("input[name=\"name\"][value=\"{$book->name}\"]");
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
            $this->entities->regenPermissions($child);
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

    public function test_copy_adds_book_to_shelves_if_edit_permissions_allows()
    {
        /** @var Bookshelf $shelfA */
        /** @var Bookshelf $shelfB */
        [$shelfA, $shelfB] = Bookshelf::query()->take(2)->get();
        /** @var Book $book */
        $book = Book::query()->first();

        $shelfA->appendBook($book);
        $shelfB->appendBook($book);

        $viewer = $this->getViewer();
        $this->giveUserPermissions($viewer, ['book-update-all', 'book-create-all', 'bookshelf-update-all']);
        $this->entities->setPermissions($shelfB);


        $this->asEditor()->post($book->getUrl('/copy'), ['name' => 'My copy book']);
        /** @var Book $copy */
        $copy = Book::query()->where('name', '=', 'My copy book')->first();

        $this->assertTrue($copy->shelves()->where('id', '=', $shelfA->id)->exists());
        $this->assertFalse($copy->shelves()->where('id', '=', $shelfB->id)->exists());
    }
}
