<?php namespace BookStack\Http\Controllers\Api;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Tools\ExportFormatter;
use BookStack\Entities\Repos\BookRepo;
use Throwable;

class BookExportApiController extends ApiController
{
    protected $bookRepo;
    protected $exportFormatter;

    /**
     * BookExportController constructor.
     */
    public function __construct(BookRepo $bookRepo, ExportFormatter $exportFormatter)
    {
        $this->bookRepo = $bookRepo;
        $this->exportFormatter = $exportFormatter;
    }

    /**
     * Export a book as a PDF file.
     * @throws Throwable
     */
    public function exportPdf(int $id)
    {
        $book = Book::visible()->findOrFail($id);
        $pdfContent = $this->exportFormatter->bookToPdf($book);
        return $this->downloadResponse($pdfContent, $book->slug . '.pdf');
    }

    /**
     * Export a book as a contained HTML file.
     * @throws Throwable
     */
    public function exportHtml(int $id)
    {
        $book = Book::visible()->findOrFail($id);
        $htmlContent = $this->exportFormatter->bookToContainedHtml($book);
        return $this->downloadResponse($htmlContent, $book->slug . '.html');
    }

    /**
     * Export a book as a plain text file.
     */
    public function exportPlainText(int $id)
    {
        $book = Book::visible()->findOrFail($id);
        $textContent = $this->exportFormatter->bookToPlainText($book);
        return $this->downloadResponse($textContent, $book->slug . '.txt');
    }
}
