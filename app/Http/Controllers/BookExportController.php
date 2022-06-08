<?php

namespace BookStack\Http\Controllers;

use BookStack\Entities\Repos\BookRepo;
use BookStack\Entities\Tools\ExportFormatter;
use Throwable;

class BookExportController extends Controller
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
        $this->middleware('can:content-export');
    }

    /**
     * Export a book as a PDF file.
     *
     * @throws Throwable
     */
    public function pdf(string $bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $pdfContent = $this->exportFormatter->bookToPdf($book);

        return $this->download()->directly($pdfContent, $bookSlug . '.pdf');
    }

    /**
     * Export a book as a contained HTML file.
     *
     * @throws Throwable
     */
    public function html(string $bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $htmlContent = $this->exportFormatter->bookToContainedHtml($book);

        return $this->download()->directly($htmlContent, $bookSlug . '.html');
    }

    /**
     * Export a book as a plain text file.
     */
    public function plainText(string $bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $textContent = $this->exportFormatter->bookToPlainText($book);

        return $this->download()->directly($textContent, $bookSlug . '.txt');
    }

    /**
     * Export a book as a markdown file.
     */
    public function markdown(string $bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $textContent = $this->exportFormatter->bookToMarkdown($book);

        return $this->download()->directly($textContent, $bookSlug . '.md');
    }
}
