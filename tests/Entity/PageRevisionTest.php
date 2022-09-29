<?php

namespace Tests\Entity;

use BookStack\Actions\ActivityType;
use BookStack\Entities\Models\Page;
use Tests\TestCase;

class PageRevisionTest extends TestCase
{
    public function test_revision_links_visible_to_viewer()
    {
        $page = $this->entities->page();

        $html = $this->withHtml($this->asViewer()->get($page->getUrl()));
        $html->assertLinkExists($page->getUrl('/revisions'));
        $html->assertElementContains('a', 'Revisions');
        $html->assertElementContains('a', 'Revision #1');
    }

    public function test_page_revision_views_viewable()
    {
        $this->asEditor();
        $page = $this->entities->page();
        $this->createRevisions($page, 1, ['name' => 'updated page', 'html' => '<p>new content</p>']);
        $pageRevision = $page->revisions->last();

        $resp = $this->get($page->getUrl() . '/revisions/' . $pageRevision->id);
        $resp->assertStatus(200);
        $resp->assertSee('new content');

        $resp = $this->get($page->getUrl() . '/revisions/' . $pageRevision->id . '/changes');
        $resp->assertStatus(200);
        $resp->assertSee('new content');
    }

    public function test_page_revision_preview_shows_content_of_revision()
    {
        $this->asEditor();
        $page = $this->entities->page();
        $this->createRevisions($page, 1, ['name' => 'updated page', 'html' => '<p>new revision content</p>']);
        $pageRevision = $page->revisions->last();
        $this->createRevisions($page, 1, ['name' => 'updated page', 'html' => '<p>Updated content</p>']);

        $revisionView = $this->get($page->getUrl() . '/revisions/' . $pageRevision->id);
        $revisionView->assertStatus(200);
        $revisionView->assertSee('new revision content');
    }

    public function test_page_revision_restore_updates_content()
    {
        $this->asEditor();
        $page = $this->entities->page();
        $this->createRevisions($page, 1, ['name' => 'updated page abc123', 'html' => '<p>new contente def456</p>']);
        $this->createRevisions($page, 1, ['name' => 'updated page again', 'html' => '<p>new content</p>']);
        $page = Page::find($page->id);

        $pageView = $this->get($page->getUrl());
        $pageView->assertDontSee('abc123');
        $pageView->assertDontSee('def456');

        $revToRestore = $page->revisions()->where('name', 'like', '%abc123')->first();
        $restoreReq = $this->put($page->getUrl() . '/revisions/' . $revToRestore->id . '/restore');
        $page = Page::find($page->id);

        $restoreReq->assertStatus(302);
        $restoreReq->assertRedirect($page->getUrl());

        $pageView = $this->get($page->getUrl());
        $pageView->assertSee('abc123');
        $pageView->assertSee('def456');
    }

    public function test_page_revision_restore_with_markdown_retains_markdown_content()
    {
        $this->asEditor();
        $page = $this->entities->page();
        $this->createRevisions($page, 1, ['name' => 'updated page abc123', 'markdown' => '## New Content def456']);
        $this->createRevisions($page, 1, ['name' => 'updated page again', 'markdown' => '## New Content Updated']);
        $page = Page::find($page->id);

        $pageView = $this->get($page->getUrl());
        $pageView->assertDontSee('abc123');
        $pageView->assertDontSee('def456');

        $revToRestore = $page->revisions()->where('name', 'like', '%abc123')->first();
        $restoreReq = $this->put($page->getUrl() . '/revisions/' . $revToRestore->id . '/restore');
        $page = Page::find($page->id);

        $restoreReq->assertStatus(302);
        $restoreReq->assertRedirect($page->getUrl());

        $pageView = $this->get($page->getUrl());
        $this->assertDatabaseHas('pages', [
            'id'       => $page->id,
            'markdown' => '## New Content def456',
        ]);
        $pageView->assertSee('abc123');
        $pageView->assertSee('def456');
    }

    public function test_page_revision_restore_sets_new_revision_with_summary()
    {
        $this->asEditor();
        $page = $this->entities->page();
        $this->createRevisions($page, 1, ['name' => 'updated page abc123', 'html' => '<p>new contente def456</p>', 'summary' => 'My first update']);
        $this->createRevisions($page, 1, ['html' => '<p>new content</p>']);
        $page->refresh();

        $revToRestore = $page->revisions()->where('name', 'like', '%abc123')->first();
        $this->put($page->getUrl() . '/revisions/' . $revToRestore->id . '/restore');
        $page->refresh();

        $this->assertDatabaseHas('page_revisions', [
            'page_id' => $page->id,
            'text'    => 'new contente def456',
            'type'    => 'version',
            'summary' => "Restored from #{$revToRestore->id}; My first update",
        ]);

        $detail = "Revision #{$revToRestore->revision_number} (ID: {$revToRestore->id}) for page ID {$revToRestore->page_id}";
        $this->assertActivityExists(ActivityType::REVISION_RESTORE, null, $detail);
    }

    public function test_page_revision_count_increments_on_update()
    {
        $page = $this->entities->page();
        $startCount = $page->revision_count;
        $this->createRevisions($page, 1);

        $this->assertTrue(Page::find($page->id)->revision_count === $startCount + 1);
    }

    public function test_revision_count_shown_in_page_meta()
    {
        $page = $this->entities->page();
        $this->createRevisions($page, 2);

        $pageView = $this->get($page->getUrl());
        $pageView->assertSee('Revision #' . $page->revision_count);
    }

    public function test_revision_deletion()
    {
        $page = $this->entities->page();
        $this->createRevisions($page, 2);
        $beforeRevisionCount = $page->revisions->count();

        // Delete the first revision
        $revision = $page->revisions->get(1);
        $resp = $this->asEditor()->delete($revision->getUrl('/delete/'));
        $resp->assertRedirect($page->getUrl('/revisions'));

        $page->refresh();
        $afterRevisionCount = $page->revisions->count();

        $this->assertTrue($beforeRevisionCount === ($afterRevisionCount + 1));

        $detail = "Revision #{$revision->revision_number} (ID: {$revision->id}) for page ID {$revision->page_id}";
        $this->assertActivityExists(ActivityType::REVISION_DELETE, null, $detail);

        // Try to delete the latest revision
        $beforeRevisionCount = $page->revisions->count();
        $resp = $this->asEditor()->delete($page->currentRevision->getUrl('/delete/'));
        $resp->assertRedirect($page->getUrl('/revisions'));

        $page->refresh();
        $afterRevisionCount = $page->revisions->count();
        $this->assertTrue($beforeRevisionCount === $afterRevisionCount);
    }

    public function test_revision_limit_enforced()
    {
        config()->set('app.revision_limit', 2);
        $page = $this->entities->page();
        $this->createRevisions($page, 12);

        $revisionCount = $page->revisions()->count();
        $this->assertEquals(2, $revisionCount);
    }

    public function test_false_revision_limit_allows_many_revisions()
    {
        config()->set('app.revision_limit', false);
        $page = $this->entities->page();
        $this->createRevisions($page, 12);

        $revisionCount = $page->revisions()->count();
        $this->assertEquals(12, $revisionCount);
    }

    public function test_revision_list_shows_editor_type()
    {
        $page = $this->entities->page();
        $this->createRevisions($page, 1, ['html' => 'new page html']);

        $resp = $this->asAdmin()->get($page->refresh()->getUrl('/revisions'));
        $this->withHtml($resp)->assertElementContains('td', '(WYSIWYG)');
        $this->withHtml($resp)->assertElementNotContains('td', '(Markdown)');

        $this->createRevisions($page, 1, ['markdown' => '# Some markdown content']);
        $resp = $this->get($page->refresh()->getUrl('/revisions'));
        $this->withHtml($resp)->assertElementContains('td', '(Markdown)');
    }

    public function test_revision_restore_action_only_visible_with_permission()
    {
        $page = $this->entities->page();
        $this->createRevisions($page, 2);

        $viewer = $this->getViewer();
        $this->actingAs($viewer);
        $respHtml = $this->withHtml($this->get($page->getUrl('/revisions')));
        $respHtml->assertElementNotContains('.actions a', 'Restore');
        $respHtml->assertElementNotExists('form[action$="/restore"]');

        $this->giveUserPermissions($viewer, ['page-update-all']);

        $respHtml = $this->withHtml($this->get($page->getUrl('/revisions')));
        $respHtml->assertElementContains('.actions a', 'Restore');
        $respHtml->assertElementExists('form[action$="/restore"]');
    }

    public function test_revision_delete_action_only_visible_with_permission()
    {
        $page = $this->entities->page();
        $this->createRevisions($page, 2);

        $viewer = $this->getViewer();
        $this->actingAs($viewer);
        $respHtml = $this->withHtml($this->get($page->getUrl('/revisions')));
        $respHtml->assertElementNotContains('.actions a', 'Delete');
        $respHtml->assertElementNotExists('form[action$="/delete"]');

        $this->giveUserPermissions($viewer, ['page-delete-all']);

        $respHtml = $this->withHtml($this->get($page->getUrl('/revisions')));
        $respHtml->assertElementContains('.actions a', 'Delete');
        $respHtml->assertElementExists('form[action$="/delete"]');
    }

    protected function createRevisions(Page $page, int $times, array $attrs = [])
    {
        $user = user();

        for ($i = 0; $i < $times; $i++) {
            $data = ['name' => 'Page update' . $i, 'summary' => 'Update entry' . $i];
            if (!isset($attrs['markdown'])) {
                $data['html'] = '<p>My update page</p>';
            }
            $this->asAdmin()->put($page->getUrl(), array_merge($data, $attrs));
            $page->refresh();
        }

        $this->actingAs($user);
    }
}
