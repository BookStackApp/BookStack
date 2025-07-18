<?php

namespace Tests\Exports;

use BookStack\Activity\Models\Tag;
use BookStack\Entities\Repos\BookRepo;
use BookStack\Entities\Tools\PageContent;
use BookStack\Uploads\Attachment;
use BookStack\Uploads\Image;
use FilesystemIterator;
use Illuminate\Support\Carbon;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;
use ZipArchive;

class ZipExportTest extends TestCase
{
    public function test_export_results_in_zip_format()
    {
        $page = $this->entities->page();
        $response = $this->asEditor()->get($page->getUrl("/export/zip"));

        $zipData = $response->streamedContent();
        $zipFile = tempnam(sys_get_temp_dir(), 'bstesta-');
        file_put_contents($zipFile, $zipData);
        $zip = new ZipArchive();
        $zip->open($zipFile, ZipArchive::RDONLY);

        $this->assertNotFalse($zip->locateName('data.json'));
        $this->assertNotFalse($zip->locateName('files/'));

        $data = json_decode($zip->getFromName('data.json'), true);
        $this->assertIsArray($data);
        $this->assertGreaterThan(0, count($data));

        $zip->close();
        unlink($zipFile);
    }

    public function test_export_metadata()
    {
        $page = $this->entities->page();
        $zipResp = $this->asEditor()->get($page->getUrl("/export/zip"));
        $zip = ZipTestHelper::extractFromZipResponse($zipResp);

        $this->assertEquals($page->id, $zip->data['page']['id'] ?? null);
        $this->assertArrayNotHasKey('book', $zip->data);
        $this->assertArrayNotHasKey('chapter', $zip->data);

        $now = time();
        $date = Carbon::parse($zip->data['exported_at'])->unix();
        $this->assertLessThan($now + 2, $date);
        $this->assertGreaterThan($now - 2, $date);

        $version = trim(file_get_contents(base_path('version')));
        $this->assertEquals($version, $zip->data['instance']['version']);

        $zipInstanceId = $zip->data['instance']['id'];
        $instanceId = setting('instance-id');
        $this->assertNotEmpty($instanceId);
        $this->assertEquals($instanceId, $zipInstanceId);
    }

    public function test_export_leaves_no_temp_files()
    {
        $tempDir = sys_get_temp_dir();
        $startTempFileCount = iterator_count((new FileSystemIterator($tempDir, FilesystemIterator::SKIP_DOTS)));

        $page = $this->entities->pageWithinChapter();
        $this->asEditor();
        $pageResp = $this->get($page->getUrl("/export/zip"));
        $pageResp->streamedContent();
        $pageResp->assertOk();
        $this->get($page->chapter->getUrl("/export/zip"))->assertOk();
        $this->get($page->book->getUrl("/export/zip"))->assertOk();

        $afterTempFileCount = iterator_count((new FileSystemIterator($tempDir, FilesystemIterator::SKIP_DOTS)));

        $this->assertEquals($startTempFileCount, $afterTempFileCount);
    }

    public function test_page_export()
    {
        $page = $this->entities->page();
        $zipResp = $this->asEditor()->get($page->getUrl("/export/zip"));
        $zip = ZipTestHelper::extractFromZipResponse($zipResp);

        $pageData = $zip->data['page'];
        $this->assertEquals([
            'id' => $page->id,
            'name' => $page->name,
            'html' => (new PageContent($page))->render(),
            'priority' => $page->priority,
            'attachments' => [],
            'images' => [],
            'tags' => [],
        ], $pageData);
    }

    public function test_page_export_with_markdown()
    {
        $page = $this->entities->page();
        $markdown = "# My page\n\nwritten in markdown for export\n";
        $page->markdown = $markdown;
        $page->save();

        $zipResp = $this->asEditor()->get($page->getUrl("/export/zip"));
        $zip = ZipTestHelper::extractFromZipResponse($zipResp);

        $pageData = $zip->data['page'];
        $this->assertEquals($markdown, $pageData['markdown']);
        $this->assertNotEmpty($pageData['html']);
    }

    public function test_page_export_with_tags()
    {
        $page = $this->entities->page();
        $page->tags()->saveMany([
            new Tag(['name' => 'Exporty', 'value' => 'Content', 'order' => 1]),
            new Tag(['name' => 'Another', 'value' => '', 'order' => 2]),
        ]);

        $zipResp = $this->asEditor()->get($page->getUrl("/export/zip"));
        $zip = ZipTestHelper::extractFromZipResponse($zipResp);

        $pageData = $zip->data['page'];
        $this->assertEquals([
            [
                'name' => 'Exporty',
                'value' => 'Content',
            ],
            [
                'name' => 'Another',
                'value' => '',
            ]
        ], $pageData['tags']);
    }

    public function test_page_export_with_images()
    {
        $this->asEditor();
        $page = $this->entities->page();
        $result = $this->files->uploadGalleryImageToPage($this, $page);
        $displayThumb = $result['response']->thumbs->gallery ?? '';
        $page->html = '<p><img src="' . $displayThumb . '" alt="My image"></p>';
        $page->save();
        $image = Image::findOrFail($result['response']->id);

        $zipResp = $this->asEditor()->get($page->getUrl("/export/zip"));
        $zip = ZipTestHelper::extractFromZipResponse($zipResp);
        $pageData = $zip->data['page'];

        $this->assertCount(1, $pageData['images']);
        $imageData = $pageData['images'][0];
        $this->assertEquals($image->id, $imageData['id']);
        $this->assertEquals($image->name, $imageData['name']);
        $this->assertEquals('gallery', $imageData['type']);
        $this->assertNotEmpty($imageData['file']);

        $filePath = $zip->extractPath("files/{$imageData['file']}");
        $this->assertFileExists($filePath);
        $this->assertEquals(file_get_contents(public_path($image->path)), file_get_contents($filePath));

        $this->assertEquals('<p><img src="[[bsexport:image:' . $imageData['id'] . ']]" alt="My image"></p>', $pageData['html']);
    }

    public function test_page_export_file_attachments()
    {
        $contents = 'My great attachment content!';

        $page = $this->entities->page();
        $this->asAdmin();
        $attachment = $this->files->uploadAttachmentDataToPage($this, $page, 'PageAttachmentExport.txt', $contents, 'text/plain');

        $zipResp = $this->get($page->getUrl("/export/zip"));
        $zip = ZipTestHelper::extractFromZipResponse($zipResp);

        $pageData = $zip->data['page'];
        $this->assertCount(1, $pageData['attachments']);

        $attachmentData = $pageData['attachments'][0];
        $this->assertEquals('PageAttachmentExport.txt', $attachmentData['name']);
        $this->assertEquals($attachment->id, $attachmentData['id']);
        $this->assertArrayNotHasKey('link', $attachmentData);
        $this->assertNotEmpty($attachmentData['file']);

        $fileRef = $attachmentData['file'];
        $filePath = $zip->extractPath("/files/$fileRef");
        $this->assertFileExists($filePath);
        $this->assertEquals($contents, file_get_contents($filePath));
    }

    public function test_page_export_link_attachments()
    {
        $page = $this->entities->page();
        $this->asEditor();
        $attachment = Attachment::factory()->create([
            'name' => 'My link attachment for export',
            'path' => 'https://example.com/cats',
            'external' => true,
            'uploaded_to' => $page->id,
            'order' => 1,
        ]);

        $zipResp = $this->get($page->getUrl("/export/zip"));
        $zip = ZipTestHelper::extractFromZipResponse($zipResp);

        $pageData = $zip->data['page'];
        $this->assertCount(1, $pageData['attachments']);

        $attachmentData = $pageData['attachments'][0];
        $this->assertEquals('My link attachment for export', $attachmentData['name']);
        $this->assertEquals($attachment->id, $attachmentData['id']);
        $this->assertEquals('https://example.com/cats', $attachmentData['link']);
        $this->assertArrayNotHasKey('file', $attachmentData);
    }

    public function test_book_export()
    {
        $book = $this->entities->bookHasChaptersAndPages();
        $book->tags()->saveMany(Tag::factory()->count(2)->make());

        $zipResp = $this->asEditor()->get($book->getUrl("/export/zip"));
        $zip = ZipTestHelper::extractFromZipResponse($zipResp);
        $this->assertArrayHasKey('book', $zip->data);

        $bookData = $zip->data['book'];
        $this->assertEquals($book->id, $bookData['id']);
        $this->assertEquals($book->name, $bookData['name']);
        $this->assertEquals($book->descriptionHtml(), $bookData['description_html']);
        $this->assertCount(2, $bookData['tags']);
        $this->assertCount($book->directPages()->count(), $bookData['pages']);
        $this->assertCount($book->chapters()->count(), $bookData['chapters']);
        $this->assertArrayNotHasKey('cover', $bookData);
    }

    public function test_book_export_with_cover_image()
    {
        $book = $this->entities->book();
        $bookRepo = $this->app->make(BookRepo::class);
        $coverImageFile = $this->files->uploadedImage('cover.png');
        $bookRepo->updateCoverImage($book, $coverImageFile);
        $coverImage = $book->cover()->first();

        $zipResp = $this->asEditor()->get($book->getUrl("/export/zip"));
        $zip = ZipTestHelper::extractFromZipResponse($zipResp);

        $this->assertArrayHasKey('cover', $zip->data['book']);
        $coverRef = $zip->data['book']['cover'];
        $coverPath = $zip->extractPath("/files/$coverRef");
        $this->assertFileExists($coverPath);
        $this->assertEquals(file_get_contents(public_path($coverImage->path)), file_get_contents($coverPath));
    }

    public function test_chapter_export()
    {
        $chapter = $this->entities->chapter();
        $chapter->tags()->saveMany(Tag::factory()->count(2)->make());

        $zipResp = $this->asEditor()->get($chapter->getUrl("/export/zip"));
        $zip = ZipTestHelper::extractFromZipResponse($zipResp);
        $this->assertArrayHasKey('chapter', $zip->data);

        $chapterData = $zip->data['chapter'];
        $this->assertEquals($chapter->id, $chapterData['id']);
        $this->assertEquals($chapter->name, $chapterData['name']);
        $this->assertEquals($chapter->descriptionHtml(), $chapterData['description_html']);
        $this->assertCount(2, $chapterData['tags']);
        $this->assertEquals($chapter->priority, $chapterData['priority']);
        $this->assertCount($chapter->pages()->count(), $chapterData['pages']);
    }

    public function test_draft_pages_are_not_included()
    {
        $editor = $this->users->editor();
        $entities = $this->entities->createChainBelongingToUser($editor);
        $book = $entities['book'];
        $page = $entities['page'];
        $chapter = $entities['chapter'];
        $book->tags()->saveMany(Tag::factory()->count(2)->make());

        $page->created_by = $editor->id;
        $page->draft = true;
        $page->save();

        $zipResp = $this->actingAs($editor)->get($book->getUrl("/export/zip"));
        $zip = ZipTestHelper::extractFromZipResponse($zipResp);
        $this->assertCount(0, $zip->data['book']['chapters'][0]['pages'] ?? ['cat']);

        $zipResp = $this->actingAs($editor)->get($chapter->getUrl("/export/zip"));
        $zip = ZipTestHelper::extractFromZipResponse($zipResp);
        $this->assertCount(0, $zip->data['chapter']['pages'] ?? ['cat']);

        $page->chapter_id = 0;
        $page->save();

        $zipResp = $this->actingAs($editor)->get($book->getUrl("/export/zip"));
        $zip = ZipTestHelper::extractFromZipResponse($zipResp);
        $this->assertCount(0, $zip->data['book']['pages'] ?? ['cat']);
    }


    public function test_cross_reference_links_are_converted()
    {
        $book = $this->entities->bookHasChaptersAndPages();
        $chapter = $book->chapters()->first();
        $page = $chapter->pages()->first();

        $book->description_html = '<p><a href="' . $chapter->getUrl() . '">Link to chapter</a></p>';
        $book->save();
        $chapter->description_html = '<p><a href="' . $page->getUrl() . '#section2">Link to page</a></p>';
        $chapter->save();
        $page->html = '<p><a href="' . $book->getUrl() . '?view=true">Link to book</a></p>';
        $page->save();

        $zipResp = $this->asEditor()->get($book->getUrl("/export/zip"));
        $zip = ZipTestHelper::extractFromZipResponse($zipResp);
        $bookData = $zip->data['book'];
        $chapterData = $bookData['chapters'][0];
        $pageData = $chapterData['pages'][0];

        $this->assertStringContainsString('href="[[bsexport:chapter:' . $chapter->id . ']]"', $bookData['description_html']);
        $this->assertStringContainsString('href="[[bsexport:page:' . $page->id . ']]#section2"', $chapterData['description_html']);
        $this->assertStringContainsString('href="[[bsexport:book:' . $book->id . ']]?view=true"', $pageData['html']);
    }

    public function test_book_and_chapter_description_links_to_images_in_pages_are_converted()
    {
        $book = $this->entities->bookHasChaptersAndPages();
        $chapter = $book->chapters()->first();
        $page = $chapter->pages()->first();

        $this->asEditor();
        $this->files->uploadGalleryImageToPage($this, $page);
        /** @var Image $image */
        $image = Image::query()->where('type', '=', 'gallery')
            ->where('uploaded_to', '=', $page->id)->first();

        $book->description_html = '<p><a href="' . $image->url . '">Link to image</a></p>';
        $book->save();
        $chapter->description_html = '<p><a href="' . $image->url . '">Link to image</a></p>';
        $chapter->save();

        $zipResp = $this->get($book->getUrl("/export/zip"));
        $zip = ZipTestHelper::extractFromZipResponse($zipResp);
        $bookData = $zip->data['book'];
        $chapterData = $bookData['chapters'][0];

        $this->assertStringContainsString('href="[[bsexport:image:' . $image->id . ']]"', $bookData['description_html']);
        $this->assertStringContainsString('href="[[bsexport:image:' . $image->id . ']]"', $chapterData['description_html']);
    }

    public function test_image_links_are_handled_when_using_external_storage_url()
    {
        $page = $this->entities->page();

        $this->asEditor();
        $this->files->uploadGalleryImageToPage($this, $page);
        /** @var Image $image */
        $image = Image::query()->where('type', '=', 'gallery')
            ->where('uploaded_to', '=', $page->id)->first();

        config()->set('filesystems.url', 'https://i.example.com/content');

        $storageUrl = 'https://i.example.com/content/' . ltrim($image->path, '/');
        $page->html = '<p><a href="' . $image->url . '">Original URL</a><a href="' . $storageUrl . '">Storage URL</a></p>';
        $page->save();

        $zipResp = $this->get($page->getUrl("/export/zip"));
        $zip = ZipTestHelper::extractFromZipResponse($zipResp);
        $pageData = $zip->data['page'];

        $ref = '[[bsexport:image:' . $image->id . ']]';
        $this->assertStringContainsString("<a href=\"{$ref}\">Original URL</a><a href=\"{$ref}\">Storage URL</a>", $pageData['html']);
    }

    public function test_cross_reference_links_external_to_export_are_not_converted()
    {
        $page = $this->entities->page();
        $page->html = '<p><a href="' . $page->book->getUrl() . '">Link to book</a></p>';
        $page->save();

        $zipResp = $this->asEditor()->get($page->getUrl("/export/zip"));
        $zip = ZipTestHelper::extractFromZipResponse($zipResp);
        $pageData = $zip->data['page'];

        $this->assertStringContainsString('href="' . $page->book->getUrl() . '"', $pageData['html']);
    }

    public function test_attachments_links_are_converted()
    {
        $page = $this->entities->page();
        $attachment = Attachment::factory()->create([
            'name' => 'My link attachment for export reference',
            'path' => 'https://example.com/cats/ref',
            'external' => true,
            'uploaded_to' => $page->id,
            'order' => 1,
        ]);

        $page->html = '<p><a href="' . url("/attachments/{$attachment->id}") . '?open=true">Link to attachment</a></p>';
        $page->save();

        $zipResp = $this->asEditor()->get($page->getUrl("/export/zip"));
        $zip = ZipTestHelper::extractFromZipResponse($zipResp);
        $pageData = $zip->data['page'];

        $this->assertStringContainsString('href="[[bsexport:attachment:' . $attachment->id . ']]?open=true"', $pageData['html']);
    }

    public function test_links_in_markdown_are_parsed()
    {
        $chapter = $this->entities->chapterHasPages();
        $page = $chapter->pages()->first();

        $page->markdown = "[Link to chapter]({$chapter->getUrl()})";
        $page->save();

        $zipResp = $this->asEditor()->get($chapter->getUrl("/export/zip"));
        $zip = ZipTestHelper::extractFromZipResponse($zipResp);
        $pageData = $zip->data['chapter']['pages'][0];

        $this->assertStringContainsString("[Link to chapter]([[bsexport:chapter:{$chapter->id}]])", $pageData['markdown']);
    }

    public function test_exports_rate_limited_low_for_guest_viewers()
    {
        $this->setSettings(['app-public' => 'true']);

        $page = $this->entities->page();
        for ($i = 0; $i < 4; $i++) {
            $this->get($page->getUrl("/export/zip"))->assertOk();
        }
        $this->get($page->getUrl("/export/zip"))->assertStatus(429);
    }

    public function test_exports_rate_limited_higher_for_logged_in_viewers()
    {
        $this->asAdmin();

        $page = $this->entities->page();
        for ($i = 0; $i < 10; $i++) {
            $this->get($page->getUrl("/export/zip"))->assertOk();
        }
        $this->get($page->getUrl("/export/zip"))->assertStatus(429);
    }
}
