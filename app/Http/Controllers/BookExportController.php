<?php

namespace BookStack\Http\Controllers;

use BookStack\Entities\Managers\BookContents;
use BookStack\Entities\ExportService;
use BookStack\Entities\Repos\BookRepo;
use Throwable;
use ZipArchive;

class BookExportController extends Controller
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
    public function pdf(string $bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $pdfContent = $this->exportService->bookToPdf($book);
        return $this->downloadResponse($pdfContent, $bookSlug . '.pdf');
    }

    /**
     * Export a book as a contained HTML file.
     * @throws Throwable
     */
    public function html(string $bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $htmlContent = $this->exportService->bookToContainedHtml($book);
        return $this->downloadResponse($htmlContent, $bookSlug . '.html');
    }

    /**
     * Export a book as a plain text file.
     */
    public function plainText(string $bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $textContent = $this->exportService->bookToPlainText($book);
        return $this->downloadResponse($textContent, $bookSlug . '.txt');
    }

    /**
     * Export a book as a markdown file.
     */
    public function markdown(string $bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $textContent = $this->exportService->bookToMarkdown($book);
        return $this->downloadResponse($textContent, $bookSlug . '.md');
    }

    /**
     * Export a book as a zip file, made of markdown files.
     */
    public function zip(string $bookSlug)
    {
        $book = $this->bookRepo->getBySlug($bookSlug);
        $z = new ZipArchive();
        $z->open("book.zip", \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $bookTree = (new BookContents($book))->getTree(false, true);
        foreach ($bookTree as $bookChild) {
            if ($bookChild->isA('chapter')) {
                $z->addEmptyDir($bookChild->name);
                foreach ($bookChild->pages as $page) {
                    $z->addFromString($bookChild->name . "/" . $page->name . ".md", $this->exportService->pageToMarkdown($page));
                }
            } else {
                $z->addFromString($bookChild->name . ".md", $this->exportService->pageToMarkdown($bookChild));
            }
        }
        return response()->download('book.zip');
        // TODO: Is not unlinking it a security issue?
    }
}
