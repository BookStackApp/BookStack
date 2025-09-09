<?php

namespace Tests\Meta;

use Tests\TestCase;

class OpensearchTest extends TestCase
{
    public function test_opensearch_endpoint()
    {
        $appName = 'MyAppNameThatsReallyLongLikeThis';
        setting()->put('app-name', $appName);
        $resultUrl = url('/search') . '?term={searchTerms}';
        $selfUrl = url('/opensearch.xml');

        $resp = $this->get('/opensearch.xml');
        $resp->assertOk();
        $resp->assertSee('<?xml version="1.0" encoding="UTF-8"?>' . "\n", false);

        $html = $this->withHtml($resp);

        $html->assertElementExists('OpenSearchDescription > ShortName');
        $html->assertElementContains('OpenSearchDescription > ShortName', mb_strimwidth($appName, 0, 16));
        $html->assertElementNotContains('OpenSearchDescription > ShortName', $appName);

        $html->assertElementExists('OpenSearchDescription > Description');
        $html->assertElementContains('OpenSearchDescription > Description', "Search {$appName}");
        $html->assertElementExists('OpenSearchDescription > Image');
        $html->assertElementExists('OpenSearchDescription > Url[rel="results"][template="' . htmlspecialchars($resultUrl) . '"]');
        $html->assertElementExists('OpenSearchDescription > Url[rel="self"][template="' . htmlspecialchars($selfUrl) . '"]');
    }

    public function test_opensearch_linked_to_from_home()
    {
        $appName = setting('app-name');
        $endpointUrl = url('/opensearch.xml');

        $resp = $this->asViewer()->get('/');
        $html = $this->withHtml($resp);

        $html->assertElementExists('head > link[rel="search"][type="application/opensearchdescription+xml"][title="' . htmlspecialchars($appName) . '"][href="' . htmlspecialchars($endpointUrl) . '"]');
    }
}
