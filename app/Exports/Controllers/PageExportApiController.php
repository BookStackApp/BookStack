<?php

namespace BookStack\Exports\Controllers;

use BookStack\Entities\Queries\PageQueries;
use BookStack\Exports\ExportFormatter;
use BookStack\Exports\ZipExports\ZipExportBuilder;
use BookStack\Http\ApiController;
use Throwable;

class PageExportApiController extends ApiController
{
    public function __construct(
        protected ExportFormatter $exportFormatter,
        protected PageQueries $queries,
    ) {
        $this->middleware('can:content-export');
    }

    /**
     * Export a page as a PDF file.
     *
     * @throws Throwable
     */
    public function exportPdf(int $id)
    {
        $page = $this->queries->findVisibleByIdOrFail($id);
        $pdfContent = $this->exportFormatter->pageToPdf($page);

        return $this->download()->directly($pdfContent, $page->slug . '.pdf');
    }

    /**
     * Export a page as a contained HTML file.
     *
     * @throws Throwable
     */
    public function exportHtml(int $id)
    {
        $page = $this->queries->findVisibleByIdOrFail($id);
        $htmlContent = $this->exportFormatter->pageToContainedHtml($page);

        return $this->download()->directly($htmlContent, $page->slug . '.html');
    }

    /**
     * Export a page as a plain text file.
     */
    public function exportPlainText(int $id)
    {
        $page = $this->queries->findVisibleByIdOrFail($id);
        $textContent = $this->exportFormatter->pageToPlainText($page);

        return $this->download()->directly($textContent, $page->slug . '.txt');
    }

    /**
     * Export a page as a markdown file.
     */
    public function exportMarkdown(int $id)
    {
        $page = $this->queries->findVisibleByIdOrFail($id);
        $markdown = $this->exportFormatter->pageToMarkdown($page);

        return $this->download()->directly($markdown, $page->slug . '.md');
    }

    /**
     * Export a page as a contained ZIP file.
     */
    public function exportZip(int $id, ZipExportBuilder $builder)
    {
        $page = $this->queries->findVisibleByIdOrFail($id);
        $zip = $builder->buildForPage($page);

        return $this->download()->streamedFileDirectly($zip, $page->slug . '.zip', true);
    }
}
