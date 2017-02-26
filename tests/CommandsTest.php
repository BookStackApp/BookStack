<?php namespace Tests;

use BookStack\JointPermission;
use BookStack\Page;
use BookStack\Repos\EntityRepo;

class CommandsTest extends TestCase
{

    public function test_clear_views_command()
    {
        $this->asEditor();
        $page = Page::first();

        $this->get($page->getUrl());

        $this->assertDatabaseHas('views', [
            'user_id' => $this->getEditor()->id,
            'viewable_id' => $page->id,
            'views' => 1
        ]);

        $exitCode = \Artisan::call('bookstack:clear-views');
        $this->assertTrue($exitCode === 0, 'Command executed successfully');

        $this->assertDatabaseMissing('views', [
            'user_id' => $this->getEditor()->id
        ]);
    }

    public function test_clear_activity_command()
    {
        $this->asEditor();
        $page = Page::first();
        \Activity::add($page, 'page_update', $page->book->id);

        $this->assertDatabaseHas('activities', [
            'key' => 'page_update',
            'entity_id' => $page->id,
            'user_id' => $this->getEditor()->id
        ]);

        $exitCode = \Artisan::call('bookstack:clear-activity');
        $this->assertTrue($exitCode === 0, 'Command executed successfully');


        $this->assertDatabaseMissing('activities', [
            'key' => 'page_update'
        ]);
    }

    public function test_clear_revisions_command()
    {
        $this->asEditor();
        $entityRepo = $this->app[EntityRepo::class];
        $page = Page::first();
        $entityRepo->updatePage($page, $page->book_id, ['name' => 'updated page', 'html' => '<p>new content</p>', 'summary' => 'page revision testing']);
        $entityRepo->updatePageDraft($page, ['name' => 'updated page', 'html' => '<p>new content in draft</p>', 'summary' => 'page revision testing']);

        $this->assertDatabaseHas('page_revisions', [
            'page_id' => $page->id,
            'type' => 'version'
        ]);
        $this->assertDatabaseHas('page_revisions', [
            'page_id' => $page->id,
            'type' => 'update_draft'
        ]);

        $exitCode = \Artisan::call('bookstack:clear-revisions');
        $this->assertTrue($exitCode === 0, 'Command executed successfully');

        $this->assertDatabaseMissing('page_revisions', [
            'page_id' => $page->id,
            'type' => 'version'
        ]);
        $this->assertDatabaseHas('page_revisions', [
            'page_id' => $page->id,
            'type' => 'update_draft'
        ]);

        $exitCode = \Artisan::call('bookstack:clear-revisions', ['--all' => true]);
        $this->assertTrue($exitCode === 0, 'Command executed successfully');

        $this->assertDatabaseMissing('page_revisions', [
            'page_id' => $page->id,
            'type' => 'update_draft'
        ]);
    }

    public function test_regen_permissions_command()
    {
        JointPermission::query()->truncate();
        $page = Page::first();

        $this->assertDatabaseMissing('joint_permissions', ['entity_id' => $page->id]);

        $exitCode = \Artisan::call('bookstack:regenerate-permissions');
        $this->assertTrue($exitCode === 0, 'Command executed successfully');

        $this->assertDatabaseHas('joint_permissions', ['entity_id' => $page->id]);
    }
}
