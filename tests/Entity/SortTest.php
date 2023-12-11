<?php

namespace Tests\Entity;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Repos\PageRepo;
use Tests\TestCase;

class SortTest extends TestCase
{
    public function test_drafts_do_not_show_up()
    {
        $this->asAdmin();
        $pageRepo = app(PageRepo::class);
        $book = $this->entities->book();
        $draft = $pageRepo->getNewDraftPage($book);

        $resp = $this->get($book->getUrl());
        $resp->assertSee($draft->name);

        $resp = $this->get($book->getUrl() . '/sort');
        $resp->assertDontSee($draft->name);
    }

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

    public function test_book_sort_page_shows()
    {
        $bookToSort = $this->entities->book();

        $resp = $this->asAdmin()->get($bookToSort->getUrl());
        $this->withHtml($resp)->assertElementExists('a[href="' . $bookToSort->getUrl('/sort') . '"]');

        $resp = $this->get($bookToSort->getUrl('/sort'));
        $resp->assertStatus(200);
        $resp->assertSee($bookToSort->name);
    }

    public function test_book_sort()
    {
        $oldBook = $this->entities->book();
        $chapterToMove = $this->entities->newChapter(['name' => 'chapter to move'], $oldBook);
        $newBook = $this->entities->newBook(['name' => 'New sort book']);
        $pagesToMove = Page::query()->take(5)->get();

        // Create request data
        $reqData = [
            [
                'id'            => $chapterToMove->id,
                'sort'          => 0,
                'parentChapter' => false,
                'type'          => 'chapter',
                'book'          => $newBook->id,
            ],
        ];
        foreach ($pagesToMove as $index => $page) {
            $reqData[] = [
                'id'            => $page->id,
                'sort'          => $index,
                'parentChapter' => $index === count($pagesToMove) - 1 ? $chapterToMove->id : false,
                'type'          => 'page',
                'book'          => $newBook->id,
            ];
        }

        $sortResp = $this->asEditor()->put($newBook->getUrl() . '/sort', ['sort-tree' => json_encode($reqData)]);
        $sortResp->assertRedirect($newBook->getUrl());
        $sortResp->assertStatus(302);
        $this->assertDatabaseHas('chapters', [
            'id'       => $chapterToMove->id,
            'book_id'  => $newBook->id,
            'priority' => 0,
        ]);
        $this->assertTrue($newBook->chapters()->count() === 1);
        $this->assertTrue($newBook->chapters()->first()->pages()->count() === 1);

        $checkPage = $pagesToMove[1];
        $checkResp = $this->get($checkPage->refresh()->getUrl());
        $checkResp->assertSee($newBook->name);
    }

    public function test_book_sort_makes_no_changes_if_new_chapter_does_not_align_with_new_book()
    {
        $page = $this->entities->pageWithinChapter();
        $otherChapter = Chapter::query()->where('book_id', '!=', $page->book_id)->first();

        $sortData = [
            'id'            => $page->id,
            'sort'          => 0,
            'parentChapter' => $otherChapter->id,
            'type'          => 'page',
            'book'          => $page->book_id,
        ];
        $this->asEditor()->put($page->book->getUrl('/sort'), ['sort-tree' => json_encode([$sortData])])->assertRedirect();

        $this->assertDatabaseHas('pages', [
            'id' => $page->id, 'chapter_id' => $page->chapter_id, 'book_id' => $page->book_id,
        ]);
    }

    public function test_book_sort_makes_no_changes_if_no_view_permissions_on_new_chapter()
    {
        $page = $this->entities->pageWithinChapter();
        /** @var Chapter $otherChapter */
        $otherChapter = Chapter::query()->where('book_id', '!=', $page->book_id)->first();
        $this->permissions->setEntityPermissions($otherChapter);

        $sortData = [
            'id'            => $page->id,
            'sort'          => 0,
            'parentChapter' => $otherChapter->id,
            'type'          => 'page',
            'book'          => $otherChapter->book_id,
        ];
        $this->asEditor()->put($page->book->getUrl('/sort'), ['sort-tree' => json_encode([$sortData])])->assertRedirect();

        $this->assertDatabaseHas('pages', [
            'id' => $page->id, 'chapter_id' => $page->chapter_id, 'book_id' => $page->book_id,
        ]);
    }

    public function test_book_sort_makes_no_changes_if_no_view_permissions_on_new_book()
    {
        $page = $this->entities->pageWithinChapter();
        /** @var Chapter $otherChapter */
        $otherChapter = Chapter::query()->where('book_id', '!=', $page->book_id)->first();
        $editor = $this->users->editor();
        $this->permissions->setEntityPermissions($otherChapter->book, ['update', 'delete'], [$editor->roles()->first()]);

        $sortData = [
            'id'            => $page->id,
            'sort'          => 0,
            'parentChapter' => $otherChapter->id,
            'type'          => 'page',
            'book'          => $otherChapter->book_id,
        ];
        $this->actingAs($editor)->put($page->book->getUrl('/sort'), ['sort-tree' => json_encode([$sortData])])->assertRedirect();

        $this->assertDatabaseHas('pages', [
            'id' => $page->id, 'chapter_id' => $page->chapter_id, 'book_id' => $page->book_id,
        ]);
    }

    public function test_book_sort_makes_no_changes_if_no_update_or_create_permissions_on_new_chapter()
    {
        $page = $this->entities->pageWithinChapter();
        /** @var Chapter $otherChapter */
        $otherChapter = Chapter::query()->where('book_id', '!=', $page->book_id)->first();
        $editor = $this->users->editor();
        $this->permissions->setEntityPermissions($otherChapter, ['view', 'delete'], [$editor->roles()->first()]);

        $sortData = [
            'id'            => $page->id,
            'sort'          => 0,
            'parentChapter' => $otherChapter->id,
            'type'          => 'page',
            'book'          => $otherChapter->book_id,
        ];
        $this->actingAs($editor)->put($page->book->getUrl('/sort'), ['sort-tree' => json_encode([$sortData])])->assertRedirect();

        $this->assertDatabaseHas('pages', [
            'id' => $page->id, 'chapter_id' => $page->chapter_id, 'book_id' => $page->book_id,
        ]);
    }

    public function test_book_sort_makes_no_changes_if_no_update_permissions_on_moved_item()
    {
        $page = $this->entities->pageWithinChapter();
        /** @var Chapter $otherChapter */
        $otherChapter = Chapter::query()->where('book_id', '!=', $page->book_id)->first();
        $editor = $this->users->editor();
        $this->permissions->setEntityPermissions($page, ['view', 'delete'], [$editor->roles()->first()]);

        $sortData = [
            'id'            => $page->id,
            'sort'          => 0,
            'parentChapter' => $otherChapter->id,
            'type'          => 'page',
            'book'          => $otherChapter->book_id,
        ];
        $this->actingAs($editor)->put($page->book->getUrl('/sort'), ['sort-tree' => json_encode([$sortData])])->assertRedirect();

        $this->assertDatabaseHas('pages', [
            'id' => $page->id, 'chapter_id' => $page->chapter_id, 'book_id' => $page->book_id,
        ]);
    }

    public function test_book_sort_makes_no_changes_if_no_delete_permissions_on_moved_item()
    {
        $page = $this->entities->pageWithinChapter();
        /** @var Chapter $otherChapter */
        $otherChapter = Chapter::query()->where('book_id', '!=', $page->book_id)->first();
        $editor = $this->users->editor();
        $this->permissions->setEntityPermissions($page, ['view', 'update'], [$editor->roles()->first()]);

        $sortData = [
            'id'            => $page->id,
            'sort'          => 0,
            'parentChapter' => $otherChapter->id,
            'type'          => 'page',
            'book'          => $otherChapter->book_id,
        ];
        $this->actingAs($editor)->put($page->book->getUrl('/sort'), ['sort-tree' => json_encode([$sortData])])->assertRedirect();

        $this->assertDatabaseHas('pages', [
            'id' => $page->id, 'chapter_id' => $page->chapter_id, 'book_id' => $page->book_id,
        ]);
    }

    public function test_book_sort_item_returns_book_content()
    {
        $bookToSort = $this->entities->book();
        $firstPage = $bookToSort->pages[0];
        $firstChapter = $bookToSort->chapters[0];

        $resp = $this->asAdmin()->get($bookToSort->getUrl() . '/sort-item');

        // Ensure book details are returned
        $resp->assertSee($bookToSort->name);
        $resp->assertSee($firstPage->name);
        $resp->assertSee($firstChapter->name);
    }

    public function test_pages_in_book_show_sorted_by_priority()
    {
        $book = $this->entities->bookHasChaptersAndPages();
        $book->chapters()->forceDelete();
        /** @var Page[] $pages */
        $pages = $book->pages()->where('chapter_id', '=', 0)->take(2)->get();
        $book->pages()->whereNotIn('id', $pages->pluck('id'))->delete();

        $resp = $this->asEditor()->get($book->getUrl());
        $this->withHtml($resp)->assertElementContains('.content-wrap a.page:nth-child(1)', $pages[0]->name);
        $this->withHtml($resp)->assertElementContains('.content-wrap a.page:nth-child(2)', $pages[1]->name);

        $pages[0]->forceFill(['priority' => 10])->save();
        $pages[1]->forceFill(['priority' => 5])->save();

        $resp = $this->asEditor()->get($book->getUrl());
        $this->withHtml($resp)->assertElementContains('.content-wrap a.page:nth-child(1)', $pages[1]->name);
        $this->withHtml($resp)->assertElementContains('.content-wrap a.page:nth-child(2)', $pages[0]->name);
    }
}
