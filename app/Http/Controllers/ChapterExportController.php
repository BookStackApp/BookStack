<?php

namespace BookStack\Http\Controllers;

use BookStack\Entities\Repos\ChapterRepo;
use BookStack\Entities\Tools\ExportFormatter;
use BookStack\Exceptions\NotFoundException;
use Throwable;

class ChapterExportController extends Controller
{
    protected $chapterRepo;
    protected $exportFormatter;

    /**
     * ChapterExportController constructor.
     */
    public function __construct(ChapterRepo $chapterRepo, ExportFormatter $exportFormatter)
    {
        $this->chapterRepo = $chapterRepo;
        $this->exportFormatter = $exportFormatter;
        $this->middleware('can:content-export');
    }

    /**
     * Exports a chapter to pdf.
     *
     * @throws NotFoundException
     * @throws Throwable
     */
    public function pdf(string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->chapterRepo->getBySlug($bookSlug, $chapterSlug);
        $pdfContent = $this->exportFormatter->chapterToPdf($chapter);

        return $this->download()->directly($pdfContent, $chapterSlug . '.pdf');
    }

    /**
     * Export a chapter to a self-contained HTML file.
     *
     * @throws NotFoundException
     * @throws Throwable
     */
    public function html(string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->chapterRepo->getBySlug($bookSlug, $chapterSlug);
        $containedHtml = $this->exportFormatter->chapterToContainedHtml($chapter);

        return $this->download()->directly($containedHtml, $chapterSlug . '.html');
    }

    /**
     * Export a chapter to a simple plaintext .txt file.
     *
     * @throws NotFoundException
     */
    public function plainText(string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->chapterRepo->getBySlug($bookSlug, $chapterSlug);
        $chapterText = $this->exportFormatter->chapterToPlainText($chapter);

        return $this->download()->directly($chapterText, $chapterSlug . '.txt');
    }

    /**
     * Export a chapter to a simple markdown file.
     *
     * @throws NotFoundException
     */
    public function markdown(string $bookSlug, string $chapterSlug)
    {
        $chapter = $this->chapterRepo->getBySlug($bookSlug, $chapterSlug);
        $chapterText = $this->exportFormatter->chapterToMarkdown($chapter);

        return $this->download()->directly($chapterText, $chapterSlug . '.md');
    }
}
