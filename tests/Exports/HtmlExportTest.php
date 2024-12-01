<?php

namespace Tests\Exports;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HtmlExportTest extends TestCase
{
    public function test_page_html_export()
    {
        $page = $this->entities->page();
        $this->asEditor();

        $resp = $this->get($page->getUrl('/export/html'));
        $resp->assertStatus(200);
        $resp->assertSee($page->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $page->slug . '.html"');
    }

    public function test_book_html_export()
    {
        $page = $this->entities->page();
        $book = $page->book;
        $this->asEditor();

        $resp = $this->get($book->getUrl('/export/html'));
        $resp->assertStatus(200);
        $resp->assertSee($book->name);
        $resp->assertSee($page->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $book->slug . '.html"');
    }

    public function test_book_html_export_shows_html_descriptions()
    {
        $book = $this->entities->bookHasChaptersAndPages();
        $chapter = $book->chapters()->first();
        $book->description_html = '<p>A description with <strong>HTML</strong> within!</p>';
        $chapter->description_html = '<p>A chapter description with <strong>HTML</strong> within!</p>';
        $book->save();
        $chapter->save();

        $resp = $this->asEditor()->get($book->getUrl('/export/html'));
        $resp->assertSee($book->description_html, false);
        $resp->assertSee($chapter->description_html, false);
    }

    public function test_chapter_html_export()
    {
        $chapter = $this->entities->chapter();
        $page = $chapter->pages[0];
        $this->asEditor();

        $resp = $this->get($chapter->getUrl('/export/html'));
        $resp->assertStatus(200);
        $resp->assertSee($chapter->name);
        $resp->assertSee($page->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $chapter->slug . '.html"');
    }

    public function test_chapter_html_export_shows_html_descriptions()
    {
        $chapter = $this->entities->chapter();
        $chapter->description_html = '<p>A description with <strong>HTML</strong> within!</p>';
        $chapter->save();

        $resp = $this->asEditor()->get($chapter->getUrl('/export/html'));
        $resp->assertSee($chapter->description_html, false);
    }

    public function test_page_html_export_contains_custom_head_if_set()
    {
        $page = $this->entities->page();

        $customHeadContent = '<style>p{color: red;}</style>';
        $this->setSettings(['app-custom-head' => $customHeadContent]);

        $resp = $this->asEditor()->get($page->getUrl('/export/html'));
        $resp->assertSee($customHeadContent, false);
    }

    public function test_page_html_export_does_not_break_with_only_comments_in_custom_head()
    {
        $page = $this->entities->page();

        $customHeadContent = '<!-- A comment -->';
        $this->setSettings(['app-custom-head' => $customHeadContent]);

        $resp = $this->asEditor()->get($page->getUrl('/export/html'));
        $resp->assertStatus(200);
        $resp->assertSee($customHeadContent, false);
    }

    public function test_page_html_export_use_absolute_dates()
    {
        $page = $this->entities->page();

        $resp = $this->asEditor()->get($page->getUrl('/export/html'));
        $resp->assertSee($page->created_at->isoFormat('D MMMM Y HH:mm:ss'));
        $resp->assertDontSee($page->created_at->diffForHumans());
        $resp->assertSee($page->updated_at->isoFormat('D MMMM Y HH:mm:ss'));
        $resp->assertDontSee($page->updated_at->diffForHumans());
    }

    public function test_page_export_does_not_include_user_or_revision_links()
    {
        $page = $this->entities->page();

        $resp = $this->asEditor()->get($page->getUrl('/export/html'));
        $resp->assertDontSee($page->getUrl('/revisions'));
        $resp->assertDontSee($page->createdBy->getProfileUrl());
        $resp->assertSee($page->createdBy->name);
    }

    public function test_page_export_sets_right_data_type_for_svg_embeds()
    {
        $page = $this->entities->page();
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
        $page = $this->entities->page();
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
        $page = $this->entities->page();
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

        $page = $this->entities->page();
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

        $page = $this->entities->page();
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
            $resp->assertDontSee('<script', false);
            $resp->assertSee('.my-test-class { color: red; }');
        }
    }

    public function test_page_export_with_deleted_creator_and_updater()
    {
        $user = $this->users->viewer(['name' => 'ExportWizardTheFifth']);
        $page = $this->entities->page();
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

    public function test_html_exports_contain_csp_meta_tag()
    {
        $entities = [
            $this->entities->page(),
            $this->entities->book(),
            $this->entities->chapter(),
        ];

        foreach ($entities as $entity) {
            $resp = $this->asEditor()->get($entity->getUrl('/export/html'));
            $this->withHtml($resp)->assertElementExists('head meta[http-equiv="Content-Security-Policy"][content*="script-src "]');
        }
    }

    public function test_html_exports_contain_body_classes_for_export_identification()
    {
        $page = $this->entities->page();

        $resp = $this->asEditor()->get($page->getUrl('/export/html'));
        $this->withHtml($resp)->assertElementExists('body.export.export-format-html.export-engine-none');
    }
}
