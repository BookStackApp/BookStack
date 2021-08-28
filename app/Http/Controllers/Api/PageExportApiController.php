<?php

namespace BookStack\Http\Controllers\Api;

use BookStack\Entities\Models\Page;
use BookStack\Entities\Tools\ExportFormatter;
use Throwable;

class PageExportApiController extends ApiController
{
    protected $exportFormatter;

    public function __construct(ExportFormatter $exportFormatter)
    {
        $this->exportFormatter = $exportFormatter;
        $this->middleware('can:content-export');
    }

    /**
     * Export a page as a PDF file.
     *
     * @throws Throwable
     */
    public function exportPdf(int $id)
    {
        $page = Page::visible()->findOrFail($id);
        $pdfContent = $this->exportFormatter->pageToPdf($page);

        return $this->downloadResponse($pdfContent, $page->slug . '.pdf');
    }

    /**
     * Export a page as a contained HTML file.
     *
     * @throws Throwable
     */
    public function exportHtml(int $id)
    {
        $page = Page::visible()->findOrFail($id);
        $htmlContent = $this->exportFormatter->pageToContainedHtml($page);

        return $this->downloadResponse($htmlContent, $page->slug . '.html');
    }

    /**
     * Export a page as a plain text file.
     */
    public function exportPlainText(int $id)
    {
        $page = Page::visible()->findOrFail($id);
        $textContent = $this->exportFormatter->pageToPlainText($page);

        return $this->downloadResponse($textContent, $page->slug . '.txt');
    }

    /**
     * Export a page as a markdown file.
     */
    public function exportMarkdown(int $id)
    {
        $page = Page::visible()->findOrFail($id);
        $markdown = $this->exportFormatter->pageToMarkdown($page);

        return $this->downloadResponse($markdown, $page->slug . '.md');
    }
}
