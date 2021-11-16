<?php

namespace Tests\Settings;

use BookStack\Util\CspService;
use Tests\TestCase;

class CustomHeadContentTest extends TestCase
{
    public function test_configured_content_shows_on_pages()
    {
        $this->setSettings(['app-custom-head' => '<script>console.log("cat");</script>']);
        $resp = $this->get('/login');
        $resp->assertSee('console.log("cat")', false);
    }

    public function test_configured_content_does_not_show_on_settings_page()
    {
        $this->setSettings(['app-custom-head' => '<script>console.log("cat");</script>']);
        $resp = $this->asAdmin()->get('/settings');
        $resp->assertDontSee('console.log("cat")', false);
    }

    public function test_divs_in_js_preserved_in_configured_content()
    {
        $this->setSettings(['app-custom-head' => '<script><div id="hello">cat</div></script>']);
        $resp = $this->get('/login');
        $resp->assertSee('<div id="hello">cat</div>', false);
    }

    public function test_nonce_application_handles_edge_cases()
    {
        $mockCSP = $this->mock(CspService::class);
        $mockCSP->shouldReceive('getNonce')->andReturn('abc123');

        $content = trim('
<script>console.log("cat");</script>
<script type="text/html"><\script>const a = `<div></div>`<\/\script></script>
<script >const a = `<div></div>`;</script>
<script type="<script text>test">const c = `<div></div>`;</script>
<script
    type="text/html"
>
const a = `<\script><\/script>`;
const b = `<script`;
</script>
<SCRIPT>const b = `↗️£`;</SCRIPT>
        ');

        $expectedOutput = trim('
<script nonce="abc123">console.log("cat");</script>
<script type="text/html" nonce="abc123"><\script>const a = `<div></div>`<\/\script></script>
<script nonce="abc123">const a = `<div></div>`;</script>
<script type="&lt;script text&gt;test" nonce="abc123">const c = `<div></div>`;</script>
<script type="text/html" nonce="abc123">
const a = `<\script><\/script>`;
const b = `<script`;
</script>
<script nonce="abc123">const b = `↗️£`;</script>
        ');

        $this->setSettings(['app-custom-head' => $content]);
        $resp = $this->get('/login');
        $resp->assertSee($expectedOutput, false);
    }
}
