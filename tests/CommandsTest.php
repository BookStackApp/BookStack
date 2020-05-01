<?php namespace Tests;

use BookStack\Actions\Comment;
use BookStack\Actions\CommentRepo;
use BookStack\Auth\Permissions\JointPermission;
use BookStack\Entities\Bookshelf;
use BookStack\Entities\Page;
use BookStack\Auth\User;
use BookStack\Entities\Repos\PageRepo;
use Symfony\Component\Console\Exception\RuntimeException;

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

    public function test_add_admin_command()
    {
        $exitCode = \Artisan::call('bookstack:create-admin', [
            '--email' => 'admintest@example.com',
            '--name' => 'Admin Test',
            '--password' => 'testing-4',
        ]);
        $this->assertTrue($exitCode === 0, 'Command executed successfully');

        $this->assertDatabaseHas('users', [
            'email' => 'admintest@example.com',
            'name' => 'Admin Test'
        ]);

        $this->assertTrue(User::where('email', '=', 'admintest@example.com')->first()->hasSystemRole('admin'), 'User has admin role as expected');
        $this->assertTrue(\Auth::attempt(['email' => 'admintest@example.com', 'password' => 'testing-4']), 'Password stored as expected');
    }

    public function test_copy_shelf_permissions_command_shows_error_when_no_required_option_given()
    {
        $this->artisan('bookstack:copy-shelf-permissions')
            ->expectsOutput('Either a --slug or --all option must be provided.')
            ->assertExitCode(0);
    }

    public function test_copy_shelf_permissions_command_using_slug()
    {
        $shelf = Bookshelf::first();
        $child = $shelf->books()->first();
        $editorRole = $this->getEditor()->roles()->first();
        $this->assertFalse(boolval($child->restricted), "Child book should not be restricted by default");
        $this->assertTrue($child->permissions()->count() === 0, "Child book should have no permissions by default");

        $this->setEntityRestrictions($shelf, ['view', 'update'], [$editorRole]);
        $this->artisan('bookstack:copy-shelf-permissions', [
            '--slug' => $shelf->slug,
        ]);
        $child = $shelf->books()->first();

        $this->assertTrue(boolval($child->restricted), "Child book should now be restricted");
        $this->assertTrue($child->permissions()->count() === 2, "Child book should have copied permissions");
        $this->assertDatabaseHas('entity_permissions', ['restrictable_id' => $child->id, 'action' => 'view', 'role_id' => $editorRole->id]);
        $this->assertDatabaseHas('entity_permissions', ['restrictable_id' => $child->id, 'action' => 'update', 'role_id' => $editorRole->id]);
    }

    public function test_copy_shelf_permissions_command_using_all()
    {
        $shelf = Bookshelf::query()->first();
        Bookshelf::query()->where('id', '!=', $shelf->id)->delete();
        $child = $shelf->books()->first();
        $editorRole = $this->getEditor()->roles()->first();
        $this->assertFalse(boolval($child->restricted), "Child book should not be restricted by default");
        $this->assertTrue($child->permissions()->count() === 0, "Child book should have no permissions by default");

        $this->setEntityRestrictions($shelf, ['view', 'update'], [$editorRole]);
        $this->artisan('bookstack:copy-shelf-permissions --all')
            ->expectsQuestion('Permission settings for all shelves will be cascaded. Books assigned to multiple shelves will receive only the permissions of it\'s last processed shelf. Are you sure you want to proceed?', 'y');
        $child = $shelf->books()->first();

        $this->assertTrue(boolval($child->restricted), "Child book should now be restricted");
        $this->assertTrue($child->permissions()->count() === 2, "Child book should have copied permissions");
        $this->assertDatabaseHas('entity_permissions', ['restrictable_id' => $child->id, 'action' => 'view', 'role_id' => $editorRole->id]);
        $this->assertDatabaseHas('entity_permissions', ['restrictable_id' => $child->id, 'action' => 'update', 'role_id' => $editorRole->id]);
    }

    public function test_update_url_command_updates_page_content()
    {
        $page = Page::query()->first();
        $page->html = '<a href="https://example.com/donkeys"></a>';
        $page->save();

        $this->artisan('bookstack:update-url https://example.com https://cats.example.com')
            ->expectsQuestion("This will search for \"https://example.com\" in your database and replace it with  \"https://cats.example.com\".\nAre you sure you want to proceed?", 'y')
            ->expectsQuestion("This operation could cause issues if used incorrectly. Have you made a backup of your existing database?", 'y');

        $this->assertDatabaseHas('pages', [
            'id' => $page->id,
            'html' => '<a href="https://cats.example.com/donkeys"></a>'
        ]);
    }

    public function test_update_url_command_requires_valid_url()
    {
        $badUrlMessage = "The given urls are expected to be full urls starting with http:// or https://";
        $this->artisan('bookstack:update-url //example.com https://cats.example.com')->expectsOutput($badUrlMessage);
        $this->artisan('bookstack:update-url https://example.com htts://cats.example.com')->expectsOutput($badUrlMessage);
        $this->artisan('bookstack:update-url example.com https://cats.example.com')->expectsOutput($badUrlMessage);

        $this->expectException(RuntimeException::class);
        $this->artisan('bookstack:update-url https://cats.example.com');
    }

    public function test_regenerate_comment_content_command()
    {
        Comment::query()->forceCreate([
            'html' => 'some_old_content',
            'text' => 'some_fresh_content',
        ]);

        $this->assertDatabaseHas('comments', [
            'html' => 'some_old_content',
        ]);

        $exitCode = \Artisan::call('bookstack:regenerate-comment-content');
        $this->assertTrue($exitCode === 0, 'Command executed successfully');

        $this->assertDatabaseMissing('comments', [
            'html' => 'some_old_content',
        ]);
        $this->assertDatabaseHas('comments', [
            'html' => "<p>some_fresh_content</p>\n",
        ]);
    }
}
