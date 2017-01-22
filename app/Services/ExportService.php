<?php namespace BookStack\Services;

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
        $cssContent = file_get_contents(public_path('/css/export-styles.css'));
        $pageHtml = view('pages/export', ['page' => $page, 'pageContent' => $this->entityRepo->renderPage($page), 'css' => $cssContent])->render();
        return $this->containHtml($pageHtml);
    }

    /**
     * Convert a page to a pdf file.
     * @param Page $page
     * @return mixed|string
     */
    public function pageToPdf(Page $page)
    {
        $cssContent = file_get_contents(public_path('/css/export-styles.css'));
        $pageHtml = view('pages/pdf', ['page' => $page, 'pageContent' => $this->entityRepo->renderPage($page), 'css' => $cssContent])->render();
//        return $pageHtml;
        $useWKHTML = config('snappy.pdf.binary') !== false;
        $containedHtml = $this->containHtml($pageHtml);
        if ($useWKHTML) {
            $pdf = \SnappyPDF::loadHTML($containedHtml);
        } else {
            $pdf = \PDF::loadHTML($containedHtml);
        }
        return $pdf->output();
    }

    /**
     * Bundle of the contents of a html file to be self-contained.
     * @param $htmlContent
     * @return mixed|string
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
                if ($isLocal && !file_exists($pathString)) continue;
                try {
                    $imageContent = file_get_contents($pathString);
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

}












