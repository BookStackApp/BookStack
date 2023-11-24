<?php

namespace BookStack\Entities\Tools;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Tools\Markdown\HtmlToMarkdown;
use BookStack\Uploads\ImageService;
use BookStack\Util\CspService;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Exception;
use Throwable;

class ExportFormatter
{
    public function __construct(
        protected ImageService $imageService,
        protected PdfGenerator $pdfGenerator,
        protected CspService $cspService
    ) {
    }

    /**
     * Convert a page to a self-contained HTML file.
     * Includes required CSS & image content. Images are base64 encoded into the HTML.
     *
     * @throws Throwable
     */
    public function pageToContainedHtml(Page $page): string
    {
        $page->html = (new PageContent($page))->render();
        $pageHtml = view('exports.page', [
            'page'       => $page,
            'format'     => 'html',
            'cspContent' => $this->cspService->getCspMetaTagValue(),
            'locale'     => user()->getLocale(),
        ])->render();

        return $this->containHtml($pageHtml);
    }

    /**
     * Convert a chapter to a self-contained HTML file.
     *
     * @throws Throwable
     */
    public function chapterToContainedHtml(Chapter $chapter): string
    {
        $pages = $chapter->getVisiblePages();
        $pages->each(function ($page) {
            $page->html = (new PageContent($page))->render();
        });
        $html = view('exports.chapter', [
            'chapter'    => $chapter,
            'pages'      => $pages,
            'format'     => 'html',
            'cspContent' => $this->cspService->getCspMetaTagValue(),
            'locale'     => user()->getLocale(),
        ])->render();

        return $this->containHtml($html);
    }

    /**
     * Convert a book to a self-contained HTML file.
     *
     * @throws Throwable
     */
    public function bookToContainedHtml(Book $book): string
    {
        $bookTree = (new BookContents($book))->getTree(false, true);
        $html = view('exports.book', [
            'book'         => $book,
            'bookChildren' => $bookTree,
            'format'       => 'html',
            'cspContent'   => $this->cspService->getCspMetaTagValue(),
            'locale'       => user()->getLocale(),
        ])->render();

        return $this->containHtml($html);
    }

    /**
     * Convert a page to a PDF file.
     *
     * @throws Throwable
     */
    public function pageToPdf(Page $page): string
    {
        $page->html = (new PageContent($page))->render();
        $html = view('exports.page', [
            'page'   => $page,
            'format' => 'pdf',
            'engine' => $this->pdfGenerator->getActiveEngine(),
            'locale' => user()->getLocale(),
        ])->render();

        return $this->htmlToPdf($html);
    }

    /**
     * Convert a chapter to a PDF file.
     *
     * @throws Throwable
     */
    public function chapterToPdf(Chapter $chapter): string
    {
        $pages = $chapter->getVisiblePages();
        $pages->each(function ($page) {
            $page->html = (new PageContent($page))->render();
        });

        $html = view('exports.chapter', [
            'chapter' => $chapter,
            'pages'   => $pages,
            'format'  => 'pdf',
            'engine'  => $this->pdfGenerator->getActiveEngine(),
            'locale'  => user()->getLocale(),
        ])->render();

        return $this->htmlToPdf($html);
    }

    /**
     * Convert a book to a PDF file.
     *
     * @throws Throwable
     */
    public function bookToPdf(Book $book): string
    {
        $bookTree = (new BookContents($book))->getTree(false, true);
        $html = view('exports.book', [
            'book'         => $book,
            'bookChildren' => $bookTree,
            'format'       => 'pdf',
            'engine'       => $this->pdfGenerator->getActiveEngine(),
            'locale'       => user()->getLocale(),
        ])->render();

        return $this->htmlToPdf($html);
    }

    /**
     * Convert normal web-page HTML to a PDF.
     *
     * @throws Exception
     */
    protected function htmlToPdf(string $html): string
    {
        $html = $this->containHtml($html);
        $html = $this->replaceIframesWithLinks($html);
        $html = $this->openDetailElements($html);

        return $this->pdfGenerator->fromHtml($html);
    }

    /**
     * Within the given HTML content, Open any detail blocks.
     */
    protected function openDetailElements(string $html): string
    {
        libxml_use_internal_errors(true);

        $doc = new DOMDocument();
        $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xPath = new DOMXPath($doc);

        $details = $xPath->query('//details');
        /** @var DOMElement $detail */
        foreach ($details as $detail) {
            $detail->setAttribute('open', 'open');
        }

        return $doc->saveHTML();
    }

    /**
     * Within the given HTML content, replace any iframe elements
     * with anchor links within paragraph blocks.
     */
    protected function replaceIframesWithLinks(string $html): string
    {
        libxml_use_internal_errors(true);

        $doc = new DOMDocument();
        $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xPath = new DOMXPath($doc);

        $iframes = $xPath->query('//iframe');
        /** @var DOMElement $iframe */
        foreach ($iframes as $iframe) {
            $link = $iframe->getAttribute('src');
            if (str_starts_with($link, '//')) {
                $link = 'https:' . $link;
            }

            $anchor = $doc->createElement('a', $link);
            $anchor->setAttribute('href', $link);
            $paragraph = $doc->createElement('p');
            $paragraph->appendChild($anchor);
            $iframe->parentNode->replaceChild($paragraph, $iframe);
        }

        return $doc->saveHTML();
    }

    /**
     * Bundle of the contents of a html file to be self-contained.
     *
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
                $imageEncoded = $this->imageService->imageUrlToBase64($srcString);
                if ($imageEncoded === null) {
                    $imageEncoded = $srcString;
                }
                $newImgTagString = str_replace($srcString, $imageEncoded, $oldImgTagString);
                $htmlContent = str_replace($oldImgTagString, $newImgTagString, $htmlContent);
            }
        }

        $linksOutput = [];
        preg_match_all("/\<a.*href\=(\'|\")(.*?)(\'|\").*?\>/i", $htmlContent, $linksOutput);

        // Update relative links to be absolute, with instance url
        if (isset($linksOutput[0]) && count($linksOutput[0]) > 0) {
            foreach ($linksOutput[0] as $index => $linkMatch) {
                $oldLinkString = $linkMatch;
                $srcString = $linksOutput[2][$index];
                if (!str_starts_with(trim($srcString), 'http')) {
                    $newSrcString = url($srcString);
                    $newLinkString = str_replace($srcString, $newSrcString, $oldLinkString);
                    $htmlContent = str_replace($oldLinkString, $newLinkString, $htmlContent);
                }
            }
        }

        return $htmlContent;
    }

    /**
     * Converts the page contents into simple plain text.
     * This method filters any bad looking content to provide a nice final output.
     */
    public function pageToPlainText(Page $page, bool $pageRendered = false, bool $fromParent = false): string
    {
        $html = $pageRendered ? $page->html : (new PageContent($page))->render();
        // Add proceeding spaces before tags so spaces remain between
        // text within elements after stripping tags.
        $html = str_replace('<', " <", $html);
        $text = trim(strip_tags($html));
        // Replace multiple spaces with single spaces
        $text = preg_replace('/ {2,}/', ' ', $text);
        // Reduce multiple horrid whitespace characters.
        $text = preg_replace('/(\x0A|\xA0|\x0A|\r|\n){2,}/su', "\n\n", $text);
        $text = html_entity_decode($text);
        // Add title
        $text = $page->name . ($fromParent ? "\n" : "\n\n") . $text;

        return $text;
    }

    /**
     * Convert a chapter into a plain text string.
     */
    public function chapterToPlainText(Chapter $chapter): string
    {
        $text = $chapter->name . "\n" . $chapter->description;
        $text = trim($text) . "\n\n";

        $parts = [];
        foreach ($chapter->getVisiblePages() as $page) {
            $parts[] = $this->pageToPlainText($page, false, true);
        }

        return $text . implode("\n\n", $parts);
    }

    /**
     * Convert a book into a plain text string.
     */
    public function bookToPlainText(Book $book): string
    {
        $bookTree = (new BookContents($book))->getTree(false, true);
        $text = $book->name . "\n" . $book->description;
        $text = rtrim($text) . "\n\n";

        $parts = [];
        foreach ($bookTree as $bookChild) {
            if ($bookChild->isA('chapter')) {
                $parts[] = $this->chapterToPlainText($bookChild);
            } else {
                $parts[] = $this->pageToPlainText($bookChild, true, true);
            }
        }

        return $text . implode("\n\n", $parts);
    }

    /**
     * Convert a page to a Markdown file.
     */
    public function pageToMarkdown(Page $page): string
    {
        if ($page->markdown) {
            return '# ' . $page->name . "\n\n" . $page->markdown;
        }

        return '# ' . $page->name . "\n\n" . (new HtmlToMarkdown($page->html))->convert();
    }

    /**
     * Convert a chapter to a Markdown file.
     */
    public function chapterToMarkdown(Chapter $chapter): string
    {
        $text = '# ' . $chapter->name . "\n\n";
        $text .= $chapter->description . "\n\n";
        foreach ($chapter->pages as $page) {
            $text .= $this->pageToMarkdown($page) . "\n\n";
        }

        return trim($text);
    }

    /**
     * Convert a book into a plain text string.
     */
    public function bookToMarkdown(Book $book): string
    {
        $bookTree = (new BookContents($book))->getTree(false, true);
        $text = '# ' . $book->name . "\n\n";
        foreach ($bookTree as $bookChild) {
            if ($bookChild instanceof Chapter) {
                $text .= $this->chapterToMarkdown($bookChild) . "\n\n";
            } else {
                $text .= $this->pageToMarkdown($bookChild) . "\n\n";
            }
        }

        return trim($text);
    }
}
