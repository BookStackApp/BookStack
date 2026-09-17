<?php

namespace Tests\Api;

use Tests\TestCase;

class ApiDocsTest extends TestCase
{
    use TestsApi;

    protected string $endpoint = '/api/docs';

    public function test_api_endpoint_redirects_to_docs()
    {
        $resp = $this->actingAsApiEditor()->get('/api');
        $resp->assertRedirect('api/docs');
    }

    public function test_docs_page_returns_view_with_docs_content()
    {
        $resp = $this->actingAsApiEditor()->get($this->endpoint);
        $resp->assertStatus(200);
        $resp->assertSee(url('/api/docs.json'));
        $resp->assertSee('Show a JSON view of the API docs data.');
        $resp->assertHeader('Content-Type', 'text/html; charset=utf-8');
    }

    public function test_docs_json_endpoint_returns_json()
    {
        $resp = $this->actingAsApiEditor()->get($this->endpoint . '.json');
        $resp->assertStatus(200);
        $resp->assertHeader('Content-Type', 'application/json');
        $resp->assertJson([
            'docs' => [[
                'name' => 'docs-display',
                'uri'  => 'api/docs',
            ]],
        ]);
    }

    public function test_download_endpoint_returns_html_by_default()
    {
        $resp = $this->actingAsApiEditor()->get($this->endpoint . '/download');
        $resp->assertStatus(200);
        $resp->assertHeader('Content-Type', 'application/octet-stream');
        $resp->assertHeader('Content-Disposition', 'attachment; filename*=UTF-8\'\'bookstack-api-docs.html');

        $resp->assertSee('BookStack API Documentation');
        $resp->assertSee('Getting Started');
        $resp->assertSee('content-permissions-update');
        $resp->assertDontSee('Jump To Section');
    }

    public function test_download_endpoint_returns_json_when_requested()
    {
        $resp = $this->actingAsApiEditor()->get($this->endpoint . '/download?format=json');
        $resp->assertStatus(200);
        $resp->assertHeader('Content-Type', 'application/octet-stream');
        $resp->assertHeader('Content-Disposition', 'attachment; filename*=UTF-8\'\'bookstack-api-docs.json');

        $resp->assertSee('Getting Started');
        $resp->assertSee('content-permissions-update');
        $resp->assertDontSee('Jump To Section');

        $content = $resp->json('getting-started-guide');
        $this->assertIsString($content);
        $this->assertGreaterThan(100, strlen($content));

        $resp->assertJson([
            'docs' => [[
                'name' => 'docs-display',
                'uri'  => 'api/docs',
            ]],
        ]);
    }
}
