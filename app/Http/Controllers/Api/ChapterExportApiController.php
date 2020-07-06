<?php namespace BookStack\Http\Controllers\Api;

use BookStack\Entities\Chapter;
use BookStack\Entities\ExportService;
use BookStack\Entities\Repos\BookRepo;
use Throwable;

class ChapterExportApiController extends ApiController
{
    protected $chapterRepo;
    protected $exportService;

    /**
     * ChapterExportController constructor.
     */
    public function __construct(BookRepo $chapterRepo, ExportService $exportService)
    {
        $this->chapterRepo = $chapterRepo;
        $this->exportService = $exportService;
        parent::__construct();
    }

    /**
     * Export a chapter as a PDF file.
     * @throws Throwable
     */
    public function exportPdf(int $id)
    {
        $chapter = Chapter::visible()->findOrFail($id);
        $pdfContent = $this->exportService->chapterToPdf($chapter);
        return $this->downloadResponse($pdfContent, $chapter->slug . '.pdf');
    }

    /**
     * Export a chapter as a contained HTML file.
     * @throws Throwable
     */
    public function exportHtml(int $id)
    {
        $chapter = Chapter::visible()->findOrFail($id);
        $htmlContent = $this->exportService->chapterToContainedHtml($chapter);
        return $this->downloadResponse($htmlContent, $chapter->slug . '.html');
    }

    /**
     * Export a chapter as a plain text file.
     */
    public function exportPlainText(int $id)
    {
        $chapter = Chapter::visible()->findOrFail($id);
        $textContent = $this->exportService->chapterToPlainText($chapter);
        return $this->downloadResponse($textContent, $chapter->slug . '.txt');
    }
}
