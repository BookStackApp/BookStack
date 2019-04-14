<?php namespace Tests;

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
        $this->setSettings(['app-homepage' => $customPage->id]);

        $homeVisit = $this->get('/');
        $homeVisit->assertSee($name);

        $pageDeleteReq = $this->delete($customPage->getUrl());
        $pageDeleteReq->assertStatus(302);
        $pageDeleteReq->assertRedirect($customPage->getUrl());
        $pageDeleteReq->assertSessionHas('error');
        $pageDeleteReq->assertSessionMissing('success');

        $homeVisit = $this->get('/');
        $homeVisit->assertSee($name);
        $homeVisit->assertStatus(200);
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

        $this->setSettings(['app-homepage-type' => 'bookshelves']);

        $this->asEditor();
        $homeVisit = $this->get('/');
        $homeVisit->assertSee('Shelves');
        $homeVisit->assertSee('bookshelf-grid-item grid-card');
        $homeVisit->assertSee('grid-card-content');
        $homeVisit->assertSee('grid-card-footer');
        $homeVisit->assertSee('featured-image-container');

        $this->setSettings(['app-homepage-type' => false]);
        $this->test_default_homepage_visible();
    }
}
