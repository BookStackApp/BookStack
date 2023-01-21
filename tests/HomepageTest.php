<?php

namespace Tests;

use BookStack\Auth\Role;
use BookStack\Auth\User;

class HomepageTest extends TestCase
{
    public function test_default_homepage_visible()
    {
        $this->asEditor();
        $homeVisit = $this->get('/');
        $homeVisit->assertSee('My Recently Viewed');
        $homeVisit->assertSee('Recently Updated Pages');
        $homeVisit->assertSee('Recent Activity');
        $homeVisit->assertSee('home-default');
    }

    public function test_custom_homepage()
    {
        $this->asEditor();
        $name = 'My custom homepage';
        $content = str_repeat('This is the body content of my custom homepage.', 20);
        $customPage = $this->entities->newPage(['name' => $name, 'html' => $content]);
        $this->setSettings(['app-homepage' => $customPage->id]);
        $this->setSettings(['app-homepage-type' => 'page']);

        $homeVisit = $this->get('/');
        $homeVisit->assertSee($name);
        $homeVisit->assertSee($content);
        $homeVisit->assertSee('My Recently Viewed');
        $homeVisit->assertSee('Recently Updated Pages');
        $homeVisit->assertSee('Recent Activity');
    }

    public function test_delete_custom_homepage()
    {
        $this->asEditor();
        $name = 'My custom homepage';
        $content = str_repeat('This is the body content of my custom homepage.', 20);
        $customPage = $this->entities->newPage(['name' => $name, 'html' => $content]);
        $this->setSettings([
            'app-homepage'      => $customPage->id,
            'app-homepage-type' => 'page',
        ]);

        $homeVisit = $this->get('/');
        $homeVisit->assertSee($name);
        $this->withHtml($homeVisit)->assertElementNotExists('#home-default');

        $pageDeleteReq = $this->delete($customPage->getUrl());
        $pageDeleteReq->assertStatus(302);
        $pageDeleteReq->assertRedirect($customPage->getUrl());
        $pageDeleteReq->assertSessionHas('error');
        $pageDeleteReq->assertSessionMissing('success');

        $homeVisit = $this->get('/');
        $homeVisit->assertSee($name);
        $homeVisit->assertStatus(200);
    }

    public function test_custom_homepage_can_be_deleted_once_custom_homepage_no_longer_used()
    {
        $this->asEditor();
        $name = 'My custom homepage';
        $content = str_repeat('This is the body content of my custom homepage.', 20);
        $customPage = $this->entities->newPage(['name' => $name, 'html' => $content]);
        $this->setSettings([
            'app-homepage'      => $customPage->id,
            'app-homepage-type' => 'default',
        ]);

        $pageDeleteReq = $this->delete($customPage->getUrl());
        $pageDeleteReq->assertStatus(302);
        $pageDeleteReq->assertSessionHas('success');
        $pageDeleteReq->assertSessionMissing('error');
    }

    public function test_custom_homepage_cannot_be_deleted_from_parent_deletion()
    {
        $page = $this->entities->page();
        $this->setSettings([
            'app-homepage'      => $page->id,
            'app-homepage-type' => 'page',
        ]);

        $this->asEditor()->delete($page->book->getUrl());
        $this->assertSessionError('Cannot delete a page while it is set as a homepage');
        $this->assertDatabaseMissing('deletions', ['deletable_id' => $page->book->id]);

        $page->refresh();
        $this->assertNull($page->deleted_at);
        $this->assertNull($page->book->deleted_at);
    }

    public function test_custom_homepage_renders_includes()
    {
        $this->asEditor();
        $included = $this->entities->page();
        $content = str_repeat('This is the body content of my custom homepage.', 20);
        $included->html = $content;
        $included->save();

        $name = 'My custom homepage';
        $customPage = $this->entities->newPage(['name' => $name, 'html' => '{{@' . $included->id . '}}']);
        $this->setSettings(['app-homepage' => $customPage->id]);
        $this->setSettings(['app-homepage-type' => 'page']);

        $homeVisit = $this->get('/');
        $homeVisit->assertSee($name);
        $homeVisit->assertSee($content);
    }

    public function test_set_book_homepage()
    {
        $editor = $this->users->editor();
        setting()->putUser($editor, 'books_view_type', 'grid');

        $this->setSettings(['app-homepage-type' => 'books']);

        $this->asEditor();
        $homeVisit = $this->get('/');
        $homeVisit->assertSee('Books');
        $homeVisit->assertSee('grid-card');
        $homeVisit->assertSee('grid-card-content');
        $homeVisit->assertSee('grid-card-footer');
        $homeVisit->assertSee('featured-image-container');

        $this->setSettings(['app-homepage-type' => false]);
        $this->test_default_homepage_visible();
    }

    public function test_set_bookshelves_homepage()
    {
        $editor = $this->users->editor();
        setting()->putUser($editor, 'bookshelves_view_type', 'grid');
        $shelf = $this->entities->shelf();

        $this->setSettings(['app-homepage-type' => 'bookshelves']);

        $this->asEditor();
        $homeVisit = $this->get('/');
        $homeVisit->assertSee('Shelves');
        $homeVisit->assertSee('grid-card-content');
        $homeVisit->assertSee('featured-image-container');
        $this->withHtml($homeVisit)->assertElementContains('.grid-card', $shelf->name);

        $this->setSettings(['app-homepage-type' => false]);
        $this->test_default_homepage_visible();
    }

    public function test_shelves_list_homepage_adheres_to_book_visibility_permissions()
    {
        $editor = $this->users->editor();
        setting()->putUser($editor, 'bookshelves_view_type', 'list');
        $this->setSettings(['app-homepage-type' => 'bookshelves']);
        $this->asEditor();

        $shelf = $this->entities->shelf();
        $book = $shelf->books()->first();

        // Ensure initially visible
        $homeVisit = $this->get('/');
        $this->withHtml($homeVisit)->assertElementContains('.content-wrap', $shelf->name);
        $this->withHtml($homeVisit)->assertElementContains('.content-wrap', $book->name);

        // Ensure book no longer visible without view permission
        $editor->roles()->detach();
        $this->permissions->grantUserRolePermissions($editor, ['bookshelf-view-all']);
        $homeVisit = $this->get('/');
        $this->withHtml($homeVisit)->assertElementContains('.content-wrap', $shelf->name);
        $this->withHtml($homeVisit)->assertElementNotContains('.content-wrap', $book->name);

        // Ensure is visible again with entity-level view permission
        $this->permissions->setEntityPermissions($book, ['view'], [$editor->roles()->first()]);
        $homeVisit = $this->get('/');
        $this->withHtml($homeVisit)->assertElementContains('.content-wrap', $shelf->name);
        $this->withHtml($homeVisit)->assertElementContains('.content-wrap', $book->name);
    }

    public function test_new_users_dont_have_any_recently_viewed()
    {
        $user = User::factory()->create();
        $viewRole = Role::getRole('Viewer');
        $user->attachRole($viewRole);

        $homeVisit = $this->actingAs($user)->get('/');
        $this->withHtml($homeVisit)->assertElementContains('#recently-viewed', 'You have not viewed any pages');
    }
}
