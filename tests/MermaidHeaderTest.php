<?php

namespace Tests;

use BookStack\Util\CspService;
use Illuminate\Testing\TestResponse;

class MermaidHeaderTest extends TestCase
{
    public function test_script_csp_nonce_matches_nonce_used_in_mermaid_include_script()
    {
        $this->setSettings(['enable-mermaid' => '1111']);

        $page = $this->entities->pageWithinChapter();
        $resp = $this->asAdmin()->get($page->getUrl());
        $scriptHeader = $this->getCspHeader($resp, 'script-src');

        $nonce = app()->make(CspService::class)->getNonce();
        $this->assertStringContainsString('nonce-'.$nonce, $scriptHeader);
        $resp->assertSee(
            '<script src="http://localhost/mermaid/mermaid.min.js" nonce="'.$nonce.'"></script>',
            false
        );
    }
    public function test_script_mermaid_not_include_if_setting_value_is_disabled()
    {
        $this->setSettings(['enable-mermaid' => 'disabled']);

        $page = $this->entities->pageWithinChapter();
        $resp = $this->asAdmin()->get($page->getUrl());
        $scriptHeader = $this->getCspHeader($resp, 'script-src');

        $nonce = app()->make(CspService::class)->getNonce();
        $this->assertStringContainsString('nonce-'.$nonce, $scriptHeader);
        $resp->assertDontSee(
            '<script src="http://localhost/mermaid/mermaid.min.js" nonce="'.$nonce.'"></script>',
            false
        );
    }

    /**
     * Get the value of the first CSP header of the given type.
     */
    protected function getCspHeader(TestResponse $resp, string $type): string
    {
        $cspHeaders = explode('; ', $resp->headers->get('Content-Security-Policy'));

        foreach ($cspHeaders as $cspHeader) {
            if (strpos($cspHeader, $type) === 0) {
                return $cspHeader;
            }
        }

        return '';
    }
}