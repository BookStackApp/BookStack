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
    }

    public function test_custom_homepage()
    {
        $this->asEditor();
        $name = 'My custom homepage';
        $content = 'This is the body content of my custom homepage.';
        $customPage = $this->newPage(['name' => $name, 'html' => $content]);
        $this->setSettings(['app-homepage' => $customPage->id]);

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
        $content = 'This is the body content of my custom homepage.';
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

        $this->setSettings(['app-book-homepage' => true]);

        $this->asEditor();
        $homeVisit = $this->get('/');
        $homeVisit->assertSee('Books');
        $homeVisit->assertSee('book-grid-item grid-card');
        $homeVisit->assertSee('grid-card-content');
        $homeVisit->assertSee('grid-card-footer');
        $homeVisit->assertSee('featured-image-container');

        $this->setSettings(['app-book-homepage' => false]);
        $this->test_default_homepage_visible();
    }
}
