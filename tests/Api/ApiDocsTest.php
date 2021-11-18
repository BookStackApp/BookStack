<?php

namespace Tests\Api;

use Tests\TestCase;

class ApiDocsTest extends TestCase
{
    use TestsApi;

    protected $endpoint = '/api/docs';

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
        $resp->assertHeader('Content-Type', 'text/html; charset=UTF-8');
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
}
