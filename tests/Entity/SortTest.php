<?php namespace Tests;

use BookStack\Book;
use BookStack\Chapter;
use BookStack\Page;
use BookStack\Repos\EntityRepo;

class SortTest extends TestCase
{
    protected $book;

    public function setUp()
    {
        parent::setUp();
        $this->book = Book::first();
    }

    public function test_drafts_do_not_show_up()
    {
        $this->asAdmin();
        $entityRepo = app(EntityRepo::class);
        $draft = $entityRepo->getDraftPage($this->book);

        $resp = $this->get($this->book->getUrl());
        $resp->assertSee($draft->name);

        $resp = $this->get($this->book->getUrl() . '/sort');
        $resp->assertDontSee($draft->name);
    }

    public function test_page_move()
    {
        $page = Page::first();
        $currentBook = $page->book;
        $newBook = Book::where('id', '!=', $currentBook->id)->first();

        $resp = $this->asAdmin()->get($page->getUrl() . '/move');
        $resp->assertSee('Move Page');

        $movePageResp = $this->put($page->getUrl() . '/move', [
            'entity_selection' => 'book:' . $newBook->id
        ]);
        $page = Page::find($page->id);

        $movePageResp->assertRedirect($page->getUrl());
        $this->assertTrue($page->book->id == $newBook->id, 'Page book is now the new book');

        $newBookResp = $this->get($newBook->getUrl());
        $newBookResp->assertSee('moved page');
        $newBookResp->assertSee($page->name);
    }

    public function test_chapter_move()
    {
        $chapter = Chapter::first();
        $currentBook = $chapter->book;
        $pageToCheck = $chapter->pages->first();
        $newBook = Book::where('id', '!=', $currentBook->id)->first();

        $chapterMoveResp = $this->asAdmin()->get($chapter->getUrl() . '/move');
        $chapterMoveResp->assertSee('Move Chapter');

        $moveChapterResp = $this->put($chapter->getUrl() . '/move', [
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

        $sortResp = $this->asAdmin()->put($newBook->getUrl() . '/sort', ['sort-tree' => json_encode($reqData)]);
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

}