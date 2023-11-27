<?php

namespace Tests\Unit;

use BookStack\Entities\Tools\PageIncludeContent;
use BookStack\Entities\Tools\PageIncludeParser;
use BookStack\Entities\Tools\PageIncludeTag;
use BookStack\Util\HtmlDocument;
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

    public function test_complex_inline_text_within_other_text()
    {
        $this->runParserTest(
            '<p>Hello {{@45#content}}there!</p>',
            ['45' => '<p id="content"><strong>Testing</strong> with<em>some</em><i>extra</i>tags</p>'],
            '<p>Hello <strong>Testing</strong> with<em>some</em><i>extra</i>tagsthere!</p>',
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

    public function test_block_content_in_allowable_parent_element()
    {
        $this->runParserTest(
            '<div>{{@45#content}}</div>',
            ['45' => '<pre id="content">doggos</pre>'],
            '<div><pre id="content">doggos</pre></div>',
        );
    }

    public function test_block_content_in_paragraph_origin_with_allowable_grandparent()
    {
        $this->runParserTest(
            '<div><p>{{@45#content}}</p></div>',
            ['45' => '<pre id="content">doggos</pre>'],
            '<div><pre id="content">doggos</pre></div>',
        );
    }

    public function test_block_content_in_paragraph_origin_with_allowable_grandparent_with_adjacent_content()
    {
        $this->runParserTest(
            '<div><p>Cute {{@45#content}} over there!</p></div>',
            ['45' => '<pre id="content">doggos</pre>'],
            '<div><p>Cute </p><pre id="content">doggos</pre><p> over there!</p></div>',
        );
    }

    public function test_block_content_in_child_within_paragraph_origin_with_allowable_grandparent_with_adjacent_content()
    {
        $this->runParserTest(
            '<div><p><strong>Cute {{@45#content}} over there!</strong></p></div>',
            ['45' => '<pre id="content">doggos</pre>'],
            '<div><pre id="content">doggos</pre><p><strong>Cute  over there!</strong></p></div>',
        );
    }

    public function test_block_content_in_paragraph_origin_within_details()
    {
        $this->runParserTest(
            '<details><p>{{@45#content}}</p></details>',
            ['45' => '<pre id="content">doggos</pre>'],
            '<details><pre id="content">doggos</pre></details>',
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

    public function test_multi_source_elem_whole_document_with_nested_content_origin()
    {
        $this->runParserTest(
            '<p><strong>{{@45}}</strong></p>',
            ['45' => '<p>Testing</p><blockquote>This</blockquote>'],
            '<p>Testing</p><blockquote>This</blockquote>',
        );
    }

    public function test_multiple_tags_in_same_origin_with_inline_content()
    {
        $this->runParserTest(
            '<p>This {{@45#content}}{{@45#content}} content is {{@45#content}}</p>',
            ['45' => '<p id="content">inline</p>'],
            '<p>This inlineinline content is inline</p>',
        );
    }

    public function test_multiple_tags_in_same_origin_with_block_content()
    {
        $this->runParserTest(
            '<p>This {{@45#content}}{{@45#content}} content is {{@45#content}}</p>',
            ['45' => '<pre id="content">block</pre>'],
            '<p>This </p><pre id="content">block</pre><pre id="content">block</pre><p> content is </p><pre id="content">block</pre>',
        );
    }

    public function test_multiple_tags_in_differing_origin_levels_with_block_content()
    {
        $this->runParserTest(
            '<div><p>This <strong>{{@45#content}}</strong> content is {{@45#content}}</p>{{@45#content}}</div>',
            ['45' => '<pre id="content">block</pre>'],
            '<div><pre id="content">block</pre><p>This  content is </p><pre id="content">block</pre><pre id="content">block</pre></div>',
        );
    }

    public function test_multiple_tags_in_shallow_origin_with_multi_block_content()
    {
        $this->runParserTest(
            '<p>{{@45}}C{{@45}}</p><div>{{@45}}{{@45}}</div>',
            ['45' => '<p>A</p><p>B</p>'],
            '<p>A</p><p>B</p><p>C</p><p>A</p><p>B</p><div><p>A</p><p>B</p><p>A</p><p>B</p></div>',
        );
    }

    protected function runParserTest(string $html, array $contentById, string $expected): void
    {
        $doc = new HtmlDocument($html);
        $parser = new PageIncludeParser($doc, function (PageIncludeTag $tag) use ($contentById): PageIncludeContent {
            $html = $contentById[strval($tag->getPageId())] ?? '';
            return PageIncludeContent::fromHtmlAndTag($html, $tag);
        });

        $parser->parse();
        $this->assertEquals($expected, $doc->getBodyInnerHtml());
    }
}
