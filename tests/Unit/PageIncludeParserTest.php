<?php

namespace Tests\Unit;

use BookStack\Entities\Tools\PageIncludeParser;
use Tests\TestCase;

class PageIncludeParserTest extends TestCase
{
    public function test_include_simple_inline_text()
    {
        $this->runParserTest(
            '<p>{{@45#content}}</p>',
            ['45' => '<p id="content">Testing</p>'],
            '<p>Testing</p>',
        );
    }

    public function test_include_simple_inline_text_with_existing_siblings()
    {
        $this->runParserTest(
            '<p>{{@45#content}} <strong>Hi</strong>there!</p>',
            ['45' => '<p id="content">Testing</p>'],
            '<p>Testing <strong>Hi</strong>there!</p>',
        );
    }

    public function test_include_simple_inline_text_within_other_text()
    {
        $this->runParserTest(
            '<p>Hello {{@45#content}}there!</p>',
            ['45' => '<p id="content">Testing</p>'],
            '<p>Hello Testingthere!</p>',
        );
    }

    protected function runParserTest(string $html, array $contentById, string $expected)
    {
        $parser = new PageIncludeParser($html, function (int $id) use ($contentById) {
            return $contentById[strval($id)] ?? '';
        });

        $result = $parser->parse();
        $this->assertEquals($expected, $result);
    }
}
