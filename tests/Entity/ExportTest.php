<?php

namespace Tests\Entity;

use BookStack\Auth\Role;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Tools\PdfGenerator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Tests\TestCase;

class ExportTest extends TestCase
{
    public function test_page_text_export()
    {
        $page = Page::query()->first();
        $this->asEditor();

        $resp = $this->get($page->getUrl('/export/plaintext'));
        $resp->assertStatus(200);
        $resp->assertSee($page->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $page->slug . '.txt"');
    }

    public function test_page_pdf_export()
    {
        $page = Page::query()->first();
        $this->asEditor();

        $resp = $this->get($page->getUrl('/export/pdf'));
        $resp->assertStatus(200);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $page->slug . '.pdf"');
    }

    public function test_page_html_export()
    {
        $page = Page::query()->first();
        $this->asEditor();

        $resp = $this->get($page->getUrl('/export/html'));
        $resp->assertStatus(200);
        $resp->assertSee($page->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $page->slug . '.html"');
    }

    public function test_book_text_export()
    {
        $page = Page::query()->first();
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
        $page = Page::query()->first();
        $book = $page->book;
        $this->asEditor();

        $resp = $this->get($book->getUrl('/export/pdf'));
        $resp->assertStatus(200);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $book->slug . '.pdf"');
    }

    public function test_book_html_export()
    {
        $page = Page::query()->first();
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
        $chapter = Chapter::query()->first();
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
        $chapter = Chapter::query()->first();
        $this->asEditor();

        $resp = $this->get($chapter->getUrl('/export/pdf'));
        $resp->assertStatus(200);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $chapter->slug . '.pdf"');
    }

    public function test_chapter_html_export()
    {
        $chapter = Chapter::query()->first();
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
        $page = Page::query()->first();

        $customHeadContent = '<style>p{color: red;}</style>';
        $this->setSettings(['app-custom-head' => $customHeadContent]);

        $resp = $this->asEditor()->get($page->getUrl('/export/html'));
        $resp->assertSee($customHeadContent, false);
    }

    public function test_page_html_export_does_not_break_with_only_comments_in_custom_head()
    {
        $page = Page::query()->first();

        $customHeadContent = '<!-- A comment -->';
        $this->setSettings(['app-custom-head' => $customHeadContent]);

        $resp = $this->asEditor()->get($page->getUrl('/export/html'));
        $resp->assertStatus(200);
        $resp->assertSee($customHeadContent, false);
    }

    public function test_page_html_export_use_absolute_dates()
    {
        $page = Page::query()->first();

        $resp = $this->asEditor()->get($page->getUrl('/export/html'));
        $resp->assertSee($page->created_at->formatLocalized('%e %B %Y %H:%M:%S'));
        $resp->assertDontSee($page->created_at->diffForHumans());
        $resp->assertSee($page->updated_at->formatLocalized('%e %B %Y %H:%M:%S'));
        $resp->assertDontSee($page->updated_at->diffForHumans());
    }

    public function test_page_export_does_not_include_user_or_revision_links()
    {
        $page = Page::query()->first();

        $resp = $this->asEditor()->get($page->getUrl('/export/html'));
        $resp->assertDontSee($page->getUrl('/revisions'));
        $resp->assertDontSee($page->createdBy->getProfileUrl());
        $resp->assertSee($page->createdBy->name);
    }

    public function test_page_export_sets_right_data_type_for_svg_embeds()
    {
        $page = Page::query()->first();
        Storage::disk('local')->makeDirectory('uploads/images/gallery');
        Storage::disk('local')->put('uploads/images/gallery/svg_test.svg', '<svg></svg>');
        $page->html = '<img src="http://localhost/uploads/images/gallery/svg_test.svg">';
        $page->save();

        $this->asEditor();
        $resp = $this->get($page->getUrl('/export/html'));
        Storage::disk('local')->delete('uploads/images/gallery/svg_test.svg');

        $resp->assertStatus(200);
        $resp->assertSee('<img src="data:image/svg+xml;base64', false);
    }

    public function test_page_image_containment_works_on_multiple_images_within_a_single_line()
    {
        $page = Page::query()->first();
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
        $page = Page::query()->first();
        $page->html = '<img src="http://localhost/uploads/images/gallery/svg_test.svg"/>'
            . '<img src="http://localhost/uploads/svg_test.svg"/>'
            . '<img src="/uploads/svg_test.svg"/>';
        $storageDisk = Storage::disk('local');
        $storageDisk->makeDirectory('uploads/images/gallery');
        $storageDisk->put('uploads/images/gallery/svg_test.svg', '<svg>good</svg>');
        $storageDisk->put('uploads/svg_test.svg', '<svg>bad</svg>');
        $page->save();

        $resp = $this->asEditor()->get($page->getUrl('/export/html'));

        $storageDisk->delete('uploads/images/gallery/svg_test.svg');
        $storageDisk->delete('uploads/svg_test.svg');

        $resp->assertDontSee('http://localhost/uploads/images/gallery/svg_test.svg', false);
        $resp->assertSee('http://localhost/uploads/svg_test.svg');
        $resp->assertSee('src="/uploads/svg_test.svg"', false);
    }

    public function test_page_export_contained_html_does_not_allow_upward_traversal_with_local()
    {
        $contents = file_get_contents(public_path('.htaccess'));
        config()->set('filesystems.images', 'local');

        $page = Page::query()->first();
        $page->html = '<img src="http://localhost/uploads/images/../../.htaccess"/>';
        $page->save();

        $resp = $this->asEditor()->get($page->getUrl('/export/html'));
        $resp->assertDontSee(base64_encode($contents));
    }

    public function test_page_export_contained_html_does_not_allow_upward_traversal_with_local_secure()
    {
        $testFilePath = storage_path('logs/test.txt');
        config()->set('filesystems.images', 'local_secure');
        file_put_contents($testFilePath, 'I am a cat');

        $page = Page::query()->first();
        $page->html = '<img src="http://localhost/uploads/images/../../logs/test.txt"/>';
        $page->save();

        $resp = $this->asEditor()->get($page->getUrl('/export/html'));
        $resp->assertDontSee(base64_encode('I am a cat'));
        unlink($testFilePath);
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
        $page = Page::query()->first();
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

    public function test_page_pdf_export_converts_iframes_to_links()
    {
        $page = Page::query()->first()->forceFill([
            'html'     => '<iframe width="560" height="315" src="//www.youtube.com/embed/ShqUjt33uOs"></iframe>',
        ]);
        $page->save();

        $pdfHtml = '';
        $mockPdfGenerator = $this->mock(PdfGenerator::class);
        $mockPdfGenerator->shouldReceive('fromHtml')
            ->with(\Mockery::capture($pdfHtml))
            ->andReturn('');

        $this->asEditor()->get($page->getUrl('/export/pdf'));
        $this->assertStringNotContainsString('iframe>', $pdfHtml);
        $this->assertStringContainsString('<p><a href="https://www.youtube.com/embed/ShqUjt33uOs">https://www.youtube.com/embed/ShqUjt33uOs</a></p>', $pdfHtml);
    }

    public function test_page_markdown_export()
    {
        $page = Page::query()->first();

        $resp = $this->asEditor()->get($page->getUrl('/export/markdown'));
        $resp->assertStatus(200);
        $resp->assertSee($page->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $page->slug . '.md"');
    }

    public function test_page_markdown_export_uses_existing_markdown_if_apparent()
    {
        $page = Page::query()->first()->forceFill([
            'markdown' => '# A header',
            'html'     => '<h1>Dogcat</h1>',
        ]);
        $page->save();

        $resp = $this->asEditor()->get($page->getUrl('/export/markdown'));
        $resp->assertSee('A header');
        $resp->assertDontSee('Dogcat');
    }

    public function test_page_markdown_export_converts_html_where_no_markdown()
    {
        $page = Page::query()->first()->forceFill([
            'markdown' => '',
            'html'     => '<h1>Dogcat</h1><p>Some <strong>bold</strong> text</p>',
        ]);
        $page->save();

        $resp = $this->asEditor()->get($page->getUrl('/export/markdown'));
        $resp->assertSee("# Dogcat\n\nSome **bold** text");
    }

    public function test_page_markdown_export_does_not_convert_callouts()
    {
        $page = Page::query()->first()->forceFill([
            'markdown' => '',
            'html'     => '<h1>Dogcat</h1><p class="callout info">Some callout text</p><p>Another line</p>',
        ]);
        $page->save();

        $resp = $this->asEditor()->get($page->getUrl('/export/markdown'));
        $resp->assertSee("# Dogcat\n\n<p class=\"callout info\">Some callout text</p>\n\nAnother line", false);
    }

    public function test_page_markdown_export_handles_bookstacks_wysiwyg_codeblock_format()
    {
        $page = Page::query()->first()->forceFill([
            'markdown' => '',
            'html'     => '<h1>Dogcat</h1>' . "\r\n" . '<pre id="bkmrk-var-a-%3D-%27cat%27%3B"><code class="language-JavaScript">var a = \'cat\';</code></pre><p>Another line</p>',
        ]);
        $page->save();

        $resp = $this->asEditor()->get($page->getUrl('/export/markdown'));
        $resp->assertSee("# Dogcat\n\n```JavaScript\nvar a = 'cat';\n```\n\nAnother line", false);
    }

    public function test_chapter_markdown_export()
    {
        $chapter = Chapter::query()->first();
        $page = $chapter->pages()->first();
        $resp = $this->asEditor()->get($chapter->getUrl('/export/markdown'));

        $resp->assertSee('# ' . $chapter->name);
        $resp->assertSee('# ' . $page->name);
    }

    public function test_book_markdown_export()
    {
        $book = Book::query()->whereHas('pages')->whereHas('chapters')->first();
        $chapter = $book->chapters()->first();
        $page = $chapter->pages()->first();
        $resp = $this->asEditor()->get($book->getUrl('/export/markdown'));

        $resp->assertSee('# ' . $book->name);
        $resp->assertSee('# ' . $chapter->name);
        $resp->assertSee('# ' . $page->name);
    }

    public function test_export_option_only_visible_and_accessible_with_permission()
    {
        $book = Book::query()->whereHas('pages')->whereHas('chapters')->first();
        $chapter = $book->chapters()->first();
        $page = $chapter->pages()->first();
        $entities = [$book, $chapter, $page];
        $user = $this->getViewer();
        $this->actingAs($user);

        foreach ($entities as $entity) {
            $resp = $this->get($entity->getUrl());
            $resp->assertSee('/export/pdf');
        }

        /** @var Role $role */
        $this->removePermissionFromUser($user, 'content-export');

        foreach ($entities as $entity) {
            $resp = $this->get($entity->getUrl());
            $resp->assertDontSee('/export/pdf');
            $resp = $this->get($entity->getUrl('/export/pdf'));
            $this->assertPermissionError($resp);
        }
    }

    public function test_wkhtmltopdf_only_used_when_allow_untrusted_is_true()
    {
        /** @var Page $page */
        $page = Page::query()->first();

        config()->set('snappy.pdf.binary', '/abc123');
        config()->set('app.allow_untrusted_server_fetching', false);

        $resp = $this->asEditor()->get($page->getUrl('/export/pdf'));
        $resp->assertStatus(200); // Sucessful response with invalid snappy binary indicates dompdf usage.

        config()->set('app.allow_untrusted_server_fetching', true);
        $resp = $this->get($page->getUrl('/export/pdf'));
        $resp->assertStatus(500); // Bad response indicates wkhtml usage
    }
}
