<?php

namespace Tests\Meta;

use Tests\TestCase;

class LicensesTest extends TestCase
{
    public function test_licenses_endpoint()
    {
        $resp = $this->get('/licenses');
        $resp->assertOk();
        $resp->assertSee('Licenses');
        $resp->assertSee('PHP Library Licenses');
        $resp->assertSee('Dan Brown and the BookStack Project contributors');
        $resp->assertSee('doctrine/dbal');
        $resp->assertSee('@codemirror/lang-html');
    }

    public function test_licenses_linked_to_from_settings()
    {
        $resp = $this->asAdmin()->get('/settings/features');
        $html = $this->withHtml($resp);
        $html->assertLinkExists(url('/licenses'), 'License Details');
    }
}
