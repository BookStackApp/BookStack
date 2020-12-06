<?php namespace Tests\Entity;


use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ExportTest extends TestCase
{

    public function test_page_text_export()
    {
        $page = Page::first();
        $this->asEditor();

        $resp = $this->get($page->getUrl('/export/plaintext'));
        $resp->assertStatus(200);
        $resp->assertSee($page->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $page->slug . '.txt"');
    }

    public function test_page_pdf_export()
    {
        $page = Page::first();
        $this->asEditor();

        $resp = $this->get($page->getUrl('/export/pdf'));
        $resp->assertStatus(200);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $page->slug . '.pdf"');
    }

    public function test_page_html_export()
    {
        $page = Page::first();
        $this->asEditor();

        $resp = $this->get($page->getUrl('/export/html'));
        $resp->assertStatus(200);
        $resp->assertSee($page->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $page->slug . '.html"');
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
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $book->slug . '.txt"');
    }

    public function test_book_pdf_export()
    {
        $page = Page::first();
        $book = $page->book;
        $this->asEditor();

        $resp = $this->get($book->getUrl('/export/pdf'));
        $resp->assertStatus(200);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $book->slug . '.pdf"');
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
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $book->slug . '.html"');
    }

    public function test_book_html_export_shows_chapter_descriptions()
    {
        $chapterDesc = 'My custom test chapter description ' . Str::random(12);
        $chapter = Chapter::query()->first();
        $chapter->description = $chapterDesc;
        $chapter->save();

        $book = $chapter->book;
        $this->asEditor();

        $resp = $this->get($book->getUrl('/export/html'));
        $resp->assertSee($chapterDesc);
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
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $chapter->slug . '.txt"');
    }

    public function test_chapter_pdf_export()
    {
        $chapter = Chapter::first();
        $this->asEditor();

        $resp = $this->get($chapter->getUrl('/export/pdf'));
        $resp->assertStatus(200);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $chapter->slug . '.pdf"');
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
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $chapter->slug . '.html"');
    }

    public function test_page_html_export_contains_custom_head_if_set()
    {
        $page = Page::first();

        $customHeadContent = "<style>p{color: red;}</style>";
        $this->setSettings(['app-custom-head' => $customHeadContent]);

        $resp = $this->asEditor()->get($page->getUrl('/export/html'));
        $resp->assertSee($customHeadContent);
    }

    public function test_page_html_export_use_absolute_dates()
    {
        $page = Page::first();

        $resp = $this->asEditor()->get($page->getUrl('/export/html'));
        $resp->assertSee($page->created_at->toDayDateTimeString());
        $resp->assertDontSee($page->created_at->diffForHumans());
        $resp->assertSee($page->updated_at->toDayDateTimeString());
        $resp->assertDontSee($page->updated_at->diffForHumans());
    }

    public function test_page_export_sets_right_data_type_for_svg_embeds()
    {
        $page = Page::first();
        Storage::disk('local')->makeDirectory('uploads/images/gallery');
        Storage::disk('local')->put('uploads/images/gallery/svg_test.svg', '<svg></svg>');
        $page->html = '<img src="http://localhost/uploads/images/gallery/svg_test.svg">';
        $page->save();

        $this->asEditor();
        $resp = $this->get($page->getUrl('/export/html'));
        Storage::disk('local')->delete('uploads/images/gallery/svg_test.svg');

        $resp->assertStatus(200);
        $resp->assertSee('<img src="data:image/svg+xml;base64');
    }

    public function test_page_export_contained_html_image_fetches_only_run_when_url_points_to_image_upload_folder()
    {
        $page = Page::first();
        $page->html = '<img src="http://localhost/uploads/images/gallery/svg_test.svg"/>'
            ."\n".'<img src="http://localhost/uploads/svg_test.svg"/>'
            ."\n".'<img src="/uploads/svg_test.svg"/>';
        $storageDisk = Storage::disk('local');
        $storageDisk->makeDirectory('uploads/images/gallery');
        $storageDisk->put('uploads/images/gallery/svg_test.svg', '<svg>good</svg>');
        $storageDisk->put('uploads/svg_test.svg', '<svg>bad</svg>');
        $page->save();

        $resp = $this->asEditor()->get($page->getUrl('/export/html'));

        $storageDisk->delete('uploads/images/gallery/svg_test.svg');
        $storageDisk->delete('uploads/svg_test.svg');

        $resp->assertDontSee('http://localhost/uploads/images/gallery/svg_test.svg');
        $resp->assertSee('http://localhost/uploads/svg_test.svg');
        $resp->assertSee('src="/uploads/svg_test.svg"');
    }

}
