<?php

namespace BookStack\Entities\Controllers;

use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Tools\ExportFormatter;
use BookStack\Http\Controllers\ApiController;
use Throwable;

class ChapterExportApiController extends ApiController
{
    protected $exportFormatter;

    /**
     * ChapterExportController constructor.
     */
    public function __construct(ExportFormatter $exportFormatter)
    {
        $this->exportFormatter = $exportFormatter;
        $this->middleware('can:content-export');
    }

    /**
     * Export a chapter as a PDF file.
     *
     * @throws Throwable
     */
    public function exportPdf(int $id)
    {
        $chapter = Chapter::visible()->findOrFail($id);
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
        $chapter = Chapter::visible()->findOrFail($id);
        $htmlContent = $this->exportFormatter->chapterToContainedHtml($chapter);

        return $this->download()->directly($htmlContent, $chapter->slug . '.html');
    }

    /**
     * Export a chapter as a plain text file.
     */
    public function exportPlainText(int $id)
    {
        $chapter = Chapter::visible()->findOrFail($id);
        $textContent = $this->exportFormatter->chapterToPlainText($chapter);

        return $this->download()->directly($textContent, $chapter->slug . '.txt');
    }

    /**
     * Export a chapter as a markdown file.
     */
    public function exportMarkdown(int $id)
    {
        $chapter = Chapter::visible()->findOrFail($id);
        $markdown = $this->exportFormatter->chapterToMarkdown($chapter);

        return $this->download()->directly($markdown, $chapter->slug . '.md');
    }
}
