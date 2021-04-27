<?php namespace Tests;

use BookStack\Auth\Role;
use BookStack\Auth\User;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Page;

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
        $customPage = $this->newPage(['name' => $name, 'html' => $content]);
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
        $customPage = $this->newPage(['name' => $name, 'html' => $content]);
        $this->setSettings([
            'app-homepage' => $customPage->id,
            'app-homepage-type' => 'page'
        ]);

        $homeVisit = $this->get('/');
        $homeVisit->assertSee($name);
        $homeVisit->assertElementNotExists('#home-default');

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
        $customPage = $this->newPage(['name' => $name, 'html' => $content]);
        $this->setSettings([
            'app-homepage' => $customPage->id,
            'app-homepage-type' => 'default'
        ]);

        $pageDeleteReq = $this->delete($customPage->getUrl());
        $pageDeleteReq->assertStatus(302);
        $pageDeleteReq->assertSessionHas('success');
        $pageDeleteReq->assertSessionMissing('error');
    }

    public function test_set_book_homepage()
    {
        $editor = $this->getEditor();
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
        $editor = $this->getEditor();
        setting()->putUser($editor, 'bookshelves_view_type', 'grid');
        $shelf = Bookshelf::query()->firstOrFail();

        $this->setSettings(['app-homepage-type' => 'bookshelves']);

        $this->asEditor();
        $homeVisit = $this->get('/');
        $homeVisit->assertSee('Shelves');
        $homeVisit->assertSee('grid-card-content');
        $homeVisit->assertSee('featured-image-container');
        $homeVisit->assertElementContains('.grid-card', $shelf->name);

        $this->setSettings(['app-homepage-type' => false]);
        $this->test_default_homepage_visible();
    }

    public function test_shelves_list_homepage_adheres_to_book_visibility_permissions()
    {
        $editor = $this->getEditor();
        setting()->putUser($editor, 'bookshelves_view_type', 'list');
        $this->setSettings(['app-homepage-type' => 'bookshelves']);
        $this->asEditor();

        $shelf = Bookshelf::query()->first();
        $book = $shelf->books()->first();

        // Ensure initially visible
        $homeVisit = $this->get('/');
        $homeVisit->assertElementContains('.content-wrap', $shelf->name);
        $homeVisit->assertElementContains('.content-wrap', $book->name);

        // Ensure book no longer visible without view permission
        $editor->roles()->detach();
        $this->giveUserPermissions($editor, ['bookshelf-view-all']);
        $homeVisit = $this->get('/');
        $homeVisit->assertElementContains('.content-wrap', $shelf->name);
        $homeVisit->assertElementNotContains('.content-wrap', $book->name);

        // Ensure is visible again with entity-level view permission
        $this->setEntityRestrictions($book, ['view'], [$editor->roles()->first()]);
        $homeVisit = $this->get('/');
        $homeVisit->assertElementContains('.content-wrap', $shelf->name);
        $homeVisit->assertElementContains('.content-wrap', $book->name);
    }

    public function test_new_users_dont_have_any_recently_viewed()
    {
        $user = factory(User::class)->create();
        $viewRole = Role::getRole('Viewer');
        $user->attachRole($viewRole);

        $homeVisit = $this->actingAs($user)->get('/');
        $homeVisit->assertElementContains('#recently-viewed', 'You have not viewed any pages');
    }
}
