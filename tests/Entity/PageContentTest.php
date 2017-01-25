<?php

class PageContentTest extends BrowserKitTest
{

    public function test_page_includes()
    {
        $page = \BookStack\Page::first();
        $secondPage = \BookStack\Page::all()->get(2);

        $secondPage->html = "<p id='section1'>Hello, This is a test</p><p id='section2'>This is a second block of content</p>";
        $secondPage->save();

        $this->asAdmin()->visit($page->getUrl())
            ->dontSee('Hello, This is a test');

        $originalHtml = $page->html;
        $page->html .= "{{@{$secondPage->id}}}";
        $page->save();

        $this->asAdmin()->visit($page->getUrl())
            ->see('Hello, This is a test')
            ->see('This is a second block of content');

        $page->html = $originalHtml . " Well {{@{$secondPage->id}#section2}}";
        $page->save();

        $this->asAdmin()->visit($page->getUrl())
            ->dontSee('Hello, This is a test')
            ->see('Well This is a second block of content');
    }

}
