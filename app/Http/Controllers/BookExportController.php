<?php

namespace BookStack\Http\Controllers;

use BookStack\Entities\ExportService;
use BookStack\Entities\Repos\EntityRepo;
use BookStack\Exceptions\NotFoundException;
use Throwable;

class BookExportController extends Controller
{
    /**
     * @var EntityRepo
     */
    protected $entityRepo;

    /**
     * @var ExportService
     */
    protected $exportService;

    /**
     * BookExportController constructor.
     * @param EntityRepo $entityRepo
     * @param ExportService $exportService
     */
    public function __construct(EntityRepo $entityRepo, ExportService $exportService)
    {
        $this->entityRepo = $entityRepo;
        $this->exportService = $exportService;
        parent::__construct();
    }

    /**
     * Export a book as a PDF file.
     * @param string $bookSlug
     * @return mixed
     * @throws NotFoundException
     * @throws Throwable
     */
    public function pdf(string $bookSlug)
    {
        $book = $this->entityRepo->getBySlug('book', $bookSlug);
        $pdfContent = $this->exportService->bookToPdf($book);
        return $this->downloadResponse($pdfContent, $bookSlug . '.pdf');
    }

    /**
     * Export a book as a contained HTML file.
     * @param string $bookSlug
     * @return mixed
     * @throws NotFoundException
     * @throws Throwable
     */
    public function html(string $bookSlug)
    {
        $book = $this->entityRepo->getBySlug('book', $bookSlug);
        $htmlContent = $this->exportService->bookToContainedHtml($book);
        return $this->downloadResponse($htmlContent, $bookSlug . '.html');
    }

    /**
     * Export a book as a plain text file.
     * @param $bookSlug
     * @return mixed
     * @throws NotFoundException
     */
    public function plainText(string $bookSlug)
    {
        $book = $this->entityRepo->getBySlug('book', $bookSlug);
        $textContent = $this->exportService->bookToPlainText($book);
        return $this->downloadResponse($textContent, $bookSlug . '.txt');
    }
}
