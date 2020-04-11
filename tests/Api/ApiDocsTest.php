<?php namespace Tests\Api;

use Tests\TestCase;

class ApiDocsTest extends TestCase
{
    use TestsApi;

    protected $endpoint = '/api/docs';

    public function test_docs_page_not_visible_to_normal_viewers()
    {
        $viewer = $this->getViewer();
        $resp = $this->actingAs($viewer)->get($this->endpoint);
        $resp->assertStatus(403);

        $resp = $this->actingAsApiEditor()->get($this->endpoint);
        $resp->assertStatus(200);
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
            'docs' => [ [
                'name' => 'docs-display',
                'uri' => 'api/docs'
            ] ]
        ]);
    }
}