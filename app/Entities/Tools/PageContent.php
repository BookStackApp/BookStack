<?php

namespace BookStack\Entities\Tools;

use BookStack\Entities\Models\Page;
use BookStack\Entities\Tools\Markdown\MarkdownToHtml;
use BookStack\Exceptions\ImageUploadException;
use BookStack\Facades\Theme;
use BookStack\Theming\ThemeEvents;
use BookStack\Uploads\ImageRepo;
use BookStack\Uploads\ImageService;
use BookStack\Users\Models\User;
use BookStack\Util\HtmlContentFilter;
use BookStack\Util\HtmlDocument;
use BookStack\Util\WebSafeMimeSniffer;
use Closure;
use DOMElement;
use DOMNode;
use DOMNodeList;
use Illuminate\Support\Str;

class PageContent
{
    public function __construct(
        protected Page $page
    ) {
    }

    /**
     * Update the content of the page with new provided HTML.
     */
    public function setNewHTML(string $html, User $updater): void
    {
        $html = $this->extractBase64ImagesFromHtml($html, $updater);
        $this->page->html = $this->formatHtml($html);
        $this->page->text = $this->toPlainText();
        $this->page->markdown = '';
    }

    /**
     * Update the content of the page with new provided Markdown content.
     */
    public function setNewMarkdown(string $markdown, User $updater): void
    {
        $markdown = $this->extractBase64ImagesFromMarkdown($markdown, $updater);
        $this->page->markdown = $markdown;
        $html = (new MarkdownToHtml($markdown))->convert();
        $this->page->html = $this->formatHtml($html);
        $this->page->text = $this->toPlainText();
    }

    /**
     * Convert all base64 image data to saved images.
     */
    protected function extractBase64ImagesFromHtml(string $htmlText, User $updater): string
    {
        if (empty($htmlText) || !str_contains($htmlText, 'data:image')) {
            return $htmlText;
        }

        $doc = new HtmlDocument($htmlText);

        // Get all img elements with image data blobs
        $imageNodes = $doc->queryXPath('//img[contains(@src, \'data:image\')]');
        /** @var DOMElement $imageNode */
        foreach ($imageNodes as $imageNode) {
            $imageSrc = $imageNode->getAttribute('src');
            $newUrl = $this->base64ImageUriToUploadedImageUrl($imageSrc, $updater);
            $imageNode->setAttribute('src', $newUrl);
        }

        return $doc->getBodyInnerHtml();
    }

    /**
     * Convert all inline base64 content to uploaded image files.
     * Regex is used to locate the start of data-uri definitions then
     * manual looping over content is done to parse the whole data uri.
     * Attempting to capture the whole data uri using regex can cause PHP
     * PCRE limits to be hit with larger, multi-MB, files.
     */
    protected function extractBase64ImagesFromMarkdown(string $markdown, User $updater): string
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

            $newUrl = $this->base64ImageUriToUploadedImageUrl($dataUri, $updater);
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
    protected function base64ImageUriToUploadedImageUrl(string $uri, User $updater): string
    {
        $imageRepo = app()->make(ImageRepo::class);
        $imageInfo = $this->parseBase64ImageUri($uri);

        // Validate user has permission to create images
        if (!$updater->can('image-create-all')) {
            return '';
        }

        // Validate extension and content
        if (empty($imageInfo['data']) || !ImageService::isExtensionSupported($imageInfo['extension'])) {
            return '';
        }

        // Validate content looks like an image via sniffing mime type
        $mimeSniffer = new WebSafeMimeSniffer();
        $mime = $mimeSniffer->sniff($imageInfo['data']);
        if (!str_starts_with($mime, 'image/')) {
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

        $doc = new HtmlDocument($htmlText);

        // Map to hold used ID references
        $idMap = [];
        // Map to hold changing ID references
        $changeMap = [];

        $this->updateIdsRecursively($doc->getBody(), 0, $idMap, $changeMap);
        $this->updateLinks($doc, $changeMap);

        // Generate inner html as a string & perform required string-level tweaks
        $html = $doc->getBodyInnerHtml();
        $html = str_replace(' ', '&nbsp;', $html);

        return $html;
    }

    /**
     * For the given DOMNode, traverse its children recursively and update IDs
     * where required (Top-level, headers & elements with IDs).
     * Will update the provided $changeMap array with changes made, where keys are the old
     * ids and the corresponding values are the new ids.
     */
    protected function updateIdsRecursively(DOMNode $element, int $depth, array &$idMap, array &$changeMap): void
    {
        /* @var DOMNode $child */
        foreach ($element->childNodes as $child) {
            if ($child instanceof DOMElement && ($depth === 0 || in_array($child->nodeName, ['h1', 'h2', 'h3', 'h4', 'h5', 'h6']) || $child->getAttribute('id'))) {
                [$oldId, $newId] = $this->setUniqueId($child, $idMap);
                if ($newId && $newId !== $oldId && !isset($idMap[$oldId])) {
                    $changeMap[$oldId] = $newId;
                }
            }

            if ($child->hasChildNodes()) {
                $this->updateIdsRecursively($child, $depth + 1, $idMap, $changeMap);
            }
        }
    }

    /**
     * Update the all links in the given xpath to apply requires changes within the
     * given $changeMap array.
     */
    protected function updateLinks(HtmlDocument $doc, array $changeMap): void
    {
        if (empty($changeMap)) {
            return;
        }

        $links = $doc->queryXPath('//body//*//*[@href]');
        /** @var DOMElement $domElem */
        foreach ($links as $domElem) {
            $href = ltrim($domElem->getAttribute('href'), '#');
            $newHref = $changeMap[$href] ?? null;
            if ($newHref) {
                $domElem->setAttribute('href', '#' . $newHref);
            }
        }
    }

    /**
     * Set a unique id on the given DOMElement.
     * A map for existing ID's should be passed in to check for current existence,
     * and this will be updated with any new IDs set upon elements.
     * Returns a pair of strings in the format [old_id, new_id].
     */
    protected function setUniqueId(DOMNode $element, array &$idMap): array
    {
        if (!$element instanceof DOMElement) {
            return ['', ''];
        }

        // Stop if there's an existing valid id that has not already been used.
        $existingId = $element->getAttribute('id');
        if (str_starts_with($existingId, 'bkmrk') && !isset($idMap[$existingId])) {
            $idMap[$existingId] = true;

            return [$existingId, $existingId];
        }

        // Create a unique id for the element
        // Uses the content as a basis to ensure output is the same every time
        // the same content is passed through.
        $contentId = 'bkmrk-' . mb_substr(strtolower(preg_replace('/\s+/', '-', trim($element->nodeValue))), 0, 20);
        $newId = urlencode($contentId);
        $loopIndex = 1;

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
        $html = $this->page->html ?? '';

        if (empty($html)) {
            return $html;
        }

        $doc = new HtmlDocument($html);
        $contentProvider = $this->getContentProviderClosure($blankIncludes);
        $parser = new PageIncludeParser($doc, $contentProvider);

        $nodesAdded = 1;
        for ($includeDepth = 0; $includeDepth < 3 && $nodesAdded !== 0; $includeDepth++) {
            $nodesAdded = $parser->parse();
        }

        if ($includeDepth > 1) {
            $idMap = [];
            $changeMap = [];
            $this->updateIdsRecursively($doc->getBody(), 0, $idMap, $changeMap);
        }

        if (!config('app.allow_content_scripts')) {
            HtmlContentFilter::removeScriptsFromDocument($doc);
        }

        return $doc->getBodyInnerHtml();
    }

    /**
     * Get the closure used to fetch content for page includes.
     */
    protected function getContentProviderClosure(bool $blankIncludes): Closure
    {
        $contextPage = $this->page;

        return function (PageIncludeTag $tag) use ($blankIncludes, $contextPage): PageIncludeContent {
            if ($blankIncludes) {
                return PageIncludeContent::fromHtmlAndTag('', $tag);
            }

            $matchedPage = Page::visible()->find($tag->getPageId());
            $content = PageIncludeContent::fromHtmlAndTag($matchedPage->html ?? '', $tag);

            if (Theme::hasListeners(ThemeEvents::PAGE_INCLUDE_PARSE)) {
                $themeReplacement = Theme::dispatch(
                    ThemeEvents::PAGE_INCLUDE_PARSE,
                    $tag->tagContent,
                    $content->toHtml(),
                    clone $contextPage,
                    $matchedPage ? (clone $matchedPage) : null,
                );

                if ($themeReplacement !== null) {
                    $content = PageIncludeContent::fromInlineHtml(strval($themeReplacement));
                }
            }

            return $content;
        };
    }

    /**
     * Parse the headers on the page to get a navigation menu.
     */
    public function getNavigation(string $htmlContent): array
    {
        if (empty($htmlContent)) {
            return [];
        }

        $doc = new HtmlDocument($htmlContent);
        $headers = $doc->queryXPath('//h1|//h2|//h3|//h4|//h5|//h6');

        return $headers->count() === 0 ? [] : $this->headerNodesToLevelList($headers);
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
}
