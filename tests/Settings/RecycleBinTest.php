<?php

namespace Tests\Settings;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Deletion;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RecycleBinTest extends TestCase
{
    public function test_recycle_bin_routes_permissions()
    {
        $page = $this->entities->page();
        $editor = $this->getEditor();
        $this->actingAs($editor)->delete($page->getUrl());
        $deletion = Deletion::query()->firstOrFail();

        $routes = [
            'GET:/settings/recycle-bin',
            'POST:/settings/recycle-bin/empty',
            "GET:/settings/recycle-bin/{$deletion->id}/destroy",
            "GET:/settings/recycle-bin/{$deletion->id}/restore",
            "POST:/settings/recycle-bin/{$deletion->id}/restore",
            "DELETE:/settings/recycle-bin/{$deletion->id}",
        ];

        foreach ($routes as $route) {
            [$method, $url] = explode(':', $route);
            $resp = $this->call($method, $url);
            $this->assertPermissionError($resp);
        }

        $this->giveUserPermissions($editor, ['restrictions-manage-all']);

        foreach ($routes as $route) {
            [$method, $url] = explode(':', $route);
            $resp = $this->call($method, $url);
            $this->assertPermissionError($resp);
        }

        $this->giveUserPermissions($editor, ['settings-manage']);

        foreach ($routes as $route) {
            DB::beginTransaction();
            [$method, $url] = explode(':', $route);
            $resp = $this->call($method, $url);
            $this->assertNotPermissionError($resp);
            DB::rollBack();
        }
    }

    public function test_recycle_bin_view()
    {
        $page = $this->entities->page();
        $book = Book::query()->whereHas('pages')->whereHas('chapters')->withCount(['pages', 'chapters'])->first();
        $editor = $this->getEditor();
        $this->actingAs($editor)->delete($page->getUrl());
        $this->actingAs($editor)->delete($book->getUrl());

        $viewReq = $this->asAdmin()->get('/settings/recycle-bin');
        $html = $this->withHtml($viewReq);
        $html->assertElementContains('table.table', $page->name);
        $html->assertElementContains('table.table', $editor->name);
        $html->assertElementContains('table.table', $book->name);
        $html->assertElementContains('table.table', $book->pages_count . ' Pages');
        $html->assertElementContains('table.table', $book->chapters_count . ' Chapters');
    }

    public function test_recycle_bin_empty()
    {
        $page = $this->entities->page();
        $book = Book::query()->where('id', '!=', $page->book_id)->whereHas('pages')->whereHas('chapters')->with(['pages', 'chapters'])->firstOrFail();
        $editor = $this->getEditor();
        $this->actingAs($editor)->delete($page->getUrl());
        $this->actingAs($editor)->delete($book->getUrl());

        $this->assertTrue(Deletion::query()->count() === 2);
        $emptyReq = $this->asAdmin()->post('/settings/recycle-bin/empty');
        $emptyReq->assertRedirect('/settings/recycle-bin');

        $this->assertTrue(Deletion::query()->count() === 0);
        $this->assertDatabaseMissing('books', ['id' => $book->id]);
        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
        $this->assertDatabaseMissing('pages', ['id' => $book->pages->first()->id]);
        $this->assertDatabaseMissing('chapters', ['id' => $book->chapters->first()->id]);

        $itemCount = 2 + $book->pages->count() + $book->chapters->count();
        $redirectReq = $this->get('/settings/recycle-bin');
        $this->assertNotificationContains($redirectReq, 'Deleted ' . $itemCount . ' total items from the recycle bin');
    }

    public function test_entity_restore()
    {
        $book = Book::query()->whereHas('pages')->whereHas('chapters')->with(['pages', 'chapters'])->firstOrFail();
        $this->asEditor()->delete($book->getUrl());
        $deletion = Deletion::query()->firstOrFail();

        $this->assertEquals($book->pages->count(), DB::table('pages')->where('book_id', '=', $book->id)->whereNotNull('deleted_at')->count());
        $this->assertEquals($book->chapters->count(), DB::table('chapters')->where('book_id', '=', $book->id)->whereNotNull('deleted_at')->count());

        $restoreReq = $this->asAdmin()->post("/settings/recycle-bin/{$deletion->id}/restore");
        $restoreReq->assertRedirect('/settings/recycle-bin');
        $this->assertTrue(Deletion::query()->count() === 0);

        $this->assertEquals($book->pages->count(), DB::table('pages')->where('book_id', '=', $book->id)->whereNull('deleted_at')->count());
        $this->assertEquals($book->chapters->count(), DB::table('chapters')->where('book_id', '=', $book->id)->whereNull('deleted_at')->count());

        $itemCount = 1 + $book->pages->count() + $book->chapters->count();
        $redirectReq = $this->get('/settings/recycle-bin');
        $this->assertNotificationContains($redirectReq, 'Restored ' . $itemCount . ' total items from the recycle bin');
    }

    public function test_permanent_delete()
    {
        $book = Book::query()->whereHas('pages')->whereHas('chapters')->with(['pages', 'chapters'])->firstOrFail();
        $this->asEditor()->delete($book->getUrl());
        $deletion = Deletion::query()->firstOrFail();

        $deleteReq = $this->asAdmin()->delete("/settings/recycle-bin/{$deletion->id}");
        $deleteReq->assertRedirect('/settings/recycle-bin');
        $this->assertTrue(Deletion::query()->count() === 0);

        $this->assertDatabaseMissing('books', ['id' => $book->id]);
        $this->assertDatabaseMissing('pages', ['id' => $book->pages->first()->id]);
        $this->assertDatabaseMissing('chapters', ['id' => $book->chapters->first()->id]);

        $itemCount = 1 + $book->pages->count() + $book->chapters->count();
        $redirectReq = $this->get('/settings/recycle-bin');
        $this->assertNotificationContains($redirectReq, 'Deleted ' . $itemCount . ' total items from the recycle bin');
    }

    public function test_permanent_delete_for_each_type()
    {
        /** @var Entity $entity */
        foreach ([new Bookshelf(), new Book(), new Chapter(), new Page()] as $entity) {
            $entity = $entity->newQuery()->first();
            $this->asEditor()->delete($entity->getUrl());
            $deletion = Deletion::query()->orderBy('id', 'desc')->firstOrFail();

            $deleteReq = $this->asAdmin()->delete("/settings/recycle-bin/{$deletion->id}");
            $deleteReq->assertRedirect('/settings/recycle-bin');
            $this->assertDatabaseMissing('deletions', ['id' => $deletion->id]);
            $this->assertDatabaseMissing($entity->getTable(), ['id' => $entity->id]);
        }
    }

    public function test_permanent_entity_delete_updates_existing_activity_with_entity_name()
    {
        $page = Page::query()->firstOrFail();
        $this->asEditor()->delete($page->getUrl());
        $deletion = $page->deletions()->firstOrFail();

        $this->assertDatabaseHas('activities', [
            'type'        => 'page_delete',
            'entity_id'   => $page->id,
            'entity_type' => $page->getMorphClass(),
        ]);

        $this->asAdmin()->delete("/settings/recycle-bin/{$deletion->id}");

        $this->assertDatabaseMissing('activities', [
            'type'        => 'page_delete',
            'entity_id'   => $page->id,
            'entity_type' => $page->getMorphClass(),
        ]);

        $this->assertDatabaseHas('activities', [
            'type'        => 'page_delete',
            'entity_id'   => null,
            'entity_type' => null,
            'detail'      => $page->name,
        ]);
    }

    public function test_auto_clear_functionality_works()
    {
        config()->set('app.recycle_bin_lifetime', 5);
        $page = Page::query()->firstOrFail();
        $otherPage = Page::query()->where('id', '!=', $page->id)->firstOrFail();

        $this->asEditor()->delete($page->getUrl());
        $this->assertDatabaseHas('pages', ['id' => $page->id]);
        $this->assertEquals(1, Deletion::query()->count());

        Carbon::setTestNow(Carbon::now()->addDays(6));
        $this->asEditor()->delete($otherPage->getUrl());
        $this->assertEquals(1, Deletion::query()->count());

        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
    }

    public function test_auto_clear_functionality_with_negative_time_keeps_forever()
    {
        config()->set('app.recycle_bin_lifetime', -1);
        $page = Page::query()->firstOrFail();
        $otherPage = Page::query()->where('id', '!=', $page->id)->firstOrFail();

        $this->asEditor()->delete($page->getUrl());
        $this->assertEquals(1, Deletion::query()->count());

        Carbon::setTestNow(Carbon::now()->addDays(6000));
        $this->asEditor()->delete($otherPage->getUrl());
        $this->assertEquals(2, Deletion::query()->count());

        $this->assertDatabaseHas('pages', ['id' => $page->id]);
    }

    public function test_auto_clear_functionality_with_zero_time_deletes_instantly()
    {
        config()->set('app.recycle_bin_lifetime', 0);
        $page = Page::query()->firstOrFail();

        $this->asEditor()->delete($page->getUrl());
        $this->assertDatabaseMissing('pages', ['id' => $page->id]);
        $this->assertEquals(0, Deletion::query()->count());
    }

    public function test_restore_flow_when_restoring_nested_delete_first()
    {
        $book = Book::query()->whereHas('pages')->whereHas('chapters')->with(['pages', 'chapters'])->firstOrFail();
        $chapter = $book->chapters->first();
        $this->asEditor()->delete($chapter->getUrl());
        $this->asEditor()->delete($book->getUrl());

        $bookDeletion = $book->deletions()->first();
        $chapterDeletion = $chapter->deletions()->first();

        $chapterRestoreView = $this->asAdmin()->get("/settings/recycle-bin/{$chapterDeletion->id}/restore");
        $chapterRestoreView->assertStatus(200);
        $chapterRestoreView->assertSeeText($chapter->name);

        $chapterRestore = $this->post("/settings/recycle-bin/{$chapterDeletion->id}/restore");
        $chapterRestore->assertRedirect('/settings/recycle-bin');
        $this->assertDatabaseMissing('deletions', ['id' => $chapterDeletion->id]);

        $chapter->refresh();
        $this->assertNotNull($chapter->deleted_at);

        $bookRestoreView = $this->asAdmin()->get("/settings/recycle-bin/{$bookDeletion->id}/restore");
        $bookRestoreView->assertStatus(200);
        $bookRestoreView->assertSeeText($chapter->name);

        $this->post("/settings/recycle-bin/{$bookDeletion->id}/restore");
        $chapter->refresh();
        $this->assertNull($chapter->deleted_at);
    }

    public function test_restore_page_shows_link_to_parent_restore_if_parent_also_deleted()
    {
        /** @var Book $book */
        $book = Book::query()->whereHas('pages')->whereHas('chapters')->with(['pages', 'chapters'])->firstOrFail();
        $chapter = $book->chapters->first();
        /** @var Page $page */
        $page = $chapter->pages->first();
        $this->asEditor()->delete($page->getUrl());
        $this->asEditor()->delete($book->getUrl());

        $bookDeletion = $book->deletions()->first();
        $pageDeletion = $page->deletions()->first();

        $pageRestoreView = $this->asAdmin()->get("/settings/recycle-bin/{$pageDeletion->id}/restore");
        $pageRestoreView->assertSee('The parent of this item has also been deleted.');
        $this->withHtml($pageRestoreView)->assertElementContains('a[href$="/settings/recycle-bin/' . $bookDeletion->id . '/restore"]', 'Restore Parent');
    }
}
