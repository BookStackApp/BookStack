<?php

namespace Tests\Unit;

use BookStack\Exceptions\HttpFetchException;
use BookStack\Util\SsrUrlValidator;
use Tests\TestCase;

class SsrUrlValidatorTest extends TestCase
{
    public function test_allowed()
    {
        $testMap = [
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
        ];

        foreach ($testMap as $test) {
            $result = (new SsrUrlValidator($test['config']))->allowed($test['url']);
            $this->assertEquals($test['result'], $result, "Failed asserting url '{$test['url']}' with config '{$test['config']}' results " . ($test['result'] ? 'true' : 'false'));
        }
    }

    public function test_enssure_allowed()
    {
        $result = (new SsrUrlValidator('https://example.com'))->ensureAllowed('https://example.com');
        $this->assertNull($result);

        $this->expectException(HttpFetchException::class);
        (new SsrUrlValidator('https://example.com'))->ensureAllowed('https://test.example.com');
    }
}
