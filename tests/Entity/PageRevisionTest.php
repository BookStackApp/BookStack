<?php namespace Entity;


use BookStack\Entities\Page;
use Tests\TestCase;

class PageRevisionTest extends TestCase
{

    public function test_page_revision_count_increments_on_update()
    {
        $page = Page::first();
        $startCount = $page->revision_count;
        $resp = $this->asEditor()->put($page->getUrl(), ['name' => 'Updated page', 'html' => 'new page html', 'summary' => 'Update a']);
        $resp->assertStatus(302);

        $this->assertTrue(Page::find($page->id)->revision_count === $startCount+1);
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

    public function test_revision_deletion() {
        $page = Page::first();
        $this->asEditor()->put($page->getUrl(), ['name' => 'Updated page', 'html' => 'new page html', 'summary' => 'Update a']);

        $page = Page::find($page->id);
        $this->asEditor()->put($page->getUrl(), ['name' => 'Updated page', 'html' => 'new page html', 'summary' => 'Update a']);

        $page = Page::find($page->id);
        $beforeRevisionCount = $page->revisions->count();

        // Delete the first revision
        $revision = $page->revisions->get(1);
        $resp = $this->asEditor()->delete($revision->getUrl('/delete/'));
        $resp->assertStatus(200);

        $page = Page::find($page->id);
        $afterRevisionCount = $page->revisions->count();

        $this->assertTrue($beforeRevisionCount === ($afterRevisionCount + 1));

        // Try to delete the latest revision
        $beforeRevisionCount = $page->revisions->count();
        $currentRevision = $page->getCurrentRevision();
        $resp = $this->asEditor()->delete($currentRevision->getUrl('/delete/'));
        $resp->assertStatus(400);

        $page = Page::find($page->id);
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
}