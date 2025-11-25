<?php

namespace Tests\Entity;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Page;
use BookStack\Uploads\Image;
use Carbon\Carbon;
use Tests\TestCase;

class PageTest extends TestCase
{
    public function test_create()
    {
        $chapter = $this->entities->chapter();
        $page = Page::factory()->make([
            'name' => 'My First Page',
        ]);

        $resp = $this->asEditor()->get($chapter->getUrl());
        $this->withHtml($resp)->assertElementContains('a[href="' . $chapter->getUrl('/create-page') . '"]', 'New Page');

        $resp = $this->get($chapter->getUrl('/create-page'));
        /** @var Page $draftPage */
        $draftPage = Page::query()
            ->where('draft', '=', true)
            ->orderBy('created_at', 'desc')
            ->first();
        $resp->assertRedirect($draftPage->getUrl());

        $resp = $this->get($draftPage->getUrl());
        $this->withHtml($resp)->assertElementContains('form[action="' . $draftPage->getUrl() . '"][method="POST"]', 'Save Page');

        $resp = $this->post($draftPage->getUrl(), $draftPage->only('name', 'html'));
        $draftPage->refresh();
        $resp->assertRedirect($draftPage->getUrl());
    }

    public function test_page_view_when_creator_is_deleted_but_owner_exists()
    {
        $page = $this->entities->page();
        $user = $this->users->viewer();
        $owner = $this->users->editor();
        $page->created_by = $user->id;
        $page->owned_by = $owner->id;
        $page->save();
        $user->delete();

        $resp = $this->asAdmin()->get($page->getUrl());
        $resp->assertStatus(200);
        $resp->assertSeeText('Owned by ' . $owner->name);
    }

    public function test_page_show_includes_pointer_section_select_mode_button()
    {
        $page = $this->entities->page();
        $resp = $this->asEditor()->get($page->getUrl());
        $this->withHtml($resp)->assertElementContains('.content-wrap button.screen-reader-only', 'Enter section select mode');
    }

    public function test_page_creation_with_markdown_content()
    {
        $this->setSettings(['app-editor' => 'markdown']);
        $book = $this->entities->book();

        $this->asEditor()->get($book->getUrl('/create-page'));
        $draft = Page::query()->where('book_id', '=', $book->id)
            ->where('draft', '=', true)->first();

        $details = [
            'markdown' => '# a title',
            'html'     => '<h1>a title</h1>',
            'name'     => 'my page',
        ];
        $resp = $this->post($book->getUrl("/draft/{$draft->id}"), $details);
        $resp->assertRedirect();

        $this->assertDatabaseHasEntityData('page', [
            'markdown' => $details['markdown'],
            'name'     => $details['name'],
            'id'       => $draft->id,
            'draft'    => false,
        ]);

        $draft->refresh();
        $resp = $this->get($draft->getUrl('/edit'));
        $resp->assertSee('# a title');
    }

    public function test_page_creation_allows_summary_to_be_set()
    {
        $book = $this->entities->book();

        $this->asEditor()->get($book->getUrl('/create-page'));
        $draft = Page::query()->where('book_id', '=', $book->id)
            ->where('draft', '=', true)->first();

        $details = [
            'html'    => '<h1>a title</h1>',
            'name'    => 'My page with summary',
            'summary' => 'Here is my changelog message for a new page!',
        ];
        $resp = $this->post($book->getUrl("/draft/{$draft->id}"), $details);
        $resp->assertRedirect();

        $this->assertDatabaseHas('page_revisions', [
            'page_id' => $draft->id,
            'summary' => 'Here is my changelog message for a new page!',
        ]);

        $draft->refresh();
        $resp = $this->get($draft->getUrl('/revisions'));
        $resp->assertSee('Here is my changelog message for a new page!');
    }

    public function test_page_delete()
    {
        $page = $this->entities->page();
        $this->assertNull($page->deleted_at);

        $deleteViewReq = $this->asEditor()->get($page->getUrl('/delete'));
        $deleteViewReq->assertSeeText('Are you sure you want to delete this page?');

        $deleteReq = $this->delete($page->getUrl());
        $deleteReq->assertRedirect($page->getParent()->getUrl());
        $this->assertActivityExists('page_delete', $page);

        $page->refresh();
        $this->assertNotNull($page->deleted_at);
        $this->assertTrue($page->deletions()->count() === 1);

        $redirectReq = $this->get($deleteReq->baseResponse->headers->get('location'));
        $this->assertNotificationContains($redirectReq, 'Page Successfully Deleted');
    }

    public function test_page_full_delete_removes_all_revisions()
    {
        $page = $this->entities->page();
        $page->revisions()->create([
            'html' => '<p>ducks</p>',
            'name' => 'my page revision',
            'type' => 'draft',
        ]);
        $page->revisions()->create([
            'html' => '<p>ducks</p>',
            'name' => 'my page revision',
            'type' => 'revision',
        ]);

        $this->assertDatabaseHas('page_revisions', [
            'page_id' => $page->id,
        ]);

        $this->asEditor()->delete($page->getUrl());
        $this->asAdmin()->post('/settings/recycle-bin/empty');

        $this->assertDatabaseMissing('page_revisions', [
            'page_id' => $page->id,
        ]);
    }

    public function test_page_full_delete_nulls_related_images()
    {
        $page = $this->entities->page();
        $image = Image::factory()->create(['type' => 'gallery', 'uploaded_to' => $page->id]);

        $this->asEditor()->delete($page->getUrl());
        $this->asAdmin()->post('/settings/recycle-bin/empty');

        $this->assertDatabaseMissing('images', [
            'type' => 'gallery',
            'uploaded_to' => $page->id,
        ]);

        $this->assertDatabaseHas('images', [
            'id' => $image->id,
            'uploaded_to' => null,
        ]);
    }

    public function test_page_within_chapter_deletion_returns_to_chapter()
    {
        $chapter = $this->entities->chapter();
        $page = $chapter->pages()->first();

        $this->asEditor()->delete($page->getUrl())
            ->assertRedirect($chapter->getUrl());
    }

    public function test_recently_updated_pages_view()
    {
        $user = $this->users->editor();
        $content = $this->entities->createChainBelongingToUser($user);

        $resp = $this->asAdmin()->get('/pages/recently-updated');
        $this->withHtml($resp)->assertElementContains('.entity-list .page:nth-child(1)', $content['page']->name);
    }

    public function test_recently_updated_pages_view_shows_updated_by_details()
    {
        $user = $this->users->editor();
        $page = $this->entities->page();

        $this->actingAs($user)->put($page->getUrl(), [
            'name' => 'Updated title',
            'html' => '<p>Updated content</p>',
        ]);

        $resp = $this->asAdmin()->get('/pages/recently-updated');
        $this->withHtml($resp)->assertElementContains('.entity-list .page:nth-child(1) small', 'by ' . $user->name);
    }

    public function test_recently_updated_pages_view_shows_parent_chain()
    {
        $user = $this->users->editor();
        $page = $this->entities->pageWithinChapter();

        $this->actingAs($user)->put($page->getUrl(), [
            'name' => 'Updated title',
            'html' => '<p>Updated content</p>',
        ]);

        $resp = $this->asAdmin()->get('/pages/recently-updated');
        $this->withHtml($resp)->assertElementContains('.entity-list .page:nth-child(1)', $page->chapter->getShortName(42));
        $this->withHtml($resp)->assertElementContains('.entity-list .page:nth-child(1)', $page->book->getShortName(42));
    }

    public function test_recently_updated_pages_view_does_not_show_parent_if_not_visible()
    {
        $user = $this->users->editor();
        $page = $this->entities->pageWithinChapter();

        $this->actingAs($user)->put($page->getUrl(), [
            'name' => 'Updated title',
            'html' => '<p>Updated content</p>',
        ]);

        $this->permissions->setEntityPermissions($page->book);
        $this->permissions->setEntityPermissions($page, ['view'], [$user->roles->first()]);

        $resp = $this->get('/pages/recently-updated');
        $resp->assertDontSee($page->book->getShortName(42));
        $resp->assertDontSee($page->chapter->getShortName(42));
        $this->withHtml($resp)->assertElementContains('.entity-list .page:nth-child(1)', 'Updated title');
    }

    public function test_recently_updated_pages_on_home()
    {
        /** @var Page $page */
        $page = Page::query()->orderBy('updated_at', 'asc')->first();
        Page::query()->where('id', '!=', $page->id)->update([
            'updated_at' => Carbon::now()->subSecond(1),
        ]);

        $resp = $this->asAdmin()->get('/');
        $this->withHtml($resp)->assertElementNotContains('#recently-updated-pages', $page->name);

        $this->put($page->getUrl(), [
            'name' => $page->name,
            'html' => $page->html,
        ]);

        $resp = $this->get('/');
        $this->withHtml($resp)->assertElementContains('#recently-updated-pages', $page->name);
    }

    public function test_page_edit_without_update_permissions_but_with_view_redirects_to_page()
    {
        $page = $this->entities->page();

        $resp = $this->asViewer()->get($page->getUrl('/edit'));
        $resp->assertRedirect($page->getUrl());

        $resp->assertSessionHas('error', 'You do not have permission to access the requested page.');
    }
}
