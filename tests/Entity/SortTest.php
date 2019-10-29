<?php namespace Tests;

use BookStack\Entities\Book;
use BookStack\Entities\Chapter;
use BookStack\Entities\Page;
use BookStack\Entities\Repos\PageRepo;

class SortTest extends TestCase
{
    protected $book;

    public function setUp(): void
    {
        parent::setUp();
        $this->book = Book::first();
    }

    public function test_drafts_do_not_show_up()
    {
        $this->asAdmin();
        $pageRepo = app(PageRepo::class);
        $draft = $pageRepo->getNewDraftPage($this->book);

        $resp = $this->get($this->book->getUrl());
        $resp->assertSee($draft->name);

        $resp = $this->get($this->book->getUrl() . '/sort');
        $resp->assertDontSee($draft->name);
    }

    public function test_page_move_into_book()
    {
        $page = Page::first();
        $currentBook = $page->book;
        $newBook = Book::where('id', '!=', $currentBook->id)->first();

        $resp = $this->asEditor()->get($page->getUrl('/move'));
        $resp->assertSee('Move Page');

        $movePageResp = $this->put($page->getUrl('/move'), [
            'entity_selection' => 'book:' . $newBook->id
        ]);
        $page = Page::find($page->id);

        $movePageResp->assertRedirect($page->getUrl());
        $this->assertTrue($page->book->id == $newBook->id, 'Page book is now the new book');

        $newBookResp = $this->get($newBook->getUrl());
        $newBookResp->assertSee('moved page');
        $newBookResp->assertSee($page->name);
    }

    public function test_page_move_into_chapter()
    {
        $page = Page::first();
        $currentBook = $page->book;
        $newBook = Book::where('id', '!=', $currentBook->id)->first();
        $newChapter = $newBook->chapters()->first();

        $movePageResp = $this->actingAs($this->getEditor())->put($page->getUrl('/move'), [
            'entity_selection' => 'chapter:' . $newChapter->id
        ]);
        $page = Page::find($page->id);

        $movePageResp->assertRedirect($page->getUrl());
        $this->assertTrue($page->book->id == $newBook->id, 'Page parent is now the new chapter');

        $newChapterResp = $this->get($newChapter->getUrl());
        $newChapterResp->assertSee($page->name);
    }

    public function test_page_move_from_chapter_to_book()
    {
        $oldChapter = Chapter::first();
        $page = $oldChapter->pages()->first();
        $newBook = Book::where('id', '!=', $oldChapter->book_id)->first();

        $movePageResp = $this->actingAs($this->getEditor())->put($page->getUrl('/move'), [
            'entity_selection' => 'book:' . $newBook->id
        ]);
        $page = Page::find($page->id);

        $movePageResp->assertRedirect($page->getUrl());
        $this->assertTrue($page->book->id == $newBook->id, 'Page parent is now the new book');
        $this->assertTrue($page->chapter === null, 'Page has no parent chapter');

        $newBookResp = $this->get($newBook->getUrl());
        $newBookResp->assertSee($page->name);
    }

    public function test_page_move_requires_create_permissions_on_parent()
    {
        $page = Page::first();
        $currentBook = $page->book;
        $newBook = Book::where('id', '!=', $currentBook->id)->first();
        $editor = $this->getEditor();

        $this->setEntityRestrictions($newBook, ['view', 'update', 'delete'], $editor->roles);

        $movePageResp = $this->actingAs($editor)->put($page->getUrl('/move'), [
            'entity_selection' => 'book:' . $newBook->id
        ]);
        $this->assertPermissionError($movePageResp);

        $this->setEntityRestrictions($newBook, ['view', 'update', 'delete', 'create'], $editor->roles);
        $movePageResp = $this->put($page->getUrl('/move'), [
            'entity_selection' => 'book:' . $newBook->id
        ]);

        $page = Page::find($page->id);
        $movePageResp->assertRedirect($page->getUrl());

        $this->assertTrue($page->book->id == $newBook->id, 'Page book is now the new book');
    }

    public function test_page_move_requires_delete_permissions()
    {
        $page = Page::first();
        $currentBook = $page->book;
        $newBook = Book::where('id', '!=', $currentBook->id)->first();
        $editor = $this->getEditor();

        $this->setEntityRestrictions($newBook, ['view', 'update', 'create', 'delete'], $editor->roles);
        $this->setEntityRestrictions($page, ['view', 'update', 'create'], $editor->roles);

        $movePageResp = $this->actingAs($editor)->put($page->getUrl('/move'), [
            'entity_selection' => 'book:' . $newBook->id
        ]);
        $this->assertPermissionError($movePageResp);
        $pageView = $this->get($page->getUrl());
        $pageView->assertDontSee($page->getUrl('/move'));

        $this->setEntityRestrictions($page, ['view', 'update', 'create', 'delete'], $editor->roles);
        $movePageResp = $this->put($page->getUrl('/move'), [
            'entity_selection' => 'book:' . $newBook->id
        ]);

        $page = Page::find($page->id);
        $movePageResp->assertRedirect($page->getUrl());
        $this->assertTrue($page->book->id == $newBook->id, 'Page book is now the new book');
    }

    public function test_chapter_move()
    {
        $chapter = Chapter::first();
        $currentBook = $chapter->book;
        $pageToCheck = $chapter->pages->first();
        $newBook = Book::where('id', '!=', $currentBook->id)->first();

        $chapterMoveResp = $this->asEditor()->get($chapter->getUrl('/move'));
        $chapterMoveResp->assertSee('Move Chapter');

        $moveChapterResp = $this->put($chapter->getUrl('/move'), [
            'entity_selection' => 'book:' . $newBook->id
        ]);

        $chapter = Chapter::find($chapter->id);
        $moveChapterResp->assertRedirect($chapter->getUrl());
        $this->assertTrue($chapter->book->id === $newBook->id, 'Chapter Book is now the new book');

        $newBookResp = $this->get($newBook->getUrl());
        $newBookResp->assertSee('moved chapter');
        $newBookResp->assertSee($chapter->name);

        $pageToCheck = Page::find($pageToCheck->id);
        $this->assertTrue($pageToCheck->book_id === $newBook->id, 'Chapter child page\'s book id has changed to the new book');
        $pageCheckResp = $this->get($pageToCheck->getUrl());
        $pageCheckResp->assertSee($newBook->name);
    }

    public function test_chapter_move_requires_delete_permissions()
    {
        $chapter = Chapter::first();
        $currentBook = $chapter->book;
        $newBook = Book::where('id', '!=', $currentBook->id)->first();
        $editor = $this->getEditor();

        $this->setEntityRestrictions($newBook, ['view', 'update', 'create', 'delete'], $editor->roles);
        $this->setEntityRestrictions($chapter, ['view', 'update', 'create'], $editor->roles);

        $moveChapterResp = $this->actingAs($editor)->put($chapter->getUrl('/move'), [
            'entity_selection' => 'book:' . $newBook->id
        ]);
        $this->assertPermissionError($moveChapterResp);
        $pageView = $this->get($chapter->getUrl());
        $pageView->assertDontSee($chapter->getUrl('/move'));

        $this->setEntityRestrictions($chapter, ['view', 'update', 'create', 'delete'], $editor->roles);
        $moveChapterResp = $this->put($chapter->getUrl('/move'), [
            'entity_selection' => 'book:' . $newBook->id
        ]);

        $chapter = Chapter::find($chapter->id);
        $moveChapterResp->assertRedirect($chapter->getUrl());
        $this->assertTrue($chapter->book->id == $newBook->id, 'Page book is now the new book');
    }

    public function test_book_sort()
    {
        $oldBook = Book::query()->first();
        $chapterToMove = $this->newChapter(['name' => 'chapter to move'], $oldBook);
        $newBook = $this->newBook(['name' => 'New sort book']);
        $pagesToMove = Page::query()->take(5)->get();

        // Create request data
        $reqData = [
            [
                'id' => $chapterToMove->id,
                'sort' => 0,
                'parentChapter' => false,
                'type' => 'chapter',
                'book' => $newBook->id
            ]
        ];
        foreach ($pagesToMove as $index => $page) {
            $reqData[] = [
                'id' => $page->id,
                'sort' => $index,
                'parentChapter' => $index === count($pagesToMove) - 1 ? $chapterToMove->id : false,
                'type' => 'page',
                'book' => $newBook->id
            ];
        }

        $sortResp = $this->asEditor()->put($newBook->getUrl() . '/sort', ['sort-tree' => json_encode($reqData)]);
        $sortResp->assertRedirect($newBook->getUrl());
        $sortResp->assertStatus(302);
        $this->assertDatabaseHas('chapters', [
            'id' => $chapterToMove->id,
            'book_id' => $newBook->id,
            'priority' => 0
        ]);
        $this->assertTrue($newBook->chapters()->count() === 1);
        $this->assertTrue($newBook->chapters()->first()->pages()->count() === 1);

        $checkPage = $pagesToMove[1];
        $checkResp = $this->get(Page::find($checkPage->id)->getUrl());
        $checkResp->assertSee($newBook->name);
    }

    public function test_page_copy()
    {
        $page = Page::first();
        $currentBook = $page->book;
        $newBook = Book::where('id', '!=', $currentBook->id)->first();

        $resp = $this->asEditor()->get($page->getUrl('/copy'));
        $resp->assertSee('Copy Page');

        $movePageResp = $this->post($page->getUrl('/copy'), [
            'entity_selection' => 'book:' . $newBook->id,
            'name' => 'My copied test page'
        ]);
        $pageCopy = Page::where('name', '=', 'My copied test page')->first();

        $movePageResp->assertRedirect($pageCopy->getUrl());
        $this->assertTrue($pageCopy->book->id == $newBook->id, 'Page was copied to correct book');
    }

    public function test_page_copy_with_no_destination()
    {
        $page = Page::first();
        $currentBook = $page->book;

        $resp = $this->asEditor()->get($page->getUrl('/copy'));
        $resp->assertSee('Copy Page');

        $movePageResp = $this->post($page->getUrl('/copy'), [
            'name' => 'My copied test page'
        ]);

        $pageCopy = Page::where('name', '=', 'My copied test page')->first();

        $movePageResp->assertRedirect($pageCopy->getUrl());
        $this->assertTrue($pageCopy->book->id == $currentBook->id, 'Page was copied to correct book');
        $this->assertTrue($pageCopy->id !== $page->id, 'Page copy is not the same instance');
    }

    public function test_page_can_be_copied_without_edit_permission()
    {
        $page = Page::first();
        $currentBook = $page->book;
        $newBook = Book::where('id', '!=', $currentBook->id)->first();
        $viewer = $this->getViewer();

        $resp = $this->actingAs($viewer)->get($page->getUrl());
        $resp->assertDontSee($page->getUrl('/copy'));

        $newBook->created_by = $viewer->id;
        $newBook->save();
        $this->giveUserPermissions($viewer, ['page-create-own']);
        $this->regenEntityPermissions($newBook);

        $resp = $this->actingAs($viewer)->get($page->getUrl());
        $resp->assertSee($page->getUrl('/copy'));

        $movePageResp = $this->post($page->getUrl('/copy'), [
            'entity_selection' => 'book:' . $newBook->id,
            'name' => 'My copied test page'
        ]);
        $movePageResp->assertRedirect();

        $this->assertDatabaseHas('pages', [
            'name' => 'My copied test page',
            'created_by' => $viewer->id,
            'book_id' => $newBook->id,
        ]);
    }

}