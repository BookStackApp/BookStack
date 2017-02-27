<?php namespace Tests;

use BookStack\Page;
use BookStack\Repos\EntityRepo;

class PageContentTest extends TestCase
{

    public function test_page_includes()
    {
        $page = Page::first();
        $secondPage = Page::all()->get(2);

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

    public function test_page_revision_views_viewable()
    {
        $this->asEditor();

        $entityRepo = $this->app[EntityRepo::class];
        $page = Page::first();
        $entityRepo->updatePage($page, $page->book_id, ['name' => 'updated page', 'html' => '<p>new content</p>', 'summary' => 'page revision testing']);
        $pageRevision = $page->revisions->last();

        $revisionView = $this->get($page->getUrl() . '/revisions/' . $pageRevision->id);
        $revisionView->assertStatus(200);
        $revisionView->assertSee('new content');

        $revisionView = $this->get($page->getUrl() . '/revisions/' . $pageRevision->id . '/changes');
        $revisionView->assertStatus(200);
        $revisionView->assertSee('new content');
    }

}
