<?php

namespace BookStack\Http\Controllers;

use BookStack\Entities\ExportService;
use BookStack\Entities\Repos\EntityRepo;
use BookStack\Exceptions\NotFoundException;
use Illuminate\Http\Response;
use Throwable;

class ChapterExportController extends Controller
{
    /**
     * @var EntityRepo
     */
    protected $entityRepo;

    /**
     * @var ExportService
     */
    protected $exportService;

    /**
     * ChapterExportController constructor.
     * @param EntityRepo $entityRepo
     * @param ExportService $exportService
     */
    public function __construct(EntityRepo $entityRepo, ExportService $exportService)
    {
        $this->entityRepo = $entityRepo;
        $this->exportService = $exportService;
        parent::__construct();
    }

    /**
     * Exports a chapter to pdf .
     * @param string $bookSlug
     * @param string $chapterSlug
     * @return Response
     * @throws NotFoundException
     * @throws Throwable
     */
    public function pdf(string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->entityRepo->getBySlug('chapter', $chapterSlug, $bookSlug);
        $pdfContent = $this->exportService->chapterToPdf($chapter);
        return $this->downloadResponse($pdfContent, $chapterSlug . '.pdf');
    }

    /**
     * Export a chapter to a self-contained HTML file.
     * @param string $bookSlug
     * @param string $chapterSlug
     * @return Response
     * @throws NotFoundException
     * @throws Throwable
     */
    public function html(string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->entityRepo->getBySlug('chapter', $chapterSlug, $bookSlug);
        $containedHtml = $this->exportService->chapterToContainedHtml($chapter);
        return $this->downloadResponse($containedHtml, $chapterSlug . '.html');
    }

    /**
     * Export a chapter to a simple plaintext .txt file.
     * @param string $bookSlug
     * @param string $chapterSlug
     * @return Response
     * @throws NotFoundException
     */
    public function plainText(string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->entityRepo->getBySlug('chapter', $chapterSlug, $bookSlug);
        $chapterText = $this->exportService->chapterToPlainText($chapter);
        return $this->downloadResponse($chapterText, $chapterSlug . '.txt');
    }
}
