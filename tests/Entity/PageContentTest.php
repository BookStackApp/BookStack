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

    public function test_page_content_scripts_escaped_by_default()
    {
        $this->asEditor();
        $page = Page::first();
        $script = '<script>console.log("hello-test")</script>';
        $page->html = "escape {$script}";
        $page->save();

        $pageView = $this->get($page->getUrl());
        $pageView->assertDontSee($script);
        $pageView->assertSee(htmlentities($script));
    }

    public function test_page_content_scripts_show_when_configured()
    {
        $this->asEditor();
        $page = Page::first();
        config()->push('app.allow_content_scripts', 'true');
        $script = '<script>console.log("hello-test")</script>';
        $page->html = "no escape {$script}";
        $page->save();

        $pageView = $this->get($page->getUrl());
        $pageView->assertSee($script);
        $pageView->assertDontSee(htmlentities($script));
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

        $content = '<p id="bkmrk-test">test a</p>'."\n".'<p id="bkmrk-test">test b</p>';
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
