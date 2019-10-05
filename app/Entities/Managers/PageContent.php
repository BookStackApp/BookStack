<?php namespace BookStack\Entities\Managers;

use BookStack\Entities\Page;
use DOMDocument;
use DOMElement;
use DOMNodeList;
use DOMXPath;

class PageContent
{

    protected $page;

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
        $this->page->html = $this->formatHtml($html);
        $this->page->text = $this->toPlainText();
    }

    /**
     * Formats a page's html to be tagged correctly within the system.
     */
    protected function formatHtml(string $htmlText): string
    {
        if ($htmlText == '') {
            return $htmlText;
        }

        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML(mb_convert_encoding($htmlText, 'HTML-ENTITIES', 'UTF-8'));

        $container = $doc->documentElement;
        $body = $container->childNodes->item(0);
        $childNodes = $body->childNodes;

        // Set ids on top-level nodes
        $idMap = [];
        foreach ($childNodes as $index => $childNode) {
            $this->setUniqueId($childNode, $idMap);
        }

        // Ensure no duplicate ids within child items
        $xPath = new DOMXPath($doc);
        $idElems = $xPath->query('//body//*//*[@id]');
        foreach ($idElems as $domElem) {
            $this->setUniqueId($domElem, $idMap);
        }

        // Generate inner html as a string
        $html = '';
        foreach ($childNodes as $childNode) {
            $html .= $doc->saveHTML($childNode);
        }

        return $html;
    }

    /**
     * Set a unique id on the given DOMElement.
     * A map for existing ID's should be passed in to check for current existence.
     * @param DOMElement $element
     * @param array $idMap
     */
    protected function setUniqueId($element, array &$idMap)
    {
        if (get_class($element) !== 'DOMElement') {
            return;
        }

        // Overwrite id if not a BookStack custom id
        $existingId = $element->getAttribute('id');
        if (strpos($existingId, 'bkmrk') === 0 && !isset($idMap[$existingId])) {
            $idMap[$existingId] = true;
            return;
        }

        // Create an unique id for the element
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
    }

    /**
     * Get a plain-text visualisation of this page.
     */
    protected function toPlainText(): string
    {
        $html = $this->render(true);
        return strip_tags($html);
    }

    /**
     * Render the page for viewing
     */
    public function render(bool $blankIncludes = false) : string
    {
        $content = $this->page->html;

        if (!config('app.allow_content_scripts')) {
            $content = $this->escapeScripts($content);
        }

        if ($blankIncludes) {
            $content = $this->blankPageIncludes($content);
        } else {
            $content = $this->parsePageIncludes($content);
        }

        return $content;
    }

    /**
     * Parse the headers on the page to get a navigation menu
     */
    public function getNavigation(string $htmlContent): array
    {
        if (empty($htmlContent)) {
            return [];
        }

        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML(mb_convert_encoding($htmlContent, 'HTML-ENTITIES', 'UTF-8'));
        $xPath = new DOMXPath($doc);
        $headers = $xPath->query("//h1|//h2|//h3|//h4|//h5|//h6");

        return $headers ? $this->headerNodesToLevelList($headers) : [];
    }

    /**
     * Convert a DOMNodeList into an array of readable header attributes
     * with levels normalised to the lower header level.
     */
    protected function headerNodesToLevelList(DOMNodeList $nodeList): array
    {
        $tree = collect($nodeList)->map(function ($header) {
            $text = trim(str_replace("\xc2\xa0", '', $header->nodeValue));
            $text = mb_substr($text, 0, 100);

            return [
                'nodeName' => strtolower($header->nodeName),
                'level' => intval(str_replace('h', '', $header->nodeName)),
                'link' => '#' . $header->getAttribute('id'),
                'text' => $text,
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
    protected function blankPageIncludes(string $html) : string
    {
        return preg_replace("/{{@\s?([0-9].*?)}}/", '', $html);
    }

    /**
     * Parse any include tags "{{@<page_id>#section}}" to be part of the page.
     */
    protected function parsePageIncludes(string $html) : string
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

            // Find page and skip this if page not found
            $matchedPage = Page::visible()->find($pageId);
            if ($matchedPage === null) {
                $html = str_replace($fullMatch, '', $html);
                continue;
            }

            // If we only have page id, just insert all page html and continue.
            if (count($splitInclude) === 1) {
                $html = str_replace($fullMatch, $matchedPage->html, $html);
                continue;
            }

            // Create and load HTML into a document
            $innerContent = $this->fetchSectionOfPage($matchedPage, $splitInclude[1]);
            $html = str_replace($fullMatch, trim($innerContent), $html);
        }

        return $html;
    }


    /**
     * Fetch the content from a specific section of the given page.
     */
    protected function fetchSectionOfPage(Page $page, string $sectionId): string
    {
        $topLevelTags = ['table', 'ul', 'ol'];
        $doc = new DOMDocument();
        libxml_use_internal_errors(true);
        $doc->loadHTML(mb_convert_encoding('<body>'.$page->html.'</body>', 'HTML-ENTITIES', 'UTF-8'));

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
     * Escape script tags within HTML content.
     */
    protected function escapeScripts(string $html) : string
    {
        if (empty($html)) {
            return $html;
        }

        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xPath = new DOMXPath($doc);

        // Remove standard script tags
        $scriptElems = $xPath->query('//script');
        foreach ($scriptElems as $scriptElem) {
            $scriptElem->parentNode->removeChild($scriptElem);
        }

        // Remove data or JavaScript iFrames
        $badIframes = $xPath->query('//*[contains(@src, \'data:\')] | //*[contains(@src, \'javascript:\')] | //*[@srcdoc]');
        foreach ($badIframes as $badIframe) {
            $badIframe->parentNode->removeChild($badIframe);
        }

        // Remove 'on*' attributes
        $onAttributes = $xPath->query('//@*[starts-with(name(), \'on\')]');
        foreach ($onAttributes as $attr) {
            /** @var \DOMAttr $attr*/
            $attrName = $attr->nodeName;
            $attr->parentNode->removeAttribute($attrName);
        }

        $html = '';
        $topElems = $doc->documentElement->childNodes->item(0)->childNodes;
        foreach ($topElems as $child) {
            $html .= $doc->saveHTML($child);
        }

        return $html;
    }
}
