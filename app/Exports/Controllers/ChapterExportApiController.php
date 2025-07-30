<?php

namespace BookStack\Exports\Controllers;

use BookStack\Entities\Queries\ChapterQueries;
use BookStack\Exports\ExportFormatter;
use BookStack\Exports\ZipExports\ZipExportBuilder;
use BookStack\Http\ApiController;
use Throwable;

class ChapterExportApiController extends ApiController
{
    public function __construct(
        protected ExportFormatter $exportFormatter,
        protected ChapterQueries $queries,
    ) {
        $this->middleware('can:content-export');
    }

    /**
     * Export a chapter as a PDF file.
     *
     * @throws Throwable
     */
    public function exportPdf(int $id)
    {
        $chapter = $this->queries->findVisibleByIdOrFail($id);
        $pdfContent = $this->exportFormatter->chapterToPdf($chapter);

        return $this->download()->directly($pdfContent, $chapter->slug . '.pdf');
    }

    /**
     * Export a chapter as a contained HTML file.
     *
     * @throws Throwable
     */
    public function exportHtml(int $id)
    {
        $chapter = $this->queries->findVisibleByIdOrFail($id);
        $htmlContent = $this->exportFormatter->chapterToContainedHtml($chapter);

        return $this->download()->directly($htmlContent, $chapter->slug . '.html');
    }

    /**
     * Export a chapter as a plain text file.
     */
    public function exportPlainText(int $id)
    {
        $chapter = $this->queries->findVisibleByIdOrFail($id);
        $textContent = $this->exportFormatter->chapterToPlainText($chapter);

        return $this->download()->directly($textContent, $chapter->slug . '.txt');
    }

    /**
     * Export a chapter as a markdown file.
     */
    public function exportMarkdown(int $id)
    {
        $chapter = $this->queries->findVisibleByIdOrFail($id);
        $markdown = $this->exportFormatter->chapterToMarkdown($chapter);

        return $this->download()->directly($markdown, $chapter->slug . '.md');
    }

    /**
     * Export a chapter as a contained ZIP file.
     */
    public function exportZip(int $id, ZipExportBuilder $builder)
    {
        $chapter = $this->queries->findVisibleByIdOrFail($id);
        $zip = $builder->buildForChapter($chapter);

        return $this->download()->streamedFileDirectly($zip, $chapter->slug . '.zip', true);
    }
}
