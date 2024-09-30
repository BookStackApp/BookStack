<?php

namespace Tests\Meta;

use Tests\TestCase;

class RobotsTest extends TestCase
{
    public function test_robots_effected_by_public_status()
    {
        $this->get('/robots.txt')->assertSee("User-agent: *\nDisallow: /");

        $this->setSettings(['app-public' => 'true']);

        $resp = $this->get('/robots.txt');
        $resp->assertSee("User-agent: *\nDisallow:");
        $resp->assertDontSee('Disallow: /');
    }

    public function test_robots_effected_by_setting()
    {
        $this->get('/robots.txt')->assertSee("User-agent: *\nDisallow: /");

        config()->set('app.allow_robots', true);

        $resp = $this->get('/robots.txt');
        $resp->assertSee("User-agent: *\nDisallow:");
        $resp->assertDontSee('Disallow: /');

        // Check config overrides app-public setting
        config()->set('app.allow_robots', false);
        $this->setSettings(['app-public' => 'true']);
        $this->get('/robots.txt')->assertSee("User-agent: *\nDisallow: /");
    }
}
