<?php

namespace Tests\Entity;

use BookStack\Entities\Tools\Markdown\HtmlToMarkdown;
use Tests\TestCase;

class MarkdownToHtmlTest extends TestCase
{
    public function test_basic_formatting_conversion()
    {
        $this->assertConversion(
            '<h1>Dogcat</h1><p>Some <strong>bold</strong> text</p>',
            "# Dogcat\n\nSome **bold** text"
        );
    }

    public function test_callouts_remain_html()
    {
        $this->assertConversion(
            '<h1>Dogcat</h1><p class="callout info">Some callout text</p><p>Another line</p>',
            "# Dogcat\n\n<p class=\"callout info\">Some callout text</p>\n\nAnother line"
        );
    }

    public function test_wysiwyg_code_format_handled_cleanly()
    {
        $this->assertConversion(
            '<h1>Dogcat</h1>' . "\r\n" . '<pre id="bkmrk-var-a-%3D-%27cat%27%3B"><code class="language-JavaScript">var a = \'cat\';</code></pre><p>Another line</p>',
            "# Dogcat\n\n```JavaScript\nvar a = 'cat';\n```\n\nAnother line"
        );
    }

    public function test_tasklist_checkboxes_are_handled()
    {
        $this->assertConversion(
            '<ul><li><input type="checkbox" checked="checked">Item A</li><li><input type="checkbox">Item B</li></ul>',
            "- [x] Item A\n- [ ] Item B"
        );
    }

    public function test_drawing_blocks_remain_html()
    {
        $this->assertConversion(
            '<div drawio-diagram="190" id="bkmrk--0" contenteditable="false"><img src="http://example.com/uploads/images/drawio/2022-04/drawing-1.png" alt="" /></div>Some text',
            '<div drawio-diagram="190"><img src="http://example.com/uploads/images/drawio/2022-04/drawing-1.png" alt=""/></div>' . "\n\nSome text"
        );
    }

    public function test_summary_tags_have_newlines_after_to_separate_content()
    {
        $this->assertConversion(
            '<details><summary>Toggle</summary><p>Test</p></details>',
            "<details><summary>Toggle</summary>\n\nTest\n\n</details>"
        );
    }

    public function test_iframes_tags_have_newlines_after_to_separate_content()
    {
        $this->assertConversion(
            '<iframe src="https://example.com"></iframe><p>Beans</p>',
            "<iframe src=\"https://example.com\"></iframe>\n\nBeans"
        );
    }

    protected function assertConversion(string $html, string $expectedMarkdown, bool $partialMdMatch = false)
    {
        $markdown = (new HtmlToMarkdown($html))->convert();

        if ($partialMdMatch) {
            static::assertStringContainsString($expectedMarkdown, $markdown);
        } else {
            static::assertEquals($expectedMarkdown, $markdown);
        }
    }
}
