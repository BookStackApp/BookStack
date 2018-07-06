<?php namespace Tests;


use BookStack\Chapter;
use BookStack\Page;

class ExportTest extends TestCase
{

    public function test_page_text_export()
    {
        $page = Page::first();
        $this->asEditor();

        $resp = $this->get($page->getUrl('/export/plaintext'));
        $resp->assertStatus(200);
        $resp->assertSee($page->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $page->slug . '.txt');
    }

    public function test_page_pdf_export()
    {
        $page = Page::first();
        $this->asEditor();

        $resp = $this->get($page->getUrl('/export/pdf'));
        $resp->assertStatus(200);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $page->slug . '.pdf');
    }

    public function test_page_html_export()
    {
        $page = Page::first();
        $this->asEditor();

        $resp = $this->get($page->getUrl('/export/html'));
        $resp->assertStatus(200);
        $resp->assertSee($page->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $page->slug . '.html');
    }

    public function test_book_text_export()
    {
        $page = Page::first();
        $book = $page->book;
        $this->asEditor();

        $resp = $this->get($book->getUrl('/export/plaintext'));
        $resp->assertStatus(200);
        $resp->assertSee($book->name);
        $resp->assertSee($page->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $book->slug . '.txt');
    }

    public function test_book_pdf_export()
    {
        $page = Page::first();
        $book = $page->book;
        $this->asEditor();

        $resp = $this->get($book->getUrl('/export/pdf'));
        $resp->assertStatus(200);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $book->slug . '.pdf');
    }

    public function test_book_html_export()
    {
        $page = Page::first();
        $book = $page->book;
        $this->asEditor();

        $resp = $this->get($book->getUrl('/export/html'));
        $resp->assertStatus(200);
        $resp->assertSee($book->name);
        $resp->assertSee($page->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $book->slug . '.html');
    }

    public function test_chapter_text_export()
    {
        $chapter = Chapter::first();
        $page = $chapter->pages[0];
        $this->asEditor();

        $resp = $this->get($chapter->getUrl('/export/plaintext'));
        $resp->assertStatus(200);
        $resp->assertSee($chapter->name);
        $resp->assertSee($page->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $chapter->slug . '.txt');
    }

    public function test_chapter_pdf_export()
    {
        $chapter = Chapter::first();
        $this->asEditor();

        $resp = $this->get($chapter->getUrl('/export/pdf'));
        $resp->assertStatus(200);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $chapter->slug . '.pdf');
    }

    public function test_chapter_html_export()
    {
        $chapter = Chapter::first();
        $page = $chapter->pages[0];
        $this->asEditor();

        $resp = $this->get($chapter->getUrl('/export/html'));
        $resp->assertStatus(200);
        $resp->assertSee($chapter->name);
        $resp->assertSee($page->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $chapter->slug . '.html');
    }

    public function test_html_export_media_protocol_updated()
    {
        $page = Page::first();
        $page->html = '<p id="bkmrk-%C2%A0-0">&nbsp;</p><p id="bkmrk-%C2%A0-1"><iframe src="//www.youtube.com/embed/LkFt_fp7FmE" width="560" height="314" allowfullscreen="allowfullscreen"></iframe></p><p id="bkmrk-"><iframe src="//player.vimeo.com/video/276396369?title=0&amp;amp;byline=0" width="425" height="350" allowfullscreen="allowfullscreen"></iframe></p><p id="bkmrk--0"><iframe style="border: 0;" src="//maps.google.com/embed?testquery=true" width="600" height="450" frameborder="0" allowfullscreen="allowfullscreen"></iframe></p><p id="bkmrk--1"><iframe src="//www.dailymotion.com/embed/video/x2rqgfm" width="480" height="432" frameborder="0" allowfullscreen="allowfullscreen"></iframe></p><p id="bkmrk-%C2%A0-2">&nbsp;</p>';
        $page->save();

        $this->asEditor();
        $resp = $this->get($page->getUrl('/export/html'));
        $resp->assertStatus(200);

        $checks = [
            'https://www.youtube.com/embed/LkFt_fp7FmE',
            'https://player.vimeo.com/video/276396369?title=0&amp;amp;byline=0',
            'https://maps.google.com/embed?testquery=true',
            'https://www.dailymotion.com/embed/video/x2rqgfm',
        ];

        foreach ($checks as $check) {
            $resp->assertSee($check);
        }

    }

}