<?php namespace Tests;


use Illuminate\Support\Str;

class SecurityHeaderTest extends TestCase
{

    public function test_cookies_samesite_lax_by_default()
    {
        $resp = $this->get("/");
        foreach ($resp->headers->getCookies() as $cookie) {
            $this->assertEquals("lax", $cookie->getSameSite());
        }
    }

    public function test_cookies_samesite_none_when_iframe_hosts_set()
    {
        $this->runWithEnv("ALLOWED_IFRAME_HOSTS", "http://example.com", function() {
            $resp = $this->get("/");
            foreach ($resp->headers->getCookies() as $cookie) {
                $this->assertEquals("none", $cookie->getSameSite());
            }
        });
    }

    public function test_secure_cookies_controlled_by_app_url()
    {
        $this->runWithEnv("APP_URL", "http://example.com", function() {
            $resp = $this->get("/");
            foreach ($resp->headers->getCookies() as $cookie) {
                $this->assertFalse($cookie->isSecure());
            }
        });

        $this->runWithEnv("APP_URL", "https://example.com", function() {
            $resp = $this->get("/");
            foreach ($resp->headers->getCookies() as $cookie) {
                $this->assertTrue($cookie->isSecure());
            }
        });
    }

    public function test_iframe_csp_self_only_by_default()
    {
        $resp = $this->get("/");
        $cspHeaders = collect($resp->headers->get('Content-Security-Policy'));
        $frameHeaders = $cspHeaders->filter(function ($val) {
            return Str::startsWith($val, 'frame-ancestors');
        });

        $this->assertTrue($frameHeaders->count() === 1);
        $this->assertEquals('frame-ancestors \'self\'', $frameHeaders->first());
    }

    public function test_iframe_csp_includes_extra_hosts_if_configured()
    {
        $this->runWithEnv("ALLOWED_IFRAME_HOSTS", "https://a.example.com https://b.example.com", function() {
            $resp = $this->get("/");
            $cspHeaders = collect($resp->headers->get('Content-Security-Policy'));
            $frameHeaders = $cspHeaders->filter(function($val) {
                return Str::startsWith($val, 'frame-ancestors');
            });

            $this->assertTrue($frameHeaders->count() === 1);
            $this->assertEquals('frame-ancestors \'self\' https://a.example.com https://b.example.com', $frameHeaders->first());
        });

    }

}