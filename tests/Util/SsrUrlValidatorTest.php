<?php

namespace Tests\Util;

use BookStack\Exceptions\HttpFetchException;
use BookStack\Util\SsrUrlValidator;
use Tests\TestCase;

class SsrUrlValidatorTest extends TestCase
{
    public function test_is_uses_app_config_by_default()
    {
        config()->set([
            'app.ssr_hosts' => 'https://donkey.example.com',
        ]);

        $validator = new SsrUrlValidator();

        $this->assertTrue($validator->allowed('https://donkey.example.com'));
        $this->assertFalse($validator->allowed('https://monkey.example.com'));
    }

    public function test_config_string_can_be_passed_in_constructor()
    {
        config()->set([
            'app.ssr_hosts' => 'https://donkey.example.com',
        ]);

        $validator = new SsrUrlValidator('https://monkey.example.com');

        $this->assertFalse($validator->allowed('https://donkey.example.com'));
        $this->assertTrue($validator->allowed('https://monkey.example.com'));
    }

    public function test_config_string_can_include_multiple_space_seperated_values()
    {
        $validator = new SsrUrlValidator('https://monkey.example.com https://cat.example.com');

        $this->assertFalse($validator->allowed('https://donkey.example.com'));
        $this->assertTrue($validator->allowed('https://monkey.example.com'));
        $this->assertTrue($validator->allowed('https://cat.example.com'));
    }

    public function test_ensure_allowed_throws_if_not_allowed()
    {
        $validator = new SsrUrlValidator('https://monkey.example.com');

        $this->assertNull($validator->ensureAllowed('https://monkey.example.com'));

        $this->assertThrows(function () use ($validator) {
            $validator->ensureAllowed('https://donkey.example.com');
        }, HttpFetchException::class, 'The URL does not match the configured allowed SSR hosts');
    }

    public function test_basic_url_matching()
    {
        $tests = [
            // Single values
            ['config' => '', 'url' => '', 'result' => false],
            ['config' => '', 'url' => 'https://example.com', 'result' => false],
            ['config' => '    ', 'url' => 'https://example.com', 'result' => false],
            ['config' => '*', 'url' => '', 'result' => false],
            ['config' => '*', 'url' => 'https://example.com', 'result' => true],
            ['config' => 'https://*', 'url' => 'https://example.com', 'result' => true],
            ['config' => 'http://*', 'url' => 'https://example.com', 'result' => false],
            ['config' => 'https://*example.com', 'url' => 'https://example.com', 'result' => true],
            ['config' => 'https://*ample.com', 'url' => 'https://example.com', 'result' => true],
            ['config' => 'https://*.example.com', 'url' => 'https://example.com', 'result' => false],
            ['config' => 'https://*.example.com', 'url' => 'https://test.example.com', 'result' => true],
            ['config' => '*//example.com', 'url' => 'https://example.com', 'result' => true],
            ['config' => '*//example.com', 'url' => 'http://example.com', 'result' => true],
            ['config' => '*//example.co', 'url' => 'http://example.co.uk', 'result' => false],
            ['config' => '*//example.co/bookstack', 'url' => 'https://example.co/bookstack/a/path', 'result' => true],
            ['config' => '*//example.co*', 'url' => 'https://example.co.uk/bookstack/a/path', 'result' => true],
            ['config' => 'https://example.com', 'url' => 'https://example.com/a/b/c?test=cat', 'result' => true],
            ['config' => 'https://example.com', 'url' => 'https://example.co.uk', 'result' => false],

            // Escapes
            ['config' => 'https://(.*?).com', 'url' => 'https://example.com', 'result' => false],
            ['config' => 'https://example.com', 'url' => 'https://example.co.uk#https://example.com', 'result' => false],

            // Multi values
            ['config' => '*//example.org *//example.com', 'url' => 'https://example.com', 'result' => true],
            ['config' => '*//example.org *//example.com', 'url' => 'https://example.com/a/b/c?test=cat#hello', 'result' => true],
            ['config' => '*.example.org *.example.com', 'url' => 'https://example.co.uk', 'result' => false],
            ['config' => '  *.example.org  *.example.com  ', 'url' => 'https://example.co.uk', 'result' => false],
            ['config' => '* *.example.com', 'url' => 'https://example.co.uk', 'result' => true],
            ['config' => '*//example.org *//example.com *//example.co.uk', 'url' => 'https://example.co.uk', 'result' => true],
            ['config' => '*//example.org *//example.com *//example.co.uk', 'url' => 'https://example.net', 'result' => false],

            // Further tests
            ['config' => 'https://monkey.example.com', 'url' => 'https://monkey.example.com/a/b', 'result' => true,],
            ['config' => 'https://monkey.example.com', 'url' => 'https://monkey.example.com/a/b?a=b#ab', 'result' => true,],
            ['config' => 'https://monkey.example.com', 'url' => 'https://monkey.example.com:8080/a', 'result' => false,],
            ['config' => '*', 'url' => 'https://a.example.com', 'result' => true,],
            ['config' => 'https://monkey.example.com', 'url' => 'http://monkey.example.com/a/b?a=b#ab', 'result' => false,],
            ['config' => 'https://monkey.example.com', 'url' => 'https://beans.monkey.example.com/a/b?a=b#ab', 'result' => false,],
            ['config' => 'https://*monkey.example.com', 'url' => 'https://amonkey.example.com/a/b?a=b#ab', 'result' => true,],
            ['config' => 'https://*monkey.example.com', 'url' => 'https://donkey.example.com/a/b/monkey.example.com/b?a=b#ab', 'result' => false,],
            ['config' => 'https://monkey.example.com', 'url' => 'https://example.com/monkey.example.com/b?a=monkey.example.com#monkey.example.com', 'result' => false,],
            ['config' => 'https://*.example.com', 'url' => 'https://a.b.example.com/a/b', 'result' => true,],
            ['config' => 'https://*.example.com', 'url' => 'https://a.b.example.a.com/a/b', 'result' => false,],
            ['config' => 'https://*.example.com', 'url' => 'https://a.com/a/b?val=a.example.com', 'result' => false,],
            ['config' => 'https://*.example.com', 'url' => 'https://a.com/a/b#example.com', 'result' => false,],
            ['config' => 'https://a.*.example.com', 'url' => 'https://a.b.c.example.com/c/d', 'result' => true,],
            ['config' => 'https://example.com/webhooks/', 'url' => 'https://example.com/webhooks/beans', 'result' => true,],
            ['config' => 'https://example.com/webhooks/', 'url' => 'https://example.com/a/webhooks/', 'result' => false,],
            ['config' => 'https://example.com:8080', 'url' => 'https://example.com/a/b', 'result' => false,],
            ['config' => 'https://example.com:8080', 'url' => 'https://example.com:8080/a/b', 'result' => true,],
            ['config' => 'https://example.com/*', 'url' => 'https://example.com:8080/a/b', 'result' => false,],
        ];

        foreach ($tests as $testCase) {
            $validator = new SsrUrlValidator($testCase['config']);
            $result = $validator->allowed($testCase['url']);
            $this->assertEquals($testCase['result'], $result, "Failed asserting expected result for config {$testCase['config']} and test value {$testCase['url']}");
        }
    }

    public function test_wildcard_does_not_match_userinfo_data_but_still_allows_it()
    {
        $validator = new SsrUrlValidator('https://*monkey.example.com');
        $this->assertFalse($validator->allowed('https://monkey.example.com@a.example.com'));

        $validator = new SsrUrlValidator('https://monkey.example.com*');
        $this->assertFalse($validator->allowed('https://monkey.example.com@a.example.com'));
        $this->assertFalse($validator->allowed('https://monkey.example.com:monkey.example.com@a.example.com'));

        $validator = new SsrUrlValidator('https://monkey.example.com');
        $this->assertTrue($validator->allowed('https://a:b@monkey.example.com'));
    }

    public function test_percent_encoded_slashes_in_host_are_rejected()
    {
        $validator = new SsrUrlValidator('*');

        $this->assertFalse($validator->allowed('https://cat.example.com%2Fa/b'));
        $this->assertFalse($validator->allowed('https://cat.example.com%2fa/b'));
        $this->assertFalse($validator->allowed('https://cat%2f.example.com/a/b'));
        $this->assertFalse($validator->allowed('https://cat.exa%2Fmple.com'));
    }
}
