<?php

namespace Tests\Entity;

use BookStack\Actions\ActivityType;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Repos\PageRepo;
use Tests\TestCase;

class PageRevisionTest extends TestCase
{
    public function test_revision_links_visible_to_viewer()
    {
        /** @var Page $page */
        $page = Page::query()->first();

        $html = $this->withHtml($this->asViewer()->get($page->getUrl()));
        $html->assertLinkExists($page->getUrl('/revisions'));
        $html->assertElementContains('a', 'Revisions');
        $html->assertElementContains('a', 'Revision #1');
    }

    public function test_page_revision_views_viewable()
    {
        $this->asEditor();

        $pageRepo = app(PageRepo::class);
        $page = Page::first();
        $pageRepo->update($page, ['name' => 'updated page', 'html' => '<p>new content</p>', 'summary' => 'page revision testing']);
        $pageRevision = $page->revisions->last();

        $revisionView = $this->get($page->getUrl() . '/revisions/' . $pageRevision->id);
        $revisionView->assertStatus(200);
        $revisionView->assertSee('new content');

        $revisionView = $this->get($page->getUrl() . '/revisions/' . $pageRevision->id . '/changes');
        $revisionView->assertStatus(200);
        $revisionView->assertSee('new content');
    }

    public function test_page_revision_preview_shows_content_of_revision()
    {
        $this->asEditor();

        $pageRepo = app(PageRepo::class);
        $page = Page::first();
        $pageRepo->update($page, ['name' => 'updated page', 'html' => '<p>new revision content</p>', 'summary' => 'page revision testing']);
        $pageRevision = $page->revisions->last();
        $pageRepo->update($page, ['name' => 'updated page', 'html' => '<p>Updated content</p>', 'summary' => 'page revision testing 2']);

        $revisionView = $this->get($page->getUrl() . '/revisions/' . $pageRevision->id);
        $revisionView->assertStatus(200);
        $revisionView->assertSee('new revision content');
    }

    public function test_page_revision_restore_updates_content()
    {
        $this->asEditor();

        $pageRepo = app(PageRepo::class);
        $page = Page::first();
        $pageRepo->update($page, ['name' => 'updated page abc123', 'html' => '<p>new contente def456</p>', 'summary' => 'initial page revision testing']);
        $pageRepo->update($page, ['name' => 'updated page again', 'html' => '<p>new content</p>', 'summary' => 'page revision testing']);
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

        $pageRepo = app(PageRepo::class);
        $page = Page::first();
        $pageRepo->update($page, ['name' => 'updated page abc123', 'markdown' => '## New Content def456', 'summary' => 'initial page revision testing']);
        $pageRepo->update($page, ['name' => 'updated page again', 'markdown' => '## New Content Updated', 'summary' => 'page revision testing']);
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

        $pageRepo = app(PageRepo::class);
        $page = Page::first();
        $pageRepo->update($page, ['name' => 'updated page abc123', 'html' => '<p>new contente def456</p>', 'summary' => 'My first update']);
        $pageRepo->update($page, ['name' => 'updated page again', 'html' => '<p>new content</p>', 'summary' => '']);
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
        $page = Page::first();
        $startCount = $page->revision_count;
        $resp = $this->asEditor()->put($page->getUrl(), ['name' => 'Updated page', 'html' => 'new page html', 'summary' => 'Update a']);
        $resp->assertStatus(302);

        $this->assertTrue(Page::find($page->id)->revision_count === $startCount + 1);
    }

    public function test_revision_count_shown_in_page_meta()
    {
        $page = Page::first();
        $this->asEditor()->put($page->getUrl(), ['name' => 'Updated page', 'html' => 'new page html', 'summary' => 'Update a']);

        $page = Page::find($page->id);
        $this->asEditor()->put($page->getUrl(), ['name' => 'Updated page', 'html' => 'new page html', 'summary' => 'Update a']);

        $page = Page::find($page->id);
        $pageView = $this->get($page->getUrl());
        $pageView->assertSee('Revision #' . $page->revision_count);
    }

    public function test_revision_deletion()
    {
        /** @var Page $page */
        $page = Page::query()->first();
        $this->asEditor()->put($page->getUrl(), ['name' => 'Updated page', 'html' => 'new page html', 'summary' => 'Update a']);

        $page->refresh();
        $this->asEditor()->put($page->getUrl(), ['name' => 'Updated page', 'html' => 'new page html', 'summary' => 'Update a']);

        $page->refresh();
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
        $page = Page::first();
        $this->asEditor()->put($page->getUrl(), ['name' => 'Updated page', 'html' => 'new page html', 'summary' => 'Update a']);
        $page = Page::find($page->id);
        $this->asEditor()->put($page->getUrl(), ['name' => 'Updated page', 'html' => 'new page html', 'summary' => 'Update a']);
        for ($i = 0; $i < 10; $i++) {
            $this->asEditor()->put($page->getUrl(), ['name' => 'Updated page', 'html' => 'new page html', 'summary' => 'Update a']);
        }

        $revisionCount = $page->revisions()->count();
        $this->assertEquals(2, $revisionCount);
    }

    public function test_false_revision_limit_allows_many_revisions()
    {
        config()->set('app.revision_limit', false);
        $page = Page::first();
        $this->asEditor()->put($page->getUrl(), ['name' => 'Updated page', 'html' => 'new page html', 'summary' => 'Update a']);
        $page = Page::find($page->id);
        $this->asEditor()->put($page->getUrl(), ['name' => 'Updated page', 'html' => 'new page html', 'summary' => 'Update a']);
        for ($i = 0; $i < 10; $i++) {
            $this->asEditor()->put($page->getUrl(), ['name' => 'Updated page', 'html' => 'new page html', 'summary' => 'Update a']);
        }

        $revisionCount = $page->revisions()->count();
        $this->assertEquals(12, $revisionCount);
    }

    public function test_revision_list_shows_editor_type()
    {
        /** @var Page $page */
        $page = Page::first();
        $this->asAdmin()->put($page->getUrl(), ['name' => 'Updated page', 'html' => 'new page html']);

        $resp = $this->get($page->refresh()->getUrl('/revisions'));
        $this->withHtml($resp)->assertElementContains('td', '(WYSIWYG)');
        $this->withHtml($resp)->assertElementNotContains('td', '(Markdown)');

        $this->asAdmin()->put($page->getUrl(), ['name' => 'Updated page', 'markdown' => '# Some markdown content']);
        $resp = $this->get($page->refresh()->getUrl('/revisions'));
        $this->withHtml($resp)->assertElementContains('td', '(Markdown)');
    }
}
