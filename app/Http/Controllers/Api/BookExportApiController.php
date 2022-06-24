<?php

namespace BookStack\Http\Controllers\Api;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Tools\ExportFormatter;
use Throwable;

class BookExportApiController extends ApiController
{
    protected $exportFormatter;

    public function __construct(ExportFormatter $exportFormatter)
    {
        $this->exportFormatter = $exportFormatter;
        $this->middleware('can:content-export');
    }

    /**
     * Export a book as a PDF file.
     *
     * @throws Throwable
     */
    public function exportPdf(int $id)
    {
        $book = Book::visible()->findOrFail($id);
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
        $book = Book::visible()->findOrFail($id);
        $htmlContent = $this->exportFormatter->bookToContainedHtml($book);

        return $this->download()->directly($htmlContent, $book->slug . '.html');
    }

    /**
     * Export a book as a plain text file.
     */
    public function exportPlainText(int $id)
    {
        $book = Book::visible()->findOrFail($id);
        $textContent = $this->exportFormatter->bookToPlainText($book);

        return $this->download()->directly($textContent, $book->slug . '.txt');
    }

    /**
     * Export a book as a markdown file.
     */
    public function exportMarkdown(int $id)
    {
        $book = Book::visible()->findOrFail($id);
        $markdown = $this->exportFormatter->bookToMarkdown($book);

        return $this->download()->directly($markdown, $book->slug . '.md');
    }
}
