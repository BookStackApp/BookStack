<?php namespace Tests\Entity;

use BookStack\Entities\Models\Book;
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
        $resp->assertSee($page->created_at->formatLocalized('%e %B %Y %H:%M:%S'));
        $resp->assertDontSee($page->created_at->diffForHumans());
        $resp->assertSee($page->updated_at->formatLocalized('%e %B %Y %H:%M:%S'));
        $resp->assertDontSee($page->updated_at->diffForHumans());
    }

    public function test_page_export_does_not_include_user_or_revision_links()
    {
        $page = Page::first();

        $resp = $this->asEditor()->get($page->getUrl('/export/html'));
        $resp->assertDontSee($page->getUrl('/revisions'));
        $resp->assertDontSee($page->createdBy->getProfileUrl());
        $resp->assertSee($page->createdBy->name);
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

    public function test_page_image_containment_works_on_multiple_images_within_a_single_line()
    {
        $page = Page::first();
        Storage::disk('local')->makeDirectory('uploads/images/gallery');
        Storage::disk('local')->put('uploads/images/gallery/svg_test.svg', '<svg></svg>');
        Storage::disk('local')->put('uploads/images/gallery/svg_test2.svg', '<svg></svg>');
        $page->html = '<img src="http://localhost/uploads/images/gallery/svg_test.svg" class="a"><img src="http://localhost/uploads/images/gallery/svg_test2.svg" class="b">';
        $page->save();

        $resp = $this->asEditor()->get($page->getUrl('/export/html'));
        Storage::disk('local')->delete('uploads/images/gallery/svg_test.svg');
        Storage::disk('local')->delete('uploads/images/gallery/svg_test2.svg');

        $resp->assertDontSee('http://localhost/uploads/images/gallery/svg_test');
    }

    public function test_page_export_contained_html_image_fetches_only_run_when_url_points_to_image_upload_folder()
    {
        $page = Page::first();
        $page->html = '<img src="http://localhost/uploads/images/gallery/svg_test.svg"/>'
            .'<img src="http://localhost/uploads/svg_test.svg"/>'
            .'<img src="/uploads/svg_test.svg"/>';
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

    public function test_exports_removes_scripts_from_custom_head()
    {
        $entities = [
            Page::query()->first(), Chapter::query()->first(), Book::query()->first(),
        ];
        setting()->put('app-custom-head', '<script>window.donkey = "cat";</script><style>.my-test-class { color: red; }</style>');

        foreach ($entities as $entity) {
            $resp = $this->asEditor()->get($entity->getUrl('/export/html'));
            $resp->assertDontSee('window.donkey');
            $resp->assertDontSee('script');
            $resp->assertSee('.my-test-class { color: red; }');
        }
    }

    public function test_page_export_with_deleted_creator_and_updater()
    {
        $user = $this->getViewer(['name' => 'ExportWizardTheFifth']);
        $page = Page::first();
        $page->created_by = $user->id;
        $page->updated_by = $user->id;
        $page->save();

        $resp = $this->asEditor()->get($page->getUrl('/export/html'));
        $resp->assertSee('ExportWizardTheFifth');

        $user->delete();
        $resp = $this->get($page->getUrl('/export/html'));
        $resp->assertStatus(200);
        $resp->assertDontSee('ExportWizardTheFifth');
    }

}
