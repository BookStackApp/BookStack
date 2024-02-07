<?php

namespace BookStack\Entities\Controllers;

use BookStack\Entities\Queries\BookQueries;
use BookStack\Entities\Tools\ExportFormatter;
use BookStack\Http\ApiController;
use Throwable;

class BookExportApiController extends ApiController
{
    public function __construct(
        protected ExportFormatter $exportFormatter,
        protected BookQueries $queries,
    ) {
        $this->middleware('can:content-export');
    }

    /**
     * Export a book as a PDF file.
     *
     * @throws Throwable
     */
    public function exportPdf(int $id)
    {
        $book = $this->queries->findVisibleByIdOrFail($id);
        $pdfContent = $this->exportFormatter->bookToPdf($book);

        return $this->download()->directly($pdfContent, $book->slug . '.pdf');
    }

    /**
     * Export a book as a contained HTML file.
     *
     * @throws Throwable
     */
    public function exportHtml(int $id)
    {
        $book = $this->queries->findVisibleByIdOrFail($id);
        $htmlContent = $this->exportFormatter->bookToContainedHtml($book);

        return $this->download()->directly($htmlContent, $book->slug . '.html');
    }

    /**
     * Export a book as a plain text file.
     */
    public function exportPlainText(int $id)
    {
        $book = $this->queries->findVisibleByIdOrFail($id);
        $textContent = $this->exportFormatter->bookToPlainText($book);

        return $this->download()->directly($textContent, $book->slug . '.txt');
    }

    /**
     * Export a book as a markdown file.
     */
    public function exportMarkdown(int $id)
    {
        $book = $this->queries->findVisibleByIdOrFail($id);
        $markdown = $this->exportFormatter->bookToMarkdown($book);

        return $this->download()->directly($markdown, $book->slug . '.md');
    }
}
