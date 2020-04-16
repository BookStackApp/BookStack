<?php namespace BookStack\Http\Controllers\Api;

use BookStack\Entities\Book;
use BookStack\Entities\ExportService;
use BookStack\Entities\Repos\BookRepo;
use Throwable;

class BooksExportApiController extends ApiController
{

    protected $bookRepo;
    protected $exportService;

    /**
     * BookExportController constructor.
     */
    public function __construct(BookRepo $bookRepo, ExportService $exportService)
    {
        $this->bookRepo = $bookRepo;
        $this->exportService = $exportService;
        parent::__construct();
    }

    /**
     * Export a book as a PDF file.
     * @throws Throwable
     */
    public function exportPdf(int $id)
    {
        $book = Book::visible()->findOrFail($id);
        $pdfContent = $this->exportService->bookToPdf($book);
        return $this->downloadResponse($pdfContent, $book->slug . '.pdf');
    }

    /**
     * Export a book as a contained HTML file.
     * @throws Throwable
     */
    public function exportHtml(int $id)
    {
        $book = Book::visible()->findOrFail($id);
        $htmlContent = $this->exportService->bookToContainedHtml($book);
        return $this->downloadResponse($htmlContent, $book->slug . '.html');
    }

    /**
     * Export a book as a plain text file.
     */
    public function exportPlainText(int $id)
    {
        $book = Book::visible()->findOrFail($id);
        $textContent = $this->exportService->bookToPlainText($book);
        return $this->downloadResponse($textContent, $book->slug . '.txt');
    }
}
