<?php

namespace Tests\Entity;

use BookStack\Entities\Models\Page;
use BookStack\Entities\Tools\PageContent;
use Tests\TestCase;
use Tests\Uploads\UsesImages;

class PageContentTest extends TestCase
{
    use UsesImages;

    protected $base64Jpeg = '/9j/2wBDAAMCAgICAgMCAgIDAwMDBAYEBAQEBAgGBgUGCQgKCgkICQkKDA8MCgsOCwkJDRENDg8QEBEQCgwSExIQEw8QEBD/yQALCAABAAEBAREA/8wABgAQEAX/2gAIAQEAAD8A0s8g/9k=';

    public function test_page_includes()
    {
        $page = $this->entities->page();
        $secondPage = $this->entities->page();

        $secondPage->html = "<p id='section1'>Hello, This is a test</p><p id='section2'>This is a second block of content</p>";
        $secondPage->save();

        $this->asEditor();

        $pageContent = $this->get($page->getUrl());
        $pageContent->assertDontSee('Hello, This is a test');

        $originalHtml = $page->html;
        $page->html .= "{{@{$secondPage->id}}}";
        $page->save();

        $pageContent = $this->get($page->getUrl());
        $pageContent->assertSee('Hello, This is a test');
        $pageContent->assertSee('This is a second block of content');

        $page->html = $originalHtml . " Well {{@{$secondPage->id}#section2}}";
        $page->save();

        $pageContent = $this->get($page->getUrl());
        $pageContent->assertDontSee('Hello, This is a test');
        $pageContent->assertSee('Well This is a second block of content');
    }

    public function test_saving_page_with_includes()
    {
        $page = $this->entities->page();
        $secondPage = $this->entities->page();

        $this->asEditor();
        $includeTag = '{{@' . $secondPage->id . '}}';
        $page->html = '<p>' . $includeTag . '</p>';

        $resp = $this->put($page->getUrl(), ['name' => $page->name, 'html' => $page->html, 'summary' => '']);

        $resp->assertStatus(302);

        $page = Page::find($page->id);
        $this->assertStringContainsString($includeTag, $page->html);
        $this->assertEquals('', $page->text);
    }

    public function test_page_includes_do_not_break_tables()
    {
        $page = $this->entities->page();
        $secondPage = $this->entities->page();

        $content = '<table id="table"><tbody><tr><td>test</td></tr></tbody></table>';
        $secondPage->html = $content;
        $secondPage->save();

        $page->html = "{{@{$secondPage->id}#table}}";
        $page->save();

        $pageResp = $this->asEditor()->get($page->getUrl());
        $pageResp->assertSee($content, false);
    }

    public function test_page_includes_do_not_break_code()
    {
        $page = $this->entities->page();
        $secondPage = $this->entities->page();

        $content = '<pre id="bkmrk-code"><code>var cat = null;</code></pre>';
        $secondPage->html = $content;
        $secondPage->save();

        $page->html = "{{@{$secondPage->id}#bkmrk-code}}";
        $page->save();

        $pageResp = $this->asEditor()->get($page->getUrl());
        $pageResp->assertSee($content, false);
    }

    public function test_page_includes_rendered_on_book_export()
    {
        $page = $this->entities->page();
        $secondPage = Page::query()
            ->where('book_id', '!=', $page->book_id)
            ->first();

        $content = '<p id="bkmrk-meow">my cat is awesome and scratchy</p>';
        $secondPage->html = $content;
        $secondPage->save();

        $page->html = "{{@{$secondPage->id}#bkmrk-meow}}";
        $page->save();

        $this->asEditor();
        $htmlContent = $this->get($page->book->getUrl('/export/html'));
        $htmlContent->assertSee('my cat is awesome and scratchy');
    }

    public function test_page_content_scripts_removed_by_default()
    {
        $this->asEditor();
        $page = $this->entities->page();
        $script = 'abc123<script>console.log("hello-test")</script>abc123';
        $page->html = "escape {$script}";
        $page->save();

        $pageView = $this->get($page->getUrl());
        $pageView->assertStatus(200);
        $pageView->assertDontSee($script, false);
        $pageView->assertSee('abc123abc123');
    }

    public function test_more_complex_content_script_escaping_scenarios()
    {
        $checks = [
            "<p>Some script</p><script>alert('cat')</script>",
            "<div><div><div><div><p>Some script</p><script>alert('cat')</script></div></div></div></div>",
            "<p>Some script<script>alert('cat')</script></p>",
            "<p>Some script <div><script>alert('cat')</script></div></p>",
            "<p>Some script <script><div>alert('cat')</script></div></p>",
            "<p>Some script <script><div>alert('cat')</script><script><div>alert('cat')</script></p><script><div>alert('cat')</script>",
        ];

        $this->asEditor();
        $page = $this->entities->page();

        foreach ($checks as $check) {
            $page->html = $check;
            $page->save();

            $pageView = $this->get($page->getUrl());
            $pageView->assertStatus(200);
            $this->withHtml($pageView)->assertElementNotContains('.page-content', '<script>');
            $this->withHtml($pageView)->assertElementNotContains('.page-content', '</script>');
        }
    }

    public function test_js_and_base64_src_urls_are_removed()
    {
        $checks = [
            '<iframe src="javascript:alert(document.cookie)"></iframe>',
            '<iframe src="JavAScRipT:alert(document.cookie)"></iframe>',
            '<iframe src="JavAScRipT:alert(document.cookie)"></iframe>',
            '<iframe SRC=" javascript: alert(document.cookie)"></iframe>',
            '<iframe src="data:text/html;base64,PHNjcmlwdD5hbGVydCgnaGVsbG8nKTwvc2NyaXB0Pg==" frameborder="0"></iframe>',
            '<iframe src="DaTa:text/html;base64,PHNjcmlwdD5hbGVydCgnaGVsbG8nKTwvc2NyaXB0Pg==" frameborder="0"></iframe>',
            '<iframe src=" data:text/html;base64,PHNjcmlwdD5hbGVydCgnaGVsbG8nKTwvc2NyaXB0Pg==" frameborder="0"></iframe>',
            '<img src="javascript:alert(document.cookie)"/>',
            '<img src="JavAScRipT:alert(document.cookie)"/>',
            '<img src="JavAScRipT:alert(document.cookie)"/>',
            '<img SRC=" javascript: alert(document.cookie)"/>',
            '<img src="data:text/html;base64,PHNjcmlwdD5hbGVydCgnaGVsbG8nKTwvc2NyaXB0Pg=="/>',
            '<img src="DaTa:text/html;base64,PHNjcmlwdD5hbGVydCgnaGVsbG8nKTwvc2NyaXB0Pg=="/>',
            '<img src=" data:text/html;base64,PHNjcmlwdD5hbGVydCgnaGVsbG8nKTwvc2NyaXB0Pg=="/>',
            '<iframe srcdoc="<script>window.alert(document.cookie)</script>"></iframe>',
            '<iframe SRCdoc="<script>window.alert(document.cookie)</script>"></iframe>',
            '<IMG SRC=`javascript:alert("RSnake says, \'XSS\'")`>',
        ];

        $this->asEditor();
        $page = $this->entities->page();

        foreach ($checks as $check) {
            $page->html = $check;
            $page->save();

            $pageView = $this->get($page->getUrl());
            $pageView->assertStatus(200);
            $html = $this->withHtml($pageView);
            $html->assertElementNotContains('.page-content', '<iframe>');
            $html->assertElementNotContains('.page-content', '<img');
            $html->assertElementNotContains('.page-content', '</iframe>');
            $html->assertElementNotContains('.page-content', 'src=');
            $html->assertElementNotContains('.page-content', 'javascript:');
            $html->assertElementNotContains('.page-content', 'data:');
            $html->assertElementNotContains('.page-content', 'base64');
        }
    }

    public function test_javascript_uri_links_are_removed()
    {
        $checks = [
            '<a id="xss" href="javascript:alert(document.cookie)>Click me</a>',
            '<a id="xss" href="javascript: alert(document.cookie)>Click me</a>',
            '<a id="xss" href="JaVaScRiPt: alert(document.cookie)>Click me</a>',
            '<a id="xss" href=" JaVaScRiPt: alert(document.cookie)>Click me</a>',
        ];

        $this->asEditor();
        $page = $this->entities->page();

        foreach ($checks as $check) {
            $page->html = $check;
            $page->save();

            $pageView = $this->get($page->getUrl());
            $pageView->assertStatus(200);
            $this->withHtml($pageView)->assertElementNotContains('.page-content', '<a id="xss"');
            $this->withHtml($pageView)->assertElementNotContains('.page-content', 'href=javascript:');
        }
    }

    public function test_form_actions_with_javascript_are_removed()
    {
        $checks = [
            '<form><input id="xss" type=submit formaction=javascript:alert(document.domain) value=Submit><input></form>',
            '<form ><button id="xss" formaction="JaVaScRiPt:alert(document.domain)">Click me</button></form>',
            '<form ><button id="xss" formaction=javascript:alert(document.domain)>Click me</button></form>',
            '<form id="xss" action=javascript:alert(document.domain)><input type=submit value=Submit></form>',
            '<form id="xss" action="JaVaScRiPt:alert(document.domain)"><input type=submit value=Submit></form>',
        ];

        $this->asEditor();
        $page = $this->entities->page();

        foreach ($checks as $check) {
            $page->html = $check;
            $page->save();

            $pageView = $this->get($page->getUrl());
            $pageView->assertStatus(200);
            $this->withHtml($pageView)->assertElementNotContains('.page-content', '<button id="xss"');
            $this->withHtml($pageView)->assertElementNotContains('.page-content', '<input id="xss"');
            $this->withHtml($pageView)->assertElementNotContains('.page-content', '<form id="xss"');
            $this->withHtml($pageView)->assertElementNotContains('.page-content', 'action=javascript:');
            $this->withHtml($pageView)->assertElementNotContains('.page-content', 'formaction=javascript:');
        }
    }

    public function test_metadata_redirects_are_removed()
    {
        $checks = [
            '<meta http-equiv="refresh" content="0; url=//external_url">',
            '<meta http-equiv="refresh" ConTeNt="0; url=//external_url">',
            '<meta http-equiv="refresh" content="0; UrL=//external_url">',
        ];

        $this->asEditor();
        $page = $this->entities->page();

        foreach ($checks as $check) {
            $page->html = $check;
            $page->save();

            $pageView = $this->get($page->getUrl());
            $pageView->assertStatus(200);
            $this->withHtml($pageView)->assertElementNotContains('.page-content', '<meta>');
            $this->withHtml($pageView)->assertElementNotContains('.page-content', '</meta>');
            $this->withHtml($pageView)->assertElementNotContains('.page-content', 'content=');
            $this->withHtml($pageView)->assertElementNotContains('.page-content', 'external_url');
        }
    }

    public function test_page_inline_on_attributes_removed_by_default()
    {
        $this->asEditor();
        $page = $this->entities->page();
        $script = '<p onmouseenter="console.log(\'test\')">Hello</p>';
        $page->html = "escape {$script}";
        $page->save();

        $pageView = $this->get($page->getUrl());
        $pageView->assertStatus(200);
        $pageView->assertDontSee($script, false);
        $pageView->assertSee('<p>Hello</p>', false);
    }

    public function test_more_complex_inline_on_attributes_escaping_scenarios()
    {
        $checks = [
            '<p onclick="console.log(\'test\')">Hello</p>',
            '<p OnCliCk="console.log(\'test\')">Hello</p>',
            '<div>Lorem ipsum dolor sit amet.</div><p onclick="console.log(\'test\')">Hello</p>',
            '<div>Lorem ipsum dolor sit amet.<p onclick="console.log(\'test\')">Hello</p></div>',
            '<div><div><div><div>Lorem ipsum dolor sit amet.<p onclick="console.log(\'test\')">Hello</p></div></div></div></div>',
            '<div onclick="console.log(\'test\')">Lorem ipsum dolor sit amet.</div><p onclick="console.log(\'test\')">Hello</p><div></div>',
            '<a a="<img src=1 onerror=\'alert(1)\'> ',
            '\<a onclick="alert(document.cookie)"\>xss link\</a\>',
        ];

        $this->asEditor();
        $page = $this->entities->page();

        foreach ($checks as $check) {
            $page->html = $check;
            $page->save();

            $pageView = $this->get($page->getUrl());
            $pageView->assertStatus(200);
            $this->withHtml($pageView)->assertElementNotContains('.page-content', 'onclick');
        }
    }

    public function test_page_content_scripts_show_when_configured()
    {
        $this->asEditor();
        $page = $this->entities->page();
        config()->push('app.allow_content_scripts', 'true');

        $script = 'abc123<script>console.log("hello-test")</script>abc123';
        $page->html = "no escape {$script}";
        $page->save();

        $pageView = $this->get($page->getUrl());
        $pageView->assertSee($script, false);
        $pageView->assertDontSee('abc123abc123');
    }

    public function test_svg_script_usage_is_removed()
    {
        $checks = [
            '<svg id="test" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="100" height="100"><a xlink:href="javascript:alert(document.domain)"><rect x="0" y="0" width="100" height="100" /></a></svg>',
            '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink"><use xlink:href="data:application/xml;base64 ,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHhtbG5zOnhsaW5rPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5L3hsaW5rIj4KPGRlZnM+CjxjaXJjbGUgaWQ9InRlc3QiIHI9IjAiIGN4PSIwIiBjeT0iMCIgc3R5bGU9ImZpbGw6ICNGMDAiPgo8c2V0IGF0dHJpYnV0ZU5hbWU9ImZpbGwiIGF0dHJpYnV0ZVR5cGU9IkNTUyIgb25iZWdpbj0nYWxlcnQoZG9jdW1lbnQuZG9tYWluKScKb25lbmQ9J2FsZXJ0KCJvbmVuZCIpJyB0bz0iIzAwRiIgYmVnaW49IjBzIiBkdXI9Ijk5OXMiIC8+CjwvY2lyY2xlPgo8L2RlZnM+Cjx1c2UgeGxpbms6aHJlZj0iI3Rlc3QiLz4KPC9zdmc+#test"/></svg>',
            '<svg><animate href=#xss attributeName=href values=javascript:alert(1) /></svg>',
            '<svg><animate href="#xss" attributeName="href" values="a;javascript:alert(1)" /></svg>',
            '<svg><animate href="#xss" attributeName="href" values="a;data:alert(1)" /></svg>',
            '<svg><animate href=#xss attributeName=href from=javascript:alert(1) to=1 /><a id=xss><text x=20 y=20>XSS</text></a>',
            '<svg><set href=#xss attributeName=href from=? to=javascript:alert(1) /><a id=xss><text x=20 y=20>XSS</text></a>',
            '<svg><g><g><g><animate href=#xss attributeName=href values=javascript:alert(1) /></g></g></g></svg>',
        ];

        $this->asEditor();
        $page = $this->entities->page();

        foreach ($checks as $check) {
            $page->html = $check;
            $page->save();

            $pageView = $this->get($page->getUrl());
            $pageView->assertStatus(200);
            $html = $this->withHtml($pageView);
            $html->assertElementNotContains('.page-content', 'alert');
            $html->assertElementNotContains('.page-content', 'xlink:href');
            $html->assertElementNotContains('.page-content', 'application/xml');
            $html->assertElementNotContains('.page-content', 'javascript');
        }
    }

    public function test_page_inline_on_attributes_show_if_configured()
    {
        $this->asEditor();
        $page = $this->entities->page();
        config()->push('app.allow_content_scripts', 'true');

        $script = '<p onmouseenter="console.log(\'test\')">Hello</p>';
        $page->html = "escape {$script}";
        $page->save();

        $pageView = $this->get($page->getUrl());
        $pageView->assertSee($script, false);
        $pageView->assertDontSee('<p>Hello</p>', false);
    }

    public function test_duplicate_ids_does_not_break_page_render()
    {
        $this->asEditor();
        $pageA = Page::query()->first();
        $pageB = Page::query()->where('id', '!=', $pageA->id)->first();

        $content = '<ul id="bkmrk-xxx-%28"></ul> <ul id="bkmrk-xxx-%28"></ul>';
        $pageA->html = $content;
        $pageA->save();

        $pageB->html = '<ul id="bkmrk-xxx-%28"></ul> <p>{{@' . $pageA->id . '#test}}</p>';
        $pageB->save();

        $pageView = $this->get($pageB->getUrl());
        $pageView->assertSuccessful();
    }

    public function test_duplicate_ids_fixed_on_page_save()
    {
        $this->asEditor();
        $page = $this->entities->page();

        $content = '<ul id="bkmrk-test"><li>test a</li><li><ul id="bkmrk-test"><li>test b</li></ul></li></ul>';
        $pageSave = $this->put($page->getUrl(), [
            'name'    => $page->name,
            'html'    => $content,
            'summary' => '',
        ]);
        $pageSave->assertRedirect();

        $updatedPage = Page::query()->where('id', '=', $page->id)->first();
        $this->assertEquals(substr_count($updatedPage->html, 'bkmrk-test"'), 1);
    }

    public function test_anchors_referencing_non_bkmrk_ids_rewritten_after_save()
    {
        $this->asEditor();
        $page = $this->entities->page();

        $content = '<h1 id="non-standard-id">test</h1><p><a href="#non-standard-id">link</a></p>';
        $this->put($page->getUrl(), [
            'name'    => $page->name,
            'html'    => $content,
            'summary' => '',
        ]);

        $updatedPage = Page::query()->where('id', '=', $page->id)->first();
        $this->assertStringContainsString('id="bkmrk-test"', $updatedPage->html);
        $this->assertStringContainsString('href="#bkmrk-test"', $updatedPage->html);
    }

    public function test_get_page_nav_sets_correct_properties()
    {
        $content = '<h1 id="testa">Hello</h1><h2 id="testb">There</h2><h3 id="testc">Donkey</h3>';
        $pageContent = new PageContent(new Page(['html' => $content]));
        $navMap = $pageContent->getNavigation($content);

        $this->assertCount(3, $navMap);
        $this->assertArrayMapIncludes([
            'nodeName' => 'h1',
            'link'     => '#testa',
            'text'     => 'Hello',
            'level'    => 1,
        ], $navMap[0]);
        $this->assertArrayMapIncludes([
            'nodeName' => 'h2',
            'link'     => '#testb',
            'text'     => 'There',
            'level'    => 2,
        ], $navMap[1]);
        $this->assertArrayMapIncludes([
            'nodeName' => 'h3',
            'link'     => '#testc',
            'text'     => 'Donkey',
            'level'    => 3,
        ], $navMap[2]);
    }

    public function test_get_page_nav_does_not_show_empty_titles()
    {
        $content = '<h1 id="testa">Hello</h1><h2 id="testb">&nbsp;</h2><h3 id="testc"></h3>';
        $pageContent = new PageContent(new Page(['html' => $content]));
        $navMap = $pageContent->getNavigation($content);

        $this->assertCount(1, $navMap);
        $this->assertArrayMapIncludes([
            'nodeName' => 'h1',
            'link'     => '#testa',
            'text'     => 'Hello',
        ], $navMap[0]);
    }

    public function test_get_page_nav_shifts_headers_if_only_smaller_ones_are_used()
    {
        $content = '<h4 id="testa">Hello</h4><h5 id="testb">There</h5><h6 id="testc">Donkey</h6>';
        $pageContent = new PageContent(new Page(['html' => $content]));
        $navMap = $pageContent->getNavigation($content);

        $this->assertCount(3, $navMap);
        $this->assertArrayMapIncludes([
            'nodeName' => 'h4',
            'level'    => 1,
        ], $navMap[0]);
        $this->assertArrayMapIncludes([
            'nodeName' => 'h5',
            'level'    => 2,
        ], $navMap[1]);
        $this->assertArrayMapIncludes([
            'nodeName' => 'h6',
            'level'    => 3,
        ], $navMap[2]);
    }

    public function test_page_text_decodes_html_entities()
    {
        $page = $this->entities->page();

        $this->actingAs($this->getAdmin())
            ->put($page->getUrl(''), [
                'name' => 'Testing',
                'html' => '<p>&quot;Hello &amp; welcome&quot;</p>',
            ]);

        $page->refresh();
        $this->assertEquals('"Hello & welcome"', $page->text);
    }

    public function test_page_markdown_table_rendering()
    {
        $this->asEditor();
        $page = $this->entities->page();

        $content = '| Syntax      | Description |
| ----------- | ----------- |
| Header      | Title       |
| Paragraph   | Text        |';
        $this->put($page->getUrl(), [
            'name' => $page->name,  'markdown' => $content,
            'html' => '', 'summary' => '',
        ]);

        $page->refresh();
        $this->assertStringContainsString('</tbody>', $page->html);

        $pageView = $this->get($page->getUrl());
        $this->withHtml($pageView)->assertElementExists('.page-content table tbody td');
    }

    public function test_page_markdown_task_list_rendering()
    {
        $this->asEditor();
        $page = $this->entities->page();

        $content = '- [ ] Item a
- [x] Item b';
        $this->put($page->getUrl(), [
            'name' => $page->name,  'markdown' => $content,
            'html' => '', 'summary' => '',
        ]);

        $page->refresh();
        $this->assertStringContainsString('input', $page->html);
        $this->assertStringContainsString('type="checkbox"', $page->html);

        $pageView = $this->get($page->getUrl());
        $this->withHtml($pageView)->assertElementExists('.page-content li.task-list-item input[type=checkbox]');
        $this->withHtml($pageView)->assertElementExists('.page-content li.task-list-item input[type=checkbox][checked]');
    }

    public function test_page_markdown_strikethrough_rendering()
    {
        $this->asEditor();
        $page = $this->entities->page();

        $content = '~~some crossed out text~~';
        $this->put($page->getUrl(), [
            'name' => $page->name,  'markdown' => $content,
            'html' => '', 'summary' => '',
        ]);

        $page->refresh();
        $this->assertStringMatchesFormat('%A<s%A>some crossed out text</s>%A', $page->html);

        $pageView = $this->get($page->getUrl());
        $this->withHtml($pageView)->assertElementExists('.page-content p > s');
    }

    public function test_page_markdown_single_html_comment_saving()
    {
        $this->asEditor();
        $page = $this->entities->page();

        $content = '<!-- Test Comment -->';
        $this->put($page->getUrl(), [
            'name' => $page->name,  'markdown' => $content,
            'html' => '', 'summary' => '',
        ]);

        $page->refresh();
        $this->assertStringMatchesFormat($content, $page->html);

        $pageView = $this->get($page->getUrl());
        $pageView->assertStatus(200);
        $pageView->assertSee($content, false);
    }

    public function test_base64_images_get_extracted_from_page_content()
    {
        $this->asEditor();
        $page = $this->entities->page();

        $this->put($page->getUrl(), [
            'name' => $page->name, 'summary' => '',
            'html' => '<p>test<img src="data:image/jpeg;base64,' . $this->base64Jpeg . '"/></p>',
        ]);

        $page->refresh();
        $this->assertStringMatchesFormat('%A<p%A>test<img src="http://localhost/uploads/images/gallery/%A.jpeg">%A</p>%A', $page->html);

        $matches = [];
        preg_match('/src="http:\/\/localhost(.*?)"/', $page->html, $matches);
        $imagePath = $matches[1];
        $imageFile = public_path($imagePath);
        $this->assertEquals(base64_decode($this->base64Jpeg), file_get_contents($imageFile));

        $this->deleteImage($imagePath);
    }

    public function test_base64_images_get_extracted_when_containing_whitespace()
    {
        $this->asEditor();
        $page = $this->entities->page();

        $base64PngWithWhitespace = "iVBORw0KGg\noAAAANSUhE\tUgAAAAEAAAA BCA   YAAAAfFcSJAAA\n\t ACklEQVR4nGMAAQAABQAB";
        $base64PngWithoutWhitespace = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAACklEQVR4nGMAAQAABQAB';
        $this->put($page->getUrl(), [
            'name' => $page->name, 'summary' => '',
            'html' => '<p>test<img src="data:image/png;base64,' . $base64PngWithWhitespace . '"/></p>',
        ]);

        $page->refresh();
        $this->assertStringMatchesFormat('%A<p%A>test<img src="http://localhost/uploads/images/gallery/%A.png">%A</p>%A', $page->html);

        $matches = [];
        preg_match('/src="http:\/\/localhost(.*?)"/', $page->html, $matches);
        $imagePath = $matches[1];
        $imageFile = public_path($imagePath);
        $this->assertEquals(base64_decode($base64PngWithoutWhitespace), file_get_contents($imageFile));

        $this->deleteImage($imagePath);
    }

    public function test_base64_images_within_html_blanked_if_not_supported_extension_for_extract()
    {
        // Relevant to https://github.com/BookStackApp/BookStack/issues/3010 and other cases
        $extensions = [
            'jiff', 'pngr', 'png ', ' png', '.png', 'png.', 'p.ng', ',png',
            'data:image/png', ',data:image/png',
        ];

        foreach ($extensions as $extension) {
            $this->asEditor();
            $page = $this->entities->page();

            $this->put($page->getUrl(), [
                'name' => $page->name, 'summary' => '',
                'html' => '<p>test<img src="data:image/' . $extension . ';base64,' . $this->base64Jpeg . '"/></p>',
            ]);

            $page->refresh();
            $this->assertStringContainsString('<img src=""', $page->html);
        }
    }

    public function test_base64_images_get_extracted_from_markdown_page_content()
    {
        $this->asEditor();
        $page = $this->entities->page();

        $this->put($page->getUrl(), [
            'name'     => $page->name, 'summary' => '',
            'markdown' => 'test ![test](data:image/jpeg;base64,' . $this->base64Jpeg . ')',
        ]);

        $page->refresh();
        $this->assertStringMatchesFormat('%A<p%A>test <img src="http://localhost/uploads/images/gallery/%A.jpeg" alt="test">%A</p>%A', $page->html);

        $matches = [];
        preg_match('/src="http:\/\/localhost(.*?)"/', $page->html, $matches);
        $imagePath = $matches[1];
        $imageFile = public_path($imagePath);
        $this->assertEquals(base64_decode($this->base64Jpeg), file_get_contents($imageFile));

        $this->deleteImage($imagePath);
    }

    public function test_markdown_base64_extract_not_limited_by_pcre_limits()
    {
        $pcreBacktrackLimit = ini_get('pcre.backtrack_limit');
        $pcreRecursionLimit = ini_get('pcre.recursion_limit');

        $this->asEditor();
        $page = $this->entities->page();

        ini_set('pcre.backtrack_limit', '500');
        ini_set('pcre.recursion_limit', '500');

        $content = str_repeat('a', 5000);
        $base64Content = base64_encode($content);

        $this->put($page->getUrl(), [
            'name'     => $page->name, 'summary' => '',
            'markdown' => 'test ![test](data:image/jpeg;base64,' . $base64Content . ') ![test](data:image/jpeg;base64,' . $base64Content . ')',
        ]);

        $page->refresh();
        $this->assertStringMatchesFormat('<p%A>test <img src="http://localhost/uploads/images/gallery/%A.jpeg" alt="test"> <img src="http://localhost/uploads/images/gallery/%A.jpeg" alt="test">%A</p>%A', $page->html);

        $matches = [];
        preg_match('/src="http:\/\/localhost(.*?)"/', $page->html, $matches);
        $imagePath = $matches[1];
        $imageFile = public_path($imagePath);
        $this->assertEquals($content, file_get_contents($imageFile));

        $this->deleteImage($imagePath);
        ini_set('pcre.backtrack_limit', $pcreBacktrackLimit);
        ini_set('pcre.recursion_limit', $pcreRecursionLimit);
    }

    public function test_base64_images_within_markdown_blanked_if_not_supported_extension_for_extract()
    {
        $page = $this->entities->page();

        $this->asEditor()->put($page->getUrl(), [
            'name'     => $page->name, 'summary' => '',
            'markdown' => 'test ![test](data:image/jiff;base64,' . $this->base64Jpeg . ')',
        ]);

        $this->assertStringContainsString('<img src=""', $page->refresh()->html);
    }

    public function test_nested_headers_gets_assigned_an_id()
    {
        $page = $this->entities->page();

        $content = '<table><tbody><tr><td><h5>Simple Test</h5></td></tr></tbody></table>';
        $this->asEditor()->put($page->getUrl(), [
            'name'    => $page->name,
            'html'    => $content,
        ]);

        // The top level <table> node will get assign the bkmrk-simple-test id because the system will
        // take the node value of h5
        // So the h5 should get the bkmrk-simple-test-1 id
        $this->assertStringContainsString('<h5 id="bkmrk-simple-test-1">Simple Test</h5>', $page->refresh()->html);
    }

    public function test_non_breaking_spaces_are_preserved()
    {
        $page = $this->entities->page();

        $content = '<p>&nbsp;</p>';
        $this->asEditor()->put($page->getUrl(), [
            'name'    => $page->name,
            'html'    => $content,
        ]);

        $this->assertStringContainsString('<p id="bkmrk-%C2%A0">&nbsp;</p>', $page->refresh()->html);
    }
}
