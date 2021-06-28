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
}
