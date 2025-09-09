<?php

namespace BookStack\Exports\Controllers;

use BookStack\Entities\Queries\BookshelfQueries;
use BookStack\Exceptions\NotFoundException;
use BookStack\Exports\ExportFormatter;
use BookStack\Exports\ZipExports\ZipExportBuilder;
use BookStack\Http\Controller;
use Throwable;

class ShelfExportController extends Controller
{
    public function __construct(
        protected BookshelfQueries $queries,
        protected ExportFormatter $exportFormatter,
    ) {
        $this->middleware('can:content-export');
        $this->middleware('throttle:exports');
    }

    /**
     * Export a shelf as a PDF file containing all books.
     *
     * @throws Throwable
     */
    public function pdf(string $shelfSlug)
    {
        $shelf = $this->queries->findVisibleBySlugOrFail($shelfSlug);
        $pdfContent = $this->exportFormatter->shelfToPdf($shelf);

        return $this->download()->directly($pdfContent, $shelfSlug . '.pdf');
    }

    /**
     * Export a shelf as a contained HTML file containing all books.
     *
     * @throws Throwable
     */
    public function html(string $shelfSlug)
    {
        $shelf = $this->queries->findVisibleBySlugOrFail($shelfSlug);
        $htmlContent = $this->exportFormatter->shelfToContainedHtml($shelf);

        return $this->download()->directly($htmlContent, $shelfSlug . '.html');
    }

    /**
     * Export a shelf as a plain text file containing all books.
     */
    public function plainText(string $shelfSlug)
    {
        $shelf = $this->queries->findVisibleBySlugOrFail($shelfSlug);
        $textContent = $this->exportFormatter->shelfToPlainText($shelf);

        return $this->download()->directly($textContent, $shelfSlug . '.txt');
    }

    /**
     * Export a shelf as a markdown file containing all books.
     */
    public function markdown(string $shelfSlug)
    {
        $shelf = $this->queries->findVisibleBySlugOrFail($shelfSlug);
        $textContent = $this->exportFormatter->shelfToMarkdown($shelf);

        return $this->download()->directly($textContent, $shelfSlug . '.md');
    }

    /**
     * Export a shelf to a contained ZIP export file containing all books.
     *
     * @throws Throwable
     */
    public function zip(string $shelfSlug)
    {
        $shelf = $this->queries->findVisibleBySlugOrFail($shelfSlug);
        $zipBuilder = new ZipExportBuilder();
        $zipContent = $zipBuilder->buildShelfZip($shelf);

        return $this->download()->directly($zipContent, $shelfSlug . '.zip');
    }
}
