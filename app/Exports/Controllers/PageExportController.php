<?php

namespace BookStack\Exports\Controllers;

use BookStack\Entities\Queries\PageQueries;
use BookStack\Entities\Tools\PageContent;
use BookStack\Exceptions\NotFoundException;
use BookStack\Exports\ExportFormatter;
use BookStack\Exports\ZipExports\ZipExportBuilder;
use BookStack\Http\Controller;
use Throwable;

class PageExportController extends Controller
{
    public function __construct(
        protected PageQueries $queries,
        protected ExportFormatter $exportFormatter,
    ) {
        $this->middleware('can:content-export');
        $this->middleware('throttle:exports');
    }

    /**
     * Exports a page to a PDF.
     * https://github.com/barryvdh/laravel-dompdf.
     *
     * @throws NotFoundException
     * @throws Throwable
     */
    public function pdf(string $bookSlug, string $pageSlug)
    {
        $page = $this->queries->findVisibleBySlugsOrFail($bookSlug, $pageSlug);
        $page->html = (new PageContent($page))->render();
        $pdfContent = $this->exportFormatter->pageToPdf($page);

        return $this->download()->directly($pdfContent, $pageSlug . '.pdf');
    }

    /**
     * Export a page to a self-contained HTML file.
     *
     * @throws NotFoundException
     * @throws Throwable
     */
    public function html(string $bookSlug, string $pageSlug)
    {
        $page = $this->queries->findVisibleBySlugsOrFail($bookSlug, $pageSlug);
        $page->html = (new PageContent($page))->render();
        $containedHtml = $this->exportFormatter->pageToContainedHtml($page);

        return $this->download()->directly($containedHtml, $pageSlug . '.html');
    }

    /**
     * Export a page to a simple plaintext .txt file.
     *
     * @throws NotFoundException
     */
    public function plainText(string $bookSlug, string $pageSlug)
    {
        $page = $this->queries->findVisibleBySlugsOrFail($bookSlug, $pageSlug);
        $pageText = $this->exportFormatter->pageToPlainText($page);

        return $this->download()->directly($pageText, $pageSlug . '.txt');
    }

    /**
     * Export a page to a simple markdown .md file.
     *
     * @throws NotFoundException
     */
    public function markdown(string $bookSlug, string $pageSlug)
    {
        $page = $this->queries->findVisibleBySlugsOrFail($bookSlug, $pageSlug);
        $pageText = $this->exportFormatter->pageToMarkdown($page);

        return $this->download()->directly($pageText, $pageSlug . '.md');
    }

    /**
     * Export a page to a contained ZIP export file.
     * @throws NotFoundException
     */
    public function zip(string $bookSlug, string $pageSlug, ZipExportBuilder $builder)
    {
        $page = $this->queries->findVisibleBySlugsOrFail($bookSlug, $pageSlug);
        $zip = $builder->buildForPage($page);

        return $this->download()->streamedFileDirectly($zip, $pageSlug . '.zip', true);
    }
}
