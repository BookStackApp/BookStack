<?php namespace BookStack\Entities;

use BookStack\Entities\Repos\EntityRepo;
use BookStack\Uploads\ImageService;
use BookStack\Exceptions\ExportException;

class ExportService
{
    protected $contentMatching = [
        'video' => ["www.youtube.com", "player.vimeo.com", "www.dailymotion.com"],
        'map' => ['maps.google.com']
    ];

    protected $entityRepo;
    protected $imageService;

    /**
     * ExportService constructor.
     * @param EntityRepo $entityRepo
     * @param ImageService $imageService
     */
    public function __construct(EntityRepo $entityRepo, ImageService $imageService)
    {
        $this->entityRepo = $entityRepo;
        $this->imageService = $imageService;
    }

    /**
     * Convert a page to a self-contained HTML file.
     * Includes required CSS & image content. Images are base64 encoded into the HTML.
     * @param \BookStack\Entities\Page $page
     * @return mixed|string
     * @throws \Throwable
     */
    public function pageToContainedHtml(Page $page)
    {
        $this->entityRepo->renderPage($page);
        $pageHtml = view('pages/export', [
            'page' => $page
        ])->render();
        return $this->containHtml($pageHtml);
    }

    /**
     * Convert a chapter to a self-contained HTML file.
     * @param \BookStack\Entities\Chapter $chapter
     * @return mixed|string
     * @throws \Throwable
     */
    public function chapterToContainedHtml(Chapter $chapter)
    {
        $pages = $this->entityRepo->getChapterChildren($chapter);
        $pages->each(function ($page) {
            $page->html = $this->entityRepo->renderPage($page);
        });
        $html = view('chapters/export', [
            'chapter' => $chapter,
            'pages' => $pages
        ])->render();
        return $this->containHtml($html);
    }

    /**
     * Convert a book to a self-contained HTML file.
     * @param Book $book
     * @return mixed|string
     * @throws \Throwable
     */
    public function bookToContainedHtml(Book $book)
    {
        $bookTree = $this->entityRepo->getBookChildren($book, true, true);
        $html = view('books/export', [
            'book' => $book,
            'bookChildren' => $bookTree
        ])->render();
        return $this->containHtml($html);
    }

    /**
     * Convert a page to a PDF file.
     * @param Page $page
     * @param bool $isTesting
     * @return mixed|string
     * @throws \Throwable
     */
    public function pageToPdf(Page $page, bool $isTesting = false)
    {
        $this->entityRepo->renderPage($page);
        $html = view('pages/pdf', [
            'page' => $page
        ])->render();
        return $this->htmlToPdf($html, $isTesting);
    }

    /**
     * Convert a chapter to a PDF file.
     * @param \BookStack\Entities\Chapter $chapter
     * @return mixed|string
     * @throws \Throwable
     */
    public function chapterToPdf(Chapter $chapter)
    {
        $pages = $this->entityRepo->getChapterChildren($chapter);
        $pages->each(function ($page) {
            $page->html = $this->entityRepo->renderPage($page);
        });
        $html = view('chapters/export', [
            'chapter' => $chapter,
            'pages' => $pages
        ])->render();
        return $this->htmlToPdf($html);
    }

    /**
     * Convert a book to a PDF file
     * @param \BookStack\Entities\Book $book
     * @return string
     * @throws \Throwable
     */
    public function bookToPdf(Book $book)
    {
        $bookTree = $this->entityRepo->getBookChildren($book, true, true);
        $html = view('books/export', [
            'book' => $book,
            'bookChildren' => $bookTree
        ])->render();
        return $this->htmlToPdf($html);
    }

    /**
     * Convert normal webpage HTML to a PDF.
     * @param $html
     * @param $isTesting
     * @return string
     * @throws \Exception
     */
    protected function htmlToPdf($html, $isTesting = false)
    {
        $containedHtml = $this->containHtml($html, true);
        if ($isTesting) {
            return $containedHtml;
        }
        $useWKHTML = config('snappy.pdf.binary') !== false;
        if ($useWKHTML) {
            $pdf = \SnappyPDF::loadHTML($containedHtml);
            $pdf->setOption('print-media-type', true);
        } else {
            $pdf = \DomPDF::loadHTML($containedHtml);
        }
        return $pdf->output();
    }

    /**
     * Bundle of the contents of a html file to be self-contained.
     * @param $htmlContent
     * @param bool $isPDF
     * @return mixed|string
     * @throws \BookStack\Exceptions\ExportException
     */
    protected function containHtml(string $htmlContent, bool $isPDF = false) : string
    {
        $dom = $this->getDOM($htmlContent);
        if ($dom === false) {
            throw new ExportException(trans('errors.dom_parse_error'));
        }

        // replace image src with base64 encoded image strings
        $images = $dom->getElementsByTagName('img');
        foreach ($images as $img) {
            $base64String = $this->imageService->imageUriToBase64($img->getAttribute('src'));
            if ($base64String !== null) {
                $img->setAttribute('src', $base64String);
                $dom->saveHTML($img);
            }
        }

        // replace all relative hrefs.
        $links = $dom->getElementsByTagName('a');
        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            if (strpos(trim($href), 'http') !== 0) {
                $newHref = url($href);
                $link->setAttribute('href', $newHref);
                $dom->saveHTML($link);
            }
        }

        // replace all src in video, audio and iframe tags
        $xmlDoc = new \DOMXPath($dom);
        $srcElements = $xmlDoc->query('//video | //audio | //iframe');
        foreach ($srcElements as $element) {
            $element = $this->fixRelativeSrc($element);
            $dom->saveHTML($element);

            if ($isPDF) {
                $src = $element->getAttribute('src');
                $label = $this->getContentLabel($src);

                $div = $dom->createElement('div');
                $textNode = $dom->createTextNode($label);

                $anchor = $dom->createElement('a');
                $anchor->setAttribute('href', $src);
                $anchor->textContent = $src;

                $div->appendChild($textNode);
                $div->appendChild($anchor);

                $element->parentNode->replaceChild($div, $element);
            }
        }

        return $dom->saveHTML();
    }

    /**
     * Converts the page contents into simple plain text.
     * This method filters any bad looking content to provide a nice final output.
     * @param Page $page
     * @return mixed
     * @throws \BookStack\Exceptions\ExportException
     */
    public function pageToPlainText(Page $page)
    {
        $html = $this->entityRepo->renderPage($page);
        $dom = $this->getDom($html);

        if ($dom === false) {
            throw new ExportException(trans('errors.dom_parse_error'));
        }

        // handle anchor tags.
        $links = $dom->getElementsByTagName('a');
        foreach ($links as $link) {
            $href = $link->getAttribute('href');
            if (strpos(trim($href), 'http') !== 0) {
                $newHref = url($href);
                $link->setAttribute('href', $newHref);
            }

            $link->textContent = trim($link->textContent . " ($href)");
            $dom->saveHTML();
        }

        $xmlDoc = new \DOMXPath($dom);
        $srcElements = $xmlDoc->query('//video | //audio | //iframe | //img');
        foreach ($srcElements as $element) {
            $element = $this->fixRelativeSrc($element);
            $fixedSrc = $element->getAttribute('src');
            $label = $this->getContentLabel($fixedSrc);
            $finalLabel = "\n\n$label $fixedSrc\n\n";

            $textNode = $dom->createTextNode($finalLabel);
            $element->parentNode->replaceChild($textNode, $element);
        }

        $text = strip_tags($dom->saveHTML());
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
     * @param \BookStack\Entities\Chapter $chapter
     * @return string
     */
    public function chapterToPlainText(Chapter $chapter)
    {
        $text = $chapter->name . "\n\n";
        $text .= $chapter->description . "\n\n";
        foreach ($chapter->pages as $page) {
            $text .= $this->pageToPlainText($page);
        }
        return $text;
    }

    /**
     * Convert a book into a plain text string.
     * @param Book $book
     * @return string
     */
    public function bookToPlainText(Book $book)
    {
        $bookTree = $this->entityRepo->getBookChildren($book, true, true);
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

    protected function getDom(string $htmlContent) : \DOMDocument
    {
        // See - https://stackoverflow.com/a/17559716/903324
        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML($htmlContent);
        libxml_clear_errors();
        return $dom;
    }

    protected function fixRelativeSrc(\DOMElement $element): \DOMElement
    {
        $src = $element->getAttribute('src');
        if (strpos(trim($src), 'http') !== 0) {
            $newSrc = 'https:' . $src;
            $element->setAttribute('src', $newSrc);
        }
        return $element;
    }


    protected function getContentLabel(string $src) : string
    {
        foreach ($this->contentMatching as $key => $possibleValues) {
            foreach ($possibleValues as $value) {
                if (strpos($src, $value)) {
                    return trans("entities.$key");
                }
            }
        }
        return trans('entities.embedded_content');
    }
}
