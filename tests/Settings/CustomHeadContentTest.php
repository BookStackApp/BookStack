<?php

namespace Tests\Settings;

use Tests\TestCase;

class CustomHeadContentTest extends TestCase
{
    public function test_configured_content_shows_on_pages()
    {
        $this->setSettings(['app-custom-head' => '<script>console.log("cat");</script>']);
        $resp = $this->get('/login');
        $resp->assertSee('console.log("cat")');
    }

    public function test_configured_content_does_not_show_on_settings_page()
    {
        $this->setSettings(['app-custom-head' => '<script>console.log("cat");</script>']);
        $resp = $this->asAdmin()->get('/settings');
        $resp->assertDontSee('console.log("cat")');
    }

    public function test_divs_in_js_preserved_in_configured_content()
    {
        $this->setSettings(['app-custom-head' => '<script><div id="hello">cat</div></script>']);
        $resp = $this->get('/login');
        $resp->assertSee('<div id="hello">cat</div>');
    }
}
