<?php namespace Tests;

use BookStack\Entities\Page;
use BookStack\Entities\Repos\EntityRepo;
use BookStack\Entities\Repos\PageRepo;

class PageContentTest extends TestCase
{

    public function test_page_includes()
    {
        $page = Page::first();
        $secondPage = Page::where('id', '!=', $page->id)->first();

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
        $page = Page::first();
        $secondPage = Page::where('id', '!=', $page->id)->first();

        $this->asEditor();
        $includeTag = '{{@' . $secondPage->id . '}}';
        $page->html = '<p>' . $includeTag . '</p>';

        $resp = $this->put($page->getUrl(), ['name' => $page->name, 'html' => $page->html, 'summary' => '']);

        $resp->assertStatus(302);

        $page = Page::find($page->id);
        $this->assertContains($includeTag, $page->html);
        $this->assertEquals('', $page->text);
    }

    public function test_page_includes_do_not_break_tables()
    {
        $page = Page::first();
        $secondPage = Page::where('id', '!=', $page->id)->first();

        $content = '<table id="table"><tbody><tr><td>test</td></tr></tbody></table>';
        $secondPage->html = $content;
        $secondPage->save();

        $page->html = "{{@{$secondPage->id}#table}}";
        $page->save();

        $this->asEditor();
        $pageResp = $this->get($page->getUrl());
        $pageResp->assertSee($content);
    }

    public function test_page_content_scripts_removed_by_default()
    {
        $this->asEditor();
        $page = Page::first();
        $script = 'abc123<script>console.log("hello-test")</script>abc123';
        $page->html = "escape {$script}";
        $page->save();

        $pageView = $this->get($page->getUrl());
        $pageView->assertStatus(200);
        $pageView->assertDontSee($script);
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
        $page = Page::first();

        foreach ($checks as $check) {
            $page->html = $check;
            $page->save();

            $pageView = $this->get($page->getUrl());
            $pageView->assertStatus(200);
            $pageView->assertElementNotContains('.page-content', '<script>');
            $pageView->assertElementNotContains('.page-content', '</script>');
        }

    }

    public function test_iframe_js_and_base64_urls_are_removed()
    {
        $checks = [
            '<iframe src="javascript:alert(document.cookie)"></iframe>',
            '<iframe SRC=" javascript: alert(document.cookie)"></iframe>',
            '<iframe src="data:text/html;base64,PHNjcmlwdD5hbGVydCgnaGVsbG8nKTwvc2NyaXB0Pg==" frameborder="0"></iframe>',
            '<iframe src=" data:text/html;base64,PHNjcmlwdD5hbGVydCgnaGVsbG8nKTwvc2NyaXB0Pg==" frameborder="0"></iframe>',
            '<iframe srcdoc="<script>window.alert(document.cookie)</script>"></iframe>'
        ];

        $this->asEditor();
        $page = Page::first();

        foreach ($checks as $check) {
            $page->html = $check;
            $page->save();

            $pageView = $this->get($page->getUrl());
            $pageView->assertStatus(200);
            $pageView->assertElementNotContains('.page-content', '<iframe>');
            $pageView->assertElementNotContains('.page-content', '</iframe>');
            $pageView->assertElementNotContains('.page-content', 'src=');
            $pageView->assertElementNotContains('.page-content', 'javascript:');
            $pageView->assertElementNotContains('.page-content', 'data:');
            $pageView->assertElementNotContains('.page-content', 'base64');
        }

    }

    public function test_page_inline_on_attributes_removed_by_default()
    {
        $this->asEditor();
        $page = Page::first();
        $script = '<p onmouseenter="console.log(\'test\')">Hello</p>';
        $page->html = "escape {$script}";
        $page->save();

        $pageView = $this->get($page->getUrl());
        $pageView->assertStatus(200);
        $pageView->assertDontSee($script);
        $pageView->assertSee('<p>Hello</p>');
    }

    public function test_more_complex_inline_on_attributes_escaping_scenarios()
    {
        $checks = [
            '<p onclick="console.log(\'test\')">Hello</p>',
            '<div>Lorem ipsum dolor sit amet.</div><p onclick="console.log(\'test\')">Hello</p>',
            '<div>Lorem ipsum dolor sit amet.<p onclick="console.log(\'test\')">Hello</p></div>',
            '<div><div><div><div>Lorem ipsum dolor sit amet.<p onclick="console.log(\'test\')">Hello</p></div></div></div></div>',
            '<div onclick="console.log(\'test\')">Lorem ipsum dolor sit amet.</div><p onclick="console.log(\'test\')">Hello</p><div></div>',
            '<a a="<img src=1 onerror=\'alert(1)\'> ',
        ];

        $this->asEditor();
        $page = Page::first();

        foreach ($checks as $check) {
            $page->html = $check;
            $page->save();

            $pageView = $this->get($page->getUrl());
            $pageView->assertStatus(200);
            $pageView->assertElementNotContains('.page-content', 'onclick');
        }

    }

    public function test_page_content_scripts_show_when_configured()
    {
        $this->asEditor();
        $page = Page::first();
        config()->push('app.allow_content_scripts', 'true');

        $script = 'abc123<script>console.log("hello-test")</script>abc123';
        $page->html = "no escape {$script}";
        $page->save();

        $pageView = $this->get($page->getUrl());
        $pageView->assertSee($script);
        $pageView->assertDontSee('abc123abc123');
    }

    public function test_page_inline_on_attributes_show_if_configured()
    {
        $this->asEditor();
        $page = Page::first();
        config()->push('app.allow_content_scripts', 'true');

        $script = '<p onmouseenter="console.log(\'test\')">Hello</p>';
        $page->html = "escape {$script}";
        $page->save();

        $pageView = $this->get($page->getUrl());
        $pageView->assertSee($script);
        $pageView->assertDontSee('<p>Hello</p>');
    }

    public function test_duplicate_ids_does_not_break_page_render()
    {
        $this->asEditor();
        $pageA = Page::first();
        $pageB = Page::query()->where('id', '!=', $pageA->id)->first();

        $content = '<ul id="bkmrk-xxx-%28"></ul> <ul id="bkmrk-xxx-%28"></ul>';
        $pageA->html = $content;
        $pageA->save();

        $pageB->html = '<ul id="bkmrk-xxx-%28"></ul> <p>{{@'. $pageA->id .'#test}}</p>';
        $pageB->save();

        $pageView = $this->get($pageB->getUrl());
        $pageView->assertSuccessful();
    }

    public function test_duplicate_ids_fixed_on_page_save()
    {
        $this->asEditor();
        $page = Page::first();

        $content = '<ul id="bkmrk-test"><li>test a</li><li><ul id="bkmrk-test"><li>test b</li></ul></li></ul>';
        $pageSave = $this->put($page->getUrl(), [
            'name' => $page->name,
            'html' => $content,
            'summary' => ''
        ]);
        $pageSave->assertRedirect();

        $updatedPage = Page::where('id', '=', $page->id)->first();
        $this->assertEquals(substr_count($updatedPage->html, "bkmrk-test\""), 1);
    }
}
