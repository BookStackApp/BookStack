<?php namespace Tests;

use BookStack\JointPermission;
use BookStack\Page;
use BookStack\Repos\EntityRepo;

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

    public function test_custom_homepage() {
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
}
