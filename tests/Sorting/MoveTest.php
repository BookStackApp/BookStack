<?php

namespace Tests\Sorting;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use Tests\TestCase;

class MoveTest extends TestCase
{
    public function test_page_move_into_book()
    {
        $page = $this->entities->page();
        $currentBook = $page->book;
        $newBook = Book::query()->where('id', '!=', $currentBook->id)->first();

        $resp = $this->asEditor()->get($page->getUrl('/move'));
        $resp->assertSee('Move Page');

        $movePageResp = $this->put($page->getUrl('/move'), [
            'entity_selection' => 'book:' . $newBook->id,
        ]);
        $page->refresh();

        $movePageResp->assertRedirect($page->getUrl());
        $this->assertTrue($page->book->id == $newBook->id, 'Page book is now the new book');

        $newBookResp = $this->get($newBook->getUrl());
        $newBookResp->assertSee('moved page');
        $newBookResp->assertSee($page->name);
    }

    public function test_page_move_into_chapter()
    {
        $page = $this->entities->page();
        $currentBook = $page->book;
        $newBook = Book::query()->where('id', '!=', $currentBook->id)->first();
        $newChapter = $newBook->chapters()->first();

        $movePageResp = $this->actingAs($this->users->editor())->put($page->getUrl('/move'), [
            'entity_selection' => 'chapter:' . $newChapter->id,
        ]);
        $page->refresh();

        $movePageResp->assertRedirect($page->getUrl());
        $this->assertTrue($page->book->id == $newBook->id, 'Page parent is now the new chapter');

        $newChapterResp = $this->get($newChapter->getUrl());
        $newChapterResp->assertSee($page->name);
    }

    public function test_page_move_from_chapter_to_book()
    {
        $oldChapter = Chapter::query()->first();
        $page = $oldChapter->pages()->first();
        $newBook = Book::query()->where('id', '!=', $oldChapter->book_id)->first();

        $movePageResp = $this->actingAs($this->users->editor())->put($page->getUrl('/move'), [
            'entity_selection' => 'book:' . $newBook->id,
        ]);
        $page->refresh();

        $movePageResp->assertRedirect($page->getUrl());
        $this->assertTrue($page->book->id == $newBook->id, 'Page parent is now the new book');
        $this->assertTrue($page->chapter === null, 'Page has no parent chapter');

        $newBookResp = $this->get($newBook->getUrl());
        $newBookResp->assertSee($page->name);
    }

    public function test_page_move_requires_create_permissions_on_parent()
    {
        $page = $this->entities->page();
        $currentBook = $page->book;
        $newBook = Book::query()->where('id', '!=', $currentBook->id)->first();
        $editor = $this->users->editor();

        $this->permissions->setEntityPermissions($newBook, ['view', 'update', 'delete'], $editor->roles->all());

        $movePageResp = $this->actingAs($editor)->put($page->getUrl('/move'), [
            'entity_selection' => 'book:' . $newBook->id,
        ]);
        $this->assertPermissionError($movePageResp);

        $this->permissions->setEntityPermissions($newBook, ['view', 'update', 'delete', 'create'], $editor->roles->all());
        $movePageResp = $this->put($page->getUrl('/move'), [
            'entity_selection' => 'book:' . $newBook->id,
        ]);

        $page->refresh();
        $movePageResp->assertRedirect($page->getUrl());

        $this->assertTrue($page->book->id == $newBook->id, 'Page book is now the new book');
    }

    public function test_page_move_requires_delete_permissions()
    {
        $page = $this->entities->page();
        $currentBook = $page->book;
        $newBook = Book::query()->where('id', '!=', $currentBook->id)->first();
        $editor = $this->users->editor();

        $this->permissions->setEntityPermissions($newBook, ['view', 'update', 'create', 'delete'], $editor->roles->all());
        $this->permissions->setEntityPermissions($page, ['view', 'update', 'create'], $editor->roles->all());

        $movePageResp = $this->actingAs($editor)->put($page->getUrl('/move'), [
            'entity_selection' => 'book:' . $newBook->id,
        ]);
        $this->assertPermissionError($movePageResp);
        $pageView = $this->get($page->getUrl());
        $pageView->assertDontSee($page->getUrl('/move'));

        $this->permissions->setEntityPermissions($page, ['view', 'update', 'create', 'delete'], $editor->roles->all());
        $movePageResp = $this->put($page->getUrl('/move'), [
            'entity_selection' => 'book:' . $newBook->id,
        ]);

        $page->refresh();
        $movePageResp->assertRedirect($page->getUrl());
        $this->assertTrue($page->book->id == $newBook->id, 'Page book is now the new book');
    }

    public function test_chapter_move()
    {
        $chapter = $this->entities->chapter();
        $currentBook = $chapter->book;
        $pageToCheck = $chapter->pages->first();
        $newBook = Book::query()->where('id', '!=', $currentBook->id)->first();

        $chapterMoveResp = $this->asEditor()->get($chapter->getUrl('/move'));
        $chapterMoveResp->assertSee('Move Chapter');

        $moveChapterResp = $this->put($chapter->getUrl('/move'), [
            'entity_selection' => 'book:' . $newBook->id,
        ]);

        $chapter = Chapter::query()->find($chapter->id);
        $moveChapterResp->assertRedirect($chapter->getUrl());
        $this->assertTrue($chapter->book->id === $newBook->id, 'Chapter Book is now the new book');

        $newBookResp = $this->get($newBook->getUrl());
        $newBookResp->assertSee('moved chapter');
        $newBookResp->assertSee($chapter->name);

        $pageToCheck = Page::query()->find($pageToCheck->id);
        $this->assertTrue($pageToCheck->book_id === $newBook->id, 'Chapter child page\'s book id has changed to the new book');
        $pageCheckResp = $this->get($pageToCheck->getUrl());
        $pageCheckResp->assertSee($newBook->name);
    }

    public function test_chapter_move_requires_delete_permissions()
    {
        $chapter = $this->entities->chapter();
        $currentBook = $chapter->book;
        $newBook = Book::query()->where('id', '!=', $currentBook->id)->first();
        $editor = $this->users->editor();

        $this->permissions->setEntityPermissions($newBook, ['view', 'update', 'create', 'delete'], $editor->roles->all());
        $this->permissions->setEntityPermissions($chapter, ['view', 'update', 'create'], $editor->roles->all());

        $moveChapterResp = $this->actingAs($editor)->put($chapter->getUrl('/move'), [
            'entity_selection' => 'book:' . $newBook->id,
        ]);
        $this->assertPermissionError($moveChapterResp);
        $pageView = $this->get($chapter->getUrl());
        $pageView->assertDontSee($chapter->getUrl('/move'));

        $this->permissions->setEntityPermissions($chapter, ['view', 'update', 'create', 'delete'], $editor->roles->all());
        $moveChapterResp = $this->put($chapter->getUrl('/move'), [
            'entity_selection' => 'book:' . $newBook->id,
        ]);

        $chapter = Chapter::query()->find($chapter->id);
        $moveChapterResp->assertRedirect($chapter->getUrl());
        $this->assertTrue($chapter->book->id == $newBook->id, 'Page book is now the new book');
    }

    public function test_chapter_move_requires_create_permissions_in_new_book()
    {
        $chapter = $this->entities->chapter();
        $currentBook = $chapter->book;
        $newBook = Book::query()->where('id', '!=', $currentBook->id)->first();
        $editor = $this->users->editor();

        $this->permissions->setEntityPermissions($newBook, ['view', 'update', 'delete'], [$editor->roles->first()]);
        $this->permissions->setEntityPermissions($chapter, ['view', 'update', 'create', 'delete'], [$editor->roles->first()]);

        $moveChapterResp = $this->actingAs($editor)->put($chapter->getUrl('/move'), [
            'entity_selection' => 'book:' . $newBook->id,
        ]);
        $this->assertPermissionError($moveChapterResp);

        $this->permissions->setEntityPermissions($newBook, ['view', 'update', 'create', 'delete'], [$editor->roles->first()]);
        $moveChapterResp = $this->put($chapter->getUrl('/move'), [
            'entity_selection' => 'book:' . $newBook->id,
        ]);

        $chapter = Chapter::query()->find($chapter->id);
        $moveChapterResp->assertRedirect($chapter->getUrl());
        $this->assertTrue($chapter->book->id == $newBook->id, 'Page book is now the new book');
    }

    public function test_chapter_move_changes_book_for_deleted_pages_within()
    {
        /** @var Chapter $chapter */
        $chapter = Chapter::query()->whereHas('pages')->first();
        $currentBook = $chapter->book;
        $pageToCheck = $chapter->pages->first();
        $newBook = Book::query()->where('id', '!=', $currentBook->id)->first();

        $pageToCheck->delete();

        $this->asEditor()->put($chapter->getUrl('/move'), [
            'entity_selection' => 'book:' . $newBook->id,
        ]);

        $pageToCheck->refresh();
        $this->assertEquals($newBook->id, $pageToCheck->book_id);
    }
}
