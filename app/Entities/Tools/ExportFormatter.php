<?php namespace BookStack\Entities\Tools;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Uploads\ImageService;
use DomPDF;
use Exception;
use SnappyPDF;
use League\HTMLToMarkdown\HtmlConverter;
use Throwable;
use ZipArchive;

class ExportFormatter
{

    protected $imageService;

    /**
     * ExportService constructor.
     */
    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    /**
     * Convert a page to a self-contained HTML file.
     * Includes required CSS & image content. Images are base64 encoded into the HTML.
     * @throws Throwable
     */
    public function pageToContainedHtml(Page $page)
    {
        $page->html = (new PageContent($page))->render();
        $pageHtml = view('pages.export', [
            'page' => $page,
            'format' => 'html',
        ])->render();
        return $this->containHtml($pageHtml);
    }

    /**
     * Convert a chapter to a self-contained HTML file.
     * @throws Throwable
     */
    public function chapterToContainedHtml(Chapter $chapter)
    {
        $pages = $chapter->getVisiblePages();
        $pages->each(function ($page) {
            $page->html = (new PageContent($page))->render();
        });
        $html = view('chapters.export', [
            'chapter' => $chapter,
            'pages' => $pages,
            'format' => 'html',
        ])->render();
        return $this->containHtml($html);
    }

    /**
     * Convert a book to a self-contained HTML file.
     * @throws Throwable
     */
    public function bookToContainedHtml(Book $book)
    {
        $bookTree = (new BookContents($book))->getTree(false, true);
        $html = view('books.export', [
            'book' => $book,
            'bookChildren' => $bookTree,
            'format' => 'html',
        ])->render();
        return $this->containHtml($html);
    }

    /**
     * Convert a page to a PDF file.
     * @throws Throwable
     */
    public function pageToPdf(Page $page)
    {
        $page->html = (new PageContent($page))->render();
        $html = view('pages.export', [
            'page' => $page,
            'format' => 'pdf',
        ])->render();
        return $this->htmlToPdf($html);
    }

    /**
     * Convert a chapter to a PDF file.
     * @throws Throwable
     */
    public function chapterToPdf(Chapter $chapter)
    {
        $pages = $chapter->getVisiblePages();
        $pages->each(function ($page) {
            $page->html = (new PageContent($page))->render();
        });

        $html = view('chapters.export', [
            'chapter' => $chapter,
            'pages' => $pages,
            'format' => 'pdf',
        ])->render();

        return $this->htmlToPdf($html);
    }

    /**
     * Convert a book to a PDF file.
     * @throws Throwable
     */
    public function bookToPdf(Book $book)
    {
        $bookTree = (new BookContents($book))->getTree(false, true);
        $html = view('books.export', [
            'book' => $book,
            'bookChildren' => $bookTree,
            'format' => 'pdf',
        ])->render();
        return $this->htmlToPdf($html);
    }

    /**
     * Convert normal web-page HTML to a PDF.
     * @throws Exception
     */
    protected function htmlToPdf(string $html): string
    {
        $containedHtml = $this->containHtml($html);
        $useWKHTML = config('snappy.pdf.binary') !== false;
        if ($useWKHTML) {
            $pdf = SnappyPDF::loadHTML($containedHtml);
            $pdf->setOption('print-media-type', true);
        } else {
            $pdf = DomPDF::loadHTML($containedHtml);
        }
        return $pdf->output();
    }

    /**
     * Bundle of the contents of a html file to be self-contained.
     * @throws Exception
     */
    protected function containHtml(string $htmlContent): string
    {
        $imageTagsOutput = [];
        preg_match_all("/\<img.*?src\=(\'|\")(.*?)(\'|\").*?\>/i", $htmlContent, $imageTagsOutput);

        // Replace image src with base64 encoded image strings
        if (isset($imageTagsOutput[0]) && count($imageTagsOutput[0]) > 0) {
            foreach ($imageTagsOutput[0] as $index => $imgMatch) {
                $oldImgTagString = $imgMatch;
                $srcString = $imageTagsOutput[2][$index];
                $imageEncoded = $this->imageService->imageUriToBase64($srcString);
                if ($imageEncoded === null) {
                    $imageEncoded = $srcString;
                }
                $newImgTagString = str_replace($srcString, $imageEncoded, $oldImgTagString);
                $htmlContent = str_replace($oldImgTagString, $newImgTagString, $htmlContent);
            }
        }

        $linksOutput = [];
        preg_match_all("/\<a.*href\=(\'|\")(.*?)(\'|\").*?\>/i", $htmlContent, $linksOutput);

        // Replace image src with base64 encoded image strings
        if (isset($linksOutput[0]) && count($linksOutput[0]) > 0) {
            foreach ($linksOutput[0] as $index => $linkMatch) {
                $oldLinkString = $linkMatch;
                $srcString = $linksOutput[2][$index];
                if (strpos(trim($srcString), 'http') !== 0) {
                    $newSrcString = url($srcString);
                    $newLinkString = str_replace($srcString, $newSrcString, $oldLinkString);
                    $htmlContent = str_replace($oldLinkString, $newLinkString, $htmlContent);
                }
            }
        }

        // Replace any relative links with system domain
        return $htmlContent;
    }

    /**
     * Converts the page contents into simple plain text.
     * This method filters any bad looking content to provide a nice final output.
     */
    public function pageToPlainText(Page $page): string
    {
        $html = (new PageContent($page))->render();
        $text = strip_tags($html);
        // Replace multiple spaces with single spaces
        $text = preg_replace('/\ {2,}/', ' ', $text);
        // Reduce multiple horrid whitespace characters.
        $text = preg_replace('/(\x0A|\xA0|\x0A|\r|\n){2,}/su', "\n\n", $text);
        $text = html_entity_decode($text);
        // Add title
        $text = $page->name . "\n\n" . $text;
        return $text;
    }

    /**
     * Convert a chapter into a plain text string.
     */
    public function chapterToPlainText(Chapter $chapter): string
    {
        $text = $chapter->name . "\n\n";
        $text .= $chapter->description . "\n\n";
        foreach ($chapter->getVisiblePages() as $page) {
            $text .= $this->pageToPlainText($page);
        }
        return $text;
    }

    /**
     * Convert a book into a plain text string.
     */
    public function bookToPlainText(Book $book): string
    {
        $bookTree = (new BookContents($book))->getTree(false, false);
        $text = $book->name . "\n\n";
        foreach ($bookTree as $bookChild) {
            if ($bookChild->isA('chapter')) {
                $text .= $this->chapterToPlainText($bookChild);
            } else {
                $text .= $this->pageToPlainText($bookChild);
            }
        }
        return $text;
    }

    /**
     * Convert a page to a Markdown file.
     * @throws Throwable
     */
    public function pageToMarkdown(Page $page)
    {
        if (property_exists($page, 'markdown') && $page->markdown != '') {
            return "# " . $page->name . "\n\n" . $page->markdown;
        } else {
            $converter = new HtmlConverter();
            return "# " . $page->name . "\n\n" . $converter->convert($page->html);
        }
    }

    /**
     * Convert a chapter to a Markdown file.
     * @throws Throwable
     */
    public function chapterToMarkdown(Chapter $chapter)
    {
        $text = "# " . $chapter->name . "\n\n";
        $text .= $chapter->description . "\n\n";
        foreach ($chapter->pages as $page) {
            $text .= $this->pageToMarkdown($page);
        }
        return $text;
    }

    /**
     * Convert a book into a plain text string.
     */
    public function bookToMarkdown(Book $book): string
    {
        $bookTree = (new BookContents($book))->getTree(false, true);
        $text = "# " . $book->name . "\n\n";
        foreach ($bookTree as $bookChild) {
            if ($bookChild->isA('chapter')) {
                $text .= $this->chapterToMarkdown($bookChild);
            } else {
                $text .= $this->pageToMarkdown($bookChild);
            }
        }
        return $text;
    }

    /**
     * Convert a book into a zip file.
     */
    public function bookToZip(Book $book): string
    {
        // TODO: Is not unlinking the file a security risk?
        $z = new ZipArchive();
        $z->open("book.zip", \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
        $bookTree = (new BookContents($book))->getTree(false, true);
        foreach ($bookTree as $bookChild) {
            if ($bookChild->isA('chapter')) {
                $z->addEmptyDir($bookChild->name);
                foreach ($bookChild->pages as $page) {
                    $filename = $bookChild->name . "/" . $page->name . ".md";
                    $z->addFromString($filename, $this->pageToMarkdown($page));
                }
            } else {
                $z->addFromString($bookChild->name . ".md", $this->pageToMarkdown($bookChild));
            }
        }
        return "book.zip";
    }
}
