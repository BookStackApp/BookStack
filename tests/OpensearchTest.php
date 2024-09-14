<?php

namespace Tests;

class OpensearchTest extends TestCase
{
    public function test_opensearch_endpoint()
    {
        $appName = setting('app-name');
        $resultUrl = url('/search') . '?term={searchTerms}';
        $selfUrl = url('/opensearch.xml');

        $resp = $this->get('/opensearch.xml');
        $resp->assertOk();

        $html = $this->withHtml($resp);

        $html->assertElementExists('OpenSearchDescription > ShortName');
        $html->assertElementContains('OpenSearchDescription > ShortName', mb_strimwidth($appName, 0, 16));

        $html->assertElementExists('OpenSearchDescription > Description');
        $html->assertElementContains('OpenSearchDescription > Description', trans('common.opensearch_description', [
            'appName' => $appName,
        ]));

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
