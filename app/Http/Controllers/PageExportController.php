<?php

namespace BookStack\Http\Controllers;

use BookStack\Entities\ExportService;
use BookStack\Entities\Managers\PageContent;
use BookStack\Entities\Repos\PageRepo;
use BookStack\Exceptions\NotFoundException;
use Throwable;

class PageExportController extends Controller
{

    protected $pageRepo;
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
     * @throws NotFoundException
     * @throws Throwable
     */
    public function pdf(string $bookSlug, string $pageSlug)
    {
        $page = $this->pageRepo->getBySlug($bookSlug, $pageSlug);
        $page->html = (new PageContent($page))->render();
        $pdfContent = $this->exportService->pageToPdf($page);
        return $this->downloadResponse($pdfContent, $pageSlug . '.pdf');
    }

    /**
     * Export a page to a self-contained HTML file.
     * @throws NotFoundException
     * @throws Throwable
     */
    public function html(string $bookSlug, string $pageSlug)
    {
        $page = $this->pageRepo->getBySlug($bookSlug, $pageSlug);
        $page->html = (new PageContent($page))->render();
        $containedHtml = $this->exportService->pageToContainedHtml($page);
        return $this->downloadResponse($containedHtml, $pageSlug . '.html');
    }

    /**
     * Export a page to a simple plaintext .txt file.
     * @throws NotFoundException
     */
    public function plainText(string $bookSlug, string $pageSlug)
    {
        $page = $this->pageRepo->getBySlug($bookSlug, $pageSlug);
        $pageText = $this->exportService->pageToPlainText($page);
        return $this->downloadResponse($pageText, $pageSlug . '.txt');
    }

    /**
     * Export a page to a simple markdown .md file.
     * @throws NotFoundException
     */
    public function markdown(string $bookSlug, string $pageSlug)
    {
        $page = $this->pageRepo->getBySlug($bookSlug, $pageSlug);
        $pageText = $this->exportService->pageToMarkdown($page);
        return $this->downloadResponse($pageText, $pageSlug . '.md');
    }
}
