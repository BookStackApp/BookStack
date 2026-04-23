<?php

namespace Tests\Exports;

use BookStack\Entities\Models\Page;
use BookStack\Exceptions\PdfExportException;
use BookStack\Exports\PdfGenerator;
use FilesystemIterator;
use Tests\TestCase;

class PdfExportTest extends TestCase
{
    public function test_page_pdf_export()
    {
        $page = $this->entities->page();
        $this->asEditor();

        $resp = $this->get($page->getUrl('/export/pdf'));
        $resp->assertStatus(200);
        $resp->assertHeader('Content-Disposition', 'attachment; filename*=UTF-8\'\'' . $page->slug . '.pdf');
    }

    public function test_book_pdf_export()
    {
        $page = $this->entities->page();
        $book = $page->book;
        $this->asEditor();

        $resp = $this->get($book->getUrl('/export/pdf'));
        $resp->assertStatus(200);
        $resp->assertHeader('Content-Disposition', 'attachment; filename*=UTF-8\'\'' . $book->slug . '.pdf');
    }

    public function test_chapter_pdf_export()
    {
        $chapter = $this->entities->chapter();
        $this->asEditor();

        $resp = $this->get($chapter->getUrl('/export/pdf'));
        $resp->assertStatus(200);
        $resp->assertHeader('Content-Disposition', 'attachment; filename*=UTF-8\'\'' . $chapter->slug . '.pdf');
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
        $mockPdfGenerator->shouldReceive('getActiveEngine')->andReturn(PdfGenerator::ENGINE_DOMPDF);

        $this->asEditor()->get($page->getUrl('/export/pdf'));
        $this->assertStringNotContainsString('iframe>', $pdfHtml);
        $this->assertStringContainsString('<p><a href="https://www.youtube.com/embed/ShqUjt33uOs">https://www.youtube.com/embed/ShqUjt33uOs</a></p>', $pdfHtml);
    }

    public function test_page_pdf_export_opens_details_blocks()
    {
        $page = $this->entities->page()->forceFill([
            'html'     => '<details><summary>Hello</summary><p>Content!</p></details>',
        ]);
        $page->save();

        $pdfHtml = '';
        $mockPdfGenerator = $this->mock(PdfGenerator::class);
        $mockPdfGenerator->shouldReceive('fromHtml')
            ->with(\Mockery::capture($pdfHtml))
            ->andReturn('');
        $mockPdfGenerator->shouldReceive('getActiveEngine')->andReturn(PdfGenerator::ENGINE_DOMPDF);

        $this->asEditor()->get($page->getUrl('/export/pdf'));
        $this->assertStringContainsString('<details open="open"', $pdfHtml);
    }

    public function test_custom_fonts_loaded_for_dom_pdf_when_used()
    {
        // Set up custom font usage
        $page = $this->entities->page()->forceFill([
            'html'     => '<p><strong>Bold</strong>text</p>',
        ]);
        $page->save();
        $this->setSettings([
            'app-custom-head' => '<style>* { font-family: "meow words"}</style>'
        ]);
        $normalFont = $this->files->testFilePath('fonts/Cardiff.ttf');
        $normalFontTarget = storage_path('fonts/dompdf/MeowWords.ttf');
        $boldFont = $this->files->testFilePath('fonts/Cardiff-Bold.ttf');
        $boldFontTarget = storage_path('fonts/dompdf/MeowWords-Bold.ttf');
        copy($normalFont, $normalFontTarget);
        copy($boldFont, $boldFontTarget);

        $resp = $this->asEditor()->get($page->getUrl('/export/pdf'));
        $resp->assertStatus(200);

        // Existance of UFM files indicates the metrics have been generated
        $this->assertFileExists(storage_path('fonts/dompdf/MeowWords.ufm'));
        $this->assertFileExists(storage_path('fonts/dompdf/MeowWords-Bold.ufm'));
        // Existence of cache json files indicates the fonts have been used
        $this->assertFileExists(storage_path('fonts/dompdf/cache/MeowWords.ufm.json'));
        $this->assertFileExists(storage_path('fonts/dompdf/cache/MeowWords-Bold.ufm.json'));

        $filesToCleanUp = [...glob(storage_path('fonts/dompdf/Meow*')), ...glob(storage_path('fonts/dompdf/cache/Meow*'))];
        foreach ($filesToCleanUp as $file) {
            unlink($file);
        }
    }

    public function test_wkhtmltopdf_only_used_when_allow_untrusted_is_true()
    {
        $page = $this->entities->page();

        config()->set('exports.snappy.pdf_binary', '/abc123');
        config()->set('app.allow_untrusted_server_fetching', false);

        $resp = $this->asEditor()->get($page->getUrl('/export/pdf'));
        $resp->assertStatus(200); // Sucessful response with invalid snappy binary indicates dompdf usage.

        config()->set('app.allow_untrusted_server_fetching', true);
        $resp = $this->get($page->getUrl('/export/pdf'));
        $resp->assertStatus(500); // Bad response indicates wkhtml usage
    }

    public function test_pdf_command_option_used_if_set()
    {
        $page = $this->entities->page();
        $command = 'cp {input_html_path} {output_pdf_path}';
        config()->set('exports.pdf_command', $command);

        $resp = $this->asEditor()->get($page->getUrl('/export/pdf'));
        $download = $resp->getContent();

        $this->assertStringContainsString(e($page->name), $download);
        $this->assertStringContainsString('<html lang=', $download);
    }

    public function test_pdf_command_option_errors_if_output_path_not_written_to()
    {
        $page = $this->entities->page();
        $command = 'echo "hi"';
        config()->set('exports.pdf_command', $command);

        $this->assertThrows(function () use ($page) {
            $this->withoutExceptionHandling()->asEditor()->get($page->getUrl('/export/pdf'));
        }, PdfExportException::class);
    }

    public function test_pdf_command_option_errors_if_command_returns_error_status()
    {
        $page = $this->entities->page();
        $command = 'exit 1';
        config()->set('exports.pdf_command', $command);

        $this->assertThrows(function () use ($page) {
            $this->withoutExceptionHandling()->asEditor()->get($page->getUrl('/export/pdf'));
        }, PdfExportException::class);
    }

    public function test_pdf_command_timeout_option_limits_export_time()
    {
        $page = $this->entities->page();
        $command = 'php -r \'sleep(4);\'';
        config()->set('exports.pdf_command', $command);
        config()->set('exports.pdf_command_timeout', 1);

        $this->assertThrows(function () use ($page) {
            $start = time();
            $this->withoutExceptionHandling()->asEditor()->get($page->getUrl('/export/pdf'));

            $this->assertTrue(time() < ($start + 3));
        }, PdfExportException::class,
            "PDF Export via command failed due to timeout at 1 second(s)");
    }

    public function test_pdf_command_option_does_not_leave_temp_files()
    {
        $tempDir = sys_get_temp_dir();
        $startTempFileCount = iterator_count((new FileSystemIterator($tempDir, FilesystemIterator::SKIP_DOTS)));

        $page = $this->entities->page();
        $command = 'cp {input_html_path} {output_pdf_path}';
        config()->set('exports.pdf_command', $command);

        $this->asEditor()->get($page->getUrl('/export/pdf'));

        $afterTempFileCount = iterator_count((new FileSystemIterator($tempDir, FilesystemIterator::SKIP_DOTS)));
        $this->assertEquals($startTempFileCount, $afterTempFileCount);
    }
}
