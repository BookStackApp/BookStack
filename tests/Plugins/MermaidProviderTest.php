<?php

namespace Tests\Unit\Plugins;

use Tests\TestCase;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use BookStack\Plugins\MermaidProvider;

class MermaidProviderTest extends TestCase
{
    public function test_returns_versions_from_github_with_cache()
    {
        Http::fake([
            MermaidProvider::MERMAID_REPOSITORY => Http::response([
                ['name' => 'v10.0.0'],
                ['name' => 'v9.4.0'],
            ]),
        ]);

        $provider = new MermaidProvider();
        $versions = $provider->getMermaidVersions();

        $this->assertContains('disabled', $versions);
        $this->assertContains('latest', $versions);
        $this->assertContains('v10.0.0', $versions);
        $this->assertContains('v9.4.0', $versions);
    }

    public function test_returns_cached_version_if_available()
    {
        Cache::shouldReceive('get')
            ->once()
            ->with('git_mermaid_versions')
            ->andReturn(['v1.0.0', 'v2.0.0']);

        $provider = new MermaidProvider();
        $versions = $provider->getMermaidVersions();

        $this->assertEquals(['disabled', 'latest', 'v1.0.0', 'v2.0.0'], $versions);
    }

    public function test_fetches_versions_and_caches_on_successful_response()
    {
        Cache::shouldReceive('get')
            ->once()
            ->with('git_mermaid_versions')
            ->andReturn(null);

        Cache::shouldReceive('put')
            ->once()
            ->withArgs(function ($key, $value, $ttl) {
                return $key === 'git_mermaid_versions'
                    && in_array('v10.0.0', $value)
                    && $ttl->greaterThan(now());
            });

        Http::fake([
            MermaidProvider::MERMAID_REPOSITORY => Http::response([
                ['name' => 'v10.0.0'],
                ['name' => 'v9.4.0'],
            ]),
        ]);

        $provider = new MermaidProvider();
        $versions = $provider->getMermaidVersions();

        $this->assertContains('v10.0.0', $versions);
        $this->assertContains('disabled', $versions);
        $this->assertContains('latest', $versions);
    }

    public function test_does_not_cache_on_failed_response()
    {
        Cache::shouldReceive('get')
            ->once()
            ->with('git_mermaid_versions')
            ->andReturn(null);

        Cache::shouldReceive('put')->never();

        Http::fake([
            '*' => Http::response(null, 500),
        ]);

        $provider = new MermaidProvider();
        $versions = $provider->getMermaidVersions();

        $this->assertEquals(['disabled', 'latest'], $versions);
    }

    public function test_returns_correct_mermaid_cdn_uri()
    {
        $this->setSettings(['enable-mermaid' => '10.1.0']);

        $provider = new MermaidProvider();
        $uri = $provider->getMermaidJsCdnUri();

        $this->assertEquals('http://localhost/mermaid/mermaid.min.js', $uri);
    }

    public function test_returns_empty_string_if_mermaid_disabled()
    {
        $this->setSettings(['enable-mermaid' => 'disabled']);

        $provider = new MermaidProvider();
        $uri = $provider->getMermaidJsCdnUri();

        $this->assertEquals('', $uri);
    }
}