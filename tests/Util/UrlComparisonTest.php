<?php

namespace Tests\Util;

use BookStack\Util\UrlComparison;
use PHPUnit\Framework\TestCase;

class UrlComparisonTest extends TestCase
{
    public function test_origins_match()
    {
        $good = [
            ['http://localhost/a/b', 'http://localhost#cat'],
            ['example.com/a/b', 'example.com?cat=dog'],
            ['https://example.com:5050/a/b', 'https://example.com:5050/a/b'],
        ];

        $bad = [
            ['http://localhost/a/b', 'http://localh0st#cat'],
            ['https://example.com/a/b', 'http://example.com?cat=dog'],
            ['https://example.com:5051/a/b', 'https://example.com:5050/a/b'],
        ];

        foreach ($good as [$a, $b]) {
            $comparison = new UrlComparison($a, $b);
            $this->assertTrue($comparison->originsMatch());
        }

        foreach ($bad as [$a, $b]) {
            $comparison = new UrlComparison($a, $b);
            $this->assertFalse($comparison->originsMatch());
        }
    }

    public function test_paths_overlap()
    {
        $good = [
            ['https://example.com', 'https://example.com/a/b/c'],
            ['https://example.com/', 'https://example.com/a/b/c'],
            ['https://example.com/a/b', 'https://example.com/a/b/c'],
            ['https://example.com/a/b/c', 'https://example.com/a'],
            ['http://donk.com/a/b/c?a=b#cat', 'https://example.com:5005/a/b#hello'],
        ];

        $bad = [
            ['https://example.com/a/c', 'https://example.com/a/b/c/d'],
            ['https://example.com/a/c', 'https://example.com/d/a/c'],
        ];

        foreach ($good as [$a, $b]) {
            $comparison = new UrlComparison($a, $b);
            $this->assertTrue($comparison->pathsOverlap());
        }

        foreach ($bad as [$a, $b]) {
            $comparison = new UrlComparison($a, $b);
            $this->assertFalse($comparison->pathsOverlap());
        }
    }
}
