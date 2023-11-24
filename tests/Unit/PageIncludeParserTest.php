<?php

namespace Tests\Unit;

use BookStack\Entities\Tools\PageIncludeParser;
use Tests\TestCase;

class PageIncludeParserTest extends TestCase
{
    public function test_simple_inline_text()
    {
        $this->runParserTest(
            '<p>{{@45#content}}</p>',
            ['45' => '<p id="content">Testing</p>'],
            '<p>Testing</p>',
        );
    }

    public function test_simple_inline_text_with_existing_siblings()
    {
        $this->runParserTest(
            '<p>{{@45#content}} <strong>Hi</strong>there!</p>',
            ['45' => '<p id="content">Testing</p>'],
            '<p>Testing <strong>Hi</strong>there!</p>',
        );
    }

    public function test_simple_inline_text_within_other_text()
    {
        $this->runParserTest(
            '<p>Hello {{@45#content}}there!</p>',
            ['45' => '<p id="content">Testing</p>'],
            '<p>Hello Testingthere!</p>',
        );
    }

    public function test_block_content_types()
    {
        $inputs = [
            '<table id="content"><td>Text</td></table>',
            '<ul id="content"><li>Item A</li></ul>',
            '<ol id="content"><li>Item A</li></ol>',
            '<pre id="content">Code</pre>',
        ];

        foreach ($inputs as $input) {
            $this->runParserTest(
                '<p>A{{@45#content}}B</p>',
                ['45' => $input],
                '<p>A</p>' . $input . '<p>B</p>',
            );
        }
    }

    public function test_block_content_nested_origin_gets_placed_before()
    {
        $this->runParserTest(
            '<p><strong>A {{@45#content}} there!</strong></p>',
            ['45' => '<pre id="content">Testing</pre>'],
            '<pre id="content">Testing</pre><p><strong>A  there!</strong></p>',
        );
    }

    public function test_block_content_nested_origin_gets_placed_after()
    {
        $this->runParserTest(
            '<p><strong>Some really good {{@45#content}} there!</strong></p>',
            ['45' => '<pre id="content">Testing</pre>'],
            '<p><strong>Some really good  there!</strong></p><pre id="content">Testing</pre>',
        );
    }

    public function test_block_content_in_shallow_origin_gets_split()
    {
        $this->runParserTest(
            '<p>Some really good {{@45#content}} there!</p>',
            ['45' => '<pre id="content">doggos</pre>'],
            '<p>Some really good </p><pre id="content">doggos</pre><p> there!</p>',
        );
    }

    public function test_block_content_in_shallow_origin_split_does_not_duplicate_id()
    {
        $this->runParserTest(
            '<p id="test" title="Hi">Some really good {{@45#content}} there!</p>',
            ['45' => '<pre id="content">doggos</pre>'],
            '<p title="Hi">Some really good </p><pre id="content">doggos</pre><p id="test" title="Hi"> there!</p>',
        );
    }

    public function test_block_content_in_shallow_origin_does_not_leave_empty_nodes()
    {
        $this->runParserTest(
            '<p>{{@45#content}}</p>',
            ['45' => '<pre id="content">doggos</pre>'],
            '<pre id="content">doggos</pre>',
        );
    }

    public function test_simple_whole_document()
    {
        $this->runParserTest(
            '<p>{{@45}}</p>',
            ['45' => '<p id="content">Testing</p>'],
            '<p id="content">Testing</p>',
        );
    }

    public function test_multi_source_elem_whole_document()
    {
        $this->runParserTest(
            '<p>{{@45}}</p>',
            ['45' => '<p>Testing</p><blockquote>This</blockquote>'],
            '<p>Testing</p><blockquote>This</blockquote>',
        );
    }

    public function test_multi_source_elem_whole_document_with_shared_content_origin()
    {
        $this->runParserTest(
            '<p>This is {{@45}} some text</p>',
            ['45' => '<p>Testing</p><blockquote>This</blockquote>'],
            '<p>This is </p><p>Testing</p><blockquote>This</blockquote><p> some text</p>',
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
