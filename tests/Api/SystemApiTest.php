<?php

namespace Tests\Api;

use Tests\TestCase;

class SystemApiTest extends TestCase
{
    use TestsApi;

    public function test_read_returns_app_info(): void
    {
        $resp = $this->actingAsApiEditor()->get('/api/system');
        $data = $resp->json();

        $this->assertStringStartsWith('v', $data['version']);
        $this->assertEquals(setting('instance-id'), $data['instance_id']);
        $this->assertEquals(setting('app-name'), $data['app_name']);
        $this->assertEquals(url('/logo.png'), $data['app_logo']);
        $this->assertEquals(url('/'), $data['base_url']);
    }
}
