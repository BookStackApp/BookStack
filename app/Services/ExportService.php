<?php namespace BookStack\Services;

use BookStack\Book;
use BookStack\Chapter;
use BookStack\Page;
use BookStack\Repos\EntityRepo;

class ExportService
{

    protected $entityRepo;

    /**
     * ExportService constructor.
     * @param $entityRepo
     */
    public function __construct(EntityRepo $entityRepo)
    {
        $this->entityRepo = $entityRepo;
    }

    /**
     * Convert a page to a self-contained HTML file.
     * Includes required CSS & image content. Images are base64 encoded into the HTML.
     * @param Page $page
     * @return mixed|string
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
     * @param Chapter $chapter
     * @return mixed|string
     */
    public function chapterToContainedHtml(Chapter $chapter)
    {
        $pages = $this->entityRepo->getChapterChildren($chapter);
        $pages->each(function($page) {
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
     * @return mixed|string
     */
    public function pageToPdf(Page $page)
    {
        $this->entityRepo->renderPage($page);
        $html = view('pages/pdf', [
            'page' => $page
        ])->render();
        return $this->htmlToPdf($html);
    }

    /**
     * Convert a chapter to a PDF file.
     * @param Chapter $chapter
     * @return mixed|string
     */
    public function chapterToPdf(Chapter $chapter)
    {
        $pages = $this->entityRepo->getChapterChildren($chapter);
        $pages->each(function($page) {
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
     * @param Book $book
     * @return string
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
     * @return string
     */
    protected function htmlToPdf($html)
    {
        $containedHtml = $this->containHtml($html);
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
     * @return mixed|string
     * @throws \Exception
     */
    protected function containHtml($htmlContent)
    {
        $imageTagsOutput = [];
        preg_match_all("/\<img.*src\=(\'|\")(.*?)(\'|\").*?\>/i", $htmlContent, $imageTagsOutput);

        // Replace image src with base64 encoded image strings
        if (isset($imageTagsOutput[0]) && count($imageTagsOutput[0]) > 0) {
            foreach ($imageTagsOutput[0] as $index => $imgMatch) {
                $oldImgString = $imgMatch;
                $srcString = $imageTagsOutput[2][$index];
                $isLocal = strpos(trim($srcString), 'http') !== 0;
                if ($isLocal) {
                    $pathString = public_path(trim($srcString, '/'));
                } else {
                    $pathString = $srcString;
                }

                // Attempt to find local files even if url not absolute
                $base = baseUrl('/');
                if (strpos($srcString, $base) === 0) {
                    $isLocal = true;
                    $relString = str_replace($base, '', $srcString);
                    $pathString = public_path(trim($relString, '/'));
                }

                if ($isLocal && !file_exists($pathString)) continue;
                try {
                    if ($isLocal) {
                        $imageContent = file_get_contents($pathString);
                    } else {
                        $ch = curl_init();
                        curl_setopt_array($ch, [CURLOPT_URL => $pathString, CURLOPT_RETURNTRANSFER => 1, CURLOPT_CONNECTTIMEOUT => 5]);
                        $imageContent = curl_exec($ch);
                        $err = curl_error($ch);
                        curl_close($ch);
                        if ($err) throw new \Exception("Image fetch failed, Received error: " . $err);
                    }
                    $imageEncoded = 'data:image/' . pathinfo($pathString, PATHINFO_EXTENSION) . ';base64,' . base64_encode($imageContent);
                    $newImageString = str_replace($srcString, $imageEncoded, $oldImgString);
                } catch (\ErrorException $e) {
                    $newImageString = '';
                }
                $htmlContent = str_replace($oldImgString, $newImageString, $htmlContent);
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
     * @param Page $page
     * @return mixed
     */
    public function pageToPlainText(Page $page)
    {
        $html = $this->entityRepo->renderPage($page);
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
     * @param Chapter $chapter
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

}












