<?php

namespace Tests\Util;

use BookStack\Util\UrlFilter;
use Tests\TestCase;

class UrlFilterTest extends TestCase
{
    public function test_it_finds_invalid_urls()
    {
        $urls = [
            'javascript:alert("bunny")',
            ' javascript:alert("bunny")',
            'JavaScript:alert("bunny")',
            "\t\n\t\nJavaScript:alert(\"bunny\")",
            'data:text/html;bunny<a></a>',
            'Data:text/html;bunny<a></a>',
            'Data:text/html;bunny<a></a>',
            "http://example.com\0javascript:alert(1)",
        ];

        foreach ($urls as $url) {
            $filter = new UrlFilter($url);
            $this->assertFalse($filter->isAllowed(), "Failed to detect invalid url: {$url}");
        }
    }

    public function test_clean()
    {
        $expectedOutputByInput = [
            'javascript:alert("bunny")' => '#badlink',
            ' javascript:alert("bunny")' => '#badlink',
            'JavaScript:alert("bunny")' => '#badlink',
            "\t\n\t\nJavaScript:alert(\"bunny\")" => '#badlink',
            'data:text/html;bunny<a></a>' => '#badlink',
            'Data:text/html;bunny<a></a>' => '#badlink',
            'Data:text/html;bunny<a></a>' => '#badlink',
            "http://example.com\0javascript:alert(1)" => '#badlink',
            "Java\tScript:alert(\"bunny\")" => '#badlink',

            'https://example.com' => 'https://example.com',
            'https://example.com/a/b' => 'https://example.com/a/b',
            'https://example.com/a/b?a=b#ab' => 'https://example.com/a/b?a=b#ab',
            'https://example.com/a/b?a=b#ab&c=d' => 'https://example.com/a/b?a=b#ab&c=d',
            'https://example.com:5050' => 'https://example.com:5050',
            'https://example.com:5050/a/b' => 'https://example.com:5050/a/b',
            'https://example.com:5050/a/b?a=b#ab' => 'https://example.com:5050/a/b?a=b#ab',
            'https://user@example.com:5011/a/b?a=b' => 'https://user@example.com:5011/a/b?a=b',
            'https://user:pass@example.com:5011/a/b?a=b' => 'https://user:pass@example.com:5011/a/b?a=b',

            '//example.com' => 'https://example.com',
            'a/b/c' => 'a/b/c',
            '/a/b/c' => '/a/b/c',

            'tel:123456789' => 'tel:123456789',
            'TEL:123456789' => 'tel:123456789',
            'maiLto:a@b.c' => 'mailto:a@b.c',
            'file://a/b/c' => 'file://a/b/c',
            'ftp://a/b/c' => 'ftp://a/b/c',
            'nntp://a/b/c' => 'nntp://a/b/c',
            'news:a/b/c' => 'news:a/b/c',
        ];

        foreach ($expectedOutputByInput as $input => $expected) {
            $filter = new UrlFilter($input);
            $output = $filter->clean();
            $this->assertEquals($expected, $output, "Failed to clean url: {$input}");
        }
    }
}
