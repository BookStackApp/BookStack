<?php

namespace Tests\Api;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use Tests\Exports\ZipTestHelper;
use Tests\TestCase;

class ExportsApiTest extends TestCase
{
    use TestsApi;

    public function test_book_html_endpoint()
    {
        $this->actingAsApiEditor();
        $book = $this->entities->book();

        $resp = $this->get("/api/books/{$book->id}/export/html");
        $resp->assertStatus(200);
        $resp->assertSee($book->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $book->slug . '.html"');
    }

    public function test_book_plain_text_endpoint()
    {
        $this->actingAsApiEditor();
        $book = $this->entities->book();

        $resp = $this->get("/api/books/{$book->id}/export/plaintext");
        $resp->assertStatus(200);
        $resp->assertSee($book->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $book->slug . '.txt"');
    }

    public function test_book_pdf_endpoint()
    {
        $this->actingAsApiEditor();
        $book = $this->entities->book();

        $resp = $this->get("/api/books/{$book->id}/export/pdf");
        $resp->assertStatus(200);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $book->slug . '.pdf"');
    }

    public function test_book_markdown_endpoint()
    {
        $this->actingAsApiEditor();
        $book = Book::visible()->has('pages')->has('chapters')->first();

        $resp = $this->get("/api/books/{$book->id}/export/markdown");
        $resp->assertStatus(200);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $book->slug . '.md"');
        $resp->assertSee('# ' . $book->name);
        $resp->assertSee('# ' . $book->pages()->first()->name);
        $resp->assertSee('# ' . $book->chapters()->first()->name);
    }

    public function test_book_zip_endpoint()
    {
        $this->actingAsApiEditor();
        $book = Book::visible()->has('pages')->has('chapters')->first();

        $resp = $this->get("/api/books/{$book->id}/export/zip");
        $resp->assertStatus(200);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $book->slug . '.zip"');

        $zip = ZipTestHelper::extractFromZipResponse($resp);
        $this->assertArrayHasKey('book', $zip->data);
    }

    public function test_chapter_html_endpoint()
    {
        $this->actingAsApiEditor();
        $chapter = $this->entities->chapter();

        $resp = $this->get("/api/chapters/{$chapter->id}/export/html");
        $resp->assertStatus(200);
        $resp->assertSee($chapter->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $chapter->slug . '.html"');
    }

    public function test_chapter_plain_text_endpoint()
    {
        $this->actingAsApiEditor();
        $chapter = $this->entities->chapter();

        $resp = $this->get("/api/chapters/{$chapter->id}/export/plaintext");
        $resp->assertStatus(200);
        $resp->assertSee($chapter->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $chapter->slug . '.txt"');
    }

    public function test_chapter_pdf_endpoint()
    {
        $this->actingAsApiEditor();
        $chapter = $this->entities->chapter();

        $resp = $this->get("/api/chapters/{$chapter->id}/export/pdf");
        $resp->assertStatus(200);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $chapter->slug . '.pdf"');
    }

    public function test_chapter_markdown_endpoint()
    {
        $this->actingAsApiEditor();
        $chapter = Chapter::visible()->has('pages')->first();

        $resp = $this->get("/api/chapters/{$chapter->id}/export/markdown");
        $resp->assertStatus(200);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $chapter->slug . '.md"');
        $resp->assertSee('# ' . $chapter->name);
        $resp->assertSee('# ' . $chapter->pages()->first()->name);
    }

    public function test_chapter_zip_endpoint()
    {
        $this->actingAsApiEditor();
        $chapter = Chapter::visible()->has('pages')->first();

        $resp = $this->get("/api/chapters/{$chapter->id}/export/zip");
        $resp->assertStatus(200);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $chapter->slug . '.zip"');

        $zip = ZipTestHelper::extractFromZipResponse($resp);
        $this->assertArrayHasKey('chapter', $zip->data);
    }

    public function test_page_html_endpoint()
    {
        $this->actingAsApiEditor();
        $page = $this->entities->page();

        $resp = $this->get("/api/pages/{$page->id}/export/html");
        $resp->assertStatus(200);
        $resp->assertSee($page->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $page->slug . '.html"');
    }

    public function test_page_plain_text_endpoint()
    {
        $this->actingAsApiEditor();
        $page = $this->entities->page();

        $resp = $this->get("/api/pages/{$page->id}/export/plaintext");
        $resp->assertStatus(200);
        $resp->assertSee($page->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $page->slug . '.txt"');
    }

    public function test_page_pdf_endpoint()
    {
        $this->actingAsApiEditor();
        $page = $this->entities->page();

        $resp = $this->get("/api/pages/{$page->id}/export/pdf");
        $resp->assertStatus(200);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $page->slug . '.pdf"');
    }

    public function test_page_markdown_endpoint()
    {
        $this->actingAsApiEditor();
        $page = $this->entities->page();

        $resp = $this->get("/api/pages/{$page->id}/export/markdown");
        $resp->assertStatus(200);
        $resp->assertSee('# ' . $page->name);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $page->slug . '.md"');
    }

    public function test_page_zip_endpoint()
    {
        $this->actingAsApiEditor();
        $page = $this->entities->page();

        $resp = $this->get("/api/pages/{$page->id}/export/zip");
        $resp->assertStatus(200);
        $resp->assertHeader('Content-Disposition', 'attachment; filename="' . $page->slug . '.zip"');

        $zip = ZipTestHelper::extractFromZipResponse($resp);
        $this->assertArrayHasKey('page', $zip->data);
    }

    public function test_cant_export_when_not_have_permission()
    {
        $types = ['html', 'plaintext', 'pdf', 'markdown', 'zip'];
        $this->actingAsApiEditor();
        $this->permissions->removeUserRolePermissions($this->users->editor(), ['content-export']);

        $book = $this->entities->book();
        foreach ($types as $type) {
            $resp = $this->get("/api/books/{$book->id}/export/{$type}");
            $this->assertPermissionError($resp);
        }

        $chapter = Chapter::visible()->has('pages')->first();
        foreach ($types as $type) {
            $resp = $this->get("/api/chapters/{$chapter->id}/export/{$type}");
            $this->assertPermissionError($resp);
        }

        $page = $this->entities->page();
        foreach ($types as $type) {
            $resp = $this->get("/api/pages/{$page->id}/export/{$type}");
            $this->assertPermissionError($resp);
        }
    }
}
