<?php

namespace Tests\Util;

use BookStack\Util\HtmlToPlainText;
use Tests\TestCase;

class HtmlToPlainTextTest extends TestCase
{
    public function test_it_converts_html_to_plain_text()
    {
        $html = <<<HTML
<p>This is a test</p>
<ul>
<li>Item 1</li>
<li>Item 2</li>
</ul>
<h2>A Header</h2>
<p>more &lt;&copy;&gt; text <strong>with bold</strong></p>
HTML;
        $expected = <<<TEXT
This is a test
Item 1
Item 2
A Header
more <©> text with bold
TEXT;

        $this->runTest($html, $expected);
    }

    public function test_adjacent_list_items_are_separated_by_newline()
    {
        $html = <<<HTML
<ul><li>Item A</li><li>Item B</li></ul>
HTML;
        $expected = <<<TEXT
Item A
Item B
TEXT;

        $this->runTest($html, $expected);
    }

    public function test_inline_formats_dont_cause_newlines()
    {
        $html = <<<HTML
<p><strong>H</strong><a>e</a><sup>l</sup><span>l</span><em>o</em></p>
HTML;
        $expected = <<<TEXT
Hello
TEXT;

        $this->runTest($html, $expected);
    }

    protected function runTest(string $html, string $expected): void
    {
        $converter = new HtmlToPlainText();
        $result = $converter->convert(trim($html));
        $this->assertEquals(trim($expected), $result);
    }
}
