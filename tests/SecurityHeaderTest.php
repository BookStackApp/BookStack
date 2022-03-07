<?php

namespace Tests;

use BookStack\Util\CspService;

class SecurityHeaderTest extends TestCase
{
    public function test_cookies_samesite_lax_by_default()
    {
        $resp = $this->get('/');
        foreach ($resp->headers->getCookies() as $cookie) {
            $this->assertEquals('lax', $cookie->getSameSite());
        }
    }

    public function test_cookies_samesite_none_when_iframe_hosts_set()
    {
        $this->runWithEnv('ALLOWED_IFRAME_HOSTS', 'http://example.com', function () {
            $resp = $this->get('/');
            foreach ($resp->headers->getCookies() as $cookie) {
                $this->assertEquals('none', $cookie->getSameSite());
            }
        });
    }

    public function test_secure_cookies_controlled_by_app_url()
    {
        $this->runWithEnv('APP_URL', 'http://example.com', function () {
            $resp = $this->get('/');
            foreach ($resp->headers->getCookies() as $cookie) {
                $this->assertFalse($cookie->isSecure());
            }
        });

        $this->runWithEnv('APP_URL', 'https://example.com', function () {
            $resp = $this->get('/');
            foreach ($resp->headers->getCookies() as $cookie) {
                $this->assertTrue($cookie->isSecure());
            }
        });
    }

    public function test_iframe_csp_self_only_by_default()
    {
        $resp = $this->get('/');
        $frameHeader = $this->getCspHeader($resp, 'frame-ancestors');

        $this->assertEquals('frame-ancestors \'self\'', $frameHeader);
    }

    public function test_iframe_csp_includes_extra_hosts_if_configured()
    {
        $this->runWithEnv('ALLOWED_IFRAME_HOSTS', 'https://a.example.com https://b.example.com', function () {
            $resp = $this->get('/');
            $frameHeader = $this->getCspHeader($resp, 'frame-ancestors');

            $this->assertNotEmpty($frameHeader);
            $this->assertEquals('frame-ancestors \'self\' https://a.example.com https://b.example.com', $frameHeader);
        });
    }

    public function test_script_csp_set_on_responses()
    {
        $resp = $this->get('/');
        $scriptHeader = $this->getCspHeader($resp, 'script-src');
        $this->assertStringContainsString('\'strict-dynamic\'', $scriptHeader);
        $this->assertStringContainsString('\'nonce-', $scriptHeader);
    }

    public function test_script_csp_nonce_matches_nonce_used_in_custom_head()
    {
        $this->setSettings(['app-custom-head' => '<script>console.log("cat");</script>']);
        $resp = $this->get('/login');
        $scriptHeader = $this->getCspHeader($resp, 'script-src');

        $nonce = app()->make(CspService::class)->getNonce();
        $this->assertStringContainsString('nonce-' . $nonce, $scriptHeader);
        $resp->assertSee('<script nonce="' . $nonce . '">console.log("cat");</script>', false);
    }

    public function test_script_csp_nonce_changes_per_request()
    {
        $resp = $this->get('/');
        $firstHeader = $this->getCspHeader($resp, 'script-src');

        $this->refreshApplication();

        $resp = $this->get('/');
        $secondHeader = $this->getCspHeader($resp, 'script-src');

        $this->assertNotEquals($firstHeader, $secondHeader);
    }

    public function test_allow_content_scripts_settings_controls_csp_script_headers()
    {
        config()->set('app.allow_content_scripts', true);
        $resp = $this->get('/');
        $scriptHeader = $this->getCspHeader($resp, 'script-src');
        $this->assertEmpty($scriptHeader);

        config()->set('app.allow_content_scripts', false);
        $resp = $this->get('/');
        $scriptHeader = $this->getCspHeader($resp, 'script-src');
        $this->assertNotEmpty($scriptHeader);
    }

    public function test_object_src_csp_header_set()
    {
        $resp = $this->get('/');
        $scriptHeader = $this->getCspHeader($resp, 'object-src');
        $this->assertEquals('object-src \'self\'', $scriptHeader);
    }

    public function test_base_uri_csp_header_set()
    {
        $resp = $this->get('/');
        $scriptHeader = $this->getCspHeader($resp, 'base-uri');
        $this->assertEquals('base-uri \'self\'', $scriptHeader);
    }

    public function test_frame_src_csp_header_set()
    {
        $resp = $this->get('/');
        $scriptHeader = $this->getCspHeader($resp, 'frame-src');
        $this->assertEquals('frame-src \'self\' https://*.draw.io https://*.youtube.com https://*.youtube-nocookie.com https://*.vimeo.com', $scriptHeader);
    }

    public function test_frame_src_csp_header_has_drawio_host_added()
    {
        config()->set([
            'app.iframe_sources' => 'https://example.com',
            'services.drawio'   => 'https://diagrams.example.com/testing?cat=dog',
        ]);

        $resp = $this->get('/');
        $scriptHeader = $this->getCspHeader($resp, 'frame-src');
        $this->assertEquals('frame-src \'self\' https://example.com https://diagrams.example.com', $scriptHeader);
    }

    public function test_cache_control_headers_are_strict_on_responses_when_logged_in()
    {
        $this->asEditor();
        $resp = $this->get('/');
        $resp->assertHeader('Cache-Control', 'max-age=0, no-store, private');
        $resp->assertHeader('Pragma', 'no-cache');
        $resp->assertHeader('Expires', 'Sun, 12 Jul 2015 19:01:00 GMT');
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
