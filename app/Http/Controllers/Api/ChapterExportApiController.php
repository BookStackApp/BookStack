<?php namespace BookStack\Http\Controllers\Api;

use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Tools\ExportFormatter;
use BookStack\Entities\Repos\BookRepo;
use Throwable;

class ChapterExportApiController extends ApiController
{
    protected $chapterRepo;
    protected $exportFormatter;

    /**
     * ChapterExportController constructor.
     */
    public function __construct(BookRepo $chapterRepo, ExportFormatter $exportFormatter)
    {
        $this->chapterRepo = $chapterRepo;
        $this->exportFormatter = $exportFormatter;
    }

    /**
     * Export a chapter as a PDF file.
     * @throws Throwable
     */
    public function exportPdf(int $id)
    {
        $chapter = Chapter::visible()->findOrFail($id);
        $pdfContent = $this->exportFormatter->chapterToPdf($chapter);
        return $this->downloadResponse($pdfContent, $chapter->slug . '.pdf');
    }

    /**
     * Export a chapter as a contained HTML file.
     * @throws Throwable
     */
    public function exportHtml(int $id)
    {
        $chapter = Chapter::visible()->findOrFail($id);
        $htmlContent = $this->exportFormatter->chapterToContainedHtml($chapter);
        return $this->downloadResponse($htmlContent, $chapter->slug . '.html');
    }

    /**
     * Export a chapter as a plain text file.
     */
    public function exportPlainText(int $id)
    {
        $chapter = Chapter::visible()->findOrFail($id);
        $textContent = $this->exportFormatter->chapterToPlainText($chapter);
        return $this->downloadResponse($textContent, $chapter->slug . '.txt');
    }
}
