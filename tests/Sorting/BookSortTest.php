<?php

namespace Tests\Sorting;

use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Repos\PageRepo;
use BookStack\Sorting\SortRule;
use Tests\TestCase;

class BookSortTest extends TestCase
{
    public function test_book_sort_page_shows()
    {
        $bookToSort = $this->entities->book();

        $resp = $this->asAdmin()->get($bookToSort->getUrl());
        $this->withHtml($resp)->assertElementExists('a[href="' . $bookToSort->getUrl('/sort') . '"]');

        $resp = $this->get($bookToSort->getUrl('/sort'));
        $resp->assertStatus(200);
        $resp->assertSee($bookToSort->name);
    }

    public function test_drafts_do_not_show_up()
    {
        $this->asAdmin();
        $pageRepo = app(PageRepo::class);
        $book = $this->entities->book();
        $draft = $pageRepo->getNewDraftPage($book);

        $resp = $this->get($book->getUrl());
        $resp->assertSee($draft->name);

        $resp = $this->get($book->getUrl('/sort'));
        $resp->assertDontSee($draft->name);
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

    public function test_book_sort_does_not_change_timestamps_on_just_order_changes()
    {
        $book = $this->entities->bookHasChaptersAndPages();
        $chapter = $book->chapters()->first();
        \DB::table('chapters')->where('id', '=', $chapter->id)->update([
            'priority' => 10001,
            'updated_at' => \Carbon\Carbon::now()->subYear(5),
        ]);

        $chapter->refresh();
        $oldUpdatedAt = $chapter->updated_at->unix();

        $sortData = [
            'id'            => $chapter->id,
            'sort'          => 0,
            'parentChapter' => false,
            'type'          => 'chapter',
            'book'          => $book->id,
        ];
        $this->asEditor()->put($book->getUrl('/sort'), ['sort-tree' => json_encode([$sortData])])->assertRedirect();

        $chapter->refresh();
        $this->assertNotEquals(10001, $chapter->priority);
        $this->assertEquals($oldUpdatedAt, $chapter->updated_at->unix());
    }

    public function test_book_sort_item_returns_book_content()
    {
        $bookToSort = $this->entities->book();
        $firstPage = $bookToSort->pages[0];
        $firstChapter = $bookToSort->chapters[0];

        $resp = $this->asAdmin()->get($bookToSort->getUrl('/sort-item'));

        // Ensure book details are returned
        $resp->assertSee($bookToSort->name);
        $resp->assertSee($firstPage->name);
        $resp->assertSee($firstChapter->name);
    }

    public function test_book_sort_item_shows_auto_sort_status()
    {
        $sort = SortRule::factory()->create(['name' => 'My sort']);
        $book = $this->entities->book();

        $resp = $this->asAdmin()->get($book->getUrl('/sort-item'));
        $this->withHtml($resp)->assertElementNotExists("span[title='Auto Sort Active: My sort']");

        $book->sort_rule_id = $sort->id;
        $book->save();

        $resp = $this->asAdmin()->get($book->getUrl('/sort-item'));
        $this->withHtml($resp)->assertElementExists("span[title='Auto Sort Active: My sort']");
    }

    public function test_auto_sort_options_shown_on_sort_page()
    {
        $sort = SortRule::factory()->create();
        $book = $this->entities->book();
        $resp = $this->asAdmin()->get($book->getUrl('/sort'));

        $this->withHtml($resp)->assertElementExists('select[name="auto-sort"] option[value="' . $sort->id . '"]');
    }

    public function test_auto_sort_option_submit_saves_to_book()
    {
        $sort = SortRule::factory()->create();
        $book = $this->entities->book();
        $bookPage = $book->pages()->first();
        $bookPage->priority = 10000;
        $bookPage->save();

        $resp = $this->asAdmin()->put($book->getUrl('/sort'), [
            'auto-sort' => $sort->id,
        ]);

        $resp->assertRedirect($book->getUrl());
        $book->refresh();
        $bookPage->refresh();

        $this->assertEquals($sort->id, $book->sort_rule_id);
        $this->assertNotEquals(10000, $bookPage->priority);

        $resp = $this->get($book->getUrl('/sort'));
        $this->withHtml($resp)->assertElementExists('select[name="auto-sort"] option[value="' . $sort->id . '"][selected]');
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
