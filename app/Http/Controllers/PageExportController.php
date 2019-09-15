<?php

namespace BookStack\Http\Controllers;

use BookStack\Entities\ExportService;
use BookStack\Entities\Repos\PageRepo;
use BookStack\Exceptions\NotFoundException;
use Illuminate\Http\Response;
use Throwable;

class PageExportController extends Controller
{
    /**
     * @var PageRepo
     */
    protected $pageRepo;

    /**
     * @var ExportService
     */
    protected $exportService;

    /**
     * PageExportController constructor.
     * @param PageRepo $pageRepo
     * @param ExportService $exportService
     */
    public function __construct(PageRepo $pageRepo, ExportService $exportService)
    {
        $this->pageRepo = $pageRepo;
        $this->exportService = $exportService;
        parent::__construct();
    }

    /**
     * Exports a page to a PDF.
     * https://github.com/barryvdh/laravel-dompdf
     * @param string $bookSlug
     * @param string $pageSlug
     * @return Response
     * @throws NotFoundException
     * @throws Throwable
     */
    public function pdf(string $bookSlug, string $pageSlug)
    {
        $page = $this->pageRepo->getPageBySlug($pageSlug, $bookSlug);
        $page->html = $this->pageRepo->renderPage($page);
        $pdfContent = $this->exportService->pageToPdf($page);
        return $this->downloadResponse($pdfContent, $pageSlug . '.pdf');
    }

    /**
     * Export a page to a self-contained HTML file.
     * @param string $bookSlug
     * @param string $pageSlug
     * @return Response
     * @throws NotFoundException
     * @throws Throwable
     */
    public function html(string $bookSlug, string $pageSlug)
    {
        $page = $this->pageRepo->getPageBySlug($pageSlug, $bookSlug);
        $page->html = $this->pageRepo->renderPage($page);
        $containedHtml = $this->exportService->pageToContainedHtml($page);
        return $this->downloadResponse($containedHtml, $pageSlug . '.html');
    }

    /**
     * Export a page to a simple plaintext .txt file.
     * @param string $bookSlug
     * @param string $pageSlug
     * @return Response
     * @throws NotFoundException
     */
    public function plainText(string $bookSlug, string $pageSlug)
    {
        $page = $this->pageRepo->getPageBySlug($pageSlug, $bookSlug);
        $pageText = $this->exportService->pageToPlainText($page);
        return $this->downloadResponse($pageText, $pageSlug . '.txt');
    }
}
