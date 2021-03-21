<?php namespace Tests\Commands;

use BookStack\Entities\Models\Page;
use BookStack\Entities\Repos\PageRepo;
use Illuminate\Support\Facades\Artisan;
use Tests\TestCase;

class ClearRevisionsCommandTest extends TestCase
{
    public function test_clear_revisions_command()
    {
        $this->asEditor();
        $pageRepo = app(PageRepo::class);
        $page = Page::first();
        $pageRepo->update($page, ['name' => 'updated page', 'html' => '<p>new content</p>', 'summary' => 'page revision testing']);
        $pageRepo->updatePageDraft($page, ['name' => 'updated page', 'html' => '<p>new content in draft</p>', 'summary' => 'page revision testing']);

        $this->assertDatabaseHas('page_revisions', [
            'page_id' => $page->id,
            'type' => 'version'
        ]);
        $this->assertDatabaseHas('page_revisions', [
            'page_id' => $page->id,
            'type' => 'update_draft'
        ]);

        $exitCode = Artisan::call('bookstack:clear-revisions');
        $this->assertTrue($exitCode === 0, 'Command executed successfully');

        $this->assertDatabaseMissing('page_revisions', [
            'page_id' => $page->id,
            'type' => 'version'
        ]);
        $this->assertDatabaseHas('page_revisions', [
            'page_id' => $page->id,
            'type' => 'update_draft'
        ]);

        $exitCode = Artisan::call('bookstack:clear-revisions', ['--all' => true]);
        $this->assertTrue($exitCode === 0, 'Command executed successfully');

        $this->assertDatabaseMissing('page_revisions', [
            'page_id' => $page->id,
            'type' => 'update_draft'
        ]);
    }
}