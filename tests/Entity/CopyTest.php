<?php

namespace Tests\Entity;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\BookChild;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Repos\BookRepo;
use Tests\TestCase;

class CopyTest extends TestCase
{
    public function test_book_show_view_has_copy_button()
    {
        $book = $this->entities->book();
        $resp = $this->asEditor()->get($book->getUrl());

        $this->withHtml($resp)->assertElementContains("a[href=\"{$book->getUrl('/copy')}\"]", 'Copy');
    }

    public function test_book_copy_view()
    {
        $book = $this->entities->book();
        $resp = $this->asEditor()->get($book->getUrl('/copy'));

        $resp->assertOk();
        $resp->assertSee('Copy Book');
        $this->withHtml($resp)->assertElementExists("input[name=\"name\"][value=\"{$book->name}\"]");
    }

    public function test_book_copy()
    {
        /** @var Book $book */
        $book = Book::query()->whereHas('chapters')->whereHas('pages')->first();
        $resp = $this->asEditor()->post($book->getUrl('/copy'), ['name' => 'My copy book']);

        /** @var Book $copy */
        $copy = Book::query()->where('name', '=', 'My copy book')->first();

        $resp->assertRedirect($copy->getUrl());
        $this->assertEquals($book->getDirectVisibleChildren()->count(), $copy->getDirectVisibleChildren()->count());

        $this->get($copy->getUrl())->assertSee($book->description_html, false);
    }

    public function test_book_copy_does_not_copy_non_visible_content()
    {
        /** @var Book $book */
        $book = Book::query()->whereHas('chapters')->whereHas('pages')->first();

        // Hide child content
        /** @var BookChild $page */
        foreach ($book->getDirectVisibleChildren() as $child) {
            $this->permissions->setEntityPermissions($child, [], []);
        }

        $this->asEditor()->post($book->getUrl('/copy'), ['name' => 'My copy book']);
        /** @var Book $copy */
        $copy = Book::query()->where('name', '=', 'My copy book')->first();

        $this->assertEquals(0, $copy->getDirectVisibleChildren()->count());
    }

    public function test_book_copy_does_not_copy_pages_or_chapters_if_user_cant_create()
    {
        /** @var Book $book */
        $book = Book::query()->whereHas('chapters')->whereHas('directPages')->whereHas('chapters')->first();
        $viewer = $this->users->viewer();
        $this->permissions->grantUserRolePermissions($viewer, ['book-create-all']);

        $this->actingAs($viewer)->post($book->getUrl('/copy'), ['name' => 'My copy book']);
        /** @var Book $copy */
        $copy = Book::query()->where('name', '=', 'My copy book')->first();

        $this->assertEquals(0, $copy->pages()->count());
        $this->assertEquals(0, $copy->chapters()->count());
    }

    public function test_book_copy_clones_cover_image_if_existing()
    {
        $book = $this->entities->book();
        $bookRepo = $this->app->make(BookRepo::class);
        $coverImageFile = $this->files->uploadedImage('cover.png');
        $bookRepo->updateCoverImage($book, $coverImageFile);

        $this->asEditor()->post($book->getUrl('/copy'), ['name' => 'My copy book'])->assertRedirect();
        /** @var Book $copy */
        $copy = Book::query()->where('name', '=', 'My copy book')->first();

        $this->assertNotNull($copy->coverInfo()->getImage());
        $this->assertNotEquals($book->coverInfo()->getImage()->id, $copy->coverInfo()->getImage()->id);
    }

    public function test_book_copy_adds_book_to_shelves_if_edit_permissions_allows()
    {
        /** @var Bookshelf $shelfA */
        /** @var Bookshelf $shelfB */
        [$shelfA, $shelfB] = Bookshelf::query()->take(2)->get();
        $book = $this->entities->book();

        $shelfA->appendBook($book);
        $shelfB->appendBook($book);

        $viewer = $this->users->viewer();
        $this->permissions->grantUserRolePermissions($viewer, ['book-update-all', 'book-create-all', 'bookshelf-update-all']);
        $this->permissions->setEntityPermissions($shelfB);


        $this->asEditor()->post($book->getUrl('/copy'), ['name' => 'My copy book']);
        /** @var Book $copy */
        $copy = Book::query()->where('name', '=', 'My copy book')->first();

        $this->assertTrue($copy->shelves()->where('id', '=', $shelfA->id)->exists());
        $this->assertFalse($copy->shelves()->where('id', '=', $shelfB->id)->exists());
    }

    public function test_chapter_show_view_has_copy_button()
    {
        $chapter = $this->entities->chapter();

        $resp = $this->asEditor()->get($chapter->getUrl());
        $this->withHtml($resp)->assertElementContains("a[href$=\"{$chapter->getUrl('/copy')}\"]", 'Copy');
    }

    public function test_chapter_copy_view()
    {
        $chapter = $this->entities->chapter();

        $resp = $this->asEditor()->get($chapter->getUrl('/copy'));
        $resp->assertOk();
        $resp->assertSee('Copy Chapter');
        $this->withHtml($resp)->assertElementExists("input[name=\"name\"][value=\"{$chapter->name}\"]");
        $this->withHtml($resp)->assertElementExists('input[name="entity_selection"]');
    }

    public function test_chapter_copy()
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->whereHas('pages')->first();
        /** @var Book $otherBook */
        $otherBook = Book::query()->where('id', '!=', $chapter->book_id)->first();

        $resp = $this->asEditor()->post($chapter->getUrl('/copy'), [
            'name'             => 'My copied chapter',
            'entity_selection' => 'book:' . $otherBook->id,
        ]);

        /** @var Chapter $newChapter */
        $newChapter = Chapter::query()->where('name', '=', 'My copied chapter')->first();

        $resp->assertRedirect($newChapter->getUrl());
        $this->assertEquals($otherBook->id, $newChapter->book_id);
        $this->assertEquals($chapter->pages->count(), $newChapter->pages->count());
    }

    public function test_chapter_copy_does_not_copy_non_visible_pages()
    {
        $chapter = $this->entities->chapterHasPages();

        // Hide pages to all non-admin roles
        /** @var Page $page */
        foreach ($chapter->pages as $page) {
            $this->permissions->setEntityPermissions($page, [], []);
        }

        $this->asEditor()->post($chapter->getUrl('/copy'), [
            'name' => 'My copied chapter',
        ]);

        /** @var Chapter $newChapter */
        $newChapter = Chapter::query()->where('name', '=', 'My copied chapter')->first();
        $this->assertEquals(0, $newChapter->pages()->count());
    }

    public function test_chapter_copy_does_not_copy_pages_if_user_cant_page_create()
    {
        $chapter = $this->entities->chapterHasPages();
        $viewer = $this->users->viewer();
        $this->permissions->grantUserRolePermissions($viewer, ['chapter-create-all']);

        // Lacking permission results in no copied pages
        $this->actingAs($viewer)->post($chapter->getUrl('/copy'), [
            'name' => 'My copied chapter',
        ]);

        /** @var Chapter $newChapter */
        $newChapter = Chapter::query()->where('name', '=', 'My copied chapter')->first();
        $this->assertEquals(0, $newChapter->pages()->count());

        $this->permissions->grantUserRolePermissions($viewer, ['page-create-all']);

        // Having permission rules in copied pages
        $this->actingAs($viewer)->post($chapter->getUrl('/copy'), [
            'name' => 'My copied again chapter',
        ]);

        /** @var Chapter $newChapter2 */
        $newChapter2 = Chapter::query()->where('name', '=', 'My copied again chapter')->first();
        $this->assertEquals($chapter->pages()->count(), $newChapter2->pages()->count());
    }

    public function test_page_copy()
    {
        $page = $this->entities->page();
        $page->html = '<p>This is some test content</p>';
        $page->save();

        $currentBook = $page->book;
        $newBook = Book::where('id', '!=', $currentBook->id)->first();

        $resp = $this->asEditor()->get($page->getUrl('/copy'));
        $resp->assertSee('Copy Page');

        $movePageResp = $this->post($page->getUrl('/copy'), [
            'entity_selection' => 'book:' . $newBook->id,
            'name'             => 'My copied test page',
        ]);
        $pageCopy = Page::where('name', '=', 'My copied test page')->first();

        $movePageResp->assertRedirect($pageCopy->getUrl());
        $this->assertTrue($pageCopy->book->id == $newBook->id, 'Page was copied to correct book');
        $this->assertStringContainsString('This is some test content', $pageCopy->html);
    }

    public function test_page_copy_with_markdown_has_both_html_and_markdown()
    {
        $page = $this->entities->page();
        $page->html = '<h1>This is some test content</h1>';
        $page->markdown = '# This is some test content';
        $page->save();
        $newBook = Book::where('id', '!=', $page->book->id)->first();

        $this->asEditor()->post($page->getUrl('/copy'), [
            'entity_selection' => 'book:' . $newBook->id,
            'name'             => 'My copied test page',
        ]);
        $pageCopy = Page::where('name', '=', 'My copied test page')->first();

        $this->assertStringContainsString('This is some test content', $pageCopy->html);
        $this->assertEquals('# This is some test content', $pageCopy->markdown);
    }

    public function test_page_copy_with_no_destination()
    {
        $page = $this->entities->page();
        $currentBook = $page->book;

        $resp = $this->asEditor()->get($page->getUrl('/copy'));
        $resp->assertSee('Copy Page');

        $movePageResp = $this->post($page->getUrl('/copy'), [
            'name' => 'My copied test page',
        ]);

        $pageCopy = Page::where('name', '=', 'My copied test page')->first();

        $movePageResp->assertRedirect($pageCopy->getUrl());
        $this->assertTrue($pageCopy->book->id == $currentBook->id, 'Page was copied to correct book');
        $this->assertTrue($pageCopy->id !== $page->id, 'Page copy is not the same instance');
    }

    public function test_page_can_be_copied_without_edit_permission()
    {
        $page = $this->entities->page();
        $currentBook = $page->book;
        $newBook = Book::where('id', '!=', $currentBook->id)->first();
        $viewer = $this->users->viewer();

        $resp = $this->actingAs($viewer)->get($page->getUrl());
        $resp->assertDontSee($page->getUrl('/copy'));

        $newBook->owned_by = $viewer->id;
        $newBook->save();
        $this->permissions->grantUserRolePermissions($viewer, ['page-create-own']);
        $this->permissions->regenerateForEntity($newBook);

        $resp = $this->actingAs($viewer)->get($page->getUrl());
        $resp->assertSee($page->getUrl('/copy'));

        $movePageResp = $this->post($page->getUrl('/copy'), [
            'entity_selection' => 'book:' . $newBook->id,
            'name'             => 'My copied test page',
        ]);
        $movePageResp->assertRedirect();

        $this->assertDatabaseHasEntityData('page', [
            'name'       => 'My copied test page',
            'created_by' => $viewer->id,
            'book_id'    => $newBook->id,
        ]);
    }
}
