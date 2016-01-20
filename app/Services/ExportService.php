<?php namespace BookStack\Services;


use BookStack\Page;

class ExportService
{


    /**
     * Convert a page to a self-contained HTML file.
     * Includes required CSS & image content. Images are base64 encoded into the HTML.
     * @param Page $page
     * @return mixed|string
     */
    public function pageToContainedHtml(Page $page)
    {
        $cssContent = file_get_contents(public_path('/css/export-styles.css'));
        $pageHtml = view('pages/pdf', ['page' => $page, 'css' => $cssContent])->render();

        $imageTagsOutput = [];
        preg_match_all("/\<img.*src\=(\'|\")(.*?)(\'|\").*?\>/i", $pageHtml, $imageTagsOutput);

        // Replace image src with base64 encoded image strings
        if (isset($imageTagsOutput[0]) && count($imageTagsOutput[0]) > 0) {
            foreach ($imageTagsOutput[0] as $index => $imgMatch) {
                $oldImgString = $imgMatch;
                $srcString = $imageTagsOutput[2][$index];
                if (strpos(trim($srcString), 'http') !== 0) {
                    $pathString = public_path($srcString);
                } else {
                    $pathString = $srcString;
                }
                $imageContent = file_get_contents($pathString);
                $imageEncoded = 'data:image/' . pathinfo($pathString, PATHINFO_EXTENSION) . ';base64,' . base64_encode($imageContent);
                $newImageString = str_replace($srcString, $imageEncoded, $oldImgString);
                $pageHtml = str_replace($oldImgString, $newImageString, $pageHtml);
            }
        }

        $linksOutput = [];
        preg_match_all("/\<a.*href\=(\'|\")(.*?)(\'|\").*?\>/i", $pageHtml, $linksOutput);

        // Replace image src with base64 encoded image strings
        if (isset($linksOutput[0]) && count($linksOutput[0]) > 0) {
            foreach ($linksOutput[0] as $index => $linkMatch) {
                $oldLinkString = $linkMatch;
                $srcString = $linksOutput[2][$index];
                if (strpos(trim($srcString), 'http') !== 0) {
                    $newSrcString = url($srcString);
                    $newLinkString = str_replace($srcString, $newSrcString, $oldLinkString);
                    $pageHtml = str_replace($oldLinkString, $newLinkString, $pageHtml);
                }
            }
        }

        // Replace any relative links with system domain
        return $pageHtml;
    }

}