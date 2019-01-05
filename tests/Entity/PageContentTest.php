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

    public function test_page_revision_views_viewable()
    {
        $this->asEditor();

        $pageRepo = app(PageRepo::class);
        $page = Page::first();
        $pageRepo->updatePage($page, $page->book_id, ['name' => 'updated page', 'html' => '<p>new content</p>', 'summary' => 'page revision testing']);
        $pageRevision = $page->revisions->last();

        $revisionView = $this->get($page->getUrl() . '/revisions/' . $pageRevision->id);
        $revisionView->assertStatus(200);
        $revisionView->assertSee('new content');

        $revisionView = $this->get($page->getUrl() . '/revisions/' . $pageRevision->id . '/changes');
        $revisionView->assertStatus(200);
        $revisionView->assertSee('new content');
    }

    public function test_page_revision_restore_updates_content()
    {
        $this->asEditor();

        $pageRepo = app(PageRepo::class);
        $page = Page::first();
        $pageRepo->updatePage($page, $page->book_id, ['name' => 'updated page abc123', 'html' => '<p>new contente def456</p>', 'summary' => 'initial page revision testing']);
        $pageRepo->updatePage($page, $page->book_id, ['name' => 'updated page again', 'html' => '<p>new content</p>', 'summary' => 'page revision testing']);
        $page =  Page::find($page->id);


        $pageView = $this->get($page->getUrl());
        $pageView->assertDontSee('abc123');
        $pageView->assertDontSee('def456');

        $revToRestore = $page->revisions()->where('name', 'like', '%abc123')->first();
        $restoreReq = $this->get($page->getUrl() . '/revisions/' . $revToRestore->id . '/restore');
        $page =  Page::find($page->id);

        $restoreReq->assertStatus(302);
        $restoreReq->assertRedirect($page->getUrl());

        $pageView = $this->get($page->getUrl());
        $pageView->assertSee('abc123');
        $pageView->assertSee('def456');
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

}
