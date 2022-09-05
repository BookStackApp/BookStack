<?php

namespace BookStack\Entities\Tools;

use BookStack\Entities\Models\Page;
use BookStack\Entities\Tools\Markdown\MarkdownToHtml;
use BookStack\Exceptions\ImageUploadException;
use BookStack\Facades\Theme;
use BookStack\Theming\ThemeEvents;
use BookStack\Uploads\ImageRepo;
use BookStack\Uploads\ImageService;
use BookStack\Util\HtmlContentFilter;
use DOMDocument;
use DOMElement;
use DOMNode;
use DOMNodeList;
use DOMXPath;
use Illuminate\Support\Str;

class PageContent
{
    protected Page $page;

    /**
     * PageContent constructor.
     */
    public function __construct(Page $page)
    {
        $this->page = $page;
    }

    /**
     * Update the content of the page with new provided HTML.
     */
    public function setNewHTML(string $html)
    {
        $html = $this->extractBase64ImagesFromHtml($html);
        $this->page->html = $this->formatHtml($html);
        $this->page->text = $this->toPlainText();
        $this->page->markdown = '';
    }

    /**
     * Update the content of the page with new provided Markdown content.
     */
    public function setNewMarkdown(string $markdown)
    {
        $markdown = $this->extractBase64ImagesFromMarkdown($markdown);
        $this->page->markdown = $markdown;
        $html = (new MarkdownToHtml($markdown))->convert();
        $this->page->html = $this->formatHtml($html);
        $this->page->text = $this->toPlainText();
    }

    /**
     * Convert all base64 image data to saved images.
     */
    protected function extractBase64ImagesFromHtml(string $htmlText): string
    {
        if (empty($htmlText) || strpos($htmlText, 'data:image') === false) {
            return $htmlText;
        }

        $doc = $this->loadDocumentFromHtml($htmlText);
        $container = $doc->documentElement;
        $body = $container->childNodes->item(0);
        $childNodes = $body->childNodes;
        $xPath = new DOMXPath($doc);

        // Get all img elements with image data blobs
        $imageNodes = $xPath->query('//img[contains(@src, \'data:image\')]');
        foreach ($imageNodes as $imageNode) {
            $imageSrc = $imageNode->getAttribute('src');
            $newUrl = $this->base64ImageUriToUploadedImageUrl($imageSrc);
            $imageNode->setAttribute('src', $newUrl);
        }

        // Generate inner html as a string
        $html = '';
        foreach ($childNodes as $childNode) {
            $html .= $doc->saveHTML($childNode);
        }

        return $html;
    }

    /**
     * Convert all inline base64 content to uploaded image files.
     * Regex is used to locate the start of data-uri definitions then
     * manual looping over content is done to parse the whole data uri.
     * Attempting to capture the whole data uri using regex can cause PHP
     * PCRE limits to be hit with larger, multi-MB, files.
     */
    protected function extractBase64ImagesFromMarkdown(string $markdown)
    {
        $matches = [];
        $contentLength = strlen($markdown);
        $replacements = [];
        preg_match_all('/!\[.*?]\(.*?(data:image\/.{1,6};base64,)/', $markdown, $matches, PREG_OFFSET_CAPTURE);

        foreach ($matches[1] as $base64MatchPair) {
            [$dataUri, $index] = $base64MatchPair;

            for ($i = strlen($dataUri) + $index; $i < $contentLength; $i++) {
                $char = $markdown[$i];
                if ($char === ')' || $char === ' ' || $char === "\n" || $char === '"') {
                    break;
                }
                $dataUri .= $char;
            }

            $newUrl = $this->base64ImageUriToUploadedImageUrl($dataUri);
            $replacements[] = [$dataUri, $newUrl];
        }

        foreach ($replacements as [$dataUri, $newUrl]) {
            $markdown = str_replace($dataUri, $newUrl, $markdown);
        }

        return $markdown;
    }

    /**
     * Parse the given base64 image URI and return the URL to the created image instance.
     * Returns an empty string if the parsed URI is invalid or causes an error upon upload.
     */
    protected function base64ImageUriToUploadedImageUrl(string $uri): string
    {
        $imageRepo = app()->make(ImageRepo::class);
        $imageInfo = $this->parseBase64ImageUri($uri);

        // Validate extension and content
        if (empty($imageInfo['data']) || !ImageService::isExtensionSupported($imageInfo['extension'])) {
            return '';
        }

        // Validate that the content is not over our upload limit
        $uploadLimitBytes = (config('app.upload_limit') * 1000000);
        if (strlen($imageInfo['data']) > $uploadLimitBytes) {
            return '';
        }

        // Save image from data with a random name
        $imageName = 'embedded-image-' . Str::random(8) . '.' . $imageInfo['extension'];

        try {
            $image = $imageRepo->saveNewFromData($imageName, $imageInfo['data'], 'gallery', $this->page->id);
        } catch (ImageUploadException $exception) {
            return '';
        }

        return $image->url;
    }

    /**
     * Parse a base64 image URI into the data and extension.
     *
     * @return array{extension: string, data: string}
     */
    protected function parseBase64ImageUri(string $uri): array
    {
        [$dataDefinition, $base64ImageData] = explode(',', $uri, 2);
        $extension = strtolower(preg_split('/[\/;]/', $dataDefinition)[1] ?? '');

        return [
            'extension' => $extension,
            'data'      => base64_decode($base64ImageData) ?: '',
        ];
    }

    /**
     * Formats a page's html to be tagged correctly within the system.
     */
    protected function formatHtml(string $htmlText): string
    {
        if (empty($htmlText)) {
            return $htmlText;
        }

        $doc = $this->loadDocumentFromHtml($htmlText);
        $container = $doc->documentElement;
        $body = $container->childNodes->item(0);
        $childNodes = $body->childNodes;
        $xPath = new DOMXPath($doc);

        // Set ids on top-level nodes
        $idMap = [];
        foreach ($childNodes as $index => $childNode) {
            [$oldId, $newId] = $this->setUniqueId($childNode, $idMap);
            if ($newId && $newId !== $oldId) {
                $this->updateLinks($xPath, '#' . $oldId, '#' . $newId);
            }
        }

        // Set ids on nested header nodes
        $nestedHeaders = $xPath->query('//body//*//h1|//body//*//h2|//body//*//h3|//body//*//h4|//body//*//h5|//body//*//h6');
        foreach ($nestedHeaders as $nestedHeader) {
            [$oldId, $newId] = $this->setUniqueId($nestedHeader, $idMap);
            if ($newId && $newId !== $oldId) {
                $this->updateLinks($xPath, '#' . $oldId, '#' . $newId);
            }
        }

        // Ensure no duplicate ids within child items
        $idElems = $xPath->query('//body//*//*[@id]');
        foreach ($idElems as $domElem) {
            [$oldId, $newId] = $this->setUniqueId($domElem, $idMap);
            if ($newId && $newId !== $oldId) {
                $this->updateLinks($xPath, '#' . $oldId, '#' . $newId);
            }
        }

        // Generate inner html as a string
        $html = '';
        foreach ($childNodes as $childNode) {
            $html .= $doc->saveHTML($childNode);
        }

        // Perform required string-level tweaks
        $html = str_replace(' ', '&nbsp;', $html);

        return $html;
    }

    /**
     * Update the all links to the $old location to instead point to $new.
     */
    protected function updateLinks(DOMXPath $xpath, string $old, string $new)
    {
        $old = str_replace('"', '', $old);
        $matchingLinks = $xpath->query('//body//*//*[@href="' . $old . '"]');
        foreach ($matchingLinks as $domElem) {
            $domElem->setAttribute('href', $new);
        }
    }

    /**
     * Set a unique id on the given DOMElement.
     * A map for existing ID's should be passed in to check for current existence.
     * Returns a pair of strings in the format [old_id, new_id].
     */
    protected function setUniqueId(DOMNode $element, array &$idMap): array
    {
        if (!$element instanceof DOMElement) {
            return ['', ''];
        }

        // Stop if there's an existing valid id that has not already been used.
        $existingId = $element->getAttribute('id');
        if (strpos($existingId, 'bkmrk') === 0 && !isset($idMap[$existingId])) {
            $idMap[$existingId] = true;

            return [$existingId, $existingId];
        }

        // Create a unique id for the element
        // Uses the content as a basis to ensure output is the same every time
        // the same content is passed through.
        $contentId = 'bkmrk-' . mb_substr(strtolower(preg_replace('/\s+/', '-', trim($element->nodeValue))), 0, 20);
        $newId = urlencode($contentId);
        $loopIndex = 0;

        while (isset($idMap[$newId])) {
            $newId = urlencode($contentId . '-' . $loopIndex);
            $loopIndex++;
        }

        $element->setAttribute('id', $newId);
        $idMap[$newId] = true;

        return [$existingId, $newId];
    }

    /**
     * Get a plain-text visualisation of this page.
     */
    protected function toPlainText(): string
    {
        $html = $this->render(true);

        return html_entity_decode(strip_tags($html));
    }

    /**
     * Render the page for viewing.
     */
    public function render(bool $blankIncludes = false): string
    {
        $content = $this->page->html ?? '';

        if (!config('app.allow_content_scripts')) {
            $content = HtmlContentFilter::removeScripts($content);
        }

        if ($blankIncludes) {
            $content = $this->blankPageIncludes($content);
        } else {
            $content = $this->parsePageIncludes($content);
        }

        return $content;
    }

    /**
     * Parse the headers on the page to get a navigation menu.
     */
    public function getNavigation(string $htmlContent): array
    {
        if (empty($htmlContent)) {
            return [];
        }

        $doc = $this->loadDocumentFromHtml($htmlContent);
        $xPath = new DOMXPath($doc);
        $headers = $xPath->query('//h1|//h2|//h3|//h4|//h5|//h6');

        return $headers ? $this->headerNodesToLevelList($headers) : [];
    }

    /**
     * Convert a DOMNodeList into an array of readable header attributes
     * with levels normalised to the lower header level.
     */
    protected function headerNodesToLevelList(DOMNodeList $nodeList): array
    {
        $tree = collect($nodeList)->map(function (DOMElement $header) {
            $text = trim(str_replace("\xc2\xa0", '', $header->nodeValue));
            $text = mb_substr($text, 0, 100);

            return [
                'nodeName' => strtolower($header->nodeName),
                'level'    => intval(str_replace('h', '', $header->nodeName)),
                'link'     => '#' . $header->getAttribute('id'),
                'text'     => $text,
            ];
        })->filter(function ($header) {
            return mb_strlen($header['text']) > 0;
        });

        // Shift headers if only smaller headers have been used
        $levelChange = ($tree->pluck('level')->min() - 1);
        $tree = $tree->map(function ($header) use ($levelChange) {
            $header['level'] -= ($levelChange);

            return $header;
        });

        return $tree->toArray();
    }

    /**
     * Remove any page include tags within the given HTML.
     */
    protected function blankPageIncludes(string $html): string
    {
        return preg_replace("/{{@\s?([0-9].*?)}}/", '', $html);
    }

    /**
     * Parse any include tags "{{@<page_id>#section}}" to be part of the page.
     */
    protected function parsePageIncludes(string $html): string
    {
        $matches = [];
        preg_match_all("/{{@\s?([0-9].*?)}}/", $html, $matches);

        foreach ($matches[1] as $index => $includeId) {
            $fullMatch = $matches[0][$index];
            $splitInclude = explode('#', $includeId, 2);

            // Get page id from reference
            $pageId = intval($splitInclude[0]);
            if (is_nan($pageId)) {
                continue;
            }

            // Find page to use, and default replacement to empty string for non-matches.
            /** @var ?Page $matchedPage */
            $matchedPage = Page::visible()->find($pageId);
            $replacement = '';

            if ($matchedPage && count($splitInclude) === 1) {
                // If we only have page id, just insert all page html and continue.
                $replacement = $matchedPage->html;
            } else if ($matchedPage && count($splitInclude) > 1) {
                // Otherwise, if our include tag defines a section, load that specific content
                $innerContent = $this->fetchSectionOfPage($matchedPage, $splitInclude[1]);
                $replacement = trim($innerContent);
            }

            $themeReplacement = Theme::dispatch(
                ThemeEvents::PAGE_INCLUDE_PARSE,
                $includeId,
                $replacement,
                clone $this->page,
                $matchedPage ? (clone $matchedPage) : null,
            );

            // Perform the content replacement
            $html = str_replace($fullMatch, $themeReplacement ?? $replacement, $html);
        }

        return $html;
    }

    /**
     * Fetch the content from a specific section of the given page.
     */
    protected function fetchSectionOfPage(Page $page, string $sectionId): string
    {
        $topLevelTags = ['table', 'ul', 'ol', 'pre'];
        $doc = $this->loadDocumentFromHtml($page->html);

        // Search included content for the id given and blank out if not exists.
        $matchingElem = $doc->getElementById($sectionId);
        if ($matchingElem === null) {
            return '';
        }

        // Otherwise replace the content with the found content
        // Checks if the top-level wrapper should be included by matching on tag types
        $innerContent = '';
        $isTopLevel = in_array(strtolower($matchingElem->nodeName), $topLevelTags);
        if ($isTopLevel) {
            $innerContent .= $doc->saveHTML($matchingElem);
        } else {
            foreach ($matchingElem->childNodes as $childNode) {
                $innerContent .= $doc->saveHTML($childNode);
            }
        }
        libxml_clear_errors();

        return $innerContent;
    }

    /**
     * Create and load a DOMDocument from the given html content.
     */
    protected function loadDocumentFromHtml(string $html): DOMDocument
    {
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $html = '<body>' . $html . '</body>';
        $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));

        return $doc;
    }
}
